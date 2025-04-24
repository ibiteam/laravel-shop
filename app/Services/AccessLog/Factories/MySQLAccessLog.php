<?php

namespace App\Services\AccessLog\Factories;

use App\Models\AccessLog;
use App\Models\AdminAccessLog;
use App\Services\AccessLog\AccessLogFormatter;
use App\Services\AccessLog\AdminAccessLogFormatter;
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

    public function manageWrite(AdminAccessLogFormatter $admin_access_log_formatter): void
    {
        AdminAccessLog::query()->create($admin_access_log_formatter->toArray());
    }
}
