<?php

it('test set app_decoration', function () {
    $data = [

    ];
    $response = $this->doGet('api/manage/set/app_decoration', $data);
    dd($response, json_encode($response, JSON_UNESCAPED_UNICODE));
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test set app_decoration decoration', function () {
    $data = [
        'id' => 1
    ];
    $response = $this->doGet('api/manage/set/app_decoration/decoration', $data);
    dd($response, json_encode($response, JSON_UNESCAPED_UNICODE));
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
