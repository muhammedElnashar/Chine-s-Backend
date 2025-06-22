<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class SectionDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = Auth::id();
        $watchedCount = $this->videos->filter(function ($video) use ($userId) {
            return $video->views->contains('user_id', $userId);
        })->count();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'position' => $this->position,
            'totalDuration' => $this->videos->sum('duration'),
            'videoCount' => $this->videos->count(),
            'VideoHasWatched'=> $watchedCount,
            'files' => FileResource::collection($this->files),
            'videos' => VideoResource::collection($this->videos),
            'exam' => new LevelExamResource($this->exam),
        ];
    }

}
