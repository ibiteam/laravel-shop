<?php

it('test login by phone api interface', function () {
    $response = $this->doPost('api/v1/auth/login/phone', ['phone' => '13311112222', 'code' => '000000']);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertArrayHasKey('token', $response['data']);
    $this->assertEquals(200, $response['code']);
});
it('test check is login api interface', function () {
    $response = $this->doGet('api/v1/auth/check_login');
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertArrayHasKey('is_login', $response['data']);
    $this->assertTrue($response['data']['is_login']);
    $this->assertEquals(200, $response['code']);
});
it('test login by password api interface', function () {
    $response = $this->doPost('api/v1/auth/login/password', ['account' => 'laravel-shop', 'password' => 'laravel-shop-1']);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test logout api interface', function () {
    $response = $this->doPost('api/v1/auth/logout');
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test forget password api interface', function () {
    $response = $this->doPost('api/v1/auth/forget-password', [
        'phone' => '13322221111',
        'code' => '662595',
        'new_password' => 'Aa123456',
        'new_password_confirmation' => 'Aa123456',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test edit password api interface', function () {
    $response = $this->doPost('api/v1/auth/edit-password', [
        'code' => '344653',
        'new_password' => 'laravel-shop-1',
        'new_password_confirmation' => 'laravel-shop-1',
    ]);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
