<?php

it('test direct init api interface', function () {
    $response = $this->doGet('api/v1/order/direct/init', [
        'no' => 'eda482c3-df45-47a4-abc5-f795db6fefae',
        'sku_id' => 3,
        'buy_number' => 3,
        'user_address_id' => 1,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test direct done api interface', function () {
    $response = $this->doPost('api/v1/order/direct/done', [
        'no' => 'eda482c3-df45-47a4-abc5-f795db6fefae',
        'sku_id' => 3,
        'buy_number' => 3,
        'user_address_id' => 1,
        'coupon_id' => '',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test cart init api interface', function () {
    $response = $this->doGet('api/v1/order/cart/init', [
        'user_address_id' => 1,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test cart done api interface', function () {
    $response = $this->doPost('api/v1/order/cart/done', [
        'user_address_id' => 1,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test pay cash desk api interface', function () {
    $response = $this->doGet('api/v1/order/cash', [
        'no' => '2025040741365428',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test wechat pay api interface', function () {
    $response = $this->doPost('api/v1/order/cash/wechat/pay', [
        'no' => '2025040741365428',
        'pay_form' => 'h5',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test my order list interface', function () {
    $response = $this->doGet('api/v1/order/my/index', [
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test my order detail interface', function () {
    $response = $this->doGet('api/v1/order/my/detail', [
        'no' => '2025040918889931',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
