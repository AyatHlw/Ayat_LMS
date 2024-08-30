<?php

namespace App\Services\WorkShopServices;

use App\Models\Group;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;

class ChatService
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new Factory)
            ->withServiceAccount(config('services.firebase.credentials'))
            ->withDatabaseUri(config('services.firebase.database_url'))
            ->create();
    }

    public function createGroup($request)
    {

        $request->validate([
            'name' => 'required',
            'workshop_id' => 'required|exists:workshops,id',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'workshop_id' => $request->workshop_id
        ]);

        $this->firebase->getDatabase()->getReference('groups/'.$group->id)
            ->set(['name' => $group->name]);

        return $group;
    }

    public function storeMessage($request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'message' => 'required|string|min:1'
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'group_id' => $request->group_id,
            'message' => $request->message
        ]);

        $this->firebase->getDatabase()->getReference('groups/'.$message->group_id.'/messages')
            ->push(['message' => $message->message, 'user_id' => $message->user_id]);

        return $message;
    }

    public function groupMessages($group_id)
    {
        $group = Group::findOrFail($group_id);
        $messages = $group->messages;

        $firebaseMessages = $this->firebase->getDatabase()->getReference('groups/'.$group_id.'/messages')
            ->getValue();

        return $firebaseMessages;
    }
}
