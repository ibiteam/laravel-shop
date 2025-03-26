# 检测入驻状态

### 请求路由
* api/pc/seller_enter/check

### 请求方式
* get

### 返回示例
```
{
  "code": 200,
  "message": "success",
  "data": {
    "seller_enter": {  // 入驻信息
      "id": 1,
      "check_status": 2, 					   // 审核状态：0-未审核，1-审核通过，2-审核未通过
      "check_desc": "营业执照不行",   // 审核失败原因
      "seller_shop": null
    },
    "enter_status": 3  // 入驻状态：1-入驻信息未提交，2-入驻信息审核中，3-入驻信息审核未通过，4-入驻信息审核通过，没有配置店铺，5-入驻信息审核通过，配置完店铺
  }
}
```
