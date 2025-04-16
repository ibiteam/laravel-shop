<?php

it('test my integrals', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/my/integrals', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($response, $res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test my collect', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/my/collect', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($response, $res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test my views', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/my/views', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($response, $res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test my coupons', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/my/coupons', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($response, $res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test my bonuses', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/my/bonuses', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($response, $res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test my batch unfollow', function () {
    $data = [
        'nos' => ['cbda6ddf-0c08-4c82-a777-c73121b9698d']
    ];
    $response = $this->doPost('api/v1/my/batch/unfollow', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($response, $res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
