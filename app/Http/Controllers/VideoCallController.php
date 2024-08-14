<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VideoCallController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function createRoom(Request $request)
    {
        $request->validate([
            'roomName' => 'required|string',
        ]);

        try {
            $room = $this->twilioService->createRoom($request->roomName);
            return response()->json($room->toArray(), 200);
        } catch (\Exception $e) {
            // Log::error('Error creating room: ' . $e->getMessage());
            // already in the service
            return response()->json(['message' => __('messages.error_creating_room')], 500);
        }
    }

    public function getRoom($roomSid)
    {
        try {
            $room = $this->twilioService->getRoom($roomSid);
            return response()->json($room->toArray(), 200);
        } catch (\Exception $e) {
            Log::error(__('messages.room_not_found', ['error' => $e->getMessage()]));
            return response()->json(['message' => __('messages.room_not_found')], 404);
        }
    }

    public function endRoom($roomSid)
    {
        try {
            $room = $this->twilioService->endRoom($roomSid);
            return response()->json($room->toArray(), 200);
        } catch (\Exception $e) {
            // Log::error('Error ending room: ' . $e->getMessage()); same as createRoom
            return response()->json(['message' => __('messages.error_ending_room')], 500);
        }
    }
}
