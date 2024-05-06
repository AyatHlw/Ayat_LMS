<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpInstructorRequest;
use App\Http\Requests\SignUpRequest;
use App\Http\Responses\Response;
use App\Models\User;
use App\Services\UserService;
// use http\Env\Response;
use App\Http\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function signUp(SignUpRequest $signUpRequest): JsonResponse
    {
        try {
            $data = $this->userService->signup($signUpRequest);
            return Response()->json(['data' => $data['user'], 'message' => $data['message']]);
        } catch (Throwable $throwable) {
            return Response()->json(['message' => $throwable->getMessage()]);
        }
    }

    public function signUpInstructor(SignUpInstructorRequest $signUpInstructorRequest): JsonResponse
    {
        try {
            $data = $this->userService->signup($signUpInstructorRequest);
            return Response()->json(['data' => $data['user'], 'message' => $data['message']]);
        } catch (Throwable $throwable) {
            return Response()->json(['message' => $throwable->getMessage()]);
        }
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $data = $this->userService->verifyEmail($request);
            return response()->json(['message' => $data['message']], 200);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::error($message, 500);
        }
    }

    public function resendVerificationCode(Request $request): JsonResponse
    {
        try {
            $data = $this->userService->resendVerificationCode($request);
            return Response::success($data, 'Verification code has been resent successfully.');
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::error($message, 422);
        }
    }

    public function signIn(SignInRequest $signInRequest): JsonResponse
    {
        try {
            $data = $this->userService->signin($signInRequest);
            return Response()->json(['data' => $data['user'], 'message' => $data['message']]);
        } catch (Throwable $throwable) {
            return Response()->json(['message' => $throwable->getMessage()]);
        }
    }

    public function signOut(): JsonResponse
    {
        try {
            $data = $this->userService->signout();
            return Response()->json(['message' => $data['message']]);
        } catch (Throwable $throwable) {
            return Response()->json(['message' => $throwable->getMessage()]);
        }
    }
}
