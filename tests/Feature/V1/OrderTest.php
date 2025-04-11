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
        'no' => '2025040918889931',
        'pay_form' => 'h5',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test my order list interface', function () {
    $response = $this->doGet('api/v1/order/my/index', [
        // 'keywords' => '测试商1',
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

it('test edit order address interface', function () {
    $response = $this->doGet('api/v1/order/my/address/edit', [
        'no' => '2025040918889931',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test update order address interface', function () {
    $response = $this->doPost('api/v1/order/my/address/update', [
        'no' => '2025040918889931',
        'user_address_id' => 2,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test destroy order api interface', function () {
    $response = $this->doPost('api/v1/order/my/destroy', [
        'no' => '2025040866420167',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test cancel order api interface', function () {
    $response = $this->doPost('api/v1/order/my/cancel', [
        'no' => '2025040893942456',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test goods evaluate api interface', function () {
    $response = $this->doGet('api/v1/evaluate/goods', [
        'no' => 'eda482c3-df45-47a4-abc5-f795db6fefae',
        'page' => 1,
        'number' => 10,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test order evaluate init api interface', function () {
    $response = $this->doGet('api/v1/order/my/evaluate/init', [
        'no' => '2025040918889931',
    ]);
    dump($response);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test order evaluate store api interface', function () {
    $response = $this->doPost('api/v1/order/my/evaluate/store', [
        'no' => '2025040918889931',
        'items' => [
            [
                'id' => 22,
                'comment' => '测试评价',
                'images' => [
                    url('images/icons/wechat_pay_logo.png'),
                ],
            ],
        ],
        'rank' => 2,
        'goods_rank' => 2,
        'price_rank' => 2,
        'bus_rank' => 2,
        'delivery_rank' => 2,
        'service_rank' => 2,
        'is_anonymous' => 1,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test order receive api interface', function () {
    $response = $this->doPost('api/v1/order/my/receive', [
        'no' => '2025040918889931',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
