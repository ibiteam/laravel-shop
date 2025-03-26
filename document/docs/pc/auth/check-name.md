# 注册-检测用户名

* 用于用户注册时，检测当前用户名是否已经注册

### 请求URL

* /api/pc/auth/check-name

### 请求方式
* GET

### 请求参数

| 字段      |是否必填|字段类型| 说明  |
|:--------| :--- | :--- |:----|
| account |Y|string| 用户名 |

### 返回示例

```
{
    "code": 200,
    "message": "success",
    "data": {
        "is_register": true,
    }
}

{
    "code": 400,
    "message": "请求失败",
    "data": null
}
```
