<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

trait ApiResponse
{
    /**
     * @param $data
     * @param $message
     * @param $status
     * @param $code
     * @return JsonResponse
     */
    public function successResponse($data = null, $message = '', $status = 'success', $code = Response::HTTP_OK)
    {
        $response = [
            'code' => 200,
            'status' => $status
        ];
        if (!empty($message)) {
            $response['message'] = $message;
        }
        if ($data) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }

    /**
     * @param $message
     * @param $code
     * @return JsonResponse
     */

    public function errorResponse($message = '', $code = 400)
    {
        $response = [
            'code' => $code,
            'message' => $message
        ];

        return response()->json($response, $code);
    }
}
