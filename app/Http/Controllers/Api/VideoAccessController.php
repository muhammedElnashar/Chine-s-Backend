<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\Request;

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
}
