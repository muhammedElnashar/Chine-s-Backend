<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
            'video_url' => asset("https://chines-app-courses.s3.us-east-1.amazonaws.com/" . $this->video_url),
            'duration' => $this->duration,
        ];
    }
}
