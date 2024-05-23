<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Laravel\Socialite\Facades\Socialite;
// use Exception;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;
use App\Http\Responses\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use Exception;

class GoogleController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $finduser = User::where('google_id', $user->id)->first();
            if ($finduser) {
                $finduser['token'] = $finduser->createToken('Auth token')->plainTextToken;
                Auth::login($finduser);
                return Response::success('Signed in successfully', $finduser);
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => encrypt('123456789'),
                    'email_verified_at' => now(),
                ]);
                Auth::login($newUser);
                return Response::success('Signed up successfully', $newUser);
            }
        } catch (Exception $e) {
            dd('Connection error !');
        }
    }
}
