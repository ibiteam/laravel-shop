# 发送短信

* 用于忘记密码、登录验证码、注册验证码等等

### 请求URL

* /api/v1/sms-action

### 请求方式
* POST

### Headers 参数

| 字段           | 是否必填 | 字段类型   | 说明                |
|:-------------|:-----|:-------|:------------------|
| phone-verify | Y    | string | 详细见 `操作类型枚举` 加密格式 |

### 请求参数

| 字段        | 是否必填 | 字段类型    | 说明               |
|:----------|:-----|:--------|:-----------------|
| phone     | Y    | integer | 手机号 详见 `操作类型枚举`  |
| action    | Y    | string  | 操作类型，详见 `操作类型枚举` |
| timestamp | Y    | string  | 当前时间戳            |

#### 操作类型枚举
| action枚举值       | 说明                   | 手机号是否必填 | 加密格式                        |
|:----------------|:---------------------|:--------|:----------------------------|
| login           | 用于发送登录验证码            | Y       | md5(phone+action+timestamp) |
| password-forget | 用于发送忘记密码验证码          | Y       | md5(phone+action+timestamp) |
| password-edit   | 用于发送修改密码验证码          | N       | md5(action+timestamp)       |
| phone-edit      | 用于发送修改手机号验证码         | Y       | md5(phone+action+timestamp) |
| phone-verify    | 用于发送修改手机号之前的验证手机号验证码 | N       | md5(action+timestamp)       |

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
