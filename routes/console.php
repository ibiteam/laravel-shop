<?php

use App\Http\Dao\AccessStatisticDao;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 访问统计
Schedule::call(function () {
    app(AccessStatisticDao::class)->statistic();
})->dailyAt('08:30');
