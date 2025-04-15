# 文件上传

* 用户文件上传

### 请求URL

* /api/v1/upload

### 请求方式
* POST

### 请求参数

| 字段         | 是否必填 | 字段类型 | 说明 |
|:-----------|:-----|:-----|:---|
| file         | Y    | file | 文件 |

#### 成功示例
```
{
    "code": 200,
    "message": "success",
    "data": {
        "url": "https://laravel-shop.ptdmeta.cn/storage/manage/2025/04/14/zun2kQlIyyJuUzxGA0DuMAAlyWW5NvsTQi0TNeLU.png"
    }
}
```
