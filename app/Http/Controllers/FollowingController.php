<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Models\Follower;
use App\Models\User;
use App\Services\FollowingService;
use Egulias\EmailValidator\Result\Reason\AtextAfterCFWS;
use FontLib\OpenType\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowingController extends Controller
{
    private FollowingService $followingService;

    public function __construct(FollowingService $followingService)
    {
        $this->followingService = $followingService;
    }

    public function follow(Request $request)
    {
        try {
            $data = $this->followingService->follow($request);
            return Response::success($data['message']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function followers($following_id)
    {
        try {
            $data = $this->followingService->followers($following_id);
            return Response::success($data['message'], $data['followers']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function followersNum($following_id)
    {
        try {
            $data = $this->followingService->followers($following_id);
            return Response::success('followers : ', count($data['followers']));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function following($follower_id)
    {
        try {
            $data = $this->followingService->following($follower_id);
            return Response::success($data['message'], $data['following']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function followingNum($follower_id)
    {
        try {
            $data = $this->followingService->following($follower_id);
            return Response::success('following : ', count($data['following']));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function unFollow($following_id)
    {
        try {
            $data = $this->followingService->unFollow($following_id);
            return Response::success($data['message']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }
}
