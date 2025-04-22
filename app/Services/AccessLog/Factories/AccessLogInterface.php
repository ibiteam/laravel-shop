<?php

namespace App\Services\AccessLog\Factories;

use App\Services\AccessLog\AccessLogFormatter;
use DateTimeInterface;

interface AccessLogInterface
{
    public function write(AccessLogFormatter $log_formatter): void;

    public function read(?string $sql = null, ?DateTimeInterface $date = null): array;
}
