# 搜索商品

### 请求路由

* api/v1/search/goods

### 请求方式

* post

### 请求参数

| 字段          | 是否必填 | 字段类型    | 说明                                                                         |
|:------------|:-----|:--------|:---------------------------------------------------------------------------|
| keywords    | Y    | string  | 搜索关键字                                                                      |
| category_id | N    | integer | 分类ID                                                                       |
| sort_type   | N    | string  | 排序类型(不传默认：sort；价格升序：price_asc；价格降序：price_desc；销量降序：sale_desc；最新：time_desc) |
| page        | Y    | integer | 页码                                                                         |
| number      | Y    | integer | 每页数量                                                                       |

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "list": [
      {
        "no": "293df896-e63b-468c-b657-1b211618bd07",
        "name": "商品C",
        "sub_name": "副标题C",
        "label": "标签C",
        "price": "399.99",
        "unit": "盒",
        "image": "imageC.jpg",
        "sales_volume": 100,
        "cretaed_at": "2020-01-01 00:00:00"
      },
      {
        "no": "8757ac6a-a621-4a0b-8a43-322fc5275ee7",
        "name": "商品B",
        "sub_name": "副标题B",
        "label": "标签B",
        "price": "299.99",
        "unit": "个",
        "image": "imageB.jpg",
        "sales_volume": 10,
        "cretaed_at": "2020-01-01 00:00:00"
      }
    ],
    "meta": {
      "total": 3,
      "per_page": 2,
      "current_page": 1
    }
  }
}
```
