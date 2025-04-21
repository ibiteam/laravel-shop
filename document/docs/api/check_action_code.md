# 验证短信验证码

* 用于校验原手机号等等

### 请求URL

* /api/v1/check/action/code

### 请求方式
* POST

### 请求参数

| 字段     |是否必填|字段类型| 说明             |
|:-------| :--- | :--- |:---------------|
| phone  |Y|integer| 手机号 详见 操作类型枚举  |
| action |Y|string| 操作类型，详见 操作类型枚举 |
| code   |Y|string| 验证码            |

#### 操作类型枚举
| action枚举值       | 说明                   | 手机号是否必填 |
|:----------------|:---------------------|:--------|
| login           | 用于发送登录验证码            | Y       |
| password-forget | 用于发送忘记密码验证码          | Y       |
| password-edit   | 用于发送修改密码验证码          | N       |
| phone-edit   | 用于发送修改手机号验证码         | Y       |
| phone-verify   | 用于发送修改手机号之前的验证手机号验证码 | N       |

### 返回示例

#### 成功示例
```
{
    "code": 200,
    "message": "短信发送成功",
    "data": null
}
```
#### 失败示例
```
{
    "code": 400,
    "message": "短信发送失败",
    "data": null
}
```
```
{
    "code": 401,
    "message": "未登录",
    "data": null
}
```
