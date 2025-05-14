<?php

namespace App\Http\Requests;

use App\Models\DailyExercise;
use Illuminate\Foundation\Http\FormRequest;

class StoreAudioWordRequest extends FormRequest
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
            'exercise_date' => 'required|date',
            'words' => 'required|array|min:2',
            'words.*.audio' => 'required|file|mimes:mp3,wav',
            'words.*.meaning' => 'required|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $date = $this->input('exercise_date');

            $exercise = DailyExercise::where('date', $date)->first();

            if ($exercise && $exercise->audioWords()->exists()) {
                $validator->errors()->add('exercise_date', 'Audio Exercise already exists for this date.');
            }

        });
    }
}
