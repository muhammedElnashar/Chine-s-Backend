<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AnswerWithAttemptCollection extends ResourceCollection
{
    protected $studentAnswerId = null;

    public function withStudentAnswer($studentAnswer)
    {
        $this->studentAnswerId = $studentAnswer?->answer_id;
        return $this;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request): array
    {
        return $this->collection->map(function ($answer) {
            return [
                'id' => $answer->id,
                'answer_text' => $answer->answer_text,
                'is_correct' => (bool) $answer->is_correct,
                'selected_by_student' => $this->studentAnswerId === $answer->id,
            ];
        })->toArray();
    }
}
