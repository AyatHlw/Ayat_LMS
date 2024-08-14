<?php

namespace App\Services\WorkShopServices;

use App\Models\Group;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatService
{
    public function createGroup($request)
    {
        $request->validate([
            'name' => 'required',
            'workshop_id' => 'required|exists:workshops,id',
        ]);

        $group = Group::find($request->workshop_id);
        if ($group) throw new \Exception(__('messages.workshop_already_has_group'), 422);

        $group = Group::create([
            'name' => $request->name,
            'workshop_id' => $request->workshop_id
        ]);
        return ['message' => __('messages.group_created_successfully'), 'group' => $group];
    }

    public function storeMessage($request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'message' => 'requred|string|min:1'
        ]);
        $message = Message::create([
            'group_id' => $request->group_id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);
        return ['message' => __('messages.message_sent'), 'data' => $message];
    }

    public function deleteMessage($message_id)
    {
        $message = Message::find($message_id);
        if (!$message) throw new \Exception(__('messages.message_not_found'), 404);

        $message->delete();
        return ['message' => __('messages.message_deleted_successfully')];
    }

    public function groupMessages($group_id)
    {
        $group = Group::find($group_id);
        if (!$group) throw new \Exception(__('messages.group_not_found'), 404);

        $messages = $group->messages;
        if (!$messages) throw new \Exception(__('messages.no_messages_yet'), 200);

        return ['message' => __('messages.group_messages'), 'data' => $messages];
    }
}
