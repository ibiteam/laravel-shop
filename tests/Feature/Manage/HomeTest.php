<?php

use Illuminate\Support\Facades\Route;

it('menus', function () {
    $response = $this->doGet('api/manage/home/menus');
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('dashboard', function () {
    $response = $this->doGet('api/manage/home/dashboard');
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});
