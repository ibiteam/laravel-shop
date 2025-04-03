<?php

namespace App\Traits;

use App\Enums\ConstantEnum;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * 成功函数.
     */
    public function success(mixed $data = null, string $message = 'success', ConstantEnum $response_enum = ConstantEnum::SUCCESS): JsonResponse
    {
        if (is_string($data)) {
            $message = $data;
            $data = null;
        }

        return response()->json([
            'code' => $response_enum->value,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * 失败函数，不携带返回数据.
     */
    public function error(string $message = 'error', ConstantEnum $response_enum = ConstantEnum::ERROR): JsonResponse
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
    public function failed(mixed $data = null, string $message = 'error', ConstantEnum $response_enum = ConstantEnum::ERROR): JsonResponse
    {
        return response()->json([
            'code' => $response_enum->value,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
