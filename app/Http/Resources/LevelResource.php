<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $hasAccess = $this->is_free || ($user && $user->hasPurchasedLevel($this->id));

        return [
            'id' => $this->id,
            'title' => $this->title,
            'position' => $this->position,
            'price' => $this->is_free ? null : $this->price,
            'videos' => $hasAccess ? VideoResource::collection($this->videos) : [],
            'videos_message' => $hasAccess ? null : 'You need to purchase this level to view its videos.',
            'exam' => $hasAccess ? new ExamResource($this->exam) : null,
            'exam_message' => $hasAccess ? null : 'You need to purchase this level to access the exam.',
            'can_access' => $hasAccess,
        ];
    }

}
