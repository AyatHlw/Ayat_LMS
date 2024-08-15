<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Services\NotificationService;
use App\Services\WorkShopServices\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function createGroup(Request $request)
    {
        try {
            $res = $this->chatService->createGroup($request);
            return Response::success($res['message'], $res['group']);
        } catch (\Throwable $e) {
            return Response::error($e->getMessage());
        }
    }

    public function storeMessage(Request $request)
    {
        try {
            $res = $this->chatService->storeMessage($request);
            return Response::success($res['message'], $res['data']);
        } catch (\Throwable $e) {
            return Response::error($e->getMessage());
        }
    }

    public function deleteMessage($message_id)
    {
        try {
            $res = $this->chatService->deleteMessage($message_id);
            return Response::success($res['message']);
        } catch (\Throwable $e) {
            return Response::error($e->getMessage());
        }
    }

    public function groupMessages($group_id)
    {
        try {
            $res = $this->chatService->groupMessages($group_id);
            return Response::success($res['message'], $res['data']);
        } catch (\Throwable $e) {
            return Response::error($e->getMessage());
        }
    }
}
