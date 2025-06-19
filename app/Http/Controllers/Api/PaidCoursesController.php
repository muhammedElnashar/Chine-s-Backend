<?php

namespace App\Http\Controllers\Api;

use App\Enum\CourseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllPaidCourseResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;


class PaidCoursesController extends Controller
{
    public function getAllPaidCourses()
    {
        $paidCourses = Course::with('levels.videos', 'levels.exam', 'levels.files', 'exam')->where('type', CourseTypeEnum::Paid)->paginate(10);
        return response()->json([
            'status' => true,
            'message' => 'Paid Courses List',
            'data' => AllPaidCourseResource::collection($paidCourses),
        ]);
    }

    public function getPaidCourse($id)
    {
        $user = auth()->user();
        $course = Course::findOrFail($id);
        if ($course->type === CourseTypeEnum::Free) {
            $course->load('levels.videos', 'levels.exam', 'levels.files', 'exam');
            return response()->json([
                'status' => true,
                'message' => 'Free Course Details',
                'data' => new CourseResource($course),
            ]);
        }
        if ($course->type === CourseTypeEnum::Paid && !$user->hasPurchased($course->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have not purchased this course.',
                ], 403);
        }

        // في حال الشراء الصحيح
        $course->load('levels.videos', 'levels.exam', 'levels.files', 'exam');
        return response()->json([
            'status' => true,
            'message' => 'Paid Course Details',
            'data' => new CourseResource($course),
        ]);
    }

    public function userCourseList()
    {
        $user= auth()->user();
        $courses = $user->purchasedCourses()->with('levels.videos', 'levels.exam', 'levels.files', 'exam')->get();
        return response()->json([
            'status' => true,
            'message' => 'User Purchased Courses List',
            'data' => AllPaidCourseResource::collection($courses),
        ]);
    }
}
