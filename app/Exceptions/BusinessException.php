<?php

namespace App\Exceptions;

use App\Enums\CustomCodeEnum;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class BusinessException extends Exception
{
    use ApiResponse;

    public function __construct(
        string $message = '',
        protected CustomCodeEnum $custom_code_enum = CustomCodeEnum::ERROR,
    ) {
        parent::__construct($message, $this->custom_code_enum->value);
    }

    public function render($request): JsonResponse
    {
        return $this->error($this->message, $this->custom_code_enum);
    }

    public function getCodeEnum(): CustomCodeEnum
    {
        return $this->custom_code_enum;
    }
}
