# 撤销申请

### 请求URL

* /api/v1/order/apply_refund/revoke

### 请求方式

* POST

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明     |
|-----------------|---------|------|--------|
| apply_refund_id | integer | Y    | 申请售后ID |

### 返回示例

```json
{
  "code": 200,
  "message": "撤销成功",
  "data": null
}
```

