# 直接下单-初始化

* 直接下单回显数据接口

### 请求URL

* /api/v1/order/direct/init

### 请求参数：

| 字段              | 是否必填    | 字段类型 | 说明       |
|-----------------|---------|------|----------|
| no              | String  | Y    | 订单编号     |
| sku_id          | Integer | N    | 商品规格ID   |
| buy_number      | Integer | Y    | 购买商品数量   |
| user_address_id | Integer | N    | 用户收货地址ID |



### 请求方式
* GET

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "user_address": {
            "id": 1,
            "recipient_name": "jeck",
            "recipient_phone": "13322221111",
            "province": "北京",
            "city": "北京",
            "district": "丰台区",
            "address_detail": "北京市丰台区国联股份",
            "is_default": 0
        },
        "goods": [
            {
                "no": "eda482c3-df45-47a4-abc5-f795db6fefae",
                "name": "测试商品1",
                "sub_name": "测试商品1-副标题",
                "label": "热卖",
                "thumb": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
                "price": "10.23",
                "integral": 0,
                "integral_name": "积分",
                "unit": "个",
                "number": 246,
                "buy_number": 3,
                "sku_id": 3,
                "sku_data": [
                    {
                        "key": "颜色",
                        "value": "红色"
                    },
                    {
                        "key": "号码",
                        "value": "L"
                    },
                    {
                        "key": "送装服务",
                        "value": "不送装"
                    }
                ]
            }
        ],
        "total": {
            "goods_amount": 30.69,
            "goods_integral": 0,
            "shipping_fee": 0,
            "coupon": 0,
            "total": 30.69
        },
        "payment_methods": [
            {
                "name": "在线支付",
                "alias": "online"
            }
        ]
    }
}
```

data.user_address 参数说明

| 字段              | 字段类型    | 说明           |
|-----------------|---------|--------------|
| id              | Integer | 用户收货地址ID     |
| recipient_name  | String  | 用户收货人        |
| recipient_phone | String  | 用户收货人手机号     |
| province        | String  | 用户收货省份       |
| city            | String  | 用户收货城市       |
| district        | String  | 用户收货区县       |
| address_detail  | String  | 用户收货详细地址     |
| is_default      | Integer | 是否默认地址 1是 0否 |

data.goods 参数说明

| 字段               | 字段类型    | 说明     |
|------------------|---------|--------|
| no               | String  | 商品编号   |
| name             | String  | 商品名称   |
| sub_name         | String  | 商品子标题  |
| label            | String  | 商品标签   |
| thumb            | String  | 商品图片   |
| price            | Float   | 商品价格   |
| integral         | Integer | 商品所需积分 |
| integral_name    | String  | 商品积分叫法 |
| unit             | String  | 商品单位   |
| number           | Integer | 商品库存   |
| buy_number       | String  | 购买数量   |
| sku_id           | Integer | 商品规格ID |
| sku_data         | Array   | 商品规格   |
| sku_data.*.key   | String  | 商品规格名  |
| sku_data.*.value | String  | 商品规格值  |


data.total 参数说明

| 字段             | 字段类型    | 说明     |
|----------------|---------|--------|
| goods_amount   | Float   | 商品总价   |
| goods_integral | Integer | 商品所需积分 |
| shipping_fee   | Float   | 运费     |
| coupon         | Float   | 优惠券金额  |
| total_amount   | Float   | 总金额    |
| total_integral | Float   | 总积分    |

data.payment_methods 参数说明

| 字段           | 字段类型    | 说明         |
|--------------|---------|------------|
| name         | String  | 支付方式名称     |
| alias        | String  | 支付方式别名     |
