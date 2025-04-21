# 提交申请售后

### 请求URL

* /api/v1/order/apply_refund/store

### 请求方式

* POST

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明                                         |
|-----------------|---------|------|--------------------------------------------|
| apply_refund_id | integer | N    | 申请售后ID，order_sn和order_detail_id 不存在时 该参数必有 |
| order_sn        | string  | N    | 订单编号，apply_refund_id不存在时 该参数必有             |
| order_detail_id | integer | N    | 订单明细ID，apply_refund_id不存在时 该参数必有           |
| number          | integer | Y    | 申请售后数量                                     |
| money           | float   | Y    | 退款金额                                       |
| type            | integer | Y    | 售后类型（0退款；1退货退款）                            |
| reason_id       | integer | Y    | 退款原因ID                                     |
| description     | string  | N    | 退款描述                                       |
| certificate     | string  | N    | 商品凭证,多个逗号隔开                                |

### 返回示例

```json
{
  "code": 200,
  "message": "success",
  "data": {
    "apply_refund_id": 1
  }
}
```

