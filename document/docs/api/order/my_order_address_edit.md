# 修改订单地址回显

* 修改订单地址回显接口

### 请求URL

* /api/v1/order/my/address/edit

### 请求参数

| 字段       | 字段类型   | 是否必须 | 说明   |
|----------|--------|------|------|
| order_sn | String | Y    | 订单编号 |

### 请求方式
* GET

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "order_sn": "2025040918889931",
        "province_name": "北京",
        "city_name": "北京",
        "district_name": "朝阳区",
        "address": "北京市丰台区国联股份",
        "consignee": "jeck",
        "phone": "133****1111"
    }
}
```

### 返回参数说明

data 返回参数说明

| 字段            | 字段类型    | 字段说明 |
|---------------|---------|------|
| order_sn      | String  | 订单编号 |
| province_name | String  | 省份名称 |
| city_name     | String  | 城市名称 |
| district_name | String  | 区县名  |
| address       | Integer | 详细地址 |
| consignee     | String  | 收货人  |
| phone         | String  | 手机号  |
