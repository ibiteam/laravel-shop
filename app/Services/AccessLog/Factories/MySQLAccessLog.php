<?php

namespace App\Services\AccessLog\Factories;

use App\Models\AccessLog;
use App\Services\AccessLog\AccessLogFormatter;

class MySQLAccessLog implements AccessLogInterface
{
    public function write(AccessLogFormatter $log_formatter): void
    {
        AccessLog::query()->create($log_formatter->toArray());
    }
}
