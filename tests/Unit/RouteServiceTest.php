<?php

use App\Enums\RouteEnum;
use App\Services\RouteService;

it('test', function () {
    $data = app(RouteService::class)->getRoutePath(RouteEnum::PAY_SUCCESS, ['no' => '333333']);
    $this->assertIsString($data);
});
