<?php

namespace App\Services;

use App\Models\PremiumUsers;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class PremiumService
{
    protected $priceId = 'price_1PnP5vJRYMa452obaT3yMrL5';

    public function createCheckoutSession($user)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $this->priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('premium.success', ['user_id' => $user->id]),
            'cancel_url' => route('premium.cancel'),
        ]);

        return $session;
    }

    public function handlePaymentSuccess($user)
    {
        $endDate = Carbon::now()->addYear();

        $premiumUser = PremiumUsers::firstOrNew(['user_id' => $user->id]);
        $premiumUser->end_date = $endDate;
        $premiumUser->save();

        return $premiumUser;
    }

    public function checkPremiumStatus($user)
    {
        $premiumUser = PremiumUsers::where('user_id', $user->id)->first();

        if ($premiumUser) {
            $endDate = Carbon::parse($premiumUser->end_date);
            return $endDate->isFuture();
        }

        return false;
    }
}
