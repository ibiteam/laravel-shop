<?php

use App\Utils\NetEaseUtil;

it('test verify captcha', function () {
    $value = '';
    $data = NetEaseUtil::verifyCaptcha($value);
    $this->assertTrue($data);
});
