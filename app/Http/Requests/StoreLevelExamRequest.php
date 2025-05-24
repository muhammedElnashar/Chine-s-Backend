<?php

namespace App\Http\Requests;

use App\Models\LevelExam;
use Illuminate\Foundation\Http\FormRequest;

class StoreLevelExamRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:1000',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*' => 'required|string|max:500',
            'questions.*.correct_answer' => 'required|integer|in:0,1,2,3',
            ];
    }
    public function messages(): array
    {
        $messages= [
            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'questions.required' => 'At least one question is required.',
            'questions.array' => 'Questions must be in an array format.',
            'questions.min' => 'At least one question is required.',
        ];
        // Dynamic messages for each question
        foreach ($this->input('questions', []) as $qIndex => $question) {
            $qNumber = $qIndex + 1;

            $messages["questions.$qIndex.question_text.required"] = "Question $qNumber: The question text is required.";
            $messages["questions.$qIndex.question_text.string"] = "Question $qNumber: The question text must be a string.";
            $messages["questions.$qIndex.question_text.max"] = "Question $qNumber: The question text may not be greater than 255 characters.";

            $messages["questions.$qIndex.answers.required"] = " Answers are required.";
            $messages["questions.$qIndex.answers.array"] = " Answers must be in an array format.";
            $messages["questions.$qIndex.answers.size"] = " Exactly 4 answers are required.";

            foreach (($question['answers'] ?? []) as $aIndex => $answer) {
                $aNumber = $aIndex + 1;
                $messages["questions.$qIndex.answers.$aIndex.required"] = " Answer $aNumber: This answer is required.";
                $messages["questions.$qIndex.answers.$aIndex.string"] = " Answer $aNumber: The answer must be a string.";
                $messages["questions.$qIndex.answers.$aIndex.max"] = "Answer $aNumber: The answer may not be greater than 255 characters.";
            }

            $messages["questions.$qIndex.correct_answer.required"] = "Question $qNumber: You must select a correct answer.";
            $messages["questions.$qIndex.correct_answer.integer"] = "Question $qNumber: The correct answer value must be an integer.";
            $messages["questions.$qIndex.correct_answer.in"] = "Question $qNumber: The correct answer must be one of the 4 options.";
        }

        return $messages;
    }

}
