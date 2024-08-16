<?php

namespace App\Services;

use App\Models\VideoCall;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;
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

    public function createRoom($roomName, $workshop_id)
    {
        try {
            Log::info(__('messages.attempting_to_create_room', ['roomName' => $roomName, 'workshop_id' => $workshop_id]));
            if (VideoCall::query()->where('workshop_id', $workshop_id)->exists()) {
                throw new \Exception('this workshop already has a video call right now.');
            }

            $room = $this->twilio->video->v1->rooms->create([
                'uniqueName' => $roomName,
                'type' => 'group'
            ]);

            VideoCall::create([
                'roomSid' => $room->sid,
                'workshop_id' => $workshop_id
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
            if ($room) {
                $video_call = VideoCall::query()->where('roomSid', $roomSid);
                $video_call?->delete();
            }
            Log::info(__('messages.room_ended_response', ['response' => json_encode($room->toArray())]));
            return $room;
        } catch (\Exception $e) {
            Log::error(__('messages.error_ending_room', ['error' => $e->getMessage()]));
            throw $e;
        }
    }

    public function getWorkshopRoom($workshop_id)
    {
        $workshop = Workshop::query()->find($workshop_id);
        if (!$workshop) {
            throw new \Exception(__('messages.workshop_not_found'));
        }
        $videoCall = $workshop->videoCall;
        if (!$videoCall) {
            throw new \Exception(__('messages.call_not_found'));
        }
        return ['message' => __('messages.fetch_room'), 'room' => $this->getRoom($videoCall->roomSid)];
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
