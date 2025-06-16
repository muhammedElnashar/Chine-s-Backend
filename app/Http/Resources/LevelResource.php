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

        return [
            'id' => $this->id,
            'title' => $this->title,
            'position' => $this->position,
            'totalDuration' => $this->videos->sum('duration'),
            'videoCount' => $this->videos->count(),
            'files' => FileResource::collection($this->files),
            'videos' => VideoResource::collection($this->videos),
            'exam' =>  new ExamResource($this->exam)
        ];
    }

}
