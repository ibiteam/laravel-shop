<?php

namespace App\Exceptions;

use App\Enums\ResponseEnum;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class BusinessException extends Exception
{
    use ApiResponse;

    public function __construct(
        string $message = '',
        protected ResponseEnum $error_code_enum = ResponseEnum::ERROR,
    ) {
        parent::__construct($message, $this->error_code_enum->value);
    }

    public function render($request): JsonResponse
    {
        return $this->error($this->message, $this->error_code_enum);
    }

    public function getErrorCodeEnum(): ResponseEnum
    {
        return $this->error_code_enum;
    }
}
