# 搜索用户地址

* 用于搜索用户地址数据

### 请求URL

* /api/v1/address/search

### 请求方式
* POST
### 请求参数

| 字段       | 是否必填 | 字段类型       | 说明 |
|:---------|:-----|:-----------|:--|
| keywords | F    | string/int | 用户名或者手机号 |

### 返回示例

```
{
    "code": 200,
    "message": "success",
    "data": [
        {
            "id": 3,
            "user_id": 1,
            "recipient_name": "jeck",
            "recipient_phone": "133****1111",
            "province": 2,
            "city": 52,
            "district": 506,
            "address_detail": "北京市丰台区国联股份",
            "is_default": 1,
            "province_name": "北京",
            "city_name": "北京",
            "district_name": "丰台区"
        },
        {
            "id": 1,
            "user_id": 1,
            "recipient_name": "唐士鑫",
            "recipient_phone": "151****1211",
            "province": 2,
            "city": 52,
            "district": 506,
            "address_detail": "北京市丰台区国联股份总部",
            "is_default": 0,
            "province_name": "北京",
            "city_name": "北京",
            "district_name": "丰台区"
        }
    ]
}
```
