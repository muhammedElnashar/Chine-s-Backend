<?php

namespace App\Http\Controllers\Api;

use App\Enum\DailyExerciseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\DailyQuestionResource;
use App\Http\Resources\DailyWordResource;
use App\Models\DailyExercise;
use Illuminate\Http\Request;

class DailyContent extends Controller
{
    public function getDailyTextExercise(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $date = $request->input('date') ?? now()->toDateString();
        $dailyQuestions = DailyExercise::whereDate('date', $date)
            ->where('type', DailyExerciseTypeEnum::Quiz)
            ->with('questions.answers')
            ->get();
        return response()->json([
            'status' => true,
            'message' => 'Daily Text Exercise for ' . $date,
            'data' => DailyQuestionResource::collection($dailyQuestions)
        ],200);
    }
    public function getDailyAudioExercise(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);
        $date = $request->input('date') ?? now()->toDateString();

        $dailyWord= DailyExercise::whereDate('date', $date)
            ->where('type',DailyExerciseTypeEnum::Audio)
            ->with('audioWords')->get();
        return response()->json([
            'status' => true,
            'message' => 'Daily Audio Word  for ' . $date,
            'data' => DailyWordResource::collection($dailyWord)
        ],200);
    }
}
