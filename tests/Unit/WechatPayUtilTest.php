<?php

use App\Enums\PayFormEnum;
use App\Enums\PaymentEnum;
use App\Http\Dao\PaymentDao;
use App\Utils\Wechat\WechatPayUtil;

it('test query refund order util', function () {
    $payment = app(PaymentDao::class)->getInfoByAlias(PaymentEnum::WECHAT);
    $wechat = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);
    $response = $wechat->queryRefundOrder('2023042111000150500000000000');
    dd($response);
});
it('test h5 pay order util', function () {
    $payment = app(PaymentDao::class)->getInfoByAlias(PaymentEnum::WECHAT);
    $wechat = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);
    $response = $wechat->h5Pay('测试', '2023042111000150500000000000', '1', '');
    dd($response);
});
it('test query order order util', function () {
    $payment = app(PaymentDao::class)->getInfoByAlias(PaymentEnum::WECHAT);
    $wechat = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);
    $response = $wechat->queryOrder('2023042111000150500000000000');
    dd($response);
});
it('test jsapi order order util', function () {
    $payment = app(PaymentDao::class)->getInfoByAlias(PaymentEnum::WECHAT);
    $wechat = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);
    $response = $wechat->jsPay('oxw7Fv_4U77wPxEZXtewaBeqQUJE', '2023042111000150500000000000', '2033042111000150500000000000', 2);
    dd($response);
});
it('test app order order util', function () {
    $payment = app(PaymentDao::class)->getInfoByAlias(PaymentEnum::WECHAT);
    $wechat = new WechatPayUtil($payment->config, PayFormEnum::PAY_FORM_H5);
    $response = $wechat->appPay('xxxxxxxxxxxxxxx', '2023042111000150500000000001', 3);
    dd($response);
});
