<?php

it('verify', function () {
    $response = $this->doGet('api/v1/order/apply_refund/verify', [
        'order_no' => '2025041096246684',
        'order_detail_id' => 1,
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('init', function () {
    $response = $this->doGet('api/v1/order/apply_refund/init', [
        'order_no' => '2025041096246684',
        'order_detail_id' => 1,
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('show', function () {
    $response = $this->doGet('api/v1/order/apply_refund/show', [
        'order_no' => '2025041096246684',
        'order_detail_id' => 1,
        'type' => 1,
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('store', function () {
    $response = $this->doPost('api/v1/order/apply_refund/store', [
        // 'apply_refund_id' => 1,
        'order_no' => '2025041096246684',
        'order_detail_id' => 1,
        'type' => 1,
        'number' => 2,
        'money' => 5,
        'reason_id' => 1,
        'description' => '测试',
        'certificate' => 'https://www.baidu.com,https://learnku.com',
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('detail', function () {
    $response = $this->doGet('api/v1/order/apply_refund/detail', [
        // 'apply_refund_id' => 1,
        'order_no' => '2025041096246684',
        'order_detail_id' => 1,
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('revoke', function () {
    $response = $this->doPost('api/v1/order/apply_refund/revoke', [
        'apply_refund_id' => 1,
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('log', function () {
    $response = $this->doGet('api/v1/order/apply_refund/log', [
        'apply_refund_id' => 1,
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('ship info', function () {
    $response = $this->doGet('api/v1/order/apply_refund/ship_info', [
        'apply_refund_id' => 1,
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('ship add', function () {
    $response = $this->doPost('api/v1/order/apply_refund/ship_add', [
        'apply_refund_id' => 1,
        'no' => '123456789',
        'ship_company_id' => 1,
        'phone' => '13322221111',
        'description' => '测试',
        'certificate' => 'https://www.baidu.com,https://learnku.com',
    ]);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});
