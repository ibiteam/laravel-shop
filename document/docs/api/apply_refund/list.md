# 退款售后列表

### 请求URL

* /api/v1/order/apply_refund/list

### 请求方式

* GET

### 请求参数

| 字段       | 字段类型    | 是否必须 | 说明    |
|----------|---------|------|-------|
| keywords | integer | N    | 搜索关键词 |
| page     | integer | Y    | 页码    |
| number   | integer | Y    | 每页条数  |

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "list": [
            {
                "id": 1,
                "no": "20250414042609644",
                "money": "5.00",
                "number": "2.00",
                "type": 1,
                "status": 5,
                "created_at": "2025-04-14 16:43:52",
                "deal_end_time": 1745552646,
                "now_time": 1744716228,
                "order_no": "2025041096246684",
                "order_detail_id": 1,
                "goods_no": "cbda6ddf-0c08-4c82-a777-c73121b9698d",
                "goods_name": "华为老年机",
                "goods_image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/01\/cr7J8mCH3mcDo1unCAixpLpngG8hi7vfhIDTFhmh.jpg",
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
                ]
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

