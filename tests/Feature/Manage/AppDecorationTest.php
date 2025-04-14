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
        'id' => 1,
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
        'id' => '', // 组件自增id
        'name' => '商品推荐',
        'component_name' => AppDecorationItem::COMPONENT_NAME_GOODS_RECOMMEND, // 组件名称
        'is_show' => Constant::ONE,
        'is_fixed_assembly' => Constant::ZERO, // 不是固定组件
        'sort' => Constant::ZERO,
        'content' => [
            'layout' => '', // 商品布局
            'width' => 350, // 图片宽度 固定 350
            'height' => 190, // 图片高度 190 ~ 250
            'title' => [
                'icon' => '', // 小图标
                'name' => '', // 标题
                'align' => 'left', // 对齐方式 left center
                'suffix' => '', // 标题右侧文案
                'url' => [
                    'name' => '',
                    'value' => '',
                ],
            ], // 图片设置
            'goods' => [
                'rule' => 1, // 推荐规则 1、智能推荐 2、手动推荐
                'sort_type' => 1, // 排序类型 1、销量 2、好评 3、低价 4、新品
                'number' => 3, // 数量限制 1 ~ 20
                'goods_ids' => [],
            ], // 商品设置
        ],
    ];

    dd(json_encode($arr), JSON_UNESCAPED_UNICODE);
});

it('test goods list', function () {
    $data = [
        'keywords' => '',
        'goods_id' => '',
        'category_id' => '',
        'number' => 10,
    ];
    $response = $this->doPost('api/manage/set/app_decoration/goods/list', $data);
    dd($response, json_encode($response, JSON_UNESCAPED_UNICODE));
});

it('test recommend data', function () {
    $data = [

    ];
    $response = $this->doGet('api/manage/set/app_decoration/recommend/data', $data);
    dd($response, json_encode($response, JSON_UNESCAPED_UNICODE));
});

it('test import goods', function () {
    $data = [
        'goods_ids' => ['1','2','3','4'],
        'goods_nos' => ['cbda6ddf-0c08-4c82-a777-c73121b9698d', '5769f804-94ea-4564-ac33-65857eeb6629'],
    ];
    $response = $this->doPost('api/manage/set/app_decoration/goods/import', $data);
    dd($response, json_encode($response, JSON_UNESCAPED_UNICODE));
});

it('test goods intelligent', function () {
    $data = [
        'number' => '',
        'sort_type' => '',
    ];
    $response = $this->doPost('api/manage/set/app_decoration/goods/intelligent', $data);
    dd($response, json_encode($response, JSON_UNESCAPED_UNICODE));
});
