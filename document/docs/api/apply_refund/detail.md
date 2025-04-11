# 售后详情

* 根据 申请售后id 或 订单id,订单明细id 获取申请售后信息

### 请求URL

* /api/v1/order/apply_refund/detail

### 请求方式

* GET

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明                                         |
|-----------------|---------|------|--------------------------------------------|
| apply_refund_id | integer | N    | 申请售后ID，order_no和order_detail_id 不存在时 该参数必有 |
| order_no        | string  | N    | 订单编号，apply_refund_id不存在时 该参数必有             |
| order_detail_id | integer | N    | 订单明细ID，apply_refund_id不存在时 该参数必有           |

### 返回示例

```json

```

