# 客服地址

* 用于获取国联云客服地址

### 请求URL

* /api/v1/chat/url

### 请求方式
* GET

### 请求参数

| 字段         | 是否必填 |字段类型| 说明            |
|:-----------|:-----| :--- |:--------------|
| no         | N    |integer| 商品编号          |
| source_url | N    |string| 地址来源 - 当前页面地址 |

#### 成功示例
```
{
    "code": 200,
    "message": "success",
    "data": {
        "url": "https:\/\/testchat.ptdplat.com\/#\/client?platform_id=174417042419&seller_id=0&source=h5&timeStamp=1744355392&sign=a33632c3c65857e4da24f8824b11eeb1"
    }
}
```
