<?php

use App\Messages\PhoneCodeMessage;
use App\Models\PhoneMsg;
use App\Utils\SmsUtil;

it('test send message', function () {
    $phone = '15175826373';
    $res = app(SmsUtil::class)->send($phone, new PhoneCodeMessage('测试注册短信', PhoneMsg::PHONE_REGISTER));
    $this->assertTrue($res);
});
