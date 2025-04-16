# 去评价初始化

* 用户点击去评价按钮，请求初始化去评价

### 请求URL

* /api/v1/order/my/evaluate/init

### 请求参数

| 字段       | 字段类型   | 是否必须 | 说明   |
|----------|--------|------|------|
| order_sn | String | Y    | 订单编号 |

### 请求方式
* GET

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "order_sn": "2025040918889931",
        "items": [
            {
                "id": 22,
                "goods_no": "eda482c3-df45-47a4-abc5-f795db6fefae",
                "goods_name": "测试商品1",
                "sku_value": "颜色:红色;号码:L;送装服务:不送装;",
                "goods_image": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png"
            }
        ]
    }
}
```

### 返回参数说明

data 返回参数说明

| 字段                  | 字段类型    | 字段说明   |
|---------------------|---------|--------|
| order_sn            | String  | 订单编号   |
| items               | Array   | 订单明细   |
| items.*.id          | Integer | 订单明细ID |
| items.*.goods_no    | String  | 订单商品编号 |
| items.*.goods_name  | String  | 订单商品名称 |
| items.*.goods_image | String  | 订单商品图片 |
| items.*.sku_value   | String  | 订单商品规格 |
