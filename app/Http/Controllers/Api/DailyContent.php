<?php

namespace App\Http\Controllers\Api;

use App\Enum\DailyExerciseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\SubmissionExamRequest;
use App\Http\Requests\SubmissionQuizRequest;
use App\Http\Resources\DailyQuestionResource;
use App\Http\Resources\DailyQuizResource;
use App\Http\Resources\DailyWordResource;
use App\Http\Resources\ExamAttemptResource;
use App\Http\Resources\ExamResource;
use App\Models\DailyExercise;
use App\Models\DailyExerciseAttempt;
use App\Models\DailyExerciseAttemptAnswer;
use App\Models\DailyExerciseQuestionAnswer;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyContent extends Controller
{
    public function getDailyTextExercise(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $date = $request->input('date') ?? now()->toDateString();
        $dailyQuestions = DailyExercise::whereDate('date', $date)
            ->where('type', DailyExerciseTypeEnum::Quiz)
            ->get();
        return response()->json([
            'status' => true,
            'message' => 'Daily Text Exercise for ' . $date,
            'data' => DailyQuizResource::collection($dailyQuestions)
        ],200);
    }
    public function getDailyAudioExercise(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);
        $date = $request->input('date') ?? now()->toDateString();

        $dailyWord= DailyExercise::whereDate('date', $date)
            ->where('type',DailyExerciseTypeEnum::Audio)
            ->with('audioWords')->get();
        return response()->json([
            'status' => true,
            'message' => 'Daily Audio Word  for ' . $date,
            'data' => DailyWordResource::collection($dailyWord)
        ],200);
    }

    public function ShowDailyExercise ($id)
    {
        $exam = DailyExercise::with('questions.answers')->where('type', DailyExerciseTypeEnum::Quiz)->find($id);
        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'Daily Exercise not found.',
            ], 404);
        }

        $exam->load('questions.answers');
        $user = auth()->user();
        $attempt = DailyExerciseAttempt::with('answers')
            ->where('exercise_id', $exam->id)
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
            'data' => new DailyQuestionResource($exam),
        ]);
    }

    public function submitDailyQuiz(SubmissionQuizRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();
        $examId = $data['exercise_id'];
        // تحقق من أن الطالب لم يقدم الامتحان مسبقًا
        if (DailyExerciseAttempt::where('exercise_id', $examId)->where('student_id', $user->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'You have already attempted this exam.',
            ], 403);
        }
        DB::beginTransaction();
        try {
            $attempt = DailyExerciseAttempt::create([
                'exercise_id' => $examId,
                'student_id' => $user->id,
                'score' => 0,
            ]);

            $score = 0;

            foreach ($data['answers'] as $answerData) {
                $questionId = $answerData['question_id'];
                $answerId = $answerData['answer_id'];

                $answer = DailyExerciseQuestionAnswer::where('id', $answerId)
                    ->where('question_id', $questionId)
                    ->first();

                $isCorrect = $answer && $answer->is_correct;

                if ($isCorrect) {
                    $score++;
                }

                DailyExerciseAttemptAnswer::create([
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
