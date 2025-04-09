# 微信支付

* 用户选择微信支付方式点击确认支付按钮请求接口

### 请求URL

* /api/v1/order/cash/wechat/pay

### 请求参数

| 字段       | 字段类型    | 是否必须 | 说明                       |
|----------|---------|------|--------------------------|
| no       | String  | Y    | 订单编号                     |
| pay_form | String  | Y    | 请求来源，详见`pay_form_enum`枚举 |

#### pay_form_enum 枚举

| 枚举值    | 枚举说明  |
|--------|-------|
| app    | app支付 |
| mini   | 小程序支付 |
| wechat | 微信网页版 |
| h5     | h5支付  |


### 请求方式
* POST

### 返回示例

#### pay_form === h5
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "payment": "https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id=wx09112938166276adffdd9e4649b2610001&package=2939717853&redirect_url=",
        "pay_form": "h5"
    }
}
```

data 参数说明

| 字段         | 字段类型   | 说明   |
|------------|--------|------|
| payment    | String | 支付链接 |
| pay_form   | String | 支付来源 |


#### pay_form === app
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "payment": {
            "appid": "wx02ef0721945840f8",
            "partnerid": "1577960031",
            "prepayid": "wx091131107897365de2b09ccf1f70500000",
            "noncestr": "67f5e9fed7c2c",
            "timestamp": 1744169470,
            "package": "Sign=WXPay",
            "sign": "B694E75F2F58F94AA9F66AD186598358"
        },
        "pay_form": "app"
    }
}
```
data 参数说明

| 字段         | 字段类型   | 说明           |
|------------|--------|--------------|
| payment    | Object | APP调起微信的相关配置 |
| pay_form   | String | 支付来源         |


#### pay_form === h5
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "payment": {
            "appId": "wx7e0fe91cede19f5e",
            "timeStamp": "1744169699",
            "nonceStr": "e0SJIMJT6fa58odi",
            "package": "prepay_id=wx091134595667097c16772e262c20940000",
            "signType": "RSA",
            "paySign": "rngBBXMYYXgERfffHDbOTyabmTqWOz/gE+T8QFfiK6yaq6iyH7SaEDMvpw+6jy1EQBtudnXQfy17wE4eIj+1zz9wbHq0HIIy+OmHGnFRaq6fSx9Lp0F+hatszh0Wwpm7sY/tWodggbDpKx4SB/BvsWoyCFX/ZVF4BjqLpR1sRR89eEFlKQDIP8GaIkenkRXKJn4s9LUtZJySYmJbGdZNmdpvJfA0pVziqpnsE83RYBPoeSLkcw+tQ994d1oM2CjxrhDGjYMb7Ir5dcf/H8xdFlRbPvIDapzRafCDI41ewfgGk8SRIjiFojRSNhKoShekhXkegcp+5/hT0B8YBJF6Ig=="
        },
        "pay_form": "wechat"
    }
}
```
data 参数说明

| 字段         | 字段类型   | 说明              |
|------------|--------|-----------------|
| payment    | Object | 微信网页版调起微信支付相关配置 |
| pay_form   | String | 支付来源            |



#### pay_form === h5
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "payment": {
            "appId": "wx7e0fe91cede19f5e",
            "timeStamp": "1744169699",
            "nonceStr": "e0SJIMJT6fa58odi",
            "package": "prepay_id=wx091134595667097c16772e262c20940000",
            "signType": "RSA",
            "paySign": "rngBBXMYYXgERfffHDbOTyabmTqWOz/gE+T8QFfiK6yaq6iyH7SaEDMvpw+6jy1EQBtudnXQfy17wE4eIj+1zz9wbHq0HIIy+OmHGnFRaq6fSx9Lp0F+hatszh0Wwpm7sY/tWodggbDpKx4SB/BvsWoyCFX/ZVF4BjqLpR1sRR89eEFlKQDIP8GaIkenkRXKJn4s9LUtZJySYmJbGdZNmdpvJfA0pVziqpnsE83RYBPoeSLkcw+tQ994d1oM2CjxrhDGjYMb7Ir5dcf/H8xdFlRbPvIDapzRafCDI41ewfgGk8SRIjiFojRSNhKoShekhXkegcp+5/hT0B8YBJF6Ig=="
        },
        "pay_form": "mini"
    }
}
```
data 参数说明

| 字段         | 字段类型   | 说明              |
|------------|--------|-----------------|
| payment    | Object | 微信小程序调起微信支付相关配置 |
| pay_form   | String | 支付来源            |

