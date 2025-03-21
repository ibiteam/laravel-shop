<?php

it('test send txt message api interface', function () {
    $response = $this->doPost('api/v1/sms-action', ['action' => 'register', 'phone' => '13322221111']);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertEquals(200, $response['code']);
});
