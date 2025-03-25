# 修改密码-手机号

* 用于用户找回密码

### 请求URL

* /api/home/auth/edit-password

### 请求方式
* POST

### 请求参数

|字段|是否必填|字段类型|说明|
| :--- | :--- | :--- | :--- |
|code|Y|string|验证码|
|new_password|Y|string|新密码|
|new_password_confirmation|Y|string|新密码确认密码|

### 返回示例

```
{
    "code": 200,
    "message": "修改密码成功",
    "data": null
}
```
