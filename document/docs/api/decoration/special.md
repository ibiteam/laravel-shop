# 专题页面数据

### 请求URL

* /api/v1/special

### 请求方式

* GET

### 请求参数

| 字段 | 字段类型 | 是否必须    | 说明   |
|----|------|---------|------|
| id | Y    | integer | 专题ID |

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "title": "标题",
    "banner": "banner图",
    "goods_list": [
      {
        "no": "293df896-e63b-468c-b657-1b211618bd07",
        "name": "xxxxx",
        "category_id": 16,
        "sub_name": "vvvv",
        "label": "xx",
        "price": "60.00",
        "unit": "",
        "integral": 0,
        "image": "https:\/\/testcdn.ibisaas.com\/2025\/03\/27\/1743041332185.jpeg",
        "sales_volume": 6,
        "created_at": "2025-03-31 19:47:15"
      },
      {
        "no": "8757ac6a-a621-4a0b-8a43-322fc5275ee7",
        "name": "vvvv士大夫士大夫vvvv士大夫士大夫",
        "category_id": 16,
        "sub_name": "dddd",
        "label": "eee",
        "price": "10.00",
        "unit": "吨",
        "integral": 0,
        "image": "https:\/\/testcdn.ibisaas.com\/2025\/03\/27\/1743041332185.jpeg",
        "sales_volume": 35,
        "created_at": "2025-03-31 19:48:40"
      }
    ]
  }
}
```
