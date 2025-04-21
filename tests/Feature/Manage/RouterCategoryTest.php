<?php

it('tree', function () {
    $response = $this->doGet('api/manage/set/router_category/tree');
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

