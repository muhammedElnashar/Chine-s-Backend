<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLevelExamRequest;
use App\Http\Requests\UpdateLevelExamRequest;
use App\Models\Course;
use App\Models\Level;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course, Level $level, Request $request)
    {
        $query = Exam::where('level_id', $level->id);

        $exams = $query->latest()->paginate(10);

        return view('level-exams.index', compact('course', 'level', 'exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course, Level $level)
    {
        return view('level-exams.create', compact('course', 'level'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLevelExamRequest $request, Course $course, Level $level)
    {
        $data = $request->validated();
        $now = now();

        DB::beginTransaction();
        try {
            $exam = Exam::create([
                'course_id' => $course->id,
                'level_id' => $level->id ,
                'title' => $data['title'],
                'description' => $data['description'],
            ]);
            foreach ($data['questions'] as $question) {
                $mediaUrl = null;
                if ($question['question_type'] !== 'text' && isset($question['question_media'])) {
                    $mediaUrl = $question['question_media']->storePublicly('questions_media', 's3');
                }
                $questionId = ExamQuestion::insertGetId([
                    'exam_id' => $exam->id,
                    'question_type' => $question['question_type'],
                    'question_text' => $question['question_text'],
                    'question_media_url' => $mediaUrl,
                    'explanation' => $question['explanation'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $answers = [];
                foreach ($question['answers'] as $index => $answer) {
                    $answers[] = [
                        'question_id' => $questionId,
                        'answer_text' => $answer,
                        'is_correct' => ($index == $question['correct_answer']),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                ExamAnswer::insert($answers);
            }
            DB::commit();
            return redirect()->route('courses.levels.exams.index', [$course, $level])->with('success', 'Exam created successfully.');

        }catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating exam: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the exam.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Level $level, Exam $exam)
    {
        $exam->load('questions.answers');

        return view('level-exams.show', compact('course', 'level', 'exam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Level $level, Exam $exam)
    {
        $exam->load('questions.answers');

        return view('level-exams.edit', compact('course', 'level', 'exam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLevelExamRequest $request, Course $course, Level $level, Exam $exam)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $exam->update([
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            $existingQuestions = $exam->questions()->with('answers')->get();
            $existingQuestionIds = $existingQuestions->pluck('id')->toArray();
            $submittedQuestionIds = collect($data['questions'])->pluck('id')->filter()->toArray();

            // حذف الأسئلة التي لم تعد موجودة
            $questionsToDelete = array_diff($existingQuestionIds, $submittedQuestionIds);

            foreach ($existingQuestions as $existingQuestion) {
                if (in_array($existingQuestion->id, $questionsToDelete)) {
                    if ($existingQuestion->question_media_url) {
                        Storage::disk('s3')->delete($existingQuestion->question_media_url);
                    }
                    $existingQuestion->answers()->delete();
                    $existingQuestion->delete();
                }
            }

            // تحديث أو إنشاء الأسئلة الجديدة
            foreach ($data['questions'] as $qIndex => $questionData) {
                $type = $questionData['question_type'] ?? 'text';
                $mediaPath = null;
                $questionText = $questionData['question_text'];

                if (!empty($questionData['id'])) {
                    // تعديل سؤال موجود
                    $existingQuestion = $existingQuestions->firstWhere('id', $questionData['id']);

                    if (!$existingQuestion) {
                        continue; // تخطي إن لم يتم العثور عليه
                    }

                    // حذف الملف السابق إن تغير النوع أو تم رفع ملف جديد
                    if (
                        $existingQuestion->question_media_url &&
                        (
                            $existingQuestion->question_type !== $type ||
                            ($type !== 'text' && isset($questionData['question_media']) && is_object($questionData['question_media']))
                        )
                    ) {
                        Storage::disk('s3')->delete($existingQuestion->question_media_url);
                        $existingQuestion->update(['question_media_url' => null]);
                    }

                    // رفع الملف الجديد إن وُجد
                    if ($type !== 'text' && isset($questionData['question_media']) && is_object($questionData['question_media'])) {
                        $mediaPath = $questionData['question_media']->storePublicly('questions_media', 's3');
                    }

                    $existingQuestion->update([
                        'question_text' => $questionText,
                        'question_type' => $type,
                        'explanation' => $questionData['explanation'] ?? null,
                        'question_media_url' => $mediaPath ?: $existingQuestion->question_media_url,
                    ]);

                    // حذف وإعادة حفظ الإجابات
                    $existingQuestion->answers()->delete();

                    foreach ($questionData['answers'] as $aIndex => $answer) {
                        ExamAnswer::create([
                            'question_id' => $existingQuestion->id,
                            'answer_text' => is_array($answer) ? $answer['text'] : $answer,
                            'is_correct' => ($aIndex == $questionData['correct_answer']),
                        ]);
                    }

                } else {
                    // إنشاء سؤال جديد
                    if ($type !== 'text' && isset($questionData['question_media']) && is_object($questionData['question_media'])) {
                        $mediaPath = $questionData['question_media']->storePublicly('questions_media', 's3');
                    }

                    $questionModel = ExamQuestion::create([
                        'exam_id' => $exam->id,
                        'question_text' => $questionText,
                        'question_type' => $type,
                        'question_media_url' => $mediaPath,
                        'explanation' => $questionData['explanation'] ?? null,
                    ]);

                    foreach ($questionData['answers'] as $aIndex => $answer) {
                        ExamAnswer::create([
                            'question_id' => $questionModel->id,
                            'answer_text' => is_array($answer) ? $answer['text'] : $answer,
                            'is_correct' => ($aIndex == $questionData['correct_answer']),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('courses.levels.exams.index', [$course, $level])
                ->with('success', 'Exam updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating exam: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the exam: ');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Level $level, Exam $exam)
    {
        DB::beginTransaction();
        try {
            foreach ($exam->questions as $question) {
                if ($question->question_media_url) {
                    Storage::disk('s3')->delete($question->question_media_url);
                }
                $question->answers()->delete();
            }
            $exam->questions()->delete();
            $exam->delete();
            DB::commit();
            return redirect()->route('courses.levels.exams.index', [$course, $level])->with('success', 'Exam deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating exam: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the exam.');
        }
    }
}
