# 注册-检测是否注册(手机号)

* 用于检测输入的手机号是否已经注册过

### 请求URL

* /api/v1/auth/check-phone

### 请求方式
* GET

### 返回示例

```
{
    "code": 200,
    "message": "success",
    "data": {
        "is_register": true
    }
}
```
