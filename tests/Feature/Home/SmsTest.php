<?php

it('test send txt message api interface', function () {
    $response = $this->doPost('api/home/sms-action', ['action' => 'register', 'phone' => '13322229999']);
    dump($response);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertEquals(200, $response['code']);
});
