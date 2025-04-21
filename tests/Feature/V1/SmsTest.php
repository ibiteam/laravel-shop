<?php

it('test send txt message api interface', function () {
    $response = $this->doPost('api/v1/sms-action', ['action' => 'login', 'phone' => '13311112222']);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertEquals(200, $response['code']);
});

it('test check action code', function () {
    $response = $this->doPost('api/v1/check/action/code', ['action' => 'password-forget', 'phone' => '13311112222', 'code' => '000000']);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertEquals(200, $response['code']);
});
