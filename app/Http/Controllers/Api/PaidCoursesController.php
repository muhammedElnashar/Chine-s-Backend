<?php

namespace App\Http\Controllers\Api;

use App\Enum\CourseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LevelResource;
use App\Models\Course;
use App\Models\Level;
use Illuminate\Http\Request;

class PaidCoursesController extends Controller
{
    public function getAllPaidCourses()
    {
        $paidCourses = Course::with('levels')->where('type', CourseTypeEnum::Paid)->paginate(10);
        return response()->json([
            'status' => true,
            'message' => 'Paid Courses List',
            'data' => CourseResource::collection($paidCourses),
        ]);
    }


}
