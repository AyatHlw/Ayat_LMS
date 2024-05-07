<?php

namespace App\Services;

use App\Mail\SendApprovalMail;
use App\Mail\SendRejectionMail;
use App\Mail\VerificationCodeMail;
use App\Models\PendingUsers;
use App\Models\User;
use App\Models\VerificationCode;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use function PHPUnit\Framework\isNull;

class UserService
{
    private FileUploader $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function signup($request): array
    {
        $request->validated();
        $image = $this->fileUploader->storeFile($request, 'image');
        $user = User::query()->create([
            'name' => $request['name'],
            'email' => $request['email'],
            'image' => $image,
            'google_id' => User::query()->count(),
            'password' => Hash::make($request['password'])
        ]);
        return $this->userCreation($request['role'], $user);
    }

    public function signupInstructor($request): array
    {
        $request->validated();
        $CV = $this->fileUploader->storeFile($request, 'CV');
        $image = $this->fileUploader->storeFile($request, 'image');
        $user = PendingUsers::query()->create([
            'name' => $request['name'],
            'email' => $request['email'],
            'role' => $request['role'],
            'CV' => $CV,
            'image' => $image,
            'password' => Hash::make($request['password'])
        ]);
        return ['user' => $user, 'message' => 'Your application has been submitted successfully. It will be reviewed by HR. Once it is reviewed, you will recieve a letter via gmail telling the result.'];
    }

    /**
     * @param $role1
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $user
     * @return array
     */
    public function userCreation($role1, \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $user): array
    {
        // create the verification code right here
        VerificationCode::query()->where('email', $user['email'])->delete();
        $verification_code = mt_rand(100000, 999999);
        $data = [
            'email' => $user['email'],
            'verification_code' => $verification_code
        ];
        VerificationCode::create($data);
        Mail::to($user['email'])->send(new VerificationCodeMail($verification_code));

        $role = Role::query()->where('name', $role1)->first();
        $user->assignRole($role);
        $permissions = $role->permissions()->pluck('name')->toArray();
        $user->givePermissionTo($permissions);
        $user->load('roles', 'permissions');
        $user = User::query()->where('email', $user['email'])->first();

        $user = $this->appendRolesAndPermissions($user);
        //$user['token'] = $user->createToken('Auth Token')->plainTextToken;
        return ['user' => $user, 'message' => 'Successful registration, a code sent to your email to verify your registration'];
    }

    public function verifyEmail($request)
    {

        $request->validate([
            'verification_code' => 'required|string|exists:verification_codes',
        ]);
        // find the code
        $verification = VerificationCode::firstWhere('verification_code', $request->verification_code);

        // check if it does not expired: the time is one hour
        if ($verification->created_at > now()->addHour()) {
            $verification->delete();
            throw new Exception('This code is expired');
        }
        $user = User::query()->where('email', $verification['email'])->first();
        $user['email_verified_at'] = now();
        $user->save();
        VerificationCode::firstWhere('email', $verification['email'])->delete();
        return ['message' => trans('Your email has been confirmed')];
    }

    public function resendVerificationCode($request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $user = User::query()->where('email', $request['email'])->first();
        if (is_null($user)) {
            throw new Exception('Email not found', 404);
        }
        VerificationCode::query()->where('email', $user['email'])->delete();
        $verification_code = mt_rand(100000, 999999);
        $data = [
            'email' => $user['email'],
            'verification_code' => $verification_code
        ];
        VerificationCode::create($data);
        Mail::to($user['email'])->send(new VerificationCodeMail($verification_code));
        return ['message', 'The code resent successfully'];
    }

    public function approveUser($request): array
    {
        $approval = $request['approval'];
        $data = PendingUsers::query()->where('email', $request['email'])->first();
        if (is_null($data)) {
            throw new Exception('User not found', 404);
        }
        $user = [];
        if ($approval) {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'image' => $data['image'],
                'google_id' => User::query()->count(),
                'created_at' => $data['created_at'],
                'password' => $data['password']
            ]);
            $role = $data['role'];
            Mail::to($data['email'])->send(new SendApprovalMail());
            $data->delete();
            return $this->userCreation($role, $user);
        } else {
            Mail::to($data['email'])->send(new SendRejectionMail($data['name']));
            $data->delete();
            $message = 'User has been declined and deleted.';
            $code = 200;
        }
        return ['user' => $user, 'message' => $message, 'code' => $code];
    }
    public function signin($request)
    {
        $request->validated();
        $user = User::query()->where('email', $request['email'])->first();
        if (is_null($user)) {
            $user = PendingUsers::query()->where('email', $request['email'])->first();
            if (!is_null($user))
                return ['user' => [], 'message' => 'You are not approved yet', 'status' => 403];
            return ['user' => [], 'message' => 'You are not signed up yet', 'status' => 404];
        }
        if (is_null($user['email_verified_at']))
            throw new Exception('Your email has not been confirmed!');
        if (!Auth::attempt($request->only('email', 'password'))) {
            return ['user' => [], 'message' => 'Email or password is not correct', 'status' => 401];
        }
        $user = $this->appendRolesAndPermissions($user);
        $user['token'] = $user->createToken('Auth token')->plainTextToken;
        return ['user' => $user, 'message' => 'Signed in successfully.', 'status' => 200];
    }

    public function signout(): array
    {
        $user = Auth::user();
        if (is_null($user)) {
            return ['message' => 'invalid token', 'status' => '401'];
        }
        Auth::user()->currentAccessToken()->delete();
        return ['message' => 'signed out successfully'];
    }

    public function appendRolesAndPermissions($user)
    {
        $roles = [];
        foreach ($user->roles as $role) {
            $roles[] = $role->name;
        }
        unset($user['roles']);
        $user['roles'] = $roles;

        $permissions = [];
        foreach ($user->permissions as $permission) {
            $permissions[] = $permission->name;
        }
        unset($user['permissions']);
        $user['permissions'] = $permissions;

        return $user;
    }
}
