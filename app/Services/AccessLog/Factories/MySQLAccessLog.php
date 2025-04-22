<?php

namespace App\Services\AccessLog\Factories;

use App\Models\AccessLog;
use App\Services\AccessLog\AccessLogFormatter;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

class MySQLAccessLog implements AccessLogInterface
{
    public function write(AccessLogFormatter $log_formatter): void
    {
        AccessLog::query()->create($log_formatter->toArray());
    }

    public function read(?string $sql = null, ?DateTimeInterface $date = null): array
    {
        return DB::connection('mysql')->select($sql);
    }
}
