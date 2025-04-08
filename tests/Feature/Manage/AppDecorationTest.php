<?php

use App\Models\AppDecorationItem;
use App\Utils\Constant;

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


it('test set app_decoration decoration store', function () {
    $data = '{"id":2,"title":"标题","button_type":1,"keywords":"关键字","description":"描述","data":[{"id":"37","name":"弹屏广告","component_name":"danping_advertisement","is_show":1,"sort":25,"is_fixed_assembly":1,"content":{"image":"https://testcdn.ibisaas.com/2024/12/23/9uYJRlTvLTmolg6xps8TiBZ5ZGgY1JcfMcRTRose.png","url":{"name":"https://www.baidu.com","value":"https://www.baidu.com"},"date_type":0,"time":["2025-04-15 00:00:00","2025-04-26 23:59:59"],"is_show":1},"data":[]},{"id":"38","name":"悬浮广告","component_name":"suspended_advertisement","is_show":1,"sort":25,"is_fixed_assembly":1,"content":{"image":"https://testcdn.ibisaas.com/2024/12/23/9uYJRlTvLTmolg6xps8TiBZ5ZGgY1JcfMcRTRose.png","url":{"name":"https://www.baidu.com","value":"https://www.baidu.com"},"date_type":0,"describe":"描述内容","time":["2025-04-15 00:00:00","2025-04-26 23:59:59"],"is_show":1},"data":[]}]}';
    $data = json_decode($data, true);
    $response = $this->doPost('api/manage/set/app_decoration/decoration/store', $data);
    dd($response, json_encode($response, JSON_UNESCAPED_UNICODE));
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test diy', function () {
    $arr = [

    ];

    dd(json_encode($arr), JSON_UNESCAPED_UNICODE);
});
