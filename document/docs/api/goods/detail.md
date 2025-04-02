# 商品详情

* 获取用户商品详情信息

### 请求URL

* /api/v1/goods/:no

### 路由参数：

|字段| 是否必填 |字段类型|说明|
|-----|------|-----|----|
|no| String     | Y    |商品编号|



### 请求方式
* GET

### 返回示例

```json
{
  "code": 200,
  "message": "success",
  "data": {
    "banner": {
      "images": [
        "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
        "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png",
        "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.png"
      ],
      "video": {
        "url": "https://testcdn.ibisaas.com/2025/01/23/NEF7tKfku7VJd9LQzcJExEdLp3PWpdzHP6yuBF7Q.mp4",
        "duration": 20
      }
    },
    "center": {
      "no": "eda482c3-df45-47a4-abc5-f795db6fefae",
      "name": "测试商品1",
      "sub_name": "测试商品1-副标题",
      "label": "热卖",
      "price": "10.23",
      "unit": "个",
      "evaluate": [ ],
      "parameters": [
        {
          "name": "产地",
          "value": "中国"
        },
        {
          "name": "许可证号",
          "value": "X0000000001"
        }
      ],
      "content": "<p> xxxxxxxxxxxxxxxxxxxxxx </p>",
      "recommend": [ ],
      "sku_params": {
        "sku_item": {
            "id": 3,
            "unique": "13_14_15",
            "price": "10.06",
            "integral": "0.00",
            "number": 288,
            "has_number": true
        },
        "spec_values": [
          {
            "id": 1,
            "name": "颜色",
            "values": [
              {
                "id": 13,
                "name": "红色",
                "thumb": "https://xxxx.xxx.xxx/2025/03/26/xxxxxxxx.png",
                "selected": true
              }
            ]
          },
          {
            "id": 2,
            "name": "号码",
            "values": [
              {
                "id": 14,
                "name": "L",
                "thumb": "",
                "selected": true
              }
            ]
          },
          {
            "id": 3,
            "name": "送装服务",
            "values": [
              {
                "id": 15,
                "name": "不送装",
                "thumb": "",
                "selected": true
              },
              {
                "id": 16,
                "name": "送装",
                "thumb": ""
              }
            ]
          }
        ]
      }
    },
    "bottom": {
      "cart_number": 0,
      "can_collect": false
    }
  }
}
```

### 返回参数说明
data.banner 参数说明

| 字段             | 字段类型    | 说明      |
|----------------|---------|---------|
| images         | Array   | 商品轮播图   |
| images.*       | String  | 商品轮播图地址 |
| video          | Object  | 商品视频    |
| video.url      | String  | 视频地址    |
| video.duration | Integer | 视频时长    |

data.center 参数说明

| 字段           | 字段类型    | 说明                                    |
|--------------|---------|---------------------------------------|
| no           | String  | 商品编号                                  |
| name         | String  | 商品名称                                  |
| sub_name     | String  | 商品副标题                                 |
| label        | String  | 商品标签                                  |
| price        | String  | 商品价格                                  |
| total        | Integer | 商品库存，多规格时为总库存                         |
| unit         | String  | 商品单位                                  |
| status       | Integer | 商品状态 1上架 0下架                          |
| can_quota    | Integer | 是否限购，0：不限购，1：限购                       |
| quota_number | Integer | 限购数量                                  |
| content      | String  | 商品详情                                  |
| evaluate     | Array   | 商品评价，详见 `data.center.evaluate` 参数说明   |
| parameters   | Array   | 产品参数，详见 `data.center.parameters` 参数说明 |
| recommend    | Array   | 商品推荐，详见 `data.center.recommend` 参数说明  |
| sku_params   | Object  | 商品规格，详见 `data.center.sku_params` 参数说明 |


data.center.evaluate 参数说明

| 字段               | 字段类型    | 说明      |
|------------------|---------|---------|


data.center.parameters 参数说明

| 字段               | 字段类型    | 说明      |
|------------------|---------|---------|
| name | String  | 参数名称   |
| value | String  | 参数值   |


data.center.recommend 参数说明

| 字段               | 字段类型    | 说明      |
|------------------|---------|---------|


data.center.sku_params 参数说明

| 字段                              | 字段类型    | 说明       |
|---------------------------------|---------|----------|
| sku_item                        | Array   | 商品规格值数据  |
| sku_item.id                     | Integer | 商品规格ID   |
| sku_item.unique                 | String  | 商品规格唯一标识 |
| sku_item.price                  | String  | 商品规格价格   |
| sku_item.integral               | String  | 商品规格积分   |
| sku_item.number                 | Integer | 商品规格库存   |
| sku_item.has_number             | Boolean | 是否还有库存   |
| spec_values                     | Array   | 商品规格数据   |
| spec_values.*.id                | Integer | 商品规格ID   |
| spec_values.*.name              | String  | 商品规格名    |
| spec_values.*.values            | Array   | 商品规格值数据  |
| spec_values.*.values.*.id       | Integer | 商品规格值ID  |
| spec_values.*.values.*.name     | String  | 商品规格值名称  |
| spec_values.*.values.*.thumb    | String  | 商品规格值缩略图 |
| spec_values.*.values.*.selected | Boolean | 是否选中     |




data.bottom 参数说明

| 字段          | 字段类型 | 说明|
|-------------|--------|----|
| cart_number  | Integer | 购物车商品数量 |
| can_collect  | Boolean | 是否收藏 |


### 特殊 code 说明

| CODE值 | 说明    |
|-------|-------|
| 4000  | 商品被删除 |
