# 我的优惠券

* 个人中心我的优惠券数据

### 请求URL

* /api/v1/my/coupons

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
                "name": "测试优惠券",
                "money": "100.00",
                "start_time": "2025-04-14 17:02:35",
                "end_time": "2025-04-30 17:02:39",
                "min_amount": "10.00",
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
| start_time  | date   | 开始时间            |
| end_time  | date   | 结束时间            |
| min_amount  | float  | 最小使用金额            |
| type  | int    | 0、不限制 1、限商品 2、限分类 |
| limit_name  | String | 限制的名称               |
