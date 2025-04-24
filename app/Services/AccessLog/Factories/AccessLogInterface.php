<?php

namespace App\Services\AccessLog\Factories;

use App\Services\AccessLog\AccessLogFormatter;
use App\Services\AccessLog\AdminAccessLogFormatter;
use DateTimeInterface;

interface AccessLogInterface
{
    public function write(AccessLogFormatter $log_formatter): void;

    public function manageWrite(AdminAccessLogFormatter $admin_access_log_formatter): void;

    public function read(?string $sql = null, ?DateTimeInterface $date = null): array;
}
