<?php

namespace App\Http\Controllers;

use App\Services\chatgpt\GeminiService;
use Illuminate\Http\Request;

class GeminiController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function generateText(Request $request)
    {
        $prompt = $request->input('prompt');
        $response = $this->geminiService->generateText($prompt);

        return response()->json(['response' => $response]);
    }
}
