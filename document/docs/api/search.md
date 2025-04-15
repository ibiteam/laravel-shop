# 导航搜索

* 用户待搜索的单独页面

### 请求URL

* /api/v1/search

### 请求方式
* GET


#### 成功示例
```
{
    "code": 200,
    "message": "success",
    "data": {
        "component_name": "home_nav",
        "logo": "https:\/\/testcdn.ibisaas.com\/2025\/03\/27\/1743041497638.jpeg",
        "keywords": "",
        "button_color": "#f71111",
        "interval": 3,
        "items": [
            {
                "title": "手机",
                "url": {
                    "name": "华为 mate 60 pro",
                    "value": "https:\/\/vue-shop.ptdmeta.cn\/good?goods_no=5769f804-94ea-4564-ac33-65857eeb6629"
                }
            },
            {
                "title": "电视",
                "url": {
                    "name": "小米100",
                    "value": "https:\/\/vue-shop.ptdmeta.cn\/good?goods_no=a571d598-8d69-4a94-970a-397a057adda0"
                }
            }
        ]
    }
}
```
