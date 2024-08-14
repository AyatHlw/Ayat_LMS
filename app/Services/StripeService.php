<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Course;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function createCheckoutSession(Course $course, $user)
    {
        $session = Session::create([
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
            'customer_email' => $user->email,
            'client_reference_id' => $user->id,
            'mode' => 'payment',
            'success_url' => route('payment.success', ['course_id' => $course->id]),
            'cancel_url' => route('payment.cancel'),
        ]);

        return $session->id;
    }
}

