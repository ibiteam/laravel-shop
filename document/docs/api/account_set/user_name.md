# 修改用户名

* 修改用户名

### 请求URL

* /api/v1/account_set/user_name

### 请求方式
* POST
### 请求参数

| 字段 | 是否必填 | 字段类型   | 说明  |
|:---|:-----|:-------|:----|
| user_name | Y    | string | 用户名 |

### 返回示例

```
{
    "code": 200,
    "message": "保存成功",
    "data": null
}


{
    "code": 400,
    "message": "用户名仅支持修改一次哦",
    "data": null
}
```
