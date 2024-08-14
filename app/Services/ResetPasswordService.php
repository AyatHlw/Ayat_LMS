<?php

namespace App\Services;

use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use mysql_xdevapi\Exception;

class ResetPasswordService
{
    public function forgotPassword(Request $request)
    {
        $data = $request->validated();

        // Delete all old codes that the user has sent before.
        ResetCodePassword::where('email', $request['email'])->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create($data);

        // Send email to user
        Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

        return ['message' => __('messages.password_reset_email_sent')];
    }

    public function checkCode(Request $request)
    {
        $request->validated();

        // Find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // Check if it has expired: the time is one hour
        if (!$passwordReset || $passwordReset->created_at < now()->subHour()) {
            if ($passwordReset) {
                $passwordReset->delete();
            }
            throw new Exception(__('messages.code_expired'));
        }

        return ['message' => __('messages.code_valid')];
    }

    public function resetPassword(Request $request): array
    {
        $request->validated();

        // Find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // Check if it has expired: the time is one hour
        if (!$passwordReset || $passwordReset->created_at < now()->subHour()) {
            if ($passwordReset) {
                $passwordReset->delete();
            }
            throw new Exception(__('messages.code_expired'), 422);
        }

        // Find user's email and update user password
        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            throw new Exception(__('messages.user_not_found'), 404);
        }

        // Update user password
        $user->update(['password' => Hash::make($request['password'])]);

        // Delete current code
        $passwordReset->delete();

        return ['message' => __('messages.password_reset_success')];
    }

}
