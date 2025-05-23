# 获取购物车商品列表

### 请求路由

* api/v1/cart/list

### 请求方式

* get

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "valid_carts": [// 有效商品
      {
        "id": 3, // 购物车id
        "buy_number": 3, // 购买数量
        "is_check": 1,   // 是否选中 0-未选中 1-选中
        "goods_sku_id": 0, // 商品sku id
        "goods": {// 商品信息
          "no": "cbda6ddf-0c08-4c82-a777-c73121b9698d", // 商品编号
          "name": "商品B",
          "image": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
          "price": "299.99",
          "unit": "个",
          "total": 100,  // 库存
          "can_quota": 1, // 是否限购 0-不限购 1-限购
          "quota_number": 10, // 限购数量
          "sku_desc": "款式:雅丹黑;内存:12+256;"   // 商品sku信息, 没有“”
        }
      },
      {
        "id": 1,
        "buy_number": 2,
        "is_check": 0,
        "goods_sku_id": 1,
        "goods": {
          "no": "bbbfrcsf-0c08-4c82-a777-c73121b9698d", // 商品编号
          "name": "测试商品1",
          "image": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
          "price": "10.06",
          "unit": "个",
          "sku_desc": ""
        }
      }
    ],
    "invalid_carts": [// 无效商品
      {
        "id": 2,
        "buy_number": 1,
        "goods": {
          "no": "werdcfrh-0c08-4c82-a777-c73121b9698d", // 商品编号
          "name": "商品A",
          "image": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
          "price": "199.99",
          "unit": "件",
          "invalid_type": "status_not_sale"
        }
      }
    ],
    "total": {// 选中结算数据
      "check_count": 1,  // 结算数量
      "total_price_format": "￥299.99",  // 有格式 ￥0.00
      "total_price": "299.99", // 总金额 无格式 0.00
      "total_integral":0  // 总积分
    }
  }
}
```
