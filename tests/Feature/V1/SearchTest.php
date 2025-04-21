<?php

it('search goods', function () {
    $response = $this->doPost('api/v1/search/goods', [
        'keywords' => '智能手机',
        'category_id' => 1,
        // 'sort_type' => 'price_desc',
        'page' => 1,
        'number' => 2,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});



it('search keywords', function () {
    $response = $this->doGet('api/v1/search/keywords', [
        'keywords' => '商品',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});
