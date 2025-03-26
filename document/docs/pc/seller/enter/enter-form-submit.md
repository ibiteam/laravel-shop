# 提交入驻信息

### 请求路由
* api/seller/seller_enter/store

### 请求方式
* post

### 请求参数
|字段|是否必填|字段类型|说明|
| :--- | :--- | :--- | :--- |
|id|Y|integer|商家id，新增时为空；编辑时候该值大于0|
|enter_info|Y|Array|入驻提交信息|

#### 请求参数举例
```
{"id":"","enter_info":[{"id":"1","name":"今天周几","type":"radio","value":"周二"},{"id":"2","name":"今天中午吃啥?","type":"file","value":"牛肉"}]}
```

### 返回示例
```
{
  "code": 200,
  "message": "success",
  "data":null
}
```
