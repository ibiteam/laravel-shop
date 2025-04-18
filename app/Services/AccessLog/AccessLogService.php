<?php

namespace App\Services\AccessLog;

use App\Services\AccessLog\Factories\AccessLogInterface;
use App\Services\AccessLog\Factories\ClickhouseAccessLog;
use App\Services\AccessLog\Factories\FileAccessLog;
use App\Services\AccessLog\Factories\MySQLAccessLog;

class AccessLogService
{
    public static function init(): ?AccessLogInterface
    {
        return match (config('custom.access_log_driver')) {
            'file' => new FileAccessLog,
            'mysql' => new MySQLAccessLog,
            'clickhouse' => new ClickhouseAccessLog,
            default => null
        };
    }
}
