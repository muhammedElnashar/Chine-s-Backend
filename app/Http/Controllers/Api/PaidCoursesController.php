<?php

namespace App\Http\Controllers\Api;

use App\Enum\CourseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllCourseResource;
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
            'data' => AllCourseResource::collection($paidCourses),
        ]);
    }


}
