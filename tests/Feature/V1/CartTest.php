<?php

it('cart list', function () {
    $response = $this->doGet('api/v1/cart/list');
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('cart number', function () {
    $response = $this->doGet('api/v1/cart/number');
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('cart store', function () {
    $response = $this->doPost('api/v1/cart/store', [
        'goods_id' => 4,
        'goods_sku_id' => 0,
        'buy_number' => 3,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('cart destroy', function () {
    $response = $this->doPost('api/v1/cart/destroy', [
        'ids' => [4],
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});


it('cart change number', function () {
    $response = $this->doPost('api/v1/cart/change_number', [
        'id' => 5,
        'goods_id' => 4,
        'goods_sku_id' => 0,
        'buy_number' => 2,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});

it('cart change check', function () {
    $response = $this->doPost('api/v1/cart/change_check', [
        'goods_id' => 0,
        'goods_sku_id' => 0,
        'is_check' => 0,
    ]);
    dd($response);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dump($res);
});
