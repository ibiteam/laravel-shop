<?php

it('test search', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/search', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($response, $res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test recommend', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/recommend', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($response, $res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test home', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/home', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($response, $res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
