# 专题页面数据

### 请求URL

* /api/v1/special

### 请求方式

* GET

### 请求参数

| 字段    | 字段类型 | 是否必须   | 说明 |
|-------|------|--------|----|
| alias | N    | string | 别名 |

### 返回示例

```
{
  "code": 200,
  "message": "success",
  "data": {
    "title": "多多超市",
    "banner_list": [
      {
        "id": 1,
        "name": "测试标题1",
        "image": "https://laravel-shop.ptdmeta.cn/storage/2025/04/22/b3MEX8ZwZkLpZ10mLB41FbIFfhy8YDbpF3OXOwzA.png",
        "link": "https://vue-shop.ptdmeta.cn/special?alias=supermarket&cat_id="
      }
    ],
    "goods_list": [
      {
        "no": "174529338233",
        "name": "图书",
        "category_id": 33,
        "sub_name": "",
        "label": "",
        "price": "1.00",
        "unit": "本",
        "integral": 666,
        "image": "https://laravel-shop.ptdmeta.cn/storage/2025/04/22/Y6CIz8mR5tqHif8bGJanbho2EJNWrKGIu1UfJ7ps.jpg",
        "sales_volume": 0,
        "created_at": "2025-04-22 11:43:02"
      },
      {
        "no": "174487311787",
        "name": "零食面包",
        "category_id": 23,
        "sub_name": "零食面包",
        "label": "推荐",
        "price": "19.99",
        "unit": "包",
        "integral": 0,
        "image": "https://laravel-shop.ptdmeta.cn/storage/manage/2025/04/17/0H2TW10UpjruzcYpnwd1zwvGuNQRJcvYZBjiSbOQ.jpg",
        "sales_volume": 3,
        "created_at": "2025-04-17 14:58:37"
      },
      {
        "no": "174487478269",
        "name": "水杯",
        "category_id": 9,
        "sub_name": "水杯",
        "label": "热卖",
        "price": "39.99",
        "unit": "个",
        "integral": 0,
        "image": "https://laravel-shop.ptdmeta.cn/storage/manage/2025/04/17/Bt8DnlItaTxgJz86Lw9m9ZcP2RAjl2blyQo3jXWF.jpg",
        "sales_volume": 1,
        "created_at": "2025-04-17 15:26:22"
      },
      {
        "no": "174487664691",
        "name": "青柠提",
        "category_id": 24,
        "sub_name": "青柠提",
        "label": "特惠",
        "price": "0.00",
        "unit": "斤",
        "integral": 399,
        "image": "https://laravel-shop.ptdmeta.cn/storage/manage/2025/04/17/NmF6gWLSrzVQKoPlTiANstRgDIKr11KMtia44TBM.jpg",
        "sales_volume": 11,
        "created_at": "2025-04-17 15:57:26"
      },
      {
        "no": "174487735745",
        "name": "卤味鸭脖",
        "category_id": 23,
        "sub_name": "鸭脖鸭脖测试谢谢",
        "label": "超级美味",
        "price": "0.01",
        "unit": "个",
        "integral": 2,
        "image": "https://laravel-shop.ptdmeta.cn/storage/manage/2025/04/17/lWzFXaASNHppHAksAqF3kZsluusegSBbZEnyZefm.jpg",
        "sales_volume": 27,
        "created_at": "2025-04-17 16:09:17"
      },
      {
        "no": "174488009322",
        "name": "牙刷",
        "category_id": 10,
        "sub_name": "牙刷",
        "label": "热卖",
        "price": "0.01",
        "unit": "",
        "integral": 199,
        "image": "https://laravel-shop.ptdmeta.cn/storage/manage/2025/04/17/BQlGFDdanHA7tSL1W1W5qPxj72WgL3DwxDXe7mkv.jpg",
        "sales_volume": 3,
        "created_at": "2025-04-17 16:54:53"
      },
      {
        "no": "174488176978",
        "name": "饼干饼干饼干饼干",
        "category_id": 23,
        "sub_name": "饼干饼干副标题测试谢谢测试谢谢9999",
        "label": "特价热卖",
        "price": "0.01",
        "unit": "块",
        "integral": 1,
        "image": "https://laravel-shop.ptdmeta.cn/storage/manage/2025/04/17/UHRrWmEeh7LIGS3USR2zXxd1k6h45VlEkST02rKk.jpg",
        "sales_volume": 6,
        "created_at": "2025-04-17 17:22:49"
      },
      {
        "no": "174531015685",
        "name": "方便面",
        "category_id": 37,
        "sub_name": "方便面方便面副标题",
        "label": "",
        "price": "0.02",
        "unit": "包",
        "integral": 1,
        "image": "https://laravel-shop.ptdmeta.cn/storage/2025/04/22/TXKcVobgNl2iHCW61sGuXgy9xAUviL4sm4r6q0Kn.jpg",
        "sales_volume": 0,
        "created_at": "2025-04-22 16:22:36"
      },
      {
        "no": "174531168293",
        "name": "包子包子包子",
        "category_id": 39,
        "sub_name": "包子包子副标题测试谢谢",
        "label": "热卖热卖热卖",
        "price": "0.02",
        "unit": "个",
        "integral": 1,
        "image": "https://laravel-shop.ptdmeta.cn/storage/2025/04/22/v0w3rC8pwgkeF2xteqpPKgJgOXSeWRiNozeujE7Q.jpg",
        "sales_volume": 0,
        "created_at": "2025-04-22 16:48:02"
      },
      {
        "no": "174531099439",
        "name": "馒头馒头馒头",
        "category_id": 38,
        "sub_name": "馒头馒头测试谢谢副标题",
        "label": "",
        "price": "0.02",
        "unit": "个",
        "integral": 1,
        "image": "https://laravel-shop.ptdmeta.cn/storage/2025/04/22/fH9Bu7cLkx3ucncYJc5HBxK97UuP4oesqdIE52Mr.jpg",
        "sales_volume": 0,
        "created_at": "2025-04-22 16:36:34"
      }
    ]
  }
}
```
