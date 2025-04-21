# 访问记录

* 个人中心我的商品访问记录

### 请求URL

* /api/v1/my/views

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
                "image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/01\/cr7J8mCH3mcDo1unCAixpLpngG8hi7vfhIDTFhmh.jpg",
                "price": "6999.00",
                "goods_name": "华为老年机",
                "goods_no": "cbda6ddf-0c08-4c82-a777-c73121b9698d",
                "unit": "",
                "updated_at": "2025-04-01 15:40:46"
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

data.list 参数说明

| 字段         | 字段类型   | 说明   |
|------------|--------|------|
| image      | string | 商品图  |
| price      | float  | 价格   |
| goods_name | string | 商品名称 |
| goods_no   | string | 商品编号 |
| unit       | string | 单位   |
| updated_at | string | 更新时间 |
