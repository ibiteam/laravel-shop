<?php

namespace App\Exceptions;

use App\Enums\ConstantEnum;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class BusinessException extends Exception
{
    use ApiResponse;

    public function __construct(
        string $message = '',
        protected ConstantEnum $custom_code_enum = ConstantEnum::ERROR,
    ) {
        parent::__construct($message, $this->custom_code_enum->value);
    }

    public function render($request): JsonResponse
    {
        return $this->error($this->message, $this->custom_code_enum);
    }

    public function getCodeEnum(): ConstantEnum
    {
        return $this->custom_code_enum;
    }
}
