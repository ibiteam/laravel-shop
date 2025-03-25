# 用户注册-手机号

* 用于用户注册成为平台用户

### 请求URL

* /api/v1/auth/register-by-phone

### 请求方式
* POST

### 请求参数

|字段|是否必填|字段类型|说明|
| :--- | :--- | :--- | :--- |
|phone|Y|string|用户名或手机号|
|password|Y|string|密码|

### 返回示例

```
{
    "code": 200,
    "message": "success",
    "data": {
        "token": "7|E0t0gCVrvHiaJPTaRey1v2sEWyVJgS3GcZb8mY86cc749fb8",
        "expires_at": 86400
    }
}
```
#### data 字段说明
|字段|字段类型|说明|
| :--- | :--- | :--- |
|token|string|用户登录成功返回token|
|expires_at|integer|token过期时间|
