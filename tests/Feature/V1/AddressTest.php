<?php

it('test address list', function () {
    $data = [

    ];
    $response = $this->doPost('api/v1/address/list', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test address search', function () {
    $data = [
        'keywords' => 'jeck',
    ];
    $response = $this->doPost('api/v1/address/search', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test address show', function () {
    $data = [
        'id' => 1,
    ];
    $response = $this->doPost('api/v1/address/show', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test address default', function () {
    $data = [
        'id' => 1,
    ];
    $response = $this->doPost('api/v1/address/default', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test address update', function () {
    $data = [
        'consignee' => 'jeck',
        'phone' => '13322221111',
        'province' => '2',
        'city' => '52',
        'district' => '506',
        'address_detail' => '北京市丰台区国联股份',
        'is_default' => 1,
    ];
    $response = $this->doPost('api/v1/address/update', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test address destroy', function () {
    $data = [
        'id' => '2',
    ];
    $response = $this->doPost('api/v1/address/destroy', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test address batch_destroy', function () {
    $data = [
        'ids' => [1],
    ];
    $response = $this->doPost('api/v1/address/batch_destroy', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test region', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/region', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});

it('test region_group', function () {
    $data = [

    ];
    $response = $this->doGet('api/v1/region_group', $data);
    $this->assertIsArray($response);
    $this->assertArrayHasKey('code', $response);
    $this->assertArrayHasKey('data', $response);
    $this->assertEquals(200, $response['code']);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
});
