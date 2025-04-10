<?php

use App\Utils\KuaiDi100Util;

it('test get query express', function () {
    $res = KuaiDi100Util::queryExpress('2222222222222222', 'jd');
    dd($res);
});
