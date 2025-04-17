<?php

it('special index', function () {
    $response = $this->doGet('api/v1/special', [
        'id' => 16,
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($res);
});
