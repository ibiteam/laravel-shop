# 检测是否登录

### 请求URL

* /api/pc/auth/check-login

### 请求方式
* GET

### 返回示例

```
{
    "code": 200,
    "message": "success",
    "data": {
        "token": "7|E0t0gCVrvHiaJPTaRey1v2sEWyVJgS3GcZb8mY86cc749fb8",
        "expires_at": 86400,
        "is_login": true
    }
}
```
### 返回参数说明 `data` 
| 参数名称      | 参数类型  | 是否必填 | 参数说明 |
| ----------- | --------- | ------- | -------- |
| token       | string    | 是      | token    |
| expires_at  | int       | 是      | 过期时间 |
| is_login    | boolean   | 是      | 是否登录 |
