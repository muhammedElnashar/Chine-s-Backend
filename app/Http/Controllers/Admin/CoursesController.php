<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoursesController extends Controller
{
    public function index()
    {
        $courses=Course::paginate(5);
        return view('courses.index',compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }
    public function store(StoreCourseRequest $request)
    {
        $data=$request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images/courses', 'public');
        }
        Course::create($data);
        return redirect()->route('courses.index')->with('success', 'Course created successfully.');

    }

    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    public function update(UpdateCourseRequest $request,Course $course)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $path = $request->file('image')->store('images/courses', 'public');
            $data['image'] = $path;
        }
        $course->update($data);
        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }
    public function destroy(Course $course)
    {
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }
}
