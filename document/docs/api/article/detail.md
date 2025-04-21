# 获取文章详情

### 请求路由

* api/v1/article/detail

### 请求方式

* get

### 请求参数

| 字段         | 字段类型    | 是否必须 | 说明   |
|------------|---------|------|------|
| article_id | integer | Y    | 文章ID |

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "id": 1,
    "title": "文章标题1",
    "cover": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/10\/2025\/04\/10\/E1RqQzW2sieOLIISxAqqiMIi6qeF4nHjMjr6eYgR.png",
    "description": "描述1",
    "keywords": "关键词1",
    "author": "作者1",
    "updated_at": "2023-10-01 10:00:00",
    "content": "<p>这是文章1的内容。<\/p>"
  }
}
```
