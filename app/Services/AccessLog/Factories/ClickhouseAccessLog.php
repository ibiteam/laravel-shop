<?php

namespace App\Services\AccessLog\Factories;

use App\Models\Clickhouse\AccessLog;
use App\Models\Clickhouse\AdminAccessLog;
use App\Services\AccessLog\AccessLogFormatter;
use App\Services\AccessLog\AdminAccessLogFormatter;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

class ClickhouseAccessLog implements AccessLogInterface
{
    public function write(AccessLogFormatter $log_formatter): void
    {
        if (! $this->checkCanRecord()) {
            return;
        }

        try {
            AccessLog::create($log_formatter->toArray());
        } catch (\Throwable) {
        }
    }

    public function read(?string $sql = null, ?DateTimeInterface $date = null): array
    {
        return DB::connection('clickhouse')->select($sql);
    }

    public function manageWrite(AdminAccessLogFormatter $admin_access_log_formatter): void
    {
        if (! $this->checkCanRecord()) {
            return;
        }

        try {
            AdminAccessLog::create($admin_access_log_formatter->toArray());
        } catch (\Throwable $vars) {
            dd($vars);
        }
    }

    private function checkCanRecord(): bool
    {
        if (! config('database.connections.clickhouse.host')) {
            return false;
        }

        return true;
    }
}
