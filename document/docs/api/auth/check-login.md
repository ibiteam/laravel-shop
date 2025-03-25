# 检测是否登录

* 用于进入前需要用户登录的页面

### 请求URL

* /api/v1/auth/check-login

### 请求方式
* GET

### 返回示例

```
{
    "code": 200,
    "message": "success",
    "data": {
        "is_login": true
    }
}
```
