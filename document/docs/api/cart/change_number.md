# 变更数量

### 请求路由

* api/v1/cart/change_number

### 请求方式

* post

### 请求参数

| 字段           | 是否必填 | 字段类型    | 说明          |
|:-------------|:-----|:--------|:------------|
| id           | Y    | integer | 购物车ID       |
| goods_no     | Y    | string  | 商品编号        |
| goods_sku_id | Y    | integer | 商品规格ID，没有传0 |
| buy_number   | Y    | integer | 购买数量        |

### 返回示例

```
{
  "code": 200,
  "message": "变更商品数量成功",
  "data": null
}
```
