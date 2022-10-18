<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');

    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');

    Route::post('/posts/{post}/like', [LikeController::class, 'store'])->name('posts.like');

    Route::post('/topics/{topic}/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/topics/{topic}/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::post('/sections/{section}/topics', [TopicController::class, 'store'])->name('topics.store');
    Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
    Route::get('/sections/{section}/topics', [TopicController::class, 'index'])->name('topics.index');
    Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');
    Route::patch('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');

    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show');
    Route::post('/sections', [SectionController::class, 'store'])->name('sections.store')->middleware('can:admin');
    Route::patch('/sections/{section}', [SectionController::class, 'update'])->name('sections.update')->middleware('can:admin');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy')->middleware('can:admin');    
});

Route::post('/signup', [AuthController::class, 'signup'])->name('user.signup');
Route::post('/login', [AuthController::class, 'login'])->name('user.login');

