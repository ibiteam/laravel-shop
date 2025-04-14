# 微信授权

* 用户通过微信APP打开网站时，需要进行微信授权，获取用户信息，进行登录

### 请求URL

* /api/v1/auth/wechat

### 请求方式
* POST

### 请求参数

| 字段   | 是否必填 | 字段类型   | 说明    |
|:-----|:-----|:-------|:------|
| code | Y    | String | 微信授权码 |

### 返回示例

```
{
    "code": 200,
    "message": "success",
    "data": {
        "token": "7|E0t0gCVrvHiaJPTaRey1v2sEWyVJgS3GcZb8mY86cc749fb8",
        "openid": "E0t0gCVrvHiaJPTaRey1v2sEWyVJgS3GcZb8mY86cc749fb8"
    }
}
```
#### data 字段说明
| 字段     | 字段类型   | 说明                      |
|:-------|:-------|:------------------------|
| token  | String | 已经登录用户返回token，未登录返回空字符串 |
| openid | String | 用户openid                |
