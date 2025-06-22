<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmissionExamRequest;
use App\Http\Resources\ExamAttemptResource;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function show($id)
    {
        $exam = Exam::with('questions.answers')->find($id);
        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'Exam not found.',
            ], 404);
        }

        $exam->load('questions.answers');
        $user = auth()->user();

        $attempt = ExamAttempt::with('answers')
            ->where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->first();

        if ($attempt) {
            $exam->studentAnswers = $attempt->answers;
            $exam->score = $attempt->score;
            return response()->json([
                'status' => true,
                'attempted' => true,
                'data' => new ExamAttemptResource($exam),
            ]);
        }

        return response()->json([
            'status' => true,
            'attempted' => false,
            'data' => new ExamResource($exam),
        ]);
    }

    public function submit(SubmissionExamRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();
        $examId = $data['exam_id'];

        // تحقق من أن الطالب لم يقدم الامتحان مسبقًا
        if (ExamAttempt::where('exam_id', $examId)->where('student_id', $user->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'You have already attempted this exam.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            $attempt = ExamAttempt::create([
                'exam_id' => $examId,
                'student_id' => $user->id,
                'score' => 0,
            ]);

            $score = 0;

            foreach ($data['answers'] as $answerData) {
                $questionId = $answerData['question_id'];
                $answerId = $answerData['answer_id'];

                $answer = ExamAnswer::where('id', $answerId)
                    ->where('question_id', $questionId)
                    ->first();

                $isCorrect = $answer && $answer->is_correct;

                if ($isCorrect) {
                    $score++;
                }

                ExamAttemptAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'is_correct' => $isCorrect,
                ]);
            }

            $attempt->update(['score' => $score]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Exam submitted successfully.',
                'score' => $score,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while submitting the exam.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }
}
