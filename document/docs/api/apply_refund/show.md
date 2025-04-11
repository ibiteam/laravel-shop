# 根据售后类型回显数据

* 根据售后类型，获取对应的数据

### 请求URL

* /api/v1/order/apply_refund/show

### 请求方式

* GET

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明              |
|-----------------|---------|------|-----------------|
| order_no        | string  | Y    | 订单编号            |
| order_detail_id | integer | Y    | 订单明细ID          |
| type            | integer | Y    | 售后类型（0退款；1退货退款） |

### 返回示例

```json

```

