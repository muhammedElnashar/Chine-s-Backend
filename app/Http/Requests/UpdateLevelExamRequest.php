<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLevelExamRequest extends FormRequest
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
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:exam_questions,id',
            'questions.*.question_text' => 'nullable|string|max:1000',
            'questions.*.question_type' => 'required|in:text,image,video,audio',
            'questions.*.question_media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,mp3,wav',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*' => 'required|max:1000',
            'questions.*.correct_answer' => 'required|integer|min:0',


        ];
    }
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->input('questions', []) as $index => $question) {
                if (($question['question_type'] ?? null) === 'text' && empty($question['question_text'])) {
                    $validator->errors()->add("questions.$index.question_text", 'The question text is required when type is text.');
                }
            }
        });
    }

}
