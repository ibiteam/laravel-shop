# 退货物流信息

### 请求URL

* /api/v1/order/apply_refund/ship_info

### 请求方式

* get

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明      |
|-----------------|---------|------|---------|
| apply_refund_id | integer | Y    | 申请售后ID  |

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "mobile": "13322221111",
        "ship_companies": [
            {
                "id": 1,
                "name": "韵达快递",
                "code": "1"
            },
            {
                "id": 2,
                "name": "申通快递",
                "code": "2"
            }
        ]
    }
}
```

