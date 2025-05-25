<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LevelResource;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{


    public function getLevelDetails($id, Request $request)
    {

        $level = Level::with(['videos', 'exam.questions.answers'])->find($id);
        if (!$level){
            return response()->json([
                'status' => false,
                'message' => 'Level not found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => new LevelResource($level),
        ]);
    }
}
