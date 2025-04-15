# 协商历史

### 请求URL

* /api/v1/order/apply_refund/log

### 请求方式

* GET

### 请求参数

| 字段              | 字段类型    | 是否必须 | 说明     |
|-----------------|---------|------|--------|
| apply_refund_id | integer | Y    | 申请售后ID |

### 返回示例

```json
{
  "code": 200,
  "message": "success",
  "data": [
    {
      "img": "",
      "name": "KfFuTOihHPSmyIE_163632",
      "created_at": "2025-04-14 17:04:59",
      "action": "因买家撤销退款申请，退款已关闭",
      "type": 0,
      "reason": "商品质量问题",
      "refund_money": "5.00",
      "refund_number": "2",
      "unit": "",
      "certificate": [
        "https:\/\/www.baidu.com",
        "https:\/\/learnku.com"
      ],
      "description": "测试",
      "refund_type": 1,
      "apply_refund_shipping": null
    },
    {
      "img": "",
      "name": "KfFuTOihHPSmyIE_163632",
      "created_at": "2025-04-14 16:47:10",
      "action": "修改了申请售后",
      "type": 0,
      "reason": "商品质量问题",
      "refund_money": "5.00",
      "refund_number": "2",
      "unit": "",
      "certificate": [
        "https:\/\/www.baidu.com",
        "https:\/\/learnku.com"
      ],
      "description": "测试",
      "refund_type": 1,
      "apply_refund_shipping": null
    },
    {
      "img": "",
      "name": "KfFuTOihHPSmyIE_163632",
      "created_at": "2025-04-14 16:43:52",
      "action": "发起了申请售后",
      "type": 0,
      "reason": "商品质量问题",
      "refund_money": "5.00",
      "refund_number": "2",
      "unit": "",
      "certificate": [
        "https:\/\/www.baidu.com",
        "https:\/\/learnku.com"
      ],
      "description": "测试",
      "refund_type": 1,
      "apply_refund_shipping": null
    }
  ]
}
```

