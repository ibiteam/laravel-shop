# 直接下单

* 直接下单请求生成订单接口

### 请求URL

* /api/v1/order/direct/done

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明               |
|-----------------|---------|------|------------------|
| no              | String  | Y    | 商品编号             |
| sku_id          | Integer | N    | 商品规格ID           |
| buy_number      | Integer | Y    | 购买数量             |
| payment_method  | String  | Y    | 支付方式：online 在线支付 |
| user_address_id | Integer | Y    | 收货地址ID           |
| remark          | String  | N    | 买家留言             |

### 请求方式
* POST

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "order_sn": "2025040918889931",
        "can_pay": true
    }
}
```

data.user_address 参数说明

| 字段       | 字段类型    | 说明         |
|----------|---------|------------|
| order_sn | String  | 订单编号       |
| can_pay  | Boolean | 是否支付，进入收银台 |
