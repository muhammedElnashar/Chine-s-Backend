<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class QuestionWithAttemptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $studentAnswer = $this->studentAnswer;

        return [
            'id' => $this->id,
            'question_type' => $this->question_type,
            'question_text' => $this->question_text,
            $this->mergeWhen($this->question_type !== 'text', [
                'question_media_url' => $this->question_media_url
                    ? Storage::disk('s3')->url($this->question_media_url)
                    : null,
            ]),

            'explanation' => $this->explanation,
            'student_answer_id' => $studentAnswer?->answer_id,
            'student_correct' => (bool) $studentAnswer?->is_correct,
            'answers' => AnswerWithAttemptCollection::make($this->answers)->withStudentAnswer($studentAnswer),
        ];
    }
}
