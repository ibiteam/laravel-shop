# 初始化申请售后

* 获取申请售后类型与售后商品信息

### 请求URL

* /api/v1/order/apply_refund/init

### 请求方式

* GET

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明     |
|-----------------|---------|------|--------|
| order_sn        | string  | Y    | 订单编号   |
| order_detail_id | integer | Y    | 订单明细ID |

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "goods_info": {// 售后商品信息
      "order_detail_id": 1,
      "order_sn": "2025041096246684",
      "goods_no": "cbda6ddf-0c08-4c82-a777-c73121b9698d",
      "goods_name": "华为老年机",
      "goods_image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/01\/cr7J8mCH3mcDo1unCAixpLpngG8hi7vfhIDTFhmh.jpg",
      "goods_number": 2,
      "goods_price": "6999",
      "goods_price_format": "￥6999",
      "goods_integral": 0,
      "goods_amount": "13998.00",
      "goods_sku_id": 4,
      "goods_sku_value": [
        {
          "key": "款式",
          "value": "雅丹黑"
        },
        {
          "key": "内存",
          "value": "12+256"
        }
      ],
      "goods_unit": "",
      "is_show_after_sales": 0
    },
    "refund_type": [// 售后类型
      {
        "label": "我要退款（无需退货）",
        "value": 0,
        "desc": "已与商家协商一致，只退款，不退货"
      },
      {
        "label": "我要退货退款",
        "value": 1,
        "desc": "需要退还收到的货物"
      }
    ]
  }
}
```

