# 搜索商品

### 请求路由

* api/v1/search/goods

### 请求方式

* post

### 请求参数

| 字段          | 是否必填 | 字段类型    | 说明                                             |
|:------------|:-----|:--------|:-----------------------------------------------|
| keywords    | Y    | string  | 搜索关键字                                          |
| category_id | N    | integer | 分类ID                                           |
| min_price   | N    | integer | 最小价格                                           |
| max_price   | N    | integer | 最大价格                                           |
| sort_type   | N    | string  | 排序类型(不传默认：sort；价格升序：price_asc；价格降序：price_desc) |
| page        | Y    | integer | 页码                                             |
| number      | Y    | integer | 每页数量                                           |

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "list": [
      {
        "id": 3,
        "category_id": 3,
        "no": "G003",
        "name": "商品C",
        "sub_name": "副标题C",
        "label": "标签C",
        "image": "imageC.jpg",
        "unit": "盒",
        "price": "399.99",
        "total": 200,
        "sales_volume": 100,
        "type": 1,
        "status": 1,
        "status_datetime": "2025-03-28 14:30:04",
        "can_quota": 0,
        "quota_number": 0,
        "sort": 30,
        "video": "videoC.mp4",
        "video_duration": 240,
        "created_at": "2025-03-28 14:30:04",
        "updated_at": "2025-03-28 14:30:04",
        "deleted_at": null
      },
      {
        "id": 2,
        "category_id": 2,
        "no": "G002",
        "name": "商品B",
        "sub_name": "副标题B",
        "label": "标签B",
        "image": "imageB.jpg",
        "unit": "个",
        "price": "299.99",
        "total": 150,
        "sales_volume": 75,
        "type": 2,
        "status": 1,
        "status_datetime": "2025-03-28 14:30:04",
        "can_quota": 1,
        "quota_number": 5,
        "sort": 20,
        "video": "videoB.mp4",
        "video_duration": 180,
        "created_at": "2025-03-28 14:30:04",
        "updated_at": "2025-03-28 14:30:04",
        "deleted_at": null
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
