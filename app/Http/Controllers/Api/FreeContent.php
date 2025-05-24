<?php

namespace App\Http\Controllers\Api;

use App\Enum\CourseTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CourseResource;
use App\Models\Article;
use App\Models\Course;
use Illuminate\Http\Request;

class FreeContent extends Controller
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
    public function getAllFreeCourses()
    {
        $freeCourses = Course::with('levels.videos','levels.exam.questions.answers')->where('type', CourseTypeEnum::Free)->paginate(5);
        // Return the articles as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Free Courses List',
            'data'=>  CourseResource::collection($freeCourses),
        ],200);
    }
}
