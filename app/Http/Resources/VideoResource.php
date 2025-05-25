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
        $user = $request->user();
        $level = $this->level;

        $hasAccess = $level->is_free || ($user && $user->hasPurchasedLevel($level->id));

        return [
            'id' => $this->id,
            'title' => $this->title,

            'video_url' => $hasAccess
                ? asset("https://chines-app-courses.s3.us-east-1.amazonaws.com/" . $this->video_url)
                : null,
            'duration' => $this->duration,
            'can_access' => $hasAccess,
        ];
    }

}
