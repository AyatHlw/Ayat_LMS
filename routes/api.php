<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
    Route::get('userInfo/{email}', 'userInfo');
    Route::post('signup', 'signUp')->name('user.signup');
    Route::post('signupInstructor', 'signUpInstructor')->name('Instructor.signup');
    Route::post('signin', 'signIn')->name('user.signin');
    Route::post('approve', 'approveForPendingUsers');
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('signout', 'signOut');
    });
});

Route::controller(\App\Http\Controllers\ResetPasswordController::class)->group(function () {
    Route::post('forgotPassword', 'forgotPassword')->name('user.forgotPassword');
    Route::post('checkCode', 'checkCode')->name('Instructor.checkCode');
    Route::post('resetPassword', 'resetPassword')->name('user.resetPassword');
});

Route::controller(\App\Http\Controllers\EmailVerificationController::class)->group(function () {
    Route::post('verifyEmail', 'verifyEmail');
    Route::post('resendVerificationCode', 'resendVerificationCode');
});

Route::controller(\App\Http\Controllers\CourseController::class)->group(function () {
    Route::prefix('course')->group(function () { // comment/route..
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('store', 'store');
            Route::delete('destroy', 'destroy'); // still some changes to work right
            Route::put('update/{course_id}', 'update');
        });
        Route::get('list', 'list');
        Route::get('{course_id}', 'show');
    });
});

Route::controller(\App\Http\Controllers\CommentController::class)->group(function () {
    Route::prefix('comment')->group(function () { // comment/route..
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('store', 'store');
            Route::delete('destroy', 'destroy'); // still some changes to work right
            Route::put('update/{comment_id}', 'update');
        });
        Route::get('{course_id}', 'showComments');
    });
});

Route::controller(\App\Http\Controllers\CategoryController::class)->group(function () {
    Route::prefix('category')->group(function () {
        Route::get('all', 'list');
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('store', 'store');
            Route::delete('destroy', 'destroy'); // still some changes to work right
            Route::put('update/{category_id}', 'update');
        });
        Route::get('{category_id}/courses', 'categoryCourses');
    });
});
