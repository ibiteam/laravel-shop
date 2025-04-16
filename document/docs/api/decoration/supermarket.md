# 超市页面数据

### 请求URL

* /api/v1/supermarket

### 请求方式

* GET

### 请求参数

| 字段          | 字段类型 | 是否必须    | 说明       |
|-------------|------|---------|----------|
| title       | N    | string  | 标题       |
| banner      | N    | string  | banner地址 |
| category_id | N    | integer | 分类ID     |
| page        | Y    | integer | 页码       |
| number      | Y    | integer | 每页数量     |

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "title": "title",
    "banner": "banner",
    "goods_list": {
      "list": [
        {
          "no": "293df896-e63b-468c-b657-1b211618bd07",
          "category_id": 16,
          "name": "xxxxx",
          "sub_name": "vvvv",
          "label": "xx",
          "price": "13.00",
          "unit": null,
          "integral": 0,
          "image": "http:\/\/shop.host\/storage\/manage\/2025\/03\/31\/UHJDM1PL8fD6croyjR10Zt6xC2qzsN5zZPRhNI4P.jpg",
          "sales_volume": 6,
          "created_at": "2025-03-31T11:47:15.000000Z"
        },
        {
          "no": "8757ac6a-a621-4a0b-8a43-322fc5275ee7",
          "category_id": 16,
          "name": "vvvv士大夫士大夫vvvv士大夫士大夫",
          "sub_name": "dddd",
          "label": "eee",
          "price": "10.00",
          "unit": "吨",
          "integral": 0,
          "image": "https:\/\/testcdn.ibisaas.com\/2025\/03\/27\/1743041332185.jpeg",
          "sales_volume": 35,
          "created_at": "2025-03-31T11:48:40.000000Z"
        }
      ],
      "meta": {
        "total": 2,
        "per_page": 10,
        "current_page": 1
      }
    }
  }
}
```
