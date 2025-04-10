# 去评价

* 用户点击去评价按钮，填写评价内容，点击提交请求接口

### 请求URL

* /api/v1/order/my/evaluate/store

### 请求参数

| 字段                         | 字段类型    | 是否必须 | 说明              |
|----------------------------|---------|------|-----------------|
| no                         | String  | Y    | 订单编号            |
| items                      | Array   | Y    | 评价明细数据          |
| items.\*.id                | Integer | Y    | 评价明细ID(订单明细ID)  |
| items.\*.comment           | String  | Y    | 评价内容            |
| items.\*.comment.images    | Array   | N    | 评价明细图片数组        |
| items.\*.comment.images.\* | String  | Y    | 评价明细图片URL       |
| is_anonymous               | Integer | Y    | 是否匿名评价(0:否,1:是) |
| rank                       | Integer | Y    | 综合评分            |
| goods_rank                 | Integer | Y    | 商品评分            |
| price_rank                 | Integer | Y    | 价格评分            |
| bus_rank                   | Integer | Y    | 商家服务评分          |
| delivery_rank              | Integer | Y    | 交货速度评分          |
| service_rank               | Integer | Y    | 服务评分            |

### 请求方式
* POST

### 返回示例

```json
{
    "code": 200,
    "message": "发表评价成功",
    "data": null
}
```
