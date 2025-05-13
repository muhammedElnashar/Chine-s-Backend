<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionsRequest;
use App\Models\DailyExercise;
use App\Services\QuestionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

class DailyQuestion extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->middleware('auth');
        $this->questionService = $questionService;

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
        $this->questionService->storeQuestions($request->validated());

        return redirect()->route('questions.index')->with('success', 'Question created successfully.');
    }

    public function destroy($id)
    {
        $exercise = DailyExercise::findOrFail($id);

        $exercise->questions()->delete();

        $exercise->delete();

        return redirect()->route('questions.index')->with('success', 'Exercise and its questions have been deleted successfully.');
    }
}
