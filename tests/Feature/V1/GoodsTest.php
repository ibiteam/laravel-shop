<?php

it('test goods detail api interface', function () {
    $goods_sn = 'eda482c3-df45-47a4-abc5-f795db6fefae';
    $response = $this->doGet("api/v1/goods/{$goods_sn}");
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test goods sku detail api interface', function () {
    $goods_sn = 'eda482c3-df45-47a4-abc5-f795db6fefae';
    $goods_sku_unique = '13_14_15';
    $response = $this->doGet("api/v1/goods/{$goods_sn}/{$goods_sku_unique}");
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test follow goods api interface', function () {
    $goods_sn = 'eda482c3-df45-47a4-abc5-f795db6fefae';
    $response = $this->doPost('api/v1/goods/follow', ['no' => $goods_sn]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test unfollow goods api interface', function () {
    $goods_sn = 'eda482c3-df45-47a4-abc5-f795db6fefae';
    $response = $this->doPost('api/v1/goods/unfollow', ['no' => $goods_sn]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test goods check number api interface', function () {
    $goods_sn = 'eda482c3-df45-47a4-abc5-f795db6fefae';
    $response = $this->doGet("api/v1/goods/{$goods_sn}/check_number", ['sku_id' => 3, 'number' => 299]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
