# 添加/编辑地址

* 地址的更新接口

### 请求URL

* /api/v1/address/update

### 请求方式
* POST

### 请求参数

| 字段 | 是否必填 | 字段类型   | 说明              |
|:---|:-----|:-------|:----------------|
| id   | Y    | int    | 地址id  添加时地址id传0 |
| consignee   | Y    | string | 联系人             |
| phone   | Y    | int    | 联系电话            |
| province   | Y    | int    | 省份id            |
| city   | Y    | int    | 城市id            |
| district   | Y    | int    | 区域id            |
| address_detail   | Y    | int    | 详细地址            |
| is_default   | Y    | int    | 是否默认地址 1、是 0、否  |


### 返回示例

```
{
    "code": 200,
    "message": "保存成功",
    "data": null
}
```
