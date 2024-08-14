<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveRequest;
use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpInstructorRequest;
use App\Http\Requests\SignUpRequest;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\Response;
use App\Mail\deleteUserMail;
use App\Models\User;
use App\Services\UserService;

// use http\Env\Response;
use App\Http\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Mail;
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

    public function profile($user_id)
    {
        try {
            $data = $this->userService->profile($user_id);
            return Response::success($data['message'], $data['user']);
        } catch (Throwable $throwable) {
            return Response::error($throwable->getMessage(), 404);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $data = $this->userService->updateProfile($request);
            return Response::success($data['message'], $data['profile']);
        } catch (Throwable $throwable) {
            return Response::error($throwable->getMessage(), 404);
        }
    }

    public function getStudents()
    {
        try {
            $data = $this->userService->users('students');
            return Response::success($data['message'], $data['users']);
        } catch (Throwable $e) {
            return Response::error($e->getMessage());
        }
    }

    public function getTeachers(){
        try {
            $data = $this->userService->users('teachers');

            return Response::success($data['message'], $data['users']);
        } catch (Throwable $e) {
            return Response::error($e->getMessage(), $e->getCode());
        }
    }


    public function signUp(SignUpRequest $signUpRequest): JsonResponse
    {
        try {
            $data = $this->userService->signup($signUpRequest);
            return Response::success($data['message'], $data['user']);
        } catch (Throwable $throwable) {
            $message = $throwable->getMessage();
            return Response::error($message);
        }
    }

    public function signUpInstructor(SignUpInstructorRequest $signUpInstructorRequest): JsonResponse
    {
        try {
            $data = $this->userService->signupInstructor($signUpInstructorRequest);
            return Response::success($data['message'], $data['user']);
        } catch (Throwable $throwable) {
            $message = $throwable->getMessage();
            return Response::error($message);
        }
    }

    public function signIn(SignInRequest $signInRequest): JsonResponse
    {
        try {
            $data = $this->userService->signin($signInRequest);
            return Response::success($data['message'], $data['user']);
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
            return Response::success($data['message'], $data['user']);
        } catch (Throwable $throwable) {
            return Response::error($throwable->getMessage(), 422);
        }
    }

    public function signOut(): JsonResponse
    {
        try {
            $data = $this->userService->signout();
            return Response::success($data['message']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::error($message);
        }
    }

    public function deleteUser($user_id)
    {
        try {
            $data = $this->userService->deleteUser($user_id);
            return Response::success($data['message']);
        } catch (Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function deleteAccount()
    {
        try {
            $data = $this->userService->deleteAccount();
            return Response::success($data['message']);
        } catch (Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function approveForPendingUsers(ApproveRequest $request)
    {
        try {
            $data = $this->userService->approveUser($request->validated());
            return Response::success($data['message']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::error($message);
        }
    }
}
