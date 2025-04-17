# 为您推荐

* 为您推荐数据展示

### 请求URL

* /api/v1/recommend

### 请求方式
* GET

### 请求参数

| 字段 | 字段类型 | 是否必须   | 说明 |
|----|------|--------|----|
| no | N    | string | 商品编号 |

#### 成功示例
```
{
    "code": 200,
    "message": "success",
    "data": {
        "list": [
            {
                "no": "5769f804-94ea-4564-ac33-65857eeb6629",
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/08\/2025\/04\/08\/v1o6GdUhmGH84kNt0a0dIPiYWF9xGFvQSFAITb5R.jpg",
                "name": "华为 mate 60 pro",
                "price": "6999.00",
                "label": "折扣",
                "sub_name": "本商品可支持申请国家补贴",
                "sales_volume": null
            },
            {
                "no": "b7841a73-5341-46eb-9856-9f0bcc305063",
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/02\/zyY1K9aWwpQbZCTfWEkS1PpPIFP9g9jNlyIJ55aj.jpg",
                "name": "懒人沙发",
                "price": "439.00",
                "label": "热卖",
                "sub_name": "懒人沙发平价款",
                "sales_volume": null
            },
            {
                "no": "85922ff0-d32e-4a89-9c51-e3c2bb825f96",
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/02\/Bzn4Qptn1gaUr8S7aOkd20c3P7DOWz88CsqeXeMn.jpg",
                "name": "懒人沙发",
                "price": "1399.00",
                "label": "热卖",
                "sub_name": "懒人沙发",
                "sales_volume": null
            },
            {
                "no": "790fde4a-a1b3-4f64-8b8a-314ea762ef65",
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/02\/N9Tk9WAyWv8pfyp6VAUNDCHxNQZfNYst7YBS0bXf.jpg",
                "name": "戴尔",
                "price": "2399.00",
                "label": "推荐",
                "sub_name": "戴尔台式机",
                "sales_volume": null
            },
            {
                "no": "a571d598-8d69-4a94-970a-397a057adda0",
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/02\/1Hg5cn8r7Ot3X0Z2fvMNcjMX01hGqUXovkX7F6CN.jpg",
                "name": "小米100",
                "price": "299.00",
                "label": "热卖",
                "sub_name": "高清液晶电视",
                "sales_volume": null
            },
            {
                "no": "bedae4a8-7413-41a8-9c54-f51a1a6e5f12",
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/01\/MxwQZrlgaeP2HMRupRReZmD87JGUplIcjFFPjvH0.jpg",
                "name": "华为低端机",
                "price": "1999.00",
                "label": "推荐",
                "sub_name": "华为低端机，物有所值的一款！",
                "sales_volume": null
            }
        ],
        "meta": {
            "total": 7,
            "per_page": 6,
            "current_page": 1
        }
    }
}
```
