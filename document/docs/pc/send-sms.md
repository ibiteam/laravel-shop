# 发送短信

* 用于忘记密码、登录验证码、注册验证码等等

### 请求URL

* /api/home/sms-action

### 请求方式
* POST

### 请求参数

|字段|是否必填|字段类型|说明|
| :--- | :--- | :--- | :--- |
|phone|Y|integer|手机号 详见 操作类型枚举|
|action|Y|string|操作类型，详见 操作类型枚举|

#### 操作类型枚举
| action枚举值       | 说明           | 手机号是否必填 |
|:----------------|:-------------|:--------|
| login           | 用于发送登录验证码    | Y       |
| register        | 用于发送注册验证码    | Y       |
| password-forget | 用于发送忘记密码验证码 | Y       |
| password-edit   | 用于发送修改密码验证码 | N       |

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
