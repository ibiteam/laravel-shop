<?php

namespace Components;

use App\Components\ComponentFactory;
use App\Models\AppDecorationItem;
use App\Models\AppDecorationItemDraft;
use Tests\TestCase;

it('test showLoginForm', function () {
    $component = ComponentFactory::getComponent(AppDecorationItem::COMPONENT_NAME_HORIZONTAL_CAROUSEL, '轮播图');
//    dd($component->parameter());
    $data = [
        'id' => '', // 组件自增id
        'name' => '轮播图',
        'component_name' => 'horizontal_carousel',
        'is_show' => 1, // 是否展示 1展示0不展示
        'sort' => 1, // 排序
        'is_fixed_assembly' => 0, // 是否是固定组件 1是 0否
        'content' => [
            'width' => 710, // 宽度：不支持修改
            'height' => 200,
            'style' => 1,  // 显示样式：默认 1、平铺 2、过渡
            'interval' => 3,
            'data' => [
                [
                    'url' => [
                        'name' => '', // 链接名称
                        'value' => '', // 链接
                    ],
                    'image' => 'q4qeqwewq', // 图片地址
                    'sort' => 1, // 排序 （1~100）
                    'is_show' => 1, // 是否显示 1展示 0隐藏
                    'date_type' => 1, // 时间类型： 1 长期  0：时间范围
                    'time' => [date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                ],
            ],
        ],
    ];
//    $data = $component->validate($data);
//    dd(json_encode($data, JSON_UNESCAPED_UNICODE));

    $item = AppDecorationItemDraft::whereComponentName('horizontal_carousel')->first()->toArray();
    $data = $component->display($item);
    dd($data);
});

