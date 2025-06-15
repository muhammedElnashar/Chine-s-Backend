<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseResource extends JsonResource
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
            'image' => $this->image ? Storage::disk('s3')->url($this->image) : null,
            'type' => $this->type,
            'price' => $this->is_free ? null : $this->price,
            'levels' => $this->levels->map(function ($level) {
                $totalDuration = $level->videos->sum('duration');
                $videoCount = $level->videos->count();

                return [
                    'id' => $level->id,
                    'title' => $level->title,
                    'description' => $level->description,
                    'position' => $level->position,
                    'videos_count' => $videoCount,
                    'total_duration' => $totalDuration,
                ];
            }),
        ];
    }
}
