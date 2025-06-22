<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Video;
use App\Models\VideoViews;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VideoAccessController extends Controller
{
    public function getPresignedUrl($id)
    {
        $video = Video::find($id);
        if (!$video){
            return response()->json([
                'status' => false,
                'message' => 'Video not found',
            ], 404);
        }
        $url = VideoService::generatePresignedUrl($video->video_url);
        return response()->json([
            'status' => true,
            'message' => 'Presigned URL generated successfully',
            'data' => [
                'url' => $url,
            ],
        ]);
    }
    public function markVideoAsWatched(Request $request)
    {
        $request->validate([
            "video_id" => [
                'required',
                Rule::exists(Video::class, 'id'),
            ],
        ]);

        $userId = auth()->id();
        $videoId = $request->input('video_id');

        $existingView = VideoViews::where('user_id', $userId)
            ->where('video_id', $videoId)
            ->first();

        if (!$existingView) {
            VideoViews::create([
                'user_id' => $userId,
                'video_id' => $videoId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Video marked as watched.',
        ]);
    }



}
