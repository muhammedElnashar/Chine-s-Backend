<?php

namespace App\Http\Controllers\Api;

use App\Enum\CourseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllCourseResource;
use App\Http\Resources\AnnouncementsResource;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CourseResource;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\Course;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function getAllArticles()
    {
        $articles = Article::all();

        // Return the articles as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Articles List',
          'data'=>  ArticleResource::collection($articles),
        ],200);
    }

    public function getAllAnnouncements()
    {
        $announcements = Announcement::all();
        return response()->json([
            'status' => true,
            'message' => 'AnnouncementsResource List',
            'data' => AnnouncementsResource::collection($announcements),
        ]);
    }

    public function getAllFreeCourses()
    {
        $freeCourses = Course::with('levels')->where('type', CourseTypeEnum::Free)->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Free Courses List',
            'data' => CourseResource::collection($freeCourses),
        ]);
    }
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
