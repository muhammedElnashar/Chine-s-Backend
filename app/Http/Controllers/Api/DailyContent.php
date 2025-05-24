<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DailyQuestionResource;
use App\Http\Resources\DailyWordResource;
use App\Models\DailyExercise;
use Illuminate\Http\Request;

class DailyContent extends Controller
{
    public function getDailyTextExercise()
    {
        $dailyQuestion= DailyExercise::whereDate('date', today())->with('questions.answers')->get();
        return response()->json([
            'status' => true,
            'message' => 'Daily Text Exercise',
            'data' => DailyQuestionResource::collection($dailyQuestion)
        ],200);
    }
    public function getDailyAudioExercise()
    {
        $dailyWord= DailyExercise::whereDate('date', today())->with('audioWords')->get();
        return response()->json([
            'status' => true,
            'message' => 'Daily Audio Exercise',
            'data' => DailyWordResource::collection($dailyWord)
        ],200);
    }
}
