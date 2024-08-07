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
            Log::info('Attempting to create room with name: ' . $roomName);
            $room = $this->twilio->video->v1->rooms->create([
                'uniqueName' => $roomName,
                'type' => 'group',
            ]);
            Log::info('Room creation response: ' . json_encode($room->toArray()));
            return $room;
        } catch (\Exception $e) {
            Log::error('Error creating room: ' . $e->getMessage());
            throw $e;
        }
    }

    public function endRoom($roomSid)
    {
        try {
            $room = $this->twilio->video->v1->rooms($roomSid)->update('completed');
            Log::info('Room ended response: ' . json_encode($room->toArray()));
            return $room;
        } catch (\Exception $e) {
            Log::error('Error ending room: ' . $e->getMessage());
            throw $e;
        }
    }


    public function getRoom($roomSid)
    {
        try {
            return $this->twilio->video->v1->rooms($roomSid)->fetch();
        } catch (\Exception $e) {
            Log::error('Error fetching room: ' . $e->getMessage());
            throw $e;
        }
    }
}
