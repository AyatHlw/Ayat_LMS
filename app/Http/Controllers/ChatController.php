<?php

namespace App\Http\Controllers;

use App\Services\WorkShopServices\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function createGroup(Request $request)
    {
        $group = $this->chatService->createGroup($request);
        return response()->json(['group' => $group]);
    }

    public function storeMessage(Request $request)
    {
        $message = $this->chatService->storeMessage($request);
        return response()->json(['message' => $message]);
    }

    public function groupMessages($group_id)
    {
        $messages = $this->chatService->groupMessages($group_id);
        return response()->json(['messages' => $messages]);
    }
}
