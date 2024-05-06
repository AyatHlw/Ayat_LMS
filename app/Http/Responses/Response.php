<?php

namespace App\Http\Responses;

class Response
{
    public static function success($message, $data)
    {
        return Response()->json(['message' => $message, 'data' => $data], 200);
    }
    public static function error($message, $code){
        return Response()->json(['message' => $message], $code);
    }
}
