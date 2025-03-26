# 创建店铺信息

### 请求路由
* api/seller/seller_enter/shop/create

### 请求方式
* post

### 请求参数
|字段|是否必填|字段类型|说明|
| :--- | :--- | :--- | :--- |
|seller_id|Y|integer|商家id|
|name|Y|string|商家名称|
|logo|Y|string|商家logo|
|title|Y|string|商家标题|
|keyword|Y|string|商家关键词|
|description|Y|string|商家描述|
|country|Y|integer|国家|
|province|Y|integer|省|
|city|Y|integer|市|
|address|Y|string|商家地址|
|ship_address|Y|string|发货地址|
|main_cate|Y|string|主营类目|
|kf_phone|Y|string|客服电话|


### 返回示例
```
{
  "code": 200,
  "message": "success",
  "data":null
}
```
