<?php

use App\Http\Dao\AccessStatisticDao;

// 访问统计
Schedule::call(function () {
    app(AccessStatisticDao::class)->statistic();
})->dailyAt('08:30');
// 删除过期的token
Schedule::command('sanctum:prune-expired --hours=24')->daily();
