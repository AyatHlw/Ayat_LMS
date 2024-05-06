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
    public function forgotPassword($request): array
    {
        $request->validated();
        // Delete all old code that user send before.
        ResetCodePassword::where('email', $request['email'])->delete();

        // Generate random code
        $request['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create($request);

        // Send email to user
        Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

        return ['message' => trans('passwords.sent')];
    }

    public function checkCode($request)
    {
        $request->validated();

        // find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            throw new Exception('This code is expired');
        }

        return ['message' => trans('passwords.code_is_valid')];
    }

    public function resetPassword($request)
    {
        $request->validated();

        // find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            throw new \Exception('This code is expired!', 422);
            // return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        // find user's email
        $user = User::query()->where('email', $passwordReset->email);
        // update user password
        $request['password'] = Hash::make($request['password']);
        $user->update($request->only('password'));

        // delete current code
        $passwordReset->delete();

        return ['message' => 'password has been reset successfully'];
    }
}
