# 获取网站配置

* 用于获取网站配置相关接口

### 请求URL

* /api/v1/shop/config

### 请求方式
* GET

### 返回示例

#### 成功示例
```
{
  "code": 200,
  "message": "success",
  "data": {
    "integral_name": "积分",
    "shop_name": "",
    "shop_keywords": "",
    "shop_description": "",
    "is_gray": 0,
    "shop_color": "#E48F34",
    "shop_logo": "",
    "cart_count": "",
  }
}
```

data 返回字段说明

| 字段名              | 字段类型   | 字段说明  |
|:-----------------|:-------|:------|
| integral_name    | string | 积分名称  |
| shop_name        | string | 网站名称  |
| shop_keywords    | string | 网站关键字 |
| shop_description | string | 网站描述  |
| is_gray          | int    | 网站是否置灰 |
| shop_color       | String | 网站主题色 |
| shop_logo        | string | 网站logo |
| cart_count       | string | 购物车数量 |
