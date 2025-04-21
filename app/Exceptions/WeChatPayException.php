<?php

namespace App\Exceptions;

use App\Enums\ConstantEnum;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class WeChatPayException extends Exception
{
    use ApiResponse;

    public function __construct(
        string $message = '',
        protected ConstantEnum $constant_enum = ConstantEnum::ERROR,
    ) {
        parent::__construct($message, $this->constant_enum->value);
    }

    public function render($request): JsonResponse
    {
        return $this->error($this->message, $this->constant_enum);
    }

    public function getCodeEnum(): ConstantEnum
    {
        return $this->constant_enum;
    }
}
