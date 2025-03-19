<?php

namespace App\Traits;

use App\Enums\ResponseEnum;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * 成功函数.
     */
    public function success(mixed $data = null, string $message = 'success', ResponseEnum $response_enum = ResponseEnum::SUCCESS): JsonResponse
    {
        return response()->json([
            'code' => $response_enum->value,
            'message' => is_string($data) ? $data : $message,
            'data' => $data,
        ]);
    }

    /**
     * 失败函数，不携带返回数据.
     */
    public function error(string $message = 'error', ResponseEnum $response_enum = ResponseEnum::ERROR): JsonResponse
    {
        return response()->json([
            'code' => $response_enum->value,
            'message' => $message,
            'data' => null,
        ]);
    }

    /**
     * 失败函数，并携带返回数据.
     */
    public function failed(mixed $data = null, string $message = 'error', ResponseEnum $response_enum = ResponseEnum::ERROR): JsonResponse
    {
        return response()->json([
            'code' => $response_enum->value,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
