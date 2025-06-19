<?php

namespace App\Http\Controllers\Admin;

use App\Enum\DailyExerciseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAudioWordRequest;
use App\Models\DailyAudioWord;
use App\Models\DailyExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DailyAudioWordController extends Controller
{
    public function index(Request $request)
    {
        $date = request()->input('date');

        if ($date) {
            $exercises = DailyExercise::whereDate('date', $date)->where('type',DailyExerciseTypeEnum::Audio)
                ->with('audioWords')
                ->orderBy('date', 'desc')
                ->paginate(1);
        } else {
            $exercises = DailyExercise::where('type',DailyExerciseTypeEnum::Audio)->with('audioWords')
                ->orderBy('date', 'desc')
                ->paginate(5);
        }

        return view('daily-words.index', compact('exercises'));
    }

    public function create()
    {
        return view('daily-words.create');
    }
    public function store(StoreAudioWordRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();

        try {
            $exercise = DailyExercise::firstOrCreate([
                'date' => $validated['exercise_date'],
                'type' => DailyExerciseTypeEnum::Audio,
                'title' => $validated['title'],
                'description' => $validated['description'],
            ]);

            foreach ($validated['words'] as $word) {
                if (isset($word['audio']) && $word['audio']->isValid()) {
                    $path = $word['audio']->storePublicly('dailyWords', 's3');
                    DailyAudioWord::create([
                        'exercise_id' => $exercise->id,
                        'audio_file' => $path,
                        'word_meaning' => $word['meaning'],
                        'created_at'=> now(),
                    ]);
                }
            }


            DB::commit();

            return redirect()->route('words.index')->with('success', 'Audio words have been saved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error saving audio words: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'error While Saving' ]);
        }
    }

    public function destroy(DailyExercise $word)
    {
        foreach ($word->audioWords as $file) {
            if ($file->audio_file) {
                Storage::disk('s3')->delete($file->audio_file);
            }
        }
        $word->audioWords()->delete();
        $word->delete();

        return redirect()->route('words.index')->with('success', 'Exercise and its Audio have been deleted successfully.');
    }

}
