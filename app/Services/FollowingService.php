<?php

namespace App\Services;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowingService
{
    public function follow(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id'
        ]);
        Follower::create([
            'user_id' => Auth::id(),
            'teacher_id' => $request->teacher_id
        ]);
        return ['message' => 'Done'];
    }

    public function followers($teacher_id)
    {
        $followers = User::firstWhere('id', $teacher_id)->followers;
        return ['message' => 'followers : ', 'followers' => $followers];
    }

    public function following($student_id)
    {
        $following = User::firstWhere('id', $student_id)->following;
        return ['message' => 'following : ', 'following' => $following];
    }

    public function unFollow($teacher_id)
    {
        Follower::firstWhere('user_id', Auth::id())
            ->where('teacher_id', $teacher_id)
            ->delete();
        return ['message' => 'done'];
    }
}
