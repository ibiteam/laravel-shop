<?php

it('test get shop config api interface', function () {
    $response = $this->doGet('api/v1/shop/config');
    dump(json_encode($response));
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
