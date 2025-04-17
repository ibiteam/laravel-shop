<?php

use App\Services\AccessLog\AccessLogFormatter;
use App\Services\AccessLog\AccessLogService;

it('test access log service', function () {
    $formatter = new AccessLogFormatter;
    $formatter->setUserId($user->id ?? 0);
    $formatter->setUrl('');
    $formatter->setMethod('get');
    $formatter->setRefererUrl('');
    $formatter->setUserAgent('');
    $formatter->setRequestData([]);
    AccessLogService::init()->write($formatter);
});
