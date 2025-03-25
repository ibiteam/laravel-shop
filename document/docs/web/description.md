# Web 接口说明

### 授权信息

请求登录后，返回信息中会携带用户的token 以及过期时间

请求需要登录的接口时，headers 中Authorization字段中携带token，token格式为：Bearer + token
```
Authorization: Bearer login-token
```

# 接口返回特殊CODE枚举

|Code值| 特殊说明                        |
|:-----|:----------------------------|
|200| 接口请求成功，并返回数据                |
|400| 接口请求失败，错误信息在 `message` 字段上  |
|401| 用户未登录                       |
|403| 用户无权限                       |
