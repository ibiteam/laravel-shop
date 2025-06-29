<?php

it('test chat url', function () {
    $data = [
        'no' => '17447944891',
        'source_url' => 'https://vue-shop.ptdmeta.cn/good?goods_no=17447944891'
    ];
    $response = $this->doGet('api/v1/chat/url', $data);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    dd($res);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);

});
