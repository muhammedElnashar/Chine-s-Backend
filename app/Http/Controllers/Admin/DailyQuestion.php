<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionsRequest;
use App\Models\DailyExercise;
use App\Models\DailyTextQuestion;
use App\Models\DailyTextQuestionAnswer;
use App\Services\QuestionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class DailyQuestion extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Request $request)
    {
        $date = request()->input('date');

        if ($date) {
            $exercises = DailyExercise::whereDate('date', $date)->with('questions.answers')->paginate(5);
        } else {
            $exercises = DailyExercise::whereDate('date', today())->with('questions.answers')->paginate(5);
        }

        return view('daily-questions.index', compact('exercises'));
    }

    public function create()
    {
        return view('daily-questions.create');
    }

    public function store(StoreQuestionsRequest $request)
    {

        $validated = $request->validated();
        DB::beginTransaction();

        try {
            $exercise = DailyExercise::firstOrCreate(['date' => $validated['exercise_date']]);
            $questionsData = [];
            foreach ($validated['questions'] as $questionData) {
                $questionsData[] = [
                    'daily_exercise_id' => $exercise->id,
                    'question_text' => $questionData['question_text'],
                    'created_at' => now(),
                ];
            }

            DailyTextQuestion::insert($questionsData);

            $questions = DailyTextQuestion::where('daily_exercise_id', $exercise->id)->get();

            $answersData = [];
            foreach ($questions as $key => $question) {
                $questionData = $validated['questions'][$key];
                foreach ($questionData['answers'] as $i => $answerText) {
                    $answersData[] = [
                        'daily_text_question_id' => $question->id,
                        'answer_text' => $answerText,
                        'is_correct' => $i == $questionData['correct_answer'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            DailyTextQuestionAnswer::insert($answersData);

            DB::commit();

            return redirect()->route('questions.index')->with('success', 'Questions have been created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error While Saving ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $exercise = DailyExercise::findOrFail($id);

        $exercise->questions()->delete();

        if (!$exercise->audioWords()->exists()) {
            $exercise->delete();
        }
        return redirect()->route('questions.index')->with('success', 'Exercise and its questions have been deleted successfully.');
    }
}
