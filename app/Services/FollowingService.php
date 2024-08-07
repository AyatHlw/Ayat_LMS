<?php

namespace App\Services;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowingService
{
    private NotificationService $noticer;
    public function __construct(NotificationService $noticer)
    {
        $this->noticer = $noticer;
    }
    public function follow(Request $request)
    {
        $request->validate([
            'following_id' => 'required|exists:users,id'
        ]);
        Auth::user()->following()->attach(User::find($request->following_id));
        return ['message' => 'Done'];
    }

    public function followers($following_id)
    {
        $followers = User::find($following_id)->followers;
        return ['message' => 'followers : ', 'followers' => $followers];
    }

    public function following($follower_id)
    {
        $following = User::find($follower_id)->following;
        return ['message' => 'following : ', 'following' => $following];
    }

    public function unFollow($following_id)
    {
        Auth::user()->following()->detach(User::find($following_id));
        return ['message' => 'done'];
    }
}
