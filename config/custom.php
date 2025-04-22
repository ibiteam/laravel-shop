<?php

return [
    /**
     * 访问日志驱动.
     *
     * Supported drivers: "mysql", "file", "clickhouse".
     */
    'access_log_driver' => env('ACCESS_LOG_DRIVER', 'mysql'),
];
