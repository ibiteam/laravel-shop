<?php

it('category', function () {
    $response = $this->doGet('api/v1/supermarket', [
        'title' => '多多超市',
        'banner' => 'banner地址',
        'category_id' => 16,
        'page' => 1,
        'number' => 10,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});
