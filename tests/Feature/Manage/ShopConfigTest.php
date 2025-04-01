<?php

it('site info', function () {
    $response = $this->doGet('api/manage/set/shop_config', ['group_name' => 'site_info']);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
