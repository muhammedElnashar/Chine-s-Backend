<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Level;
use App\Models\LevelFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LevelFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course, Level $level)
    {
        $files=$level->files()->paginate(5);
        return view('level-files.index', compact('course', 'level', 'files'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course  ,Level $level)
    {

        return view('level-files.create',compact('course','level'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course, Level $level)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,mp3,wav|max:10240',
        ]);
        $filePath = $request->file('file')->storePublicly('level_files', 's3');
        $level->files()->create([
            'name' => $data['name'],
            'path' => $filePath,
        ]);
        return redirect()->route('courses.levels.files.index', [$course, $level])
            ->with('success', 'File uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(LevelFile $levelFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LevelFile $levelFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LevelFile $levelFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Level $level, LevelFile $file)
    {
        if ($file->path) {
            Storage::disk('s3')->delete($file->path);
        }
        $file->delete();

        return redirect()->back()->with('success', 'File deleted successfully!');
    }

}
