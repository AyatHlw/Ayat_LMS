<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FollowingController;
use Illuminate\Support\Facades\Route;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function (){
    return response()->json(['message' => 'Unauthenticated']);
})->name('login');

Route::get('auth/google', [\App\Http\Controllers\AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);
Route::get('approve', [\App\Http\Controllers\AuthController::class, 'approveForPendingUsers']);
Route::get('show/{course_id}', [\App\Http\Controllers\CourseController::class, 'show']);
Route::get('list', [\App\Http\Controllers\CourseController::class, 'list']);
Route::get('comments/{course_id}', [\App\Http\Controllers\CommentController::class, 'showComments']);
Route::get('course/get', [\App\Http\Controllers\ReportController::class, 'courseReports']);
Route::get('user/{id}', [\App\Http\Controllers\AuthController::class, 'profile']);

Route::get('users/teacher', [AuthController::class, 'getTeachers'])->name('user.teachers');
Route::get('users/student', [AuthController::class, 'getStudents'])->name('user.students');

Route::get('/video-call', function () {
    return view('video-call');
});
Route::get('/api/token', function () {
    $identity = 'user_' . uniqid();

    $token = new AccessToken(
        config('services.twilio.sid'),
        config('services.twilio.key'),
        config('services.twilio.secret'),
        3600,
        $identity
    );

    $videoGrant = new VideoGrant();
    $token->addGrant($videoGrant);

    return response()->json(['token' => $token->toJWT()]);
});

Route::controller(CourseController::class)->group(function () {
    Route::prefix('course')->group(function () { // comment/route..
        Route::get('list', 'list');
        Route::get('showCourseDetails/{course_id}', 'showCourseDetails');
        Route::get('getTopCourses', 'getTopCourses');
        Route::get('teacher/{teacher_id}', 'getTeacherCourses');
    });
});
