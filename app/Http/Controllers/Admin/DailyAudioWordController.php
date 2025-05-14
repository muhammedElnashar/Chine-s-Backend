<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAudioWordRequest;
use App\Models\DailyAudioWord;
use App\Models\DailyExercise;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyAudioWordController extends Controller
{
    public function index(Request $request)
    {
        $date = request()->input('date');

        if ($date) {
            $exercises = DailyExercise::whereDate('date', $date)->with('audioWords')->paginate(5);
        } else {
            $exercises = DailyExercise::whereDate('date', today())->with('audioWords')->paginate(5);
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
            ]);

            foreach ($validated['words'] as $word) {
                $path = $word['audio']->store('audio_words', 'public');
                DailyAudioWord::create([
                    'daily_exercise_id' => $exercise->id,
                    'audio_file' => $path,
                    'word_meaning' => $word['meaning'],
                ]);
            }

            DB::commit();

            return redirect()->route('words.index')->with('success', 'Audio words have been saved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'error While Savsing' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $exercise = DailyExercise::findOrFail($id);

        $exercise->audioWords()->delete();

        if (!$exercise->questions()->exists()) {
            $exercise->delete();
        }
        return redirect()->route('words.index')->with('success', 'Exercise and its Audio have been deleted successfully.');
    }

}
