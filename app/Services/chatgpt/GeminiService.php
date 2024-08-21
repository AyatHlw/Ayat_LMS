<?php

namespace App\Services\chatgpt;

use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function generateText($prompt)
    {
        $response = Gemini::generateText($prompt);

        return $response;
    }

}

