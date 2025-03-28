# 获取树状分类

### 请求路由
* api/v1/category

### 请求方式
* get

### 返回示例
```
{
  "code": 200,
  "message": "success",
  "data": [
    {
      "id": 1,
      "parent_id": 0,
      "name": "电子产品",
      "children": [
        {
          "id": 6,
          "parent_id": 1,
          "name": "手机",
          "logo": null,
          "children": [
            {
              "id": 16,
              "parent_id": 6,
              "name": "智能手机",
              "logo": null
            },
            {
              "id": 17,
              "parent_id": 6,
              "name": "平板电脑",
              "logo": null
            }
          ]
        },
        {
          "id": 7,
          "parent_id": 1,
          "name": "电脑",
          "children": [
            {
              "id": 18,
              "parent_id": 7,
              "name": "笔记本电脑",
              "logo": null
            },
            {
              "id": 19,
              "parent_id": 7,
              "name": "台式电脑",
              "logo": null
            }
          ]
        },
        {
          "id": 8,
          "parent_id": 1,
          "name": "电视",
          "children": [
            {
              "id": 20,
              "parent_id": 8,
              "name": "智能电视",
              "logo": null
            }
          ]
        }
      ]
    },
    {
      "id": 2,
      "parent_id": 0,
      "name": "家居用品",
      "children": [
        {
          "id": 9,
          "parent_id": 2,
          "name": "厨房用具",
          "logo": null
        },
        {
          "id": 10,
          "parent_id": 2,
          "name": "家具",
          "logo": null
        }
      ]
    },
    {
      "id": 3,
      "parent_id": 0,
      "name": "图书",
      "children": [
        {
          "id": 11,
          "parent_id": 3,
          "name": "儿童图书",
          "logo": null
        },
        {
          "id": 12,
          "parent_id": 3,
          "name": "小说",
          "logo": null
        }
      ]
    },
    {
      "id": 4,
      "parent_id": 0,
      "name": "服装",
      "children": [
        {
          "id": 13,
          "parent_id": 4,
          "name": "服装",
          "logo": null
        }
      ]
    },
    {
      "id": 5,
      "parent_id": 0,
      "name": "运动户外",
      "children": [
        {
          "id": 14,
          "parent_id": 5,
          "name": "运动鞋",
          "logo": null
        },
        {
          "id": 15,
          "parent_id": 5,
          "name": "户外装备",
          "logo": null
        }
      ]
    }
  ]
}
```
