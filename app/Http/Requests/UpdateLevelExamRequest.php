<?php


namespace App\Http\Requests;

class UpdateLevelExamRequest extends StoreLevelExamRequest
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
            'questions.*.id' => 'nullable|integer|exists:level_exam_questions,id',
            'questions.*.question_text' => 'required|string|max:1000',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.id' => 'nullable|integer|exists:level_exam_answers,id',
            'questions.*.answers.*.text' => 'required|string|max:500',
            'questions.*.correct_answer' => 'required|integer|in:0,1,2,3',
        ];
    }}
