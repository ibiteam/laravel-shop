<?php

it('test order delivery list', function () {
    $data = [
        'order_sn' => '2025041560493453',
        'page' => 1,
        'number' => 10,
    ];
    $response = $this->doGet('api/v1/order/delivery/list', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($res);
});

it('test order delivery logistics', function () {
    $data = [
        'delivery_no' => 'D123456789',
    ];
    $response = $this->doGet('api/v1/order/delivery/logistics', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($res);
});
