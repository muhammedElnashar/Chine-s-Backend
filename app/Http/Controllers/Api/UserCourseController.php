<?php

namespace App\Http\Controllers\Api;

use App\Enum\CourseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllCourseResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\SectionDetailsResource;
use App\Models\Course;
use App\Models\Level;
use Illuminate\Http\Request;

class UserCourseController extends Controller
{

    public function getCourseDetails($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        if ($course->type === CourseTypeEnum::Free || $user->hasPurchased($course->id)) {
            $course->load('levels');
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

    public function getSectionDetails($id)
    {
        $section = Level::with('videos', 'files', 'exam', 'course')->find($id);
        if (!$section) {
            return response()->json([
                'status' => false,
                'message' => 'Section not found',
            ], 404);
        }
        $user = auth()->user();

        $course = $section->course;
        if ($course->type->value === CourseTypeEnum::Free->value || $user->hasPurchased($course->id)) {
            return response()->json([
                'status' => true,
                'message' => 'Section Details',
                'data' => new SectionDetailsResource($section),
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unauthorized access to section',
        ], 403);
    }

    public function userCourseList()
    {
        $user= auth()->user();
        $courses = $user->purchasedCourses()->with('levels')->get();
        return response()->json([
            'status' => true,
            'message' => 'User Purchased Courses List',
            'data' => CourseResource::collection($courses),
        ]);
    }
}
