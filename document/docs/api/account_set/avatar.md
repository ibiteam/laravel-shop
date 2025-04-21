# 修改头像

* 修改头像

### 请求URL

* /api/v1/account_set/avatar

### 请求方式
* POST
### 请求参数

| 字段     | 是否必填 | 字段类型   | 说明 |
|:-------|:-----|:-------|:---|
| avatar | Y    | string | 头像 |

### 返回示例

```
{
    "code": 200,
    "message": "保存成功",
    "data": null
}


{
    "code": 400,
    "message": "头像不能为空",
    "data": null
}
```
