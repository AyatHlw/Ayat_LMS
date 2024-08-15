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


    public function createCheckoutSession($course_id)
    {
        $course = Course::findOrFail($course_id);

        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $course->title,
                    ],
                    'unit_amount' => $course->cost * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/payment/success'),
            'cancel_url' => url('/payment/cancel'),
        ]);

        return response()->json([
            'url' => $checkoutSession->url
        ]);
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

