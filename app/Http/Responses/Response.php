<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class Response
{
   /* public static function success($message, $data)
    {
        return Response()->json(['message' => $message, 'data' => $data], 200);
    }
   */
    public static function success($data, $message):JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], 200);
    }

    /*
    public static function error($message, $code){
        return Response()->json(['message' => $message], $code);
    }
    */
    public static function error($message, $code=500):JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }

    public static function Validation($message, $code=422):JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }
}
