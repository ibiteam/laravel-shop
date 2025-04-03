<?php

namespace App\Exceptions;

use App\Enums\ConstantEnum;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class ProcessDataException extends Exception
{
    use ApiResponse;

    public function __construct(
        string $message = '',
        protected array $data = [],
        protected ConstantEnum $constant_enum = ConstantEnum::ERROR
    ) {
        parent::__construct($message, $this->constant_enum->value);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function render($request): JsonResponse
    {
        return $this->failed($this->getData(), $this->message, $this->constant_enum);
    }

    public function getCodeEnum(): ConstantEnum
    {
        return $this->constant_enum;
    }
}
