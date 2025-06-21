<?php

namespace App\Http\Controllers\Api;

use App\Enum\CourseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllCourseResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;

class UserCourseController extends Controller
{

    public function getCourseDetails($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        if ($course->type === CourseTypeEnum::Free || $user->hasPurchased($course->id)) {
            $course->load('levels.videos', 'levels.exam', 'levels.files', 'exam');
            return response()->json([
                'status' => true,
                'message' => 'Course Details',
                'data' => new CourseResource($course),
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'You have not purchased this course.',
        ], 403);
    }

    public function userCourseList()
    {
        $user= auth()->user();
        $courses = $user->purchasedCourses()->with('levels.videos.views', 'levels.exam', 'levels.files', 'exam')->get();
        return response()->json([
            'status' => true,
            'message' => 'User Purchased Courses List',
            'data' => AllCourseResource::collection($courses),
        ]);
    }
}
