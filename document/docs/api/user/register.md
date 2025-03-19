# 用户注册

- 用户注册请求的接口

### 请求URL

- /api/v1/register

### 请求参数

|参数名|是否必填|类型|说明|
|:----    |:---|:----- |-----   |
|user_name |是  |String |用户名   |
|phone |是  |String |手机号|



### 响应参数

|参数名|类型|说明|
|:----  |:----- |-----   |
|token  |String | 登录token   |


### 返回示例
**正确时返回:**

```json
{
	"status": 1,
	"error": "",
	"data": {
	   "token":"123453453453"
	}
}
```

**错误时返回:**


```json
{
	"status": 0,
	"error": "注册失败",
	"data": null
}
```
