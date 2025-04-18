<?php

use PhpClickHouseLaravel\ClickhouseServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    ClickhouseServiceProvider::class,
];
