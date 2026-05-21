<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait ApiResponseTrait
{
    function apiResponse($data = null, $message = null, $status = 200)
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, $status);
    }

}
