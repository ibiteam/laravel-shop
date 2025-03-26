# 入驻表单信息

### 请求路由
* api/seller/seller_enter/configs

### 请求方式
* get

### 请求参数
|字段|是否必填|字段类型|说明|
| :--- | :--- | :--- | :--- |
|id|Y|integer|商家id，新增时为0；编辑时候该值大于0|

### 返回示例
```
{
  "code": 200,
  "message": "success",
  "data": {
    "id": 0,    // 商家id，新增时为0；编辑时候该值大于0
    "enter_configs": [  // 入驻表单信息
      {
        "id": 2,
        "name": "今天中午吃啥?",
        "type": "file",
        "is_need": 1,
        "limit_type": null,
        "limit_number": 1,
        "is_show": 1,
        "select_options": null,
        "template_name": "吃饭模板",
        "template_url": "https://testcdn.ibisaas.com/2024/12/18/RXUtBoJRp2eK7ZMYFzdV51pLCU2YknYm3NuKyfmA.xlsx",
        "tips": "1、炒粉。2、米粉。3、拌粉",
        "style_type": null,
        "style": null,
        "sort": 2,
        "icon": "&#xe834;",
        "value": ""          // 新增时为空；编辑时对应回显的值
      },
      {
        "id": 1,
        "name": "今天周ji",
        "type": "radio",
        "is_need": 1,
        "limit_type": null,
        "limit_number": 1,
        "is_show": 1,
        "select_options": [
          {
            "name": "周一"
          },
          {
            "name": "周二"
          },
          {
            "name": "周三"
          },
          {
            "name": "周四"
          },
          {
            "name": "周五"
          }
        ],
        "template_name": null,
        "template_url": null,
        "tips": null,
        "style_type": null,
        "style": null,
        "sort": 1,
        "icon": "&#xe82d;",
        "value": ""
      }
    ]
  }
}
```
