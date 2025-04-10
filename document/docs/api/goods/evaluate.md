# 商品评价

* 商品详情中更多评价按钮，点击后跳转到商品评价页面

### 请求URL

* /api/v1/evaluate/goods

### 请求参数：

| 字段 | 是否必填   | 字段类型 | 说明       |
|----|--------|------|----------|
| no | String | Y    | 商品编号     |



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
                "id": 1,
                "nickname": "匿名用户",
                "avatar": "",
                "content": "服务号",
                "images": [
                    "https://xxx.xxx.xxx/wuQ8a6oFLb7WiTr2HLs4Og3yiqA0LS61XGcG130m.jpeg",
                    "https://xxx.xxx.xxx/wuQ8a6oFLb7WiTr2HLs4Og3yiqA0LS61XGcG130m.jpeg"
                ],
                "rank": 5,
                "goods_rank": 5,
                "price_rank": 5,
                "bus_rank": 5,
                "delivery_rank": 5,
                "service_rank": 5
            },
            {
                "id": 2,
                "nickname": "匿名用户",
                "avatar": "",
                "content": "服务好",
                "images": [
                    "https://xxx.xxx.xxx/wuQ8a6oFLb7WiTr2HLs4Og3yiqA0LS61XGcG130m.jpeg",
                    "https://xxx.xxx.xxx/wuQ8a6oFLb7WiTr2HLs4Og3yiqA0LS61XGcG130m.jpeg"
                ],
                "rank": 5,
                "goods_rank": 5,
                "price_rank": 5,
                "bus_rank": 5,
                "delivery_rank": 5,
                "service_rank": 5
            }
        ],
        "meta": {
            "total": 2,
            "per_page": 10,
            "current_page": 1
        }
    }
}
```


### 返回参数说明

data.list 返回参数说明

| 字段            | 字段类型    | 字段说明   |
|---------------|---------|--------|
| id            | Integer | 评价ID   |
| nickname      | String  | 评价用户昵称 |
| avatar        | String  | 评价用户头像 |
| content       | String  | 评价内容   |
| images        | Array   | 评价图片   |
| images.*      | String  | 评价图片地址 |
| rank          | Integer | 综合评分   |
| goods_rank    | Integer | 商品评分   |
| price_rank    | Integer | 价格评分   |
| bus_rank      | Integer | 商家服务评分 |
| delivery_rank | Integer | 交货速度评分 |
| service_rank  | Integer | 服务评分   |

data.meta 返回参数说明

| 字段           | 字段类型    | 字段说明 |
|--------------|---------|------|
| total        | Integer | 总记录数 |
| per_page     | Integer | 每页条数 |
| current_page | Integer | 当前页码 |
