<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Models\Course;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    // إنشاء جلسة دفع
    public function createCheckoutSession($course_id)
    {
        $course = Course::findOrFail($course_id);

        if ($course->cost == 0) {
            return response()->json(['message' => 'Course is free, no payment required'], 400);
        }

        $sessionId = $this->stripeService->createCheckoutSession($course, Auth::user());

//        return response()->json(['id' => $sessionId], 200);
        return Response::success('Checkout Session Created Successfully',['id' => $sessionId],200);
    }

    public function paymentSuccess($course_id)
    {
        $course = Course::findOrFail($course_id);
        $user = Auth::user();

        $user->enrolledCourses()->attach($course_id, [
            'student_mark' => 0,
            'is_passed' => 0,
        ]);

        return response()->json(['message' => 'Payment successful, you are now enrolled in the course.']);
    }

    public function paymentCancel()
    {
        return response()->json(['message' => 'Payment was cancelled.']);
    }
}

