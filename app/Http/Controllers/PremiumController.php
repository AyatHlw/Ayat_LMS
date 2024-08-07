<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Models\PremiumUsers;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isNan;
use function PHPUnit\Framework\isNull;

class PremiumController extends Controller
{
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
            return Response::success('user added.');
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
            if(is_null($subscription)) return Response::error('This user does not have a subscription.');
            $subscription['end_date'] = $request->end_date;
            $subscription->save();
            return Response::success('subscription extended.');
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }
    // will be scheduled , there won't be need for it..
    public function removeUser($user_id)
    {
        try {
            PremiumUsers::firstWhere('user_id', $user_id)->delete();
            return Response::success('user removed.');
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }
}
