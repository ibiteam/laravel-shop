# 我的积分

* 个人中心我的积分数据

### 请求URL

* /api/v1/my/integrals

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
                "number": 10,
                "type": 1,
                "desc": "系统赠送",
                "created_at": "2025-04-16 09:52:33"
            }
        ],
        "meta": {
            "total": 1,
            "per_page": 10,
            "current_page": 1
        },
        "all_integral": 10
    }
}
```

data.list 参数说明

| 字段       | 字段类型   | 说明             |
|----------|--------|----------------|
| number | int    | 积分数量           |
| type  | int    | 积分类型 1、增加 2、减少 |
| desc  | String | 积分描述           |
| created_at  | String | 添加时间           |

data.all_integral 参数说明

| 字段       | 字段类型   | 说明   |
|----------|--------|------|
| all_integral | String | 全部积分 |
