<?php

namespace App\Services;

use App\Http\Responses\Response;
use App\Mail\DeleteUserMail;
use App\Mail\SendApprovalMail;
use App\Mail\SendRejectionMail;
use App\Mail\VerificationCodeMail;
use App\Models\PendingUsers;
use App\Models\User;
use App\Models\VerificationCode;
use App\Notifications\Notice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\JWT\Contract\Token;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use function PHPUnit\Framework\isNull;

class UserService
{
    private FileUploader $fileUploader;
    private NotificationService $noticer;

    public function __construct(FileUploader $fileUploader, NotificationService $noticer)
    {
        $this->fileUploader = $fileUploader;
        $this->noticer = $noticer;
    }

    public function profile($user_id)
    {
        $user = User::find($user_id);
        if (is_null($user)) $user = PendingUsers::find($user_id);
        if (is_null($user)) {
            throw new Exception(__('messages.user_not_found'), 404);
        }
        // (new NotificationService)->send($user, 'profile', 'someone entered your profile', '\Notice');
        return ['message' => __('messages.profile'), 'user' => $user, 'code' => 200];
    }

    public function users($type)
    {
        if ($type == 'teachers') {
            $users = User::role('teacher')->get();
        } else if ($type == 'students') {
            $users = User::role('student')->get();
        } else if ($type == 'admins') {
            $users = User::role('admin')->get();
        } else {
            throw new \Exception(__('messages.invalid_role_type'));
        }
        if ($users->isEmpty()) {
            if ($type == 'admins') throw new \Exception(__('messages.no_admins'), 200);
            else throw new \Exception($type == 'teachers' ? __('messages.no_teachers') : __('messages.no_students'), 200);
        }
        $message = ($type == 'teachers' ? __('messages.teachers_list') : ($type == 'students' ? __('messages.students_list') : __('messages.admins_list')));
        return ['message' => $message, 'users' => $users];
    }

    public function getUnderReviewUsers()
    {
        $users = PendingUsers::query()->get();
        if (!$users) throw new \Exception(__('messages.no_teachers_under_review'));
        return ['message' => __('messages.teachers_under_review'), 'users' => $users];
    }

    public function updateProfile($request)
    {
        $user = Auth::user();
        if (isset($request['name'])) $user['name'] = $request['name'];
        $user->save();

        if (isset($request['password'])) {
            $request->validate([
                'old_password' => 'required',
                'password' => 'confirmed'
            ]);
            $matching = Hash::check($request->old_password, Auth::user()->getAuthPassword());
            if (!$matching) {
                throw new \Exception(__('messages.old_password_mismatch'));
            }
            $user['password'] = Hash::make($request->password);
        }

        if (isset($request['image'])) {
            $request->validate(['image' => 'image|mimes:jpeg,png,jpg,gif|max:5120']);
            $user['image'] = $this->fileUploader->storeFile($request, 'image');
        }

        $user->save();
        return ['message' => __('messages.profile_updated_successfully'), 'profile' => $user];
    }

    public function addAdmin($request)
    {
        $image = $this->fileUploader->storeFile($request, 'image');
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request['email'],
            'image' => $image,
            'google_id' => User::query()->count(),
            'password' => Hash::make($request['password'])
        ]);
        return $this->userCreation('admin', $user);
    }

    public function signup($request): array
    {
        $request->validated();
        $image = $this->fileUploader->storeFile($request, 'image');
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request['email'],
            'image' => $image,
            'google_id' => User::query()->count(),
            'email_verified_at' => now(),
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
        return ['user' => $user, 'message' => __('messages.application_submitted')];
    }

    /**
     * @param $role1
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $user
     * @return array
     */
    public function userCreation($role1, \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $user): array
    {
        if ($role1 != 'admin') {// create the verification code right here
            VerificationCode::query()->where('email', $user['email'])->delete();
            $verification_code = mt_rand(100000, 999999);
            $data = [
                'email' => $user['email'],
                'verification_code' => $verification_code
            ];
            VerificationCode::create($data);
            Mail::to($user['email'])->send(new VerificationCodeMail($verification_code));
        }
        $role = Role::query()->where('name', $role1)->first();
        $user->assignRole($role);
        $permissions = $role->permissions()->pluck('name')->toArray();
        $user->givePermissionTo($permissions);
        $user->load('roles', 'permissions');
        $user = User::query()->where('email', $user['email'])->first();

        $user = $this->appendRolesAndPermissions($user);

        if ($role1 != 'admin') $message = __('messages.registration_successful');
        else $message = 'Admin created.';
        return ['user' => $user, 'message' => $message];
    }

    public function approveUser($request): array
    {
        $approval = $request['approval'];
        $data = PendingUsers::query()->where('email', $request['email'])->first();
        if (is_null($data)) {
            throw new Exception(__('messages.user_not_found'), 404);
        }
        $user = [];
        if ($approval) {
            Mail::to($data['email'])->send(new SendApprovalMail($data['name']));
            $role = $data['role'];
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'image' => $data['image'],
                'google_id' => User::query()->count(),
                'created_at' => $data['created_at'],
                'password' => $data['password']
            ]);
            $data->delete();
            $this->userCreation($role, $user);
            return ['message' => __('messages.user_approved')];
        } else {
            Mail::to($data['email'])->send(new SendRejectionMail($data['name']));
            $data->delete();
            $message = __('messages.user_declined');
            $code = 200;
        }
        return ['user' => $user, 'message' => $message, 'code' => $code];
    }

    public function signin($request)
    {
        $user = User::query()->where('email', $request['email'])->first();
        if (is_null($user)) {
            $user = PendingUsers::query()->where('email', $request['email'])->first();

            if (!is_null($user)) {
                return ['user' => [], 'message' => __('messages.not_approved_yet'), 'status' => 403];
            }

            return ['user' => [], 'message' => __('messages.not_signed_up_yet'), 'status' => 404];
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return ['user' => [], 'message' => __('messages.invalid_credentials'), 'status' => 401];
        }

        if (is_null($user['email_verified_at'])) {
            throw new Exception(__('messages.email_not_confirmed'));
        }

        $user = $this->appendRolesAndPermissions($user);
        $user['token'] = $user->createToken('Auth token')->plainTextToken;
        $user['fcm_token'] = $request->fcm_token;

        return ['user' => $user, 'message' => __('messages.signin_successful'), 'status' => 200];
    }

    public function googleSignin(): array
    {
        $user = Socialite::driver('google')->user();
        $finduser = User::where('email', $user->email)->first();
        if ($finduser) {
            if (is_null($finduser->email_verified_at)) {
                $finduser->email_verified_at = now();
                $finduser->save();
            }
            if ($finduser->google_id != $user->id) {
                $finduser->google_id = $user->id;
                $finduser->save();
            }
            $finduser['token'] = $finduser->createToken('Auth token')->plainTextToken;
            Auth::login($finduser);
            return ['message' => __('messages.signin_successful'), 'user' => $finduser];
        } else {
            $newUser = User::create([
                'name' => $user->name ?? __('messages.default_name'),
                'email' => $user->email,
                'google_id' => $user->id,
                'email_verified_at' => now(),
                'password' => encrypt('123456789'),
            ]);
            $role = Role::query()->where('name', 'student')->first();
            $user = User::where('email', $newUser['email'])->first();
            $user->assignRole($role);
            $permissions = $role->permissions()->pluck('name')->toArray();
            $user->givePermissionTo($permissions);
            $user->load('roles', 'permissions');
            $user = User::query()->where('email', $newUser['email'])->first();
            $user = $this->appendRolesAndPermissions($user);
            $user['token'] = $user->createToken('Auth token')->plainTextToken;
            Auth::login($user);
            return ['message' => __('messages.signup_successful'), 'user' => $user];
        }
    }

    public function signout(): array
    {
        $user = Auth::user();

        if (is_null($user)) {
            return ['message' => __('messages.invalid_token'), 'status' => 401];
        }

        Auth::user()->currentAccessToken()->delete();
        return ['message' => __('messages.signout_successful')];
    }

    public function deleteUser($user_id)
    {
        $user = User::find($user_id);
        if (!$user) throw new \Exception(__('messages.user_not_found'), 404);
        if (($user->hasRole('admin') && !Auth::user()->hasRole('superAdmin')) || $user->hasRole('superAdmin'))
            throw new \Exception(__('messages.prohibited_delete_admin'), 422);
        Mail::to($user['email'])->send(new DeleteUserMail($user['name']));
        $user->delete();
        return ['message' => __('messages.user_deleted_successfully')];
    }

    public function deleteAccount()
    {
        User::find(Auth::id())->delete();
        return ['message' => __('messages.account_deleted_successfully')];
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
