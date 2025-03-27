<?php

it('test account_set get_info', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/account_set/get_info', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test account_set user_name', function () {
    $data = [
        'user_name' => 'laravel_shop',
    ];
    $response = $this->doPost('api/v1/account_set/user_name', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test account_set nickname', function () {
    $data = [
        'nickname' => 'lc_1742536033_4969_26',
    ];
    $response = $this->doPost('api/v1/account_set/nickname', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test account_set avatar', function () {
    $data = [
        'avatar' => '',
    ];
    $response = $this->doPost('api/v1/account_set/avatar', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);

});

it('test account_set phone', function () {
    $data = [
//        'phone' => '13311112222',
        'phone' => '15145678902',
        'code' => '000000',
    ];
    $response = $this->doPost('api/v1/account_set/phone', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});
