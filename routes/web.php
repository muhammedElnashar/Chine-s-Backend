<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CoursesController;
use App\Http\Controllers\Admin\DailyAudioWordController;
use App\Http\Controllers\Admin\DailyQuestion;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\LevelExamController;
use App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [\App\Http\Controllers\Admin\HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {
    Route::resource('articles', ArticleController::class);
    Route::resource('daily/questions', DailyQuestion::class);
    Route::resource('daily/words', DailyAudioWordController::class);
    Route::resource('courses', CoursesController::class);
    Route::resource('courses.levels', LevelController::class);
    Route::resource('courses.levels.exams', LevelExamController::class);
    Route::resource('courses.levels.videos', VideoController::class);
    Route::post('/s3/multipart-urls', [VideoController::class, 'getMultipartUploadUrls'])->name('s3.multipart-urls');
    Route::post('/s3/multipart-complete', [VideoController::class, 'completeMultipartUpload'])->name('s3.multipart-complete');
    Route::post('/s3/multipart-abort', [VideoController::class, 'abortMultipartUpload'])->name('s3.multipart-abort');
});
