# 检测是否允许申请售后

### 请求URL

* /api/v1/order/apply_refund/verify

### 请求方式

* GET

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明     |
|-----------------|---------|------|--------|
| order_sn        | string  | Y    | 订单编号   |
| order_detail_id | integer | Y    | 订单明细ID |

### 返回示例

```json
{
  "code": 200,
  "message": "允许申请售后",
  "data": null
}
```

