# Api 接口说明

### 授权信息

请求登录后，返回信息中会携带用户的token 以及过期时间

请求需要登录的接口时，headers 中Authorization字段中携带token，token格式为：Bearer + token
```
Authorization: Bearer login-token
```
