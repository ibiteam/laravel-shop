# 修改订单地址

* 修改订单地址请求接口

### 请求URL

* /api/v1/order/my/address/update

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明       |
|-----------------|---------|------|----------|
| order_sn        | String  | Y    | 订单编号     |
| user_address_id | Integer | Y    | 用户收货地址ID |

### 请求方式
* POST

### 返回示例

```json
{
    "code": 200,
    "message": "修改地址成功",
    "data": null
}
```
