# 订单详情

* 我的订单详情

### 请求URL

* /api/v1/order/my/detail

### 请求参数

| 字段     | 字段类型   | 是否必须 | 说明   |
|--------|--------|------|------|
| no     | String | Y    | 订单编号 |


### 请求方式
* GET

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "status": 3,
        "order": {
            "order_sn": "2025040918889931",
            "paid_at": null,
            "remark": "",
            "status": 3
        },
        "logistics": {
            "title": "运输中",
            "description": "当前订单已拆分成0个运单运输，点击可以查看物流轨迹",
            "shipped_at": null
        },
        "address": {
            "consignee": "jeck",
            "address": "北京北京丰台区北京市丰台区国联股份",
            "phone": "133****1111"
        },
        "items": [
            {
                "id": 1,
                "goods_no": "eda482c3-df45-47a4-abc5-f795db6fefae",
                "goods_name": "测试商品1",
                "goods_image": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
                "goods_price": "￥10.23",
                "goods_integral": "￥10",
                "number": 3,
                "sku_value": "颜色:红色;号码:L;送装服务:不送装;",
                "sku_id": 3,
                "goods_unit": "件",
                "show_refund": 0
            }
        ],
        "amounts": {
            "goods_amount": "￥30.69",
            "money_paid": "￥0.00",
            "shipping_fee": "￥0.00",
            "coupon_amount": "￥0.00",
            "integral": "￥0.00",
            "order_amount": "￥30.69"
        }
    }
}
```

### 返回参数说明

data 返回参数说明

| 字段                    | 字段类型    | 字段说明                     |
|-----------------------|---------|--------------------------|
| status                | Integer | 订单状态，详见 `status_enum`枚举  |
| order                 | Object  | 订单信息                     |
| order.order_sn        | String  | 订单编号                     |
| order.paid_at         | String  | 支付时间                     |
| order.remark          | String  | 买家留言                     |
| order.status          | Integer | 订单状态，，详见 `status_enum`枚举 |
| logistics             | Object  | 物流信息                     |
| logistics.title       | String  | 物流信息标题                   |
| logistics.description | String  | 物流信息描述                   |
| logistics.shipped_at  | String  | 最新物流时间                   |
| address               | Object  | 收货人信息                    |
| address.consignee     | String  | 收货人姓名                    |
| address.address       | String  | 收货人地址                    |
| address.phone         | String  | 收货人电话                    |
| items                 | Array   | 订单商品信息                   |
| amounts               | Object  | 订单金额                     |
| amounts.goods_amount  | String  | 订单商品总金额                  |
| amounts.money_paid    | String  | 已支付金额                    |
| amounts.shipping_fee  | String  | 运费                       |
| amounts.coupon_amount | String  | 优惠（优惠券）金额                |
| amounts.integral      | String  | 订单总积分                    |
| amounts.order_amount  | String  | 订单应付总金额                  |


data.list.*.items 返回参数说明

| 字段             | 字段类型    | 字段说明                        |
|----------------|---------|-----------------------------|
| id             | Integer | 订单明细ID                      |
| goods_no       | String  | 商品编号                        |
| goods_name     | String  | 商品名称                        |
| goods_image    | String  | 商品图片                        |
| goods_price    | String  | 商品价格                        |
| goods_integral | String  | 商品积分                        |
| goods_unit     | String  | 商品单位                        |
| number         | Integer | 购买数量                        |
| sku_value      | String  | 商品规格                        |
| sku_id         | Integer | 商品规格ID                      |
| refund_action  | Integer | 订单商品操作，详见 `refund_action`枚举 |


status_enum 枚举说明

| 枚举值 | 说明   |
|-----|------|
| 1   | 待确认  |
| 2   | 已取消  |
| 3   | 待付款  |
| 4   | 待发货  |
| 5   | 待收货  |
| 6   | 已完成  |
| 7   | 部分发货 |

refund_action 枚举说明

| 枚举值 | 说明            |
|-----|---------------|
| 0   | 不展示申请售后相关按钮操作 |
| 1   | 展示`申请售后`按钮    |
| 2   | 展示`退款中`按钮     |
| 3   | 展示`退款成功`按钮    |


