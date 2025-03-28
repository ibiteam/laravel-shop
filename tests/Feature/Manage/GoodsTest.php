<?php

it('add goods api interface', function () {
    $data = [
        'id' => 0,
        'category_id' => 1,
        'name' => '测试商品1',
        'label' => '热卖',
        'sub_name' => '测试商品1-副标题',
        'images' => [
            ['image' => 'https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png', 'type' => 'main'],
            ['image' => 'https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png', 'type' => 'detail'],
            ['image' => 'https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png', 'type' => 'detail'],
        ],
        'video' => 'https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.mp4',
        'video_duration' => 20,
        'content' => '<p> xxxxxxxxxxxxxxxxxxxxxx </p>',
        'unit' => '个',
        'spec_data' => [
            ['id' => 0, 'name' => '颜色', 'values' => [['id' => 0, 'name' => '红色']]],
            ['id' => 0, 'name' => '号码', 'values' => [['id' => 0, 'name' => 'L']]],
            ['id' => 0, 'name' => '送装服务', 'values' => [['id' => 0, 'name' => '不送装'], ['id' => 0, 'name' => '送装']]],
        ],
        'sku_data' => [
            ['id' => 0, 'thumb' => 'https://xxxx.xxx.xxx/2025/03/26/xxxxxx.png', 'price' => 10.06, 'number' => 288, 'is_show' => 1, 'template_1' => '红色', 'template_2' => 'L', 'template_3' => '不送装'],
            ['id' => 0, 'thumb' => 'https://xxxx.xxx.xxx/2025/03/26/xxxxxxxx.png', 'price' => 12.01, 'number' => 299, 'is_show' => 1, 'template_1' => '红色', 'template_2' => 'L', 'template_3' => '送装'],
        ],
        'price' => 10.23,
        'total' => 139,
        'type' => 1,
        'status' => 1,
        'can_quota' => 0,
        'quota_number' => 0,
        'parameters' => [
            ['name' => '产地', 'value' => '中国'],
            ['name' => '许可证号', 'value' => 'X0000000001'],
        ],
    ];
    $response = $this->doPost('api/manage/goods/info/update', $data);

    dump($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
