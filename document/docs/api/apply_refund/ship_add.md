# 用户填写退货物流信息

### 请求URL

* /api/v1/order/apply_refund/ship_add

### 请求方式

* post

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明      |
|-----------------|---------|------|---------|
| apply_refund_id | integer | Y    | 申请售后ID  |
| no              | string  | Y    | 物流单号    |
| ship_company_id | integer | Y    | 快递公司ID  |
| phone           | string  | Y    | 手机号     |
| description     | string  | N    | 描述说明    |
| certificate     | string  | N    | 凭证，逗号隔开 |

### 返回示例

```json
{
    "code": 200,
    "message": "填写成功",
    "data": null
}
```

