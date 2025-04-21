<?php

it('add goods api interface', function () {
    $data = [
        'id' => 2,
        'category_id' => 1,
        'name' => '测试商品1',
        'label' => '热卖',
        'sub_name' => '测试商品1-副标题',
        'images' => [
            'https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png',
            'https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png',
            'https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png',
        ],
        'video' => 'https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.mp4',
        'video_duration' => 20,
        'content' => '<p> xxxxxxxxxxxxxxxxxxxxxx </p>',
        'unit' => '个',
        'spec_data' => [
            ['name' => '颜色', 'values' => [['name' => '红色']]],
            ['name' => '号码', 'values' => [['name' => 'L']]],
            ['name' => '送装服务', 'values' => [['name' => '不送装'], ['name' => '送装']]],
        ],
        'sku_data' => [
            ['thumb' => 'https://xxxx.xxx.xxx/2025/03/26/xxxxxx.png', 'price' => 10.06, 'number' => 288, 'is_show' => 1, 'template_1' => '红色', 'template_2' => 'L', 'template_3' => '不送装'],
            ['thumb' => 'https://xxxx.xxx.xxx/2025/03/26/xxxxxxxx.png', 'price' => 12.01, 'number' => 299, 'is_show' => 1, 'template_1' => '红色', 'template_2' => 'L', 'template_3' => '送装'],
        ],
        'integral' => 0,
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
it('test store goods parameter template api interface', function () {
    $response = $this->doPost('api/manage/goods/parameter/template/store', [
        'name' => '测试参数模板',
        'values' => [
            ['name' => '颜色', 'value' => '红色'],
            ['name' => '颜色', 'value' => '蓝色'],
            ['name' => '颜色', 'value' => '绿色'],
        ],
    ]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test update goods parameter template api interface', function () {
    $response = $this->doPost('api/manage/goods/parameter/template/update', [
        'id' => 1,
        'name' => '测试参数模板1',
        'values' => [
            ['name' => '颜色', 'value' => '红色'],
            ['name' => '产地', 'value' => '中国'],
            ['name' => '包装', 'value' => '袋装'],
        ],
    ]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test destroy goods parameter template api interface', function () {
    $response = $this->doPost('api/manage/goods/parameter/template/destroy', ['id' => 1]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

it('test store goods sku template api interface', function () {
    $response = $this->doPost('api/manage/goods/sku/template/store', [
        'name' => '测试SKU模板',
        'values' => [
            ['name' => '颜色', 'values' => [['name' => '红色']]],
            ['name' => '号码', 'values' => [['name' => 'L']]],
            ['name' => '送装服务', 'values' => [['name' => '不送装'], ['name' => '送装']]],
        ],
    ]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test update goods sku template api interface', function () {
    $response = $this->doPost('api/manage/goods/sku/template/update', [
        'id' => 1,
        'name' => '测试SKU模板',
        'values' => [
            ['name' => '颜色', 'values' => [['name' => '红色']]],
            ['name' => '号码', 'values' => [['name' => 'XL']]],
            ['name' => '送装服务', 'values' => [['name' => '不送装'], ['name' => '送装']]],
        ],
    ]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
it('test destroy goods sku template api interface', function () {
    $response = $this->doPost('api/manage/goods/sku/template/destroy', ['id' => 1]);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
