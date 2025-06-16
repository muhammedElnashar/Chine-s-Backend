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
        $isFree = $this->type === 'free';

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image ? Storage::disk('s3')->url($this->image) : null,
            'type' => $this->type,
            'price' => $isFree ? null : $this->price,
            'levels' => LevelResource::collection($this->whenLoaded('levels')),
            'course_exam' => new ExamResource($this->exam ),
        ];
    }

}
