# 物流列表

### 请求URL

* /api/v1/order/delivery/list

### 请求方式

* GET

### 请求参数

| 字段       | 字段类型    | 是否必须 | 说明   |
|----------|---------|------|------|
| order_sn | integer | Y    | 订单编号 |
| page     | integer | Y    | 页码   |
| number   | integer | Y    | 每页条数 |

### 返回示例

```json
{
  "code": 200,
  "message": "success",
  "data": {
    "list": [
      {
        "id": 16,
        "delivery_no": "D123456789",
        "order_sn": "2025041560493453",
        "ship_company_name": "圆通快递",
        "ship_no": "S123456789",
        "status": 1,
        "shipped_at": "2023-10-01 10:00:00",
        "received_at": "2023-10-05 12:00:00",
        "remark": "已签收"
      }
    ],
    "meta": {
      "total": 1,
      "per_page": 10,
      "current_page": 1
    }
  }
}
```

