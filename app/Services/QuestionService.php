<?php

namespace App\Services;

use App\Helpers\ErrorHandler;
use App\Models\DailyExercise;
use App\Models\DailyTextQuestion;
use App\Models\DailyTextQuestionAnswer;
use Illuminate\Support\Facades\DB;

class QuestionService
{
    public function storeQuestions(array $validated)
    {
        DB::beginTransaction();

        try {
            $exercise = DailyExercise::create([
                'date' => $validated['exercise_date'],
            ]);

            $questionsData = [];
            $answersData = [];

            foreach ($validated['questions'] as $index => $questionData) {
                $questionsData[] = [
                    'daily_exercise_id' => $exercise->id,
                    'question_text' => $questionData['question_text'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($questionData['answers'] as $answerIndex => $answerText) {
                    $answersData[] = [
                        'daily_text_question_id' => $exercise->id,
                        'answer_text' => $answerText,
                        'is_correct' => ($answerIndex == $questionData['correct_answer']) ? true : false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            DailyTextQuestion::insert($questionsData);

            $questions = DailyTextQuestion::where('daily_exercise_id', $exercise->id)->get();

            foreach ($questions as $key => $question) {
                $start = $key * 4;
                $end = ($key + 1) * 4;
                $questionAnswers = array_slice($answersData, $start, 4);

                foreach ($questionAnswers as $answer) {
                    $answer['daily_text_question_id'] = $question->id;
                }


                DailyTextQuestionAnswer::insert($questionAnswers);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }
}

