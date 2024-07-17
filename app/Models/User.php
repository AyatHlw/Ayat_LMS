<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use http\Env\Response;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'image',
        'password',
        'google_id',
        'verification_code',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'creator_id');
    }

    public function comments()
    {
        return $this->hasMany(CourseComment::class, 'comment_id');
    }

    public function quizResults()
    {
        return $this->hasMany(QuizResult::class, 'student_id');
    }

    public function hasPassedQuiz($quizId)
    {
        return $this->quizResults()->where('quiz_id', $quizId)->where('passed', true)->exists();
    }

    public function isPremium($user_id)
    {
        return PremiumUsers::where('user_id', $user_id)->exists();
    }

    public function followers()
    {
        return $this->hasMany(Follower::class, 'teacher_id')->join('users', 'users.id', '=', 'followers.user_id');
    }
    public function following(){
        return $this->hasMany(Follower::class, 'user_id')->join('users', 'users.id', '=', 'followers.teacher_id');
    }
}
