# 删除购物车
 * 支持单个和多个

### 请求路由

* api/v1/cart/destroy

### 请求方式

* post

### 请求参数

| 字段  | 是否必填 | 字段类型  | 说明 |
|:----|:-----|:------|:----------|
| ids | Y    | Array | 购物车ID，传数组 |

### 返回示例

```
{
  "code": 200,
  "message": "添加成功",
  "data": null
}
```
