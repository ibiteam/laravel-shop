# 用户信息

* 获取用户信息

### 请求URL

* /api/v1/account_set/get_info

### 请求方式
* GET

### 返回示例

```
{
    "code": 200,
    "message": "success",
    "data": {
        "user_name": "laravel_shop", // 用户名
        "nickname": "lc_1742536033_4969_26", // 昵称
        "phone": "15145678901", // 手机号
        "avatar": "", // 头像
        "is_modify": true // 是否修改过用户名
    }
}
```
