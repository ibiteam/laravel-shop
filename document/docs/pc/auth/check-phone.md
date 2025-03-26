# 注册-检测手机号

* 用于用户注册时，检测当前手机号是否已经注册

### 请求URL

* /api/pc/auth/check-phone

### 请求方式
* GET

### 请求参数

| 字段    |是否必填| 字段类型    | 说明  |
|:------| :--- |:--------|:----|
| phone |Y| integer | 手机号 |

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
