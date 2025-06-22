<?php

namespace App\Http\Requests;

use App\Models\DailyExerciseQuestion;
use App\Models\DailyExerciseQuestionAnswer;
use App\Models\ExamQuestion;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmissionQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'exercise_id' => 'required|exists:daily_exercises,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:daily_exercise_questions,id',
            'answers.*.answer_id' => 'required|exists:daily_exercise_question_answers,id',
        ];
    }
    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            $exerciseId = $this->input('exercise_id');
            $answers = collect($this->input('answers'));
            $submittedQuestionIds = $answers->pluck('question_id')->toArray();

            $expectedQuestions = DailyExerciseQuestion::where('exercise_id', $exerciseId)->get();
            $missing = $expectedQuestions->whereNotIn('id', $submittedQuestionIds);

            foreach ($missing as $question) {
                $validator->errors()->add(
                    'answers',
                    "You did not answer the question: '{$question->question_text}' (ID #{$question->id})."
                );
            }

            foreach ($answers as $index => $answer) {
                if (empty($answer['question_id']) || empty($answer['answer_id'])) continue;

                $isValid = DailyExerciseQuestionAnswer::where([
                    ['id', $answer['answer_id']],
                    ['question_id', $answer['question_id']],
                ])->exists();

                if (!$isValid) {
                    $validator->errors()->add(
                        "answers[$index].answer_id",
                        "Answer ID {$answer['answer_id']} does not belong to Question ID {$answer['question_id']}."
                    );
                }
            }
        });
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Your submission could not be processed. Please check the validation errors.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
