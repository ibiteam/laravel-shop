# 添加购物车

### 请求路由

* api/v1/cart/store

### 请求方式

* post

### 请求参数

| 字段           | 是否必填 | 字段类型    | 说明          |
|:-------------|:-----|:--------|:------------|
| goods_no     | Y    | string  | 商品编号        |
| goods_sku_id | Y    | integer | 商品规格ID，没有传0 |
| buy_number   | Y    | integer | 购买数量        |

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "number":1    // 购物车数量
  }
}
```
