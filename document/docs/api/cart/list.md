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
        "id": 3,
        "buy_number": 3, // 购买数量
        "is_check": 1,   // 是否选中 0-未选中 1-选中
        "goods_sku_id": 0, // 商品sku id
        "goods": {// 商品信息
          "id": 3,
          "name": "商品B",
          "image": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
          "price": "299.99",
          "unit": "个",
          "skus": [ ]  // 商品sku信息
        }
      },
      {
        "id": 1,
        "buy_number": 2,
        "is_check": 0,
        "goods_sku_id": 1,
        "goods": {
          "id": 1,
          "name": "测试商品1",
          "image": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
          "price": "10.06",
          "unit": "个",
          "skus": [
            {
              "id": 1,
              "goods_id": 1,
              "sku_value": "1|2|3",
              "price": "10.06",
              "number": 288,
              "is_show": 1,
              "sort": 1,
              "created_at": "2025-03-28 17:30:36",
              "updated_at": "2025-03-28 17:30:36"
            },
            {
              "id": 2,
              "goods_id": 1,
              "sku_value": "1|2|4",
              "price": "12.01",
              "number": 299,
              "is_show": 1,
              "sort": 1,
              "created_at": "2025-03-28 17:30:36",
              "updated_at": "2025-03-28 17:30:36"
            }
          ]
        }
      }
    ],
    "invalid_carts": [// 无效商品
      {
        "id": 2,
        "buy_number": 1,
        "goods": {
          "id": 2,
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
      "total_price": "299.99" // 无格式 0.00
    }
  }
}
```
