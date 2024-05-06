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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(\App\Http\Controllers\authController::class)->group(function () {
    Route::post('signup', 'signUp')->name('user.signup');
    Route::post('signupInstructor', 'signUpInstructor')->name('Instructor.signup');
    Route::post('signin', 'signIn')->name('user.signin');
    Route::post('verifyEmail', 'verifyEmail');
    Route::post('resendVerificationCode', 'resendVerificationCode');
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('signout', 'signOut');
    });
});

Route::controller(\App\Http\Controllers\ResetPasswordController::class)->group(function () {
    Route::post('forgotPassword', 'forgotPassword')->name('user.forgotPassword');
    Route::post('checkCode', 'checkCode')->name('Instructor.checkCode');
    Route::post('resetPassword', 'resetPassword')->name('user.resetPassword');
});
