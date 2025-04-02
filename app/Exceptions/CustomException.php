<?php

namespace App\Exceptions;

use App\Enums\CustomCodeEnum;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class CustomException extends Exception
{
    use ApiResponse;

    public function __construct(
        string $message = '',
        protected array $data = [],
        protected CustomCodeEnum $custom_code_enum = CustomCodeEnum::ERROR
    ) {
        parent::__construct($message, $this->custom_code_enum->value);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function render($request): JsonResponse
    {
        return $this->failed($this->getData(), $this->message, $this->custom_code_enum);
    }

    public function getCodeEnum(): CustomCodeEnum
    {
        return $this->custom_code_enum;
    }
}
