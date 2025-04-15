# 我的订单

* 我的订单列表

### 请求URL

* /api/v1/order/my/index

### 请求参数

| 字段       | 字段类型    | 是否必须 | 说明                  |
|----------|---------|------|---------------------|
| keywords | String  | N    | 关键词：订单编号、商品名称、商品货号  |
| type     | String  | N    | 类型，详见 `type_enum`枚举 |
| page     | Integer | Y    | 页码                  |
| number   | Integer | Y    | 每页条数                |

type_enum 枚举说明

| 字段值           | 说明  |
|---------------|-----|
| all           | 全部  |
| not_pay       | 待支付 |
| wait_ship     | 待发货 |
| wait_receive  | 待收货 |
| wait_evaluate | 待评价 |
| success       | 已完成 |


### 请求方式
* GET

### 返回示例

```json
{
    "code": 200,
    "message": "success",
    "data": {
        "list": [
            {
                "no": "2025040918889931",
                "status": 3,
                "items": [
                    {
                        "goods_no": "eda482c3-df45-47a4-abc5-f795db6fefae",
                        "goods_name": "测试商品1",
                        "goods_image": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
                        "goods_price": "￥10.23",
                        "number": 3,
                        "goods_unit": "件",
                        "sku_value": "颜色:红色;号码:L;送装服务:不送装;",
                        "sku_id": 3
                    }
                ],
                "order_amount": "￥30.69",
                "logistics": null,
                "evaluate": null,
                "buttons": [
                    {
                        "text": "取消订单",
                        "action": "cancel"
                    },
                    {
                        "text": "修改地址",
                        "action": "edit_address"
                    },
                    {
                        "text": "去支付",
                        "action": "pay"
                    }
                ]
            }
        ],
        "meta": {
            "total": 15,
            "per_page": 10,
            "current_page": 1
        }
    }
}
```

### 返回参数说明

data.list 返回参数说明

| 字段               | 字段类型    | 字段说明                                  |
|------------------|---------|---------------------------------------|
| no               | String  | 订单编号                                  |
| status           | Integer | 订单状态，详见 `status_enum`枚举               |
| items            | Array   | 订单商品数据，详见 `data.list.*.items`返回参数说明   |
| order_amount     | String  | 订单总金额                                 |
| logistics        | Array   | 物流信息，详见 `data.list.*.logistics`返回参数说明 |
| evaluate         | Array   | 订单评价，详见 `data.list.*.evaluate`返回参数说明  |
| buttons          | Array   | 订单操作按钮                                |
| buttons.*.action | String  | 按钮操作，详见 `button_enum`枚举               |
| buttons.*.text   | String  | 按钮操作文字                                |

data.list.*.items 返回参数说明

| 字段          | 字段类型    | 字段说明   |
|-------------|---------|--------|
| goods_no    | String  | 商品编号   |
| goods_name  | String  | 商品名称   |
| goods_unit  | String  | 商品单位   |
| goods_image | String  | 商品图片   |
| goods_price | String  | 商品价格   |
| number      | Integer | 购买数量   |
| sku_value   | String  | 商品规格   |
| sku_id      | Integer | 商品规格ID |

data.list.*.logistics 返回参数说明

| 字段          | 字段类型    | 字段说明   |
|-------------|---------|--------|
| title       | String  | 快递组件标题 |
| description | String  | 快递组件描述 |
| shipped_at  | String  | 发货时间   |

data.list.*.evaluate 返回参数说明

| 字段            | 字段类型    | 字段说明  |
|---------------|---------|-------|
| default_value | Integer | 默认评价值 |
| description   | String  | 描述    |

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

button_enum 枚举说明

| 枚举值          | 说明   |
|--------------|------|
| cancel       | 取消订单 |
| delete       | 删除订单 |
| again        | 再次购买 |
| edit_address | 修改地址 |
| pay          | 去支付  |
| refund       | 申请售后 |
| logistics    | 查看物流 |
| receive      | 确认收货 |
| evaluate     | 去评价  |



data.meta 返回参数说明

| 字段           | 字段类型    | 字段说明 |
|--------------|---------|------|
| total        | Integer | 总记录数 |
| per_page     | Integer | 每页条数 |
| current_page | Integer | 当前页码 |

