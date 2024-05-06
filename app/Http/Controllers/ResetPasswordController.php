<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Services\ResetPasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    private ResetPasswordService $resetPasswordService;

    public function __construct(ResetPasswordService $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    public function forgotPassword(Request $request)
    {
        try {
            $data = $this->resetPasswordService->forgotPassword($request);
            return Response()->json(['message' => $data['message']], 200);
        } catch (\Throwable $throwable) {
            return Response::error($throwable->getMessage(), 422);
        }
    }

    public function checkCode(Request $request)
    {
        try {
            $data = $this->resetPasswordService->checkCode($request);
            return Response()->json(['message' => $data['message']], 200);
        } catch (\Throwable $throwable) {
            return Response::error($throwable->getMessage(), 422);
        }
    }
    public function resetPassword(Request $request)
    {
        try {
            $data = $this->resetPasswordService->resetPassword($request);
            return Response()->json(['message' => $data['message']], 200);
        } catch (\Throwable $throwable) {
            return Response::error($throwable->getMessage(), 422);
        }
    }
}
