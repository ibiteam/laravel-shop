<?php

use App\Enums\RouterEnum;
use App\Services\RouterService;

it('test', function () {
    $data = app(RouterService::class)->getRouterPath(RouterEnum::PAY_SUCCESS->value, ['no' => '333333']);
    $this->assertIsString($data);
});
