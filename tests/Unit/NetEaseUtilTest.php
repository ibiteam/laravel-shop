<?php

use App\Utils\NetEaseUtil;

it('test verify captcha', function () {
    $value = '';
    $data = app(NetEaseUtil::class)->verifyCaptcha($value);
    $this->assertTrue($data);
});
