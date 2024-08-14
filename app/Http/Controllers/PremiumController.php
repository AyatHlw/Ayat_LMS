<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Models\PremiumUsers;
use App\Models\User;
use App\Services\PremiumService;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isNan;
use function PHPUnit\Framework\isNull;

class PremiumController extends Controller
{
    protected $premiumService;

    public function __construct(PremiumService $premiumService)
    {
        $this->premiumService = $premiumService;
    }

    public function addUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'end_date' => 'required|date'
            ]);
            PremiumUsers::create([
                'user_id' => $request->user_id,
                'end_date' => $request->end_date
            ]);
            return Response::success(__('messages.user_added'));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function extendUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'end_date' => 'required|date'
            ]);
            $subscription = PremiumUsers::firstWhere('user_id', $request->user_id);
            if(is_null($subscription)) {
                return Response::error(__('messages.no_subscription'));
            }
            $subscription['end_date'] = $request->end_date;
            $subscription->save();
            return Response::success(__('messages.subscription_extended'));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }
    // will be scheduled , there won't be need for it..
    public function removeUser($user_id)
    {
        try {
            $subscription = PremiumUsers::firstWhere('user_id', $user_id);
            if (is_null($subscription)) {
                return Response::error(__('messages.no_subscription'));
            }
            $subscription->delete();
            return Response::success(__('messages.user_removed'));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function createCheckoutSession(Request $request)
    {
        $user = Auth::user();

        $session = $this->premiumService->createCheckoutSession($user);

        return response()->json(['id' => $session->id]);
    }

    public function paymentSuccess(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $this->premiumService->handlePaymentSuccess($user);

        return response()->json(['message' => 'Payment successful, you are now a premium user.']);
    }

    public function checkPremiumStatus()
    {
        $user = Auth::user();
        $isPremium = $this->premiumService->checkPremiumStatus($user);

        return response()->json(['is_premium' => $isPremium]);
    }


}
