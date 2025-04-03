<?php

it('test payment method index api interface', function () {
    $response = $this->doGet('/api/manage/set/payment/method', ['name' => '微信', 'is_enabled' => true, 'page' => 1, 'number' => 15]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test payment method edit api interface', function () {
    $response = $this->doGet('/api/manage/set/payment/method/edit', ['id' => 1]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test payment method update api interface', function () {
    $data = [
        'id' => 1,
        'name' => '微信',
        'is_enabled' => false,
        'icon' => url('/images/icons/wechat_pay_logo.png'),
        'description' => '微信支付',
        'config' => [
            'mic_id' => '123456',
            'secret_key' => '123456',
            'v2_secret_key' => '123456',
            'private_key' => '123456',
            'certificate' => '123456',
        ],
        'limit' => 0,
        'is_recommend' => true,
        'sort' => 1,
    ];
    $response = $this->doPost('/api/manage/set/payment/method/update', $data);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test payment method change field api interface', function () {
    $data = [
        'id' => 1,
        // 'field' => 'is_enabled',
        'field' => 'is_recommend',
    ];
    $response = $this->doPost('/api/manage/set/payment/method/change/field', $data);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
