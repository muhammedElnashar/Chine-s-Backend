<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'image' => asset("storage/" . $this->image),
        'type' => $this->type,
        'price' => $this->price,
        'levels' => LevelResource::collection($this->levels),
    ];
    }
}
