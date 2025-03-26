<?php

beforeEach(function () {
    $this->source = 'pc';
});

it('seller enter check', function () {
    $response = $this->doGet('api/pc/seller_enter/check');
    dump($response);
});

it('seller enter configs', function () {
    $response = $this->doGet('api/pc/seller_enter/configs', [
        'id' => 1,
    ]);
    dump($response);
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
    dump($response);
});

it('seller enter shop create', function () {
    $response = $this->doPost('api/pc/seller_enter/shop/create', [
        'seller_id' => '1',
        'name' => 'shop1',
        'logo' => 'required|string',
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
    dump($response);
});
