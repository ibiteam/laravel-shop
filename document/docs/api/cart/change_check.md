# 变更选中结算
* 支持全选和全不选
 
### 请求路由

* api/v1/cart/change_check

### 请求方式

* post

### 请求参数

| 字段           | 是否必填 | 字段类型    | 说明               |
|:-------------|:-----|:--------|:-----------------|
| goods_id     | Y    | integer | 商品ID（全选、全不选时 传0） |
| goods_sku_id | Y    | integer | 商品规格ID，没有传0      |
| is_check     | Y    | integer | 是否选中 0否 1是       |

### 返回示例

```
{
  "code": 200,
  "message": "操作成功",
  "data": null
}
```
