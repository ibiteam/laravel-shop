# 根据售后类型回显数据

* 根据售后类型，获取对应的数据

### 请求URL

* /api/v1/order/apply_refund/show

### 请求方式

* GET

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明              |
|-----------------|---------|------|-----------------|
| order_sn        | string  | Y    | 订单编号            |
| order_detail_id | integer | Y    | 订单明细ID          |
| type            | integer | Y    | 售后类型（0退款；1退货退款） |

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "type": 1,
        "order_detail": {
            "goods_no": "cbda6ddf-0c08-4c82-a777-c73121b9698d",
            "goods_name": "华为老年机",
            "goods_image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/01\/cr7J8mCH3mcDo1unCAixpLpngG8hi7vfhIDTFhmh.jpg",
            "goods_price": "￥6999.00",
            "goods_number": "2",
            "goods_unit": "",
            "goods_sku_id": 4,
            "goods_amount": "13998",
            "goods_amount_format": "￥13998.00",
            "order_sn": "2025041096246684",
            "shipping_fee": "0.00",
            "pay_at": "2025-04-11 13:50:00",
            "refund_max_amount": "13998",
            "refund_max_number": "2"
        },
        "reason": [
            {
                "id": 10,
                "content": "其他原因"
            },
            {
                "id": 8,
                "content": "颜色与描述不符"
            },
            {
                "id": 6,
                "content": "商品与图片不符"
            },
            {
                "id": 4,
                "content": "快递损坏"
            },
            {
                "id": 2,
                "content": "商品描述不符"
            },
            {
                "id": 1,
                "content": "商品质量问题"
            }
        ],
        "explain": "1、订单退款后，退款金额将按支付方式原路返回，订单关闭；\n2、订单关闭后，无法恢复；\n3、如订单已使用的优惠券，订单关闭后优惠券不返还；\n4、如遇订单拆分，部分订单退款后优惠券不返还。"
    }
}
```

