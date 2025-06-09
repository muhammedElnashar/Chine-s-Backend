<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLevelExamRequest;
use App\Http\Requests\UpdateLevelExamRequest;
use App\Models\Course;
use App\Models\Level;
use App\Models\LevelExam;
use App\Models\LevelExamAnswer;
use App\Models\LevelExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LevelExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course, Level $level, Request $request)
    {
        $query = LevelExam::where('level_id', $level->id);

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

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
        DB::beginTransaction();
        try {
            $exam = LevelExam::create([
                'level_id' => $level->id,
                'title' => $data['title'],
                'description' => $data['description'],
            ]);
            foreach ($data['questions'] as $question) {
                $mediaUrl = null;
                if ($question['question_type'] != 'text' && isset($question['question_media'])) {
                    $mediaUrl = $question['question_media']->storePublicly('questions_media', 's3');
                }
                $questionId = LevelExamQuestion::insertGetId([
                    'level_exam_id' => $exam->id,
                    'question_type' => $question['question_type'],
                    'question_text' => $question['question_type'] == 'text' ? $question['question_text'] : null,
                    'question_media_url' => $mediaUrl,
                    'explanation' => $question['explanation'] ?? null,
                    'created_at' => now(),
                ]);
                foreach ($question['answers'] as $index => $answer) {
                    LevelExamAnswer::insert([
                        'question_id' => $questionId,
                        'answer_text' => $answer,
                        'is_correct' => ($index == $question['correct_answer']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('courses.levels.exams.index', [$course, $level])->with('success', 'Exam created successfully.');

        }catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while creating the exam.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Level $level, LevelExam $exam)
    {
        $exam->load('questions.answers'); // لتحميل العلاقات

        return view('level-exams.show', compact('course', 'level', 'exam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Level $level, LevelExam $exam)
    {
        $exam->load('questions.answers');

        return view('level-exams.edit', compact('course', 'level', 'exam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLevelExamRequest $request, Course $course, Level $level, LevelExam $exam)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $exam->update([
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            foreach ($exam->questions as $question) {
                if ($question->question_media_url) {
                    Storage::disk('s3')->delete($question->question_media_url);
                }
                $question->answers()->delete();
            }
            $exam->questions()->delete();

            foreach ($data['questions'] as $qIndex => $question) {
                $mediaPath = null;
                $type = $question['question_type'] ?? 'text';

                if ($type !== 'text' && isset($question['question_media']) && is_object($question['question_media'])) {
                    $mediaPath = $question['question_media']->storePublicly('questions_media', 's3');
                }
                $questionText = $type === 'text' ? $question['question_text'] : null;

                $questionModel = LevelExamQuestion::create([
                    'level_exam_id' => $exam->id,
                    'question_text' => $questionText,
                    'question_type' => $type,
                    'question_media_url' => $mediaPath,
                    'explanation' => $question['explanation'] ?? null,
                ]);

                // حفظ الإجابات
                foreach ($question['answers'] as $index => $answer) {
                    LevelExamAnswer::create([
                        'question_id' => $questionModel->id,
                        'answer_text' => is_array($answer) ? $answer['text'] : $answer,
                        'is_correct' => ($index == $question['correct_answer']),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('courses.levels.exams.index', [$course, $level])
                ->with('success', 'Exam updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while updating the exam: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Level $level, LevelExam $exam)
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
            return redirect()->back()->with('error', 'An error occurred while deleting the exam.');
        }
    }
}
