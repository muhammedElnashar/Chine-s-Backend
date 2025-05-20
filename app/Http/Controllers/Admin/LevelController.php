<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLevelRequest;
use App\Http\Requests\UpdateLevelRequest;
use App\Models\Course;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $levels = $course->levels()->paginate(5);
        return view('levels.index', compact('course', 'levels'));    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        return view('levels.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLevelRequest $request,Course $course)
    {
        $data = $request->validated();
        $maxPosition = $course->levels()->max('position') ?? 0;
        $data['position'] = $maxPosition + 1;
        $course->levels()->create($data);
        return redirect()->route('courses.levels.index', $course)->with('success', 'Level created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Level $level)
    {
        return view('levels.edit', compact('course', 'level'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLevelRequest $request, Course $course, Level $level)
    {
        $data = $request->validated();
        $level->update($data);

        return redirect()->route('courses.levels.index', $course)->with('success', 'Level updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Level $level)
    {
        if ($level->course_id !== $course->id) {
            abort(403, 'This level does not belong to the specified course.');
        }

        $level->delete();

        return redirect()
            ->route('courses.levels.index', $course)
            ->with('success', 'Level deleted successfully.');
    }
}
