<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\TagController;
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

// Routes accessible by superAdmin only
Route::group(['middleware' => ['role:superAdmin']], function () {
});


// Routes accessible by admin only
Route::group(['middleware' => ['role:admin']], function () {
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::controller(AuthController::class)->group(function () {
            Route::post('approve', 'approveForPendingUsers')->name('user.approve');
        });
            Route::get('/getAllCoursesForAdmin', [CourseController::class, 'getAllCoursesForAdmin']);
            Route::post('/courses/{id}/approve', [CourseController::class, 'approveCourse']);
            Route::post('/courses/{id}/reject', [CourseController::class, 'rejectCourse']);
            Route::post('/tags/createTag', [TagController::class, 'createTag']);
            Route::get('/tags/deleteTag/{tagId}', [TagController::class, 'deleteTag']);
            Route::post('/tags/updateTag/{tagId}', [TagController::class, 'updateTag']);
    });
});


// Routes accessible by teacher only
Route::group(['middleware' => ['role:teacher']], function () {
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::controller(CourseController::class)->group(function () {
            Route::post('course/createCourse', 'createCourse')->name('course.add');
            Route::post('createCourseWithYouTubeLinks', 'createCourseWithYouTubeLinks')->name('course.add');
            Route::post('/quizzes/createQuiz', 'createQuiz');
            Route::get('/quizzes/showQuizForTeachers/{id}', 'showQuizForTeachers');
            Route::put('/quizzes/updateQuiz/{id}', 'updateQuiz');
            Route::delete('/questions/delete/{id}', 'deleteQuestion');
            Route::get('/quizzes/showQuizForTeachers/{id}','showQuizForTeachers');
        });
        Route::post('/tags/addTagsToCourse/{courseId}', [TagController::class, 'addTagsToCourse']);
    });
});

// Routes accessible by student only
Route::group(['middleware' => ['role:student']], function () {
    Route::group(['middleware' => ['auth:sanctum']], function () {
        //comments
        Route::controller(CommentController::class)->group(function () {
            Route::prefix('comment')->group(function () { // comment/route..
                Route::group(['middleware' => ['auth:sanctum']], function () {
                    Route::post('add', 'store')->name('course.comment');
                    Route::delete('destroy/{commentCourse_id}', 'destroy')->name('course.delete_comment'); // still some changes to work right
                    Route::put('update/{comment_id}', 'update')->name('course.comment');
                });
                Route::get('{course_id}', 'showComments')->name('course.comment');
            });
        });
        //cources
        Route::post('/quizzes/checkAnswers', [CourseController::class, 'checkAnswers']);
        Route::get('/quizzes/showQuizForStudents/{id}', [CourseController::class, 'showQuizForStudents']);

    });
});

// Common routes

Route::controller(AuthController::class)->group(function () {
    Route::get('userInfo/{email}', 'userInfo');
    Route::post('signup', 'signUp')->name('user.sign_up');
    Route::post('signupInstructor', 'signUpInstructor')->name('instructor.sign_up');
    Route::post('signin', 'signIn')->name('user.sign_in');
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('signout', 'signOut')->name('user.sign_out');
    });
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::post('forgotPassword', 'forgotPassword')->name('check.email_password');
    Route::post('checkCode', 'checkCode')->name('check.email_password');
    Route::post('resetPassword', 'resetPassword')->name('check.email_password');
});

Route::controller(EmailVerificationController::class)->group(function () {
    Route::post('verifyEmail', 'verifyEmail')->name('check.email_password');
    Route::post('resendVerificationCode', 'resendVerificationCode')->name('check.email_password');
});

Route::controller(CourseController::class)->group(function () {
    Route::prefix('course')->group(function () { // comment/route..
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::delete('destroy/{course_id}', 'destroy'); // still some changes to work right
            Route::put('update/{course_id}', 'update');
//            Route::post('createCourse',  'createCourse');
        });
        Route::get('list', 'list');
        Route::get('showCourseDetails/{course_id}', 'showCourseDetails');
        Route::get('getTopCourses', 'getTopCourses');
    });
});

Route::controller(CategoryController::class)->group(function () {
    Route::prefix('category')->group(function () {
        Route::get('all', 'list');
        Route::get('{category_id}/courses', 'categoryCourses');
    });
});


Route::controller(TagController::class)->group(function () {
    Route::get('/tags/getCourseTags/{courseId}', 'getCourseTags');
    Route::get('/tags/getTagsByCategory/{categoryId}', 'getTagsByCategory');
});


