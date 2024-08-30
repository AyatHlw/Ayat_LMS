<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatgptController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseWithStudentController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\VideoCallController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;

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
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['middleware' => ['role:superAdmin']], function () {
        Route::get('users/admin', [AuthController::class, 'getAdmins']);
        Route::post('admin/add', [AuthController::class, 'addAdmin']);
    });

// Routes accessible by admin only
    Route::group(['middleware' => ['role:admin|superAdmin']], function () {
        Route::post('approve', [AuthController::class, 'approveForPendingUsers'])->name('user.approve');
        Route::post('category/create', [CategoryController::class, 'createCategory'])->name('category.create');
        Route::post('category/update/{category_id}', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('category/destroy/{category_id}', [CategoryController::class, 'destroy'])->name('category.delete');
        Route::get('/getAllCoursesForAdmin', [CourseController::class, 'getAllCoursesForAdmin']);
        Route::post('/courses/{id}/approve', [CourseController::class, 'approveCourse']);
        Route::post('/courses/{id}/reject', [CourseController::class, 'rejectCourse']);
        Route::post('/tags/createTag', [TagController::class, 'createTag']);
        Route::delete('/tags/deleteTag/{tagId}', [TagController::class, 'deleteTag']);
        Route::post('/tags/updateTag/{tagId}', [TagController::class, 'updateTag']);
        Route::delete('course/delete/{course_id}', [CourseController::class, 'destroy']);

        Route::delete('user/{id}/delete', [AuthController::class, 'deleteUser'])->name('user.delete');
        Route::get('users/underReview', [AuthController::class, 'getUnderReviewUsers']);
        Route::controller(ReportController::class)->prefix('report')->group(function () {
            Route::get('course/get', 'courseReports')->name('reports.get');
            Route::get('course/get/{report_id}', 'courseReportDetails')->name('reports.show');
            Route::delete('course/delete/{report_id}', 'destroyCourseReport')->name('reports.delete');
            Route::get('comment/get', 'commentReports')->name('reports.get');
            Route::get('comment/get/{report_id}', 'commentReportDetails')->name('reports.show');
            Route::delete('comment/delete/{report_id}', 'destroyCommentReport')->name('reports.delete');
        });
    });


// Routes accessible by teacher only
    Route::group(['middleware' => ['role:teacher']], function () {
        Route::controller(CourseController::class)->group(function () {
            Route::post('course/createCourse', 'createCourse')->name('course.add');
            Route::post('createCourseWithYouTubeLinks', 'createCourseWithYouTubeLinks')->name('course.add');
            Route::delete('course/destroy/{course_id}', 'destroy');
            Route::post('course/update/{course_id}', 'update');
            Route::post('/quizzes/createQuiz', 'createQuiz');
            Route::get('/quizzes/showQuizForTeachers/{id}', 'showQuizForTeachers');
            Route::post('/quizzes/updateQuiz/{id}', 'updateQuiz');
            Route::delete('/questions/delete/{id}', 'deleteQuestion');
            Route::get('/quizzes/showQuizForTeachers/{id}', 'showQuizForTeachers');
            Route::get('quizzes/showCourseQuiz/{course_id}', 'showCourseQuiz');
        });
        Route::post('/tags/addTagsToCourse/{courseId}', [TagController::class, 'addTagsToCourse']);
        Route::controller(WorkshopController::class)->group(function () {
            Route::post('workshop/createWorkshop', 'createWorkshop')->name('workshop.create');
            Route::post('workshop/update/{workshop_id}', 'update')->name('workshop.update');
            Route::delete('workshop/delete/{workshop_id}', 'destroy')->name('workshop.delete');
        });
        Route::post('workshop/group/create', [ChatController::class, 'createGroup']);
        Route::post('workshop/group/message', [ChatController::class, 'storeMessage']);
        Route::get('workshop/group/{group_id}/messages', [ChatController::class, 'groupMessages']);
        // message delete tomorrow pi Ezn Allah
        Route::delete('workshop/group/message/{message_id}/delete', [ChatController::class, 'deleteMessage']);
        Route::delete('account/delete', [AuthController::class, 'deleteAccount']);
    });

// Routes accessible by student only
    Route::group(['middleware' => ['role:student']], function () {
        Route::controller(CommentController::class)->group(function () {
            Route::prefix('comment')->group(function () { // comment/route..
                Route::post('add', 'store')->name('course.comment');
                Route::delete('destroy/{comment_id}', 'destroy')->name('course.delete_comment'); // still some changes to work right
                Route::post('update/{comment_id}', 'update')->name('course.comment');
            });
        });
        Route::controller(FollowingController::class)->group(function () {
            Route::post('follow', 'follow');
            Route::delete('unFollow/{following_id}', 'unFollow');
        });
        Route::post('course/report', [ReportController::class, 'courseReport'])->name('report.report');
        Route::post('comment/report', [ReportController::class, 'commentReport'])->name('report.report');
        Route::get('certificate/{course_id}', [CertificateController::class, 'getCertificate'])->name('certificate.get');
        Route::post('/quizzes/checkAnswers', [CourseController::class, 'checkAnswers']);
        Route::get('/quizzes/showQuizForStudents/{id}', [CourseController::class, 'showQuizForStudents']);
        Route::post('workshop/enroll/{workshop_id}', [WorkshopController::class, 'enroll_in_workshop'])->name('workshop.enroll');
        Route::delete('account/delete', [AuthController::class, 'deleteAccount']);

        Route::post('course/favorite', [CourseWithStudentController::class, 'addToFavorites']);
        Route::get('course/favorites', [CourseWithStudentController::class, 'favoritesList']);;
        Route::delete('course/unFavorite/{course_id}', [CourseWithStudentController::class, 'removeFromFavorites']);;

        Route::post('video/watchLater', [CourseWithStudentController::class, 'addToWatchLater']);
        Route::get('video/watchLaterList', [CourseWithStudentController::class, 'watchLaterList']);;
        Route::delete('video/watchLater/remove/{video_id}', [CourseWithStudentController::class, 'removeFromWatchLater']);;
    });
    // Common authed routes
    Route::get('signout', [AuthController::class, 'signOut'])->name('user.sign_out');
    Route::post('profile/update', [AuthController::class, 'updateProfile']);
    Route::delete('account/delete', [AuthController::class, 'deleteAccount']);

    Route::post('workshop/group/message', [ChatController::class, 'storeMessage']);
    Route::get('workshop/group/{group_id}/messages', [ChatController::class, 'groupMessages']);

    Route::post('savePreferences', [\App\Http\Controllers\RecommendationController::class, 'savePreferences']);
    Route::get('getUserRecommendedCourses', [\App\Http\Controllers\RecommendationController::class, 'getUserRecommendedCourses']);

    Route::get('Notification/markAsRead/all', [NotificationController::class, 'markAllAsRead']);
    Route::get('Notification/markAsRead/{notificationId}', [NotificationController::class, 'markAsRead']);
    Route::get('Notification/delete/{notificationId}', [NotificationController::class, 'destroy']);

   // Route::group(['middleware' => ['enrolled']], function () {
        Route::get('/course/showAllVideos/{course_id}', [CourseController::class, 'showAllVideos']);
        Route::get('/course/showOneVideo/{course_id}/{video_id}', [CourseController::class, 'showOneVideo']);
   // });


    Route::post('/create-checkout-session/{course_id}', [PaymentController::class, 'createCheckoutSession']);
    Route::get('/payment-success/{course_id}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment-cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');

    Route::post('/premium/checkout', [PremiumController::class, 'createCheckoutSession']);
    Route::get('/premium/success', [PremiumController::class, 'paymentSuccess'])->name('premium.success');
    Route::get('/premium/cancel', function () {
        return response()->json(['message' => 'Payment canceled.'], 400);
    })->name('premium.cancel');
    Route::get('/premium/status', [PremiumController::class, 'checkPremiumStatus']);

});

// Common routes

Route::controller(AuthController::class)->group(function () {
    Route::get('user/{id}', 'profile');
    Route::post('signup', 'signUp')->name('user.sign_up');
    Route::post('signupInstructor', 'signUpInstructor')->name('instructor.sign_up');
    Route::post('signin', 'signIn')->name('user.sign_in');

    Route::get('users/teacher', 'getTeachers')->name('user.teachers');
    Route::get('users/student', 'getStudents')->name('user.students');

});
Route::controller(FollowingController::class)->group(function () {
    Route::get('followers/{teacher_id}', 'followers');
    Route::get('followers/count/{teacher_id}', 'followersNum');

    Route::get('following/{student_id}', 'following');
    Route::get('following/count/{student_id}', 'followingNum');
});

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
        Route::get('list', 'list');
        Route::get('showCourseDetails/{course_id}', 'showCourseDetails');
        Route::get('getTopCourses', 'getTopCourses');
        Route::get('teacher/{teacher_id}', 'getTeacherCourses');
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
        Route::get('{category_id}', 'categoryDetails');
    });
});

Route::get('categoryDetails/{category_id}', [CategoryController::class, 'categoryDetails']);

Route::controller(TagController::class)->group(function () {
    Route::get('/tags/getCourseTags/{courseId}', 'getCourseTags');
    Route::get('/tags/getTagsByCategory/{categoryId}', 'getTagsByCategory');
    Route::get('/tags/all', 'getAllTags');
    Route::get('tag/details/{tag_id}', 'tagDetails');
});


Route::controller(WorkshopController::class)->group(function () {
    Route::get('workshops', 'index');
    Route::get('workshop/details/{workshop_id}', 'showWorkshopDetails');
});

Route::controller(VideoCallController::class)->group(function () {
Route::post('rooms', 'createRoom');
Route::get('rooms/{roomSid}', 'getRoom');
Route::post('rooms/end-room/{roomSid}', 'endRoom');
});

Route::post('/chatgpt', [GeminiController::class, 'generateText']);


