<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamAttemptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'student_score' => $this->score,
            'total_score' => $this->questions->count(), // ← عدد الأسئلة
            'questions' => new ExamAttemptQuestionCollection($this->questions, $this->studentAnswers),
        ];

    }
}
