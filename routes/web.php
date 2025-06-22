<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CourseExamController;
use App\Http\Controllers\Admin\CoursesController;
use App\Http\Controllers\Admin\DailyAudioWordController;
use App\Http\Controllers\Admin\DailyQuestion;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\LevelFileController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth', 'admin_access'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('users', AdminController::class);
    Route::resource('articles', ArticleController::class);
    Route::resource('announcements', AnnouncementController::class);
    Route::resource('daily/exercises', DailyQuestion::class);
    Route::resource('daily/words', DailyAudioWordController::class);
    Route::resource('courses', CoursesController::class);
    Route::resource('courses.exams', CourseExamController::class);
    Route::resource('courses.levels', LevelController::class);
    Route::resource('courses.levels.exams', ExamController::class);
    Route::resource('courses.levels.files', LevelFileController::class);
    Route::resource('courses.levels.videos', VideoController::class);
    Route::resource('payments', PaymentController::class);
    Route::post('/s3/multipart-urls', [VideoController::class, 'getMultipartUploadUrls'])->name('s3.multipart-urls');
    Route::post('/s3/multipart-complete', [VideoController::class, 'completeMultipartUpload'])->name('s3.multipart-complete');
    Route::post('/s3/multipart-abort', [VideoController::class, 'abortMultipartUpload'])->name('s3.multipart-abort');
    Route::post('/admin/videos/{video}/presigned', [VideoController::class, 'adminPresignedUrl'])->name('admin.videos.presigned');

});
