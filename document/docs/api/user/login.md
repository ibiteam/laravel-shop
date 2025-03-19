# 用户登陆

- 用户登录请求的接口

### 请求URL

- /api/v1/login

### 请求参数

|参数名|是否必填|类型|说明|
|:----    |:---|:----- |-----   |
|user_name |是  |String |用户名   |
|password |是  |String |密码|
|access_token |是  |String | 密钥   |



### 响应参数

|参数名|类型|说明|
|:----  |:----- |-----   |
|token  |String | 登录token   |


### 返回示例
**正确时返回:**

```
{
	"status": 1,
	"error": ''
	"data": {
	   "token":"123453453453"
	},
}
```

**错误时返回:**


```
{
	"status": 0,
	"error": '用户名或密码错误'
	"data": null,
}
```
