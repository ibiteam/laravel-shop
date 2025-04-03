<?php

it('test send txt message api interface', function () {
    $response = $this->doPost('api/v1/sms-action', ['action' => 'password-forget', 'phone' => '13311112222']);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertEquals(200, $response['code']);
});
