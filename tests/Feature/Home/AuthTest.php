<?php
beforeEach(function () {
    $this->source = 'pc';
    $this->token = '13|Z7nmO2cW1OFE7ldiijXO64I7jVsMYkAA99bPvBQX4ec641fd';
});
it('test check user name is register', function () {
    $response = $this->doGet('api/home/auth/check-name', ['account' => 'laravel-shop-2']);
    dump($response);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertEquals(200, $response['code']);
});
it('test check phone is register', function () {
    $response = $this->doGet('api/home/auth/check-phone', ['phone' => '13322229999']);
    dump($response);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertEquals(200, $response['code']);
});
it('test register', function () {
    $response = $this->doPost('api/home/auth/register', [
        'account' => 'laravel-shop',
        'password' => 'a12345678',
        'password_confirmation' => 'a12345678',
        'phone' => '13322229997',
        'code' => '000000',
    ]);
    dump($response);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertEquals(200, $response['code']);
});
it('test login by password api interface', function () {
    $response = $this->doPost('api/home/auth/login-by-password',['account' => 'laravel-shop','password' => 'laravel-shop-1']);
    dump($response);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test login by phone api interface', function () {
    $response = $this->doPost('api/home/auth/login-by-phone', ['phone' => '13311112222', 'code' => '000000']);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertArrayHasKey('token', $response['data']);
    $this->assertEquals(200, $response['code']);
});

