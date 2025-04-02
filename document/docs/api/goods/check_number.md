# 检测商品库存

* 实时检测商品库存

### 请求URL

* /api/v1/goods/:no/check_number

### 请求参数：

| 字段     | 是否必填    | 字段类型 | 说明     |
|--------|---------|------|--------|
| sku_id | Integer | N    | 商品规格ID |
| number | Integer | Y    | 购买商品数量 |



### 请求方式
* GET

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "total": 299,
        "can_buy": true
    }
}
```
```json
{
    "code": 200,
    "message": "库存数量只有288个，您最多只能购买288个",
    "data": {
        "total": 288,
        "can_buy": false
    }
}
```

data 参数说明

| 字段      | 字段类型    | 说明     |
|---------|---------|--------|
| total   | Integer | 商品库存   |
| can_buy | Boolean | 是否可以购买 |
