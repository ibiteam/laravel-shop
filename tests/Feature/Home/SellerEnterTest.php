<?php

it('seller enter check', function () {
    $response = $this->doGet('api/pc/seller_enter/check');
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('seller enter configs', function () {
    $response = $this->doGet('api/pc/seller_enter/configs', [
        'id' => 1,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('seller enter store', function () {
    $response = $this->doPost('api/pc/seller_enter/store', [
        'id' => 1,
        'enter_info' => [
            [
                'id' => 1,
                'type' => 'file',
                'name' => '今天中午吃啥?',
                'value' => 'https://www.baidu.com/img/bd_logo1.png',
            ],
            [
                'id' => 2,
                'type' => 'radio',
                'name' => '今天周ji',
                'value' => '周一',
            ],
        ],
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('seller enter shop create', function () {
    $response = $this->doPost('api/pc/seller_enter/shop/create', [
        'seller_id' => '1',
        'name' => 'shop1',
        'logo' => 'a.jpg',
        'title' => '',
        'keyword' => '',
        'description' => '',
        'country' => 1,
        'province' => 2,
        'city' => 3,
        'address' => 'asd',
        'ship_address' => 'dsa',
        'main_cate' => '鞋',
        'kf_phone' => '123456',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});
