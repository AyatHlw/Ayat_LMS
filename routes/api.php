<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\VideoCallController;
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
    Route::controller(PremiumController::class)->group(function () {
        Route::post('premium/addUser', 'addUser')->name('premium.add');
        Route::put('premium/extendUser', 'extendUser')->name('premium.extend');
        Route::post('premium/removeUser', 'removeUser')->name('premium.remove');
    });
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
        Route::delete('deleteReport/{report_id}', [ReportController::class, 'destroy'])->name('report.delete');
        Route::get('reports', [ReportController::class, 'reports'])->name('report.get');
    });
});


// Routes accessible by teacher only
Route::group(['middleware' => ['role:teacher']], function () {
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::controller(CourseController::class)->group(function () {
            Route::post('course/createCourse', 'createCourse')->name('course.add');
            Route::post('course/createCourseWithYouTubeLinks', 'createCourseWithYouTubeLinks')->name('course.add');
            Route::delete('course/destroy/{course_id}', 'destroy');
            Route::post('course/update/{course_id}', 'update');
            Route::post('/quizzes/createQuiz', 'createQuiz');
            Route::get('/quizzes/showQuizForTeachers/{id}', 'showQuizForTeachers');
            Route::put('/quizzes/updateQuiz/{id}', 'updateQuiz');
            Route::delete('/questions/delete/{id}', 'deleteQuestion');
            Route::get('/quizzes/showQuizForTeachers/{id}', 'showQuizForTeachers');
        });
        Route::post('/tags/addTagsToCourse/{courseId}', [TagController::class, 'addTagsToCourse']);
    });
});

// Routes accessible by student only
Route::group(['middleware' => ['role:student']], function () {
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::controller(CommentController::class)->group(function () {
            Route::prefix('comment')->group(function () { // comment/route..
                Route::post('add', 'store')->name('course.comment');
                Route::delete('destroy/{comment_id}', 'destroy')->name('course.delete_comment'); // still some changes to work right
                Route::post('update/{comment_id}', 'update')->name('course.comment');
            });
        });
        Route::controller(FollowingController::class)->group(function () {
            Route::post('follow', 'follow');
            Route::delete('unFollow/{teacher_id}', 'unFollow');
        });
        Route::post('report', [ReportController::class, 'create'])->name('report.create');
        Route::get('certificate/{course_id}', [CertificateController::class, 'getCertificate'])->name('certificate.get');
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

Route::get('followers/{teacher_id}', [FollowingController::class, 'followers']);
Route::get('following/{student_id}', [FollowingController::class, 'following']);

Route::get('comments/{course_id}', [CommentController::class, 'showComments']);

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
        /*Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::delete('destroy/{course_id}', 'destroy'); // still some changes to work right
            Route::put('update/{course_id}', 'update');
//            Route::post('createCourse',  'createCourse');
        });*/
        Route::get('list', 'list');
        Route::get('showCourseDetails/{course_id}', 'showCourseDetails');
        Route::get('getTopCourses', 'getTopCourses');
    });
});

Route::controller(SearchController::class)->group(function () {
    Route::post('course/search', 'searchCourse');
    Route::post('user/search', 'searchUser');
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
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('savePreferences', [\App\Http\Controllers\RecommendationController::class, 'savePreferences']);
    Route::get('getUserRecommendedCourses', [\App\Http\Controllers\RecommendationController::class, 'getUserRecommendedCourses']);
});

Route::post('rooms', [VideoCallController::class, 'createRoom']);
Route::get('rooms/{roomSid}', [VideoCallController::class, 'getRoom']);
Route::post('rooms/end-room/{roomSid}', [VideoCallController::class, 'endRoom']);
