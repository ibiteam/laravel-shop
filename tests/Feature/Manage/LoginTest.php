<?php

it('test showLoginForm', function () {
    $data = [

    ];
    $response = $this->doGet('api/manage/login', $data);

    dd($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test login', function () {
    $data = [
        'user_name' => 'admin',
        'password' => 'Aa123456',
    ];
    $response = $this->doPost('api/manage/login', $data);

    dd($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
