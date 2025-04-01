<?php
// 目录
it('test material folder', function () {
    $data = [

    ];
    $response = $this->doGet('api/manage/material/folder', $data);
    dd(json_encode($response, JSON_UNESCAPED_UNICODE));
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

// 根据类型获取父级目录
it('test material folder parent', function () {
    $data = [
        'dir_type' => 2
    ];
    $response = $this->doGet('api/manage/material/folder/parent', $data);
    dd($response, json_encode($response, JSON_UNESCAPED_UNICODE));
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

// 获取素材列表
it('test material', function () {
    $data = [
        'directory_id' => 1
    ];
    $response = $this->doGet('api/manage/material', $data);
    dd($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

// 重命名
it('test material rename', function () {
    $data = [
        'id' => 1,
        'name' => '测试名称',
    ];
    $response = $this->doGet('api/manage/material/rename', $data);
    dd($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});


// 新建文件夹
it('test material new folder', function () {
    $data = [
        'directory_id' => 1
    ];
    $response = $this->doGet('api/manage/material/new/folder', $data);
    dd($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});


// 删除素材
it('test material destory', function () {
    $data = [
        'id' => 1
    ];
    $response = $this->doGet('api/manage/material/destory', $data);
    dd($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

// 批量删除素材
it('test material batch destory', function () {
    $data = [
        'ids' => [1,2]
    ];
    $response = $this->doGet('api/manage/material/batch/destory', $data);
    dd($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

// 移动素材
it('test material move', function () {
    $data = [
        'id' => 1,
        'target_directory_id' => 2,
    ];
    $response = $this->doGet('api/manage/material/move', $data);
    dd($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});

// 批量移动素材
it('test material batch move', function () {
    $data = [
        'ids' => [1,2],
        'target_directory_id' => 0,
    ];
    $response = $this->doGet('api/manage/material/batch/move', $data);
    dd($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
});
