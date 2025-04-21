# 地址详情

* 单条地址数据

### 请求URL

* /api/v1/address/show

### 请求方式
* POST
### 请求参数

| 字段 | 是否必填 | 字段类型       | 说明   |
|:---|:-----|:-----------|:-----|
| id | Y    | int | 地址id |

### 返回示例

```
{
    "code": 200,
    "message": "success",
    "data": {
        "id": 1,
        "user_id": 1,
        "consignee": "唐士鑫",
        "phone": "151****1211",
        "province": 2,
        "city": 52,
        "district": 506,
        "address_detail": "北京市丰台区国联股份总部",
        "is_default": 0,
        "province_name": "北京",
        "city_name": "北京",
        "district_name": "丰台区"
    }
}
```
