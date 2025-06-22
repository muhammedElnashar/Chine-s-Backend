<?php

namespace App\Http\Requests;

use App\Models\ExamQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class SubmissionExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_id' => 'required|exists:exams,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:exam_questions,id',
            'answers.*.answer_id' => 'required|exists:exam_answers,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            $examId = $this->input('exam_id');
            $answers = collect($this->input('answers'));
            $submittedQuestionIds = $answers->pluck('question_id');

            $allExamQuestions = ExamQuestion::where('exam_id', $examId)->get();

            $missingQuestions = $allExamQuestions->filter(function ($question) use ($submittedQuestionIds) {
                return !$submittedQuestionIds->contains($question->id);
            });

            foreach ($missingQuestions as $question) {
                $validator->errors()->add(
                    'answers',
                    "You did not answer the question: '{$question->question_text}' (ID #{$question->id})."
                );
            }

            foreach ($answers as $index => $answer) {
                if (!isset($answer['question_id'], $answer['answer_id'])) {
                    continue;
                }

                $valid = \App\Models\ExamAnswer::where('id', $answer['answer_id'])
                    ->where('question_id', $answer['question_id'])
                    ->exists();

                if (!$valid) {
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
