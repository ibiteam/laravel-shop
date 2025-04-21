# 选择SKU

* 获取用户选择的SKU的最新信息

### 请求URL

* /api/v1/goods/:no/:unique

### 路由参数：

| 字段     | 是否必填 |字段类型| 说明       |
|--------|------|-----|----------|
| no     | String     | Y    | 商品编号     |
| unique | String     | Y    | 商品规格唯一标识 |



### 请求方式
* GET

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "id": 3,
        "unique": "13_14_15",
        "price": "10.06",
        "integral": "0.00",
        "number": 288,
        "has_number": true
    }
}
```
data 参数说明

| 字段         | 字段类型    | 说明       |
|------------|---------|----------|
| id         | Integer | 商品规格ID   |
| unique     | String  | 商品规格唯一标识 |
| price      | String  | 商品规格价格   |
| integral   | String  | 商品规格积分   |
| number     | Integer | 商品规格库存   |
| has_number | Boolean | 是否还有库存   |
