<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
                ?Storage::disk('s3')->url($this->video_url)
                : null,
            'duration' => $this->duration,
            'can_access' => $hasAccess,
        ];
    }

}
