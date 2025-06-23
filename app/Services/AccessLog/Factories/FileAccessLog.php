<?php

namespace App\Services\AccessLog\Factories;

use App\Services\AccessLog\AccessLogFormatter;
use App\Services\AccessLog\AdminAccessLogFormatter;
use DateTimeInterface;

class FileAccessLog implements AccessLogInterface
{
    public function write(AccessLogFormatter $log_formatter): void
    {
        // 文件存不存在，不存在则创建文件
        $file_path = storage_path('access_log/'.$this->getFileName());

        if (! file_exists($file_path)) {
            touch($file_path);
        }
        $log_entry = $log_formatter->toJson();

        // 检查文件是否为空
        if (file_exists($file_path) && filesize($file_path) > 0) {
            $log_entry = PHP_EOL.$log_entry;
        }

        file_put_contents($file_path, $log_entry, FILE_APPEND);
    }

    public function read(?string $sql = null, ?DateTimeInterface $date = null): array
    {
        if (! $date) {
            return [];
        }

        $file_name_date = $date->format('Y-m-d');

        $file_path = storage_path('access_log/'.$this->getFileName($file_name_date));

        if (! file_exists($file_path)) {
            return [];
        }

        $contents = file_get_contents($file_path);

        return explode(PHP_EOL, $contents);
    }

    public function manageWrite(AdminAccessLogFormatter $admin_access_log_formatter): void
    {
        // 文件存不存在，不存在则创建文件
        $file_path = storage_path('admin_access_log/'.$this->getFileName());

        if (! file_exists($file_path)) {
            touch($file_path);
        }
        $log_entry = $admin_access_log_formatter->toJson();

        // 检查文件是否为空
        if (file_exists($file_path) && filesize($file_path) > 0) {
            $log_entry = PHP_EOL.$log_entry;
        }

        file_put_contents($file_path, $log_entry, FILE_APPEND);
    }

    private function getFileName(string $file_name_date = ''): string
    {
        if (! $file_name_date) {
            $file_name_date = date('Y-m-d');
        }

        return 'access-log-'.$file_name_date.'.log';
    }
}
