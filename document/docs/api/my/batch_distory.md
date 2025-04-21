# 批量删除记录

* 个人中心我的商品浏览记录，批量删除

### 请求URL

* /api/v1/my/views/batch/distory

### 请求方式
* POST

### 请求参数

| 字段  | 字段类型  | 是否必须 | 说明    |
|-----|-------|------|-------|
| ids | array | Y    | 记录ids |


### 返回示例

```json
{
    "code": 200,
    "message": "删除成功",
    "data": null
}
```
