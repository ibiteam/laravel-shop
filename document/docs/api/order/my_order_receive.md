# 确认收货

* 当前用户针对已发货的订单进行确认收货

### 请求URL

* /api/v1/order/my/receive

### 请求参数

| 字段       | 字段类型    | 是否必须 | 说明       |
|----------|---------|------|----------|
| order_sn | String  | Y    | 订单编号     |

### 请求方式
* POST

### 返回示例

```json
{
    "code": 200,
    "message": "确认收货成功",
    "data": null
}
```
