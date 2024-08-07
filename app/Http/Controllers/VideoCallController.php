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
            Log::error('Error creating room: ' . $e->getMessage());
            return response()->json(['message' => 'Error creating room'], 500);
        }
    }

    public function getRoom($roomSid)
    {
        try {
            $room = $this->twilioService->getRoom($roomSid);
            return response()->json($room->toArray(), 200);
        } catch (\Exception $e) {
            Log::error('Room not found: ' . $e->getMessage());
            return response()->json(['message' => 'Room not found.'], 404);
        }
    }

    public function endRoom($roomSid)
    {
        try {
            $room = $this->twilioService->endRoom($roomSid);
            return response()->json($room->toArray(), 200);
        } catch (\Exception $e) {
            Log::error('Error ending room: ' . $e->getMessage());
            return response()->json(['message' => 'Error ending room'], 500);
        }
    }
}
