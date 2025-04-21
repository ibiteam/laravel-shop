# 收藏商品

* 个人中心我的收藏

### 请求URL

* /api/v1/my/collect

### 请求方式
* GET

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "list": [
            {
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/02\/zyY1K9aWwpQbZCTfWEkS1PpPIFP9g9jNlyIJ55aj.jpg",
                "price": "0.01",
                "goods_name": "懒人沙发",
                "goods_no": "b7841a73-5341-46eb-9856-9f0bcc305063",
                "unit": ""
            },
            {
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/01\/cr7J8mCH3mcDo1unCAixpLpngG8hi7vfhIDTFhmh.jpg",
                "price": "6999.00",
                "goods_name": "华为老年机",
                "goods_no": "cbda6ddf-0c08-4c82-a777-c73121b9698d",
                "unit": ""
            },
            {
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/08\/2025\/04\/08\/v1o6GdUhmGH84kNt0a0dIPiYWF9xGFvQSFAITb5R.jpg",
                "price": "6999.00",
                "goods_name": "华为 mate 60 pro",
                "goods_no": "5769f804-94ea-4564-ac33-65857eeb6629",
                "unit": ""
            }
        ],
        "meta": {
            "total": 3,
            "per_page": 10,
            "current_page": 1
        }
    }
}
```

data.list 参数说明

| 字段       | 字段类型   | 说明   |
|----------|--------|------|
| image | string | 商品图  |
| price | float  | 价格   |
| goods_name | string | 商品名称 |
| goods_no | string | 商品编号 |
| unit | string | 单位   |
