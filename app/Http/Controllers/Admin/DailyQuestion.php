<?php

namespace App\Http\Controllers\Admin;

use App\Enum\DailyExerciseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\DailyExercise;
use App\Models\DailyExerciseQuestion;
use App\Models\DailyExerciseQuestionAnswer;
use App\Services\QuestionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class DailyQuestion extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Request $request)
    {
        $date = request()->input('date');

        if ($date) {
            $exercises = DailyExercise::whereDate('date', $date)->where('type',DailyExerciseTypeEnum::Quiz)->with('questions.answers')->paginate(5);
        } else {
            $exercises = DailyExercise::with('questions.answers')->where('type',DailyExerciseTypeEnum::Quiz)->paginate(5);
        }

        return view('daily-exercise.index', compact('exercises'));
    }

    public function create()
    {
        return view('daily-exercise.create');
    }

    public function store(StoreQuizRequest $request)
    {
        $data = $request->validated();
        $now = now();
        DB::beginTransaction();
        try {
            $exercise = DailyExercise::create([
                'date' => $data['exercise_date'],
                'type' => DailyExerciseTypeEnum::Quiz,
                'title' => $data['title'],
                'description' => $data['description'],
            ]);
            foreach ($data['questions'] as $question) {
                $mediaUrl = null;
                if ($question['question_type'] !== 'text' && isset($question['question_media'])) {
                    $mediaUrl = $question['question_media']->storePublicly('quiz_media', 's3');
                }
                $questionId = DailyExerciseQuestion::insertGetId([
                    'exercise_id' => $exercise->id,
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

                DailyExerciseQuestionAnswer::insert($answers);
            }
            DB::commit();
            return redirect()->route('exercises.index')->with('success', 'Exam created successfully.');

        }catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating exam: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the exam.' );
        }
    }
    public function show(DailyExercise $exercise)
    {
        $exercise->load('questions.answers');

        return view('daily-exercise.show', compact('exercise'));
    }
    public function edit(DailyExercise $exercise)
    {
        $exercise->load('questions.answers');

        return view('daily-exercise.edit', compact('exercise'));
    }
    public function update(UpdateQuizRequest $request, DailyExercise $exercise)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $exercise->update([
                'date'=>$data['exercise_date'],
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            $existingQuestions = $exercise->questions()->with('answers')->get();
            $existingQuestionIds = $existingQuestions->pluck('id')->toArray();
            $submittedQuestionIds = collect($data['questions'])->pluck('id')->filter()->toArray();

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
                        $mediaPath = $questionData['question_media']->storePublicly('quiz_media', 's3');
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
                        DailyExerciseQuestionAnswer::create([
                            'question_id' => $existingQuestion->id,
                            'answer_text' => is_array($answer) ? $answer['text'] : $answer,
                            'is_correct' => ($aIndex == $questionData['correct_answer']),
                        ]);
                    }

                } else {
                    // إنشاء سؤال جديد
                    if ($type !== 'text' && isset($questionData['question_media']) && is_object($questionData['question_media'])) {
                        $mediaPath = $questionData['question_media']->storePublicly('quiz_media', 's3');
                    }

                    $questionModel = DailyExerciseQuestion::create([
                        'exercise_id' => $exercise->id,
                        'question_text' => $questionText,
                        'question_type' => $type,
                        'question_media_url' => $mediaPath,
                        'explanation' => $questionData['explanation'] ?? null,
                    ]);

                    foreach ($questionData['answers'] as $aIndex => $answer) {
                        DailyExerciseQuestionAnswer::create([
                            'question_id' => $questionModel->id,
                            'answer_text' => is_array($answer) ? $answer['text'] : $answer,
                            'is_correct' => ($aIndex == $questionData['correct_answer']),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('exercises.index', )
                ->with('success', 'Exam updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating exam: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the exam: ');
        }
    }

    public function destroy(DailyExercise $exercise)
    {
        DB::beginTransaction();
        try {
            foreach ($exercise->questions as $question) {
                if ($question->question_media_url) {
                    Storage::disk('s3')->delete($question->question_media_url);
                }
                $question->answers()->delete();
            }
            $exercise->questions()->delete();
            $exercise->delete();
            DB::commit();
            return redirect()->route('exercises.index')->with('success', 'Exam deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating exam: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the exam.');
        }
    }

}
