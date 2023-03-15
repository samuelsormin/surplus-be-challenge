<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    public function __construct()
    {
        // 
    }

    /**
     * Send success response
     * 
     * @param array $data
     * @param string $message
     * @param integer $code
     * 
     * @return JsonResponse
     */
    public function sendResponse($data, $message, $code = 200): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, $code);
    }

    /**
     * Send error response
     * 
     * @param string $message
     * @param integer $code
     * 
     * @return JsonResponse
     */
    public function sendError($message, $code = 400): JsonResponse
    {
        $response = [
            'status' => false,
            'message' =>  $message,
            'data' => [],
        ];

        return response()->json($response, $code);
    }
}
