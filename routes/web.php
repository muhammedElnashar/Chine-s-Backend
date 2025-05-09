<?php

use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [\App\Http\Controllers\Admin\HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {
Route::resource('articles',ArticleController::class);
});
