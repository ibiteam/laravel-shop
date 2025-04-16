# 我的红包

* 个人中心我的红包数据

### 请求URL

* /api/v1/my/bonuses

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
                "name": "测试红包",
                "money": "10.00",
                "use_start_time": "2025-04-15 16:55:00",
                "use_end_time": "2025-04-30 16:55:03",
                "min_amount": "0.00",
                "type": 0,
                "limit_name": ""
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

| 字段       | 字段类型   | 说明                |
|----------|--------|-------------------|
| name | String | 名称                |
| money  | float  | 金额                |
| use_start_time  | date   | 使用开始时间            |
| use_end_time  | date   | 使用结束时间            |
| min_amount  | float  | 最小使用金额            |
| type  | int    | 0、不限制 1、限商品 2、限分类 |
| limit_name  | String | 限制的名称               |
