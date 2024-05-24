<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveRequest;
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
use Illuminate\Support\Js;
use Laravel\Socialite\Facades\Socialite;
use Psy\Util\Json;
use Throwable;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function userInfo($email)
    {
        try {
            $data = $this->userService->userInfo($email);
            return Response::success($data['user'], $data['message']);
        } catch (Throwable $throwable) {
            return Response::error($throwable->getMessage(), 404);
        }
    }

    public function signUp(SignUpRequest $signUpRequest): JsonResponse
    {
        try {
            $data = $this->userService->signup($signUpRequest);
            return Response::success($data['user'], $data['message']);
        } catch (Throwable $throwable) {
            $message = $throwable->getMessage();
            return Response::error($message);
        }
    }

    public function signUpInstructor(SignUpInstructorRequest $signUpInstructorRequest): JsonResponse
    {
        try {
            $data = $this->userService->signupInstructor($signUpInstructorRequest);
            return Response::success($data['user'], $data['message']);
        } catch (Throwable $throwable) {
            $message = $throwable->getMessage();
            return Response::error($message);
        }
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $data = $this->userService->verifyEmail($request);
            return Response::success($data['user'], $data['message']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::error($message);
        }
    }

    public function resendVerificationCode(Request $request): JsonResponse
    {
        try {
            $data = $this->userService->resendVerificationCode($request);
            if (isset($data['error'])) {
                return Response::error([], $data['error']);
            }
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
            return Response::success($data['user'], $data['message']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::error($message);
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): JsonResponse
    {
        try {
            $data = $this->userService->googleSignin();
            return Response::success($data['user'], $data['message']);
        } catch (Throwable $throwable) {
            return Response::error($throwable->getMessage(), 422);
        }
    }

    public function signOut(): JsonResponse
    {
        try {
            $data = $this->userService->signout();
            return Response::success($data['user'], $data['message']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::error($message);
        }
    }

    public function approveForPendingUsers(ApproveRequest $request)
    {
        try {
            $data = $this->userService->approveUser($request->validated());
            return Response::success($data['user'], $data['message']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::error($message);
        }
    }
}
