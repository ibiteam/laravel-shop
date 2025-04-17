<?php

namespace App\Services\AccessLog\Factories;

use App\Services\AccessLog\AccessLogFormatter;

interface AccessLogInterface
{
    public function write(AccessLogFormatter $log_formatter);
}
