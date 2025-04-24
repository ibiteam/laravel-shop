<?php

namespace App\Services\AccessLog\Factories;

use App\Models\Clickhouse\AccessLog;
use App\Services\AccessLog\AccessLogFormatter;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

class ClickhouseAccessLog implements AccessLogInterface
{
    public function write(AccessLogFormatter $log_formatter): void
    {
        if (! config('database.connections.clickhouse.host')) {
            return;
        }

        if (! file_exists($this->filePath())) {
            $table_name = (new AccessLog)->getTable();

            // 判断表是否存在
            if (! $this->tableExists($table_name)) {
                return;
            }
        }

        if (! $this->existsTableFile()) {
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

    private function existsTableFile(): bool
    {
        $tmp_clickhouse_file = $this->filePath();

        if (file_exists($tmp_clickhouse_file) && file_get_contents($tmp_clickhouse_file) == 1) {
            return true;
        }

        return false;
    }

    private function filePath(): string
    {
        return public_path('clickhouse.txt');
    }

    /**
     * 检测表是否存在.
     */
    private function tableExists(string $table_name): bool
    {
        if ($this->existsTableFile()) {
            return true;
        }
        $query = "EXISTS TABLE {$table_name}";

        try {
            $result = DB::connection('clickhouse')->select($query);
            $response = ($result[0]['result'] ?? 0) === 1;

            file_put_contents($this->filePath(), 1);

            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }
}
