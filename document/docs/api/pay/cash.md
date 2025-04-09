# 订单收银台

* 订单收银台获取订单信息以及支付信息接口

### 请求URL

* /api/v1/order/cash

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明   |
|-----------------|---------|------|------|
| no              | String  | Y    | 订单编号 |

### 请求方式
* POST

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "order": {
            "no": "2025040741365428",
            "order_amount": "30.69",
            "created_at": "2025-04-07 19:55:53"
        },
        "payment_methods": [
            {
                "name": "微信支付",
                "description": "此方式仅支持付款金额≤900元的订单",
                "icon": "http://shop.host/storage/manage/2025/04/02/Zyd93rqYDmIFOb3ICSoqjt9awR2dcaIC3OLdbwaH.png",
                "alias": "wechat",
                "is_recommend": true,
                "sort": 0,
                "can_use": true
            }
        ]
    }
}
```

data.order 参数说明

| 字段           | 字段类型   | 说明   |
|--------------|--------|------|
| no           | String | 订单编号 |
| order_amount | String | 订单金额 |
| created_at   | String | 下单时间 |

data.payment_methods 参数说明

| 字段           | 字段类型    | 说明     |
|--------------|---------|--------|
| name         | String  | 支付方式名称 |
| description  | String  | 支付方式描述 |
| icon         | String  | 支付方式图标 |
| alias        | String  | 支付方式别名 |
| is_recommend | Boolean | 是否推荐   |
| sort         | Integer | 排序     |
| can_use      | Boolean | 是否可用   |

