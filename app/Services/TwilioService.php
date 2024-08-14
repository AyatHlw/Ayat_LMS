<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            env('TWILIO_API_KEY'),
            env('TWILIO_API_SECRET'),
            env('TWILIO_ACCOUNT_SID')
        );
    }

    public function createRoom($roomName)
    {
        try {
            Log::info(__('messages.attempting_to_create_room', ['roomName' => $roomName]));
            $room = $this->twilio->video->v1->rooms->create([
                'uniqueName' => $roomName,
                'type' => 'group',
            ]);
            Log::info(__('messages.room_creation_response', ['response' => json_encode($room->toArray())]));
            return $room;
        } catch (\Exception $e) {
            Log::error(__('messages.error_creating_room', ['error' => $e->getMessage()]));
            throw $e;
        }
    }

    public function endRoom($roomSid)
    {
        try {
            $room = $this->twilio->video->v1->rooms($roomSid)->update('completed');
            Log::info(__('messages.room_ended_response', ['response' => json_encode($room->toArray())]));
            return $room;
        } catch (\Exception $e) {
            Log::error(__('messages.error_ending_room', ['error' => $e->getMessage()]));
            throw $e;
        }
    }


    public function getRoom($roomSid)
    {
        try {
            return $this->twilio->video->v1->rooms($roomSid)->fetch();
        } catch (\Exception $e) {
            Log::error(__('messages.error_fetching_room', ['error' => $e->getMessage()]));
            throw $e;
        }
    }
}
