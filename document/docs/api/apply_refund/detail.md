# 售后详情

* 根据 申请售后id 或 订单id,订单明细id 获取申请售后信息

### 请求URL

* /api/v1/order/apply_refund/detail

### 请求方式

* GET

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明                                         |
|-----------------|---------|------|--------------------------------------------|
| apply_refund_id | integer | N    | 申请售后ID，order_no和order_detail_id 不存在时 该参数必有 |
| order_sn        | string  | N    | 订单编号，apply_refund_id不存在时 该参数必有             |
| order_detail_id | integer | N    | 订单明细ID，apply_refund_id不存在时 该参数必有           |

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "refund_info": {// 申请售后信息
      "id": 1,
      "no": "20250414042609644",
      "status": 2,
      "money_format": "￥5.00",
      "money": "5",
      "number": "2",
      "reason_id": 1,
      "reason_content": '七天无理由',
      "is_revoke": 0,
      "description": "测试",
      "certificate": [
        "https:\/\/www.baidu.com",
        "https:\/\/learnku.com"
      ],
      "apply_refund_shipping_id": 1,
      "apply_refund_shipping_no": "202504153468237648",
      "updated_at": "2025-04-14 16:47:10",
      "result": null,
      "job_time": 1744706830,
      "system_time": 1744621252
    },
    "type": 1,
    "address": {// 物流信息
      "apply_refund_shipping": {
        "id": 1,
        "apply_refund_id": 1,
        "no": "202504153468237648",
        "ship_company_id": 1,
        "phone": "15110000000",
        "description": null,
        "certificate": null,
        "created_at": "2025-04-14 16:59:31",
        "updated_at": "2025-04-14 16:59:33"
      }
    },
    "order_detail": {// 订单明细信息
      "goods_no": "cbda6ddf-0c08-4c82-a777-c73121b9698d",
      "goods_name": "华为老年机",
      "goods_image": "https:\/\/laravel-shop.ptdmeta.cn\/storage\/manage\/2025\/04\/01\/cr7J8mCH3mcDo1unCAixpLpngG8hi7vfhIDTFhmh.jpg",
      "goods_price": "￥6999.00",
      "goods_integral": 0,
      "goods_number": "2",
      "goods_unit": "",
      "goods_sku_id": 4,
      "goods_sku_value": [
        {
          "key": "款式",
          "value": "雅丹黑"
        },
        {
          "key": "内存",
          "value": "12+256"
        }
      ],
      "goods_amount": "13998",
      "goods_amount_format": "￥13998.00",
      "order_sn": "2025041096246684",
      "shipping_fee": "0.00",
      "pay_time": "2025-04-11T05:50:00.000000Z"
    },
    "from_init": {// 退款原因相关信息
      "reason": [
        {
          "id": 10,
          "content": "其他原因"
        },
        {
          "id": 8,
          "content": "颜色与描述不符"
        },
        {
          "id": 6,
          "content": "商品与图片不符"
        },
        {
          "id": 4,
          "content": "快递损坏"
        },
        {
          "id": 2,
          "content": "商品描述不符"
        },
        {
          "id": 1,
          "content": "商品质量问题"
        }
      ],
      "refund_max_amount": "13998",
      "refund_max_number": "2"
    }
  }
}
```

