# 获取省市区信息

### 请求URL

* /api/pc/region

### 请求方式
* get


#### 成功示例
```
{
  "code": 200,
  "message": "success",
  "data": [
    {
      "value": 2,
      "label": "北京",
      "text": "北京",
      "pinyin": "bj",
      "pinyin_first": "B",
      "code": "11",
      "children": [
        {
          "value": 52,
          "label": "北京",
          "text": "北京",
          "pinyin": "bj",
          "pinyin_first": "B",
          "code": "1100",
          "children": [
            {
              "value": 500,
              "label": "东城区",
              "text": "东城区",
              "pinyin": "dcq",
              "pinyin_first": "D",
              "code": "110101"
            },
            {
              "value": 501,
              "label": "西城区",
              "text": "西城区",
              "pinyin": "xcq",
              "pinyin_first": "X",
              "code": "110102"
            },
            {
              "value": 502,
              "label": "海淀区",
              "text": "海淀区",
              "pinyin": "hdq",
              "pinyin_first": "H",
              "code": "110108"
            },
            {
              "value": 503,
              "label": "朝阳区",
              "text": "朝阳区",
              "pinyin": "zyq",
              "pinyin_first": "Z",
              "code": "110105"
            },
            {
              "value": 506,
              "label": "丰台区",
              "text": "丰台区",
              "pinyin": "ftq",
              "pinyin_first": "F",
              "code": "110106"
            },
            {
              "value": 507,
              "label": "石景山区",
              "text": "石景山区",
              "pinyin": "sjsq",
              "pinyin_first": "S",
              "code": "110107"
            },
            {
              "value": 508,
              "label": "房山区",
              "text": "房山区",
              "pinyin": "fsq",
              "pinyin_first": "F",
              "code": "110111"
            },
            {
              "value": 509,
              "label": "门头沟区",
              "text": "门头沟区",
              "pinyin": "mtgq",
              "pinyin_first": "M",
              "code": "110109"
            },
            {
              "value": 511,
              "label": "顺义区",
              "text": "顺义区",
              "pinyin": "syq",
              "pinyin_first": "S",
              "code": "110113"
            },
            {
              "value": 512,
              "label": "昌平区",
              "text": "昌平区",
              "pinyin": "cpq",
              "pinyin_first": "C",
              "code": "110114"
            },
            {
              "value": 513,
              "label": "怀柔区",
              "text": "怀柔区",
              "pinyin": "hrq",
              "pinyin_first": "H",
              "code": "110116"
            },
            {
              "value": 514,
              "label": "平谷区",
              "text": "平谷区",
              "pinyin": "pgq",
              "pinyin_first": "P",
              "code": "110117"
            },
            {
              "value": 515,
              "label": "大兴区",
              "text": "大兴区",
              "pinyin": "dxq",
              "pinyin_first": "D",
              "code": "110115"
            },
            {
              "value": 516,
              "label": "密云区",
              "text": "密云区",
              "pinyin": "myq",
              "pinyin_first": "M",
              "code": "110118"
            },
            {
              "value": 517,
              "label": "延庆区",
              "text": "延庆区",
              "pinyin": "yqq",
              "pinyin_first": "Y",
              "code": "110119"
            },
            {
              "value": 3589,
              "label": "通州区",
              "text": "通州区",
              "pinyin": "tzq",
              "pinyin_first": "T",
              "code": "110112"
            }
          ]
        }
      ]
    },
    {
      "value": 3,
      "label": "安徽省",
      "text": "安徽省",
      "pinyin": "ahs",
      "pinyin_first": "A",
      "code": "34",
      "children": [
        {
          "value": 36,
          "label": "安庆市",
          "text": "安庆市",
          "pinyin": "aqs",
          "pinyin_first": "A",
          "code": "3408",
          "children": [
            {
              "value": 398,
              "label": "迎江区",
              "text": "迎江区",
              "pinyin": "yjq",
              "pinyin_first": "Y",
              "code": "340802"
            },
            {
              "value": 399,
              "label": "大观区",
              "text": "大观区",
              "pinyin": "dgq",
              "pinyin_first": "D",
              "code": "340803"
            },
            {
              "value": 400,
              "label": "宜秀区",
              "text": "宜秀区",
              "pinyin": "yxq",
              "pinyin_first": "Y",
              "code": "340811"
            },
            {
              "value": 401,
              "label": "桐城市",
              "text": "桐城市",
              "pinyin": "tcs",
              "pinyin_first": "T",
              "code": "340881"
            },
            {
              "value": 402,
              "label": "怀宁县",
              "text": "怀宁县",
              "pinyin": "hnx",
              "pinyin_first": "H",
              "code": "340822"
            },
            {
              "value": 405,
              "label": "太湖县",
              "text": "太湖县",
              "pinyin": "thx",
              "pinyin_first": "T",
              "code": "340825"
            },
            {
              "value": 406,
              "label": "宿松县",
              "text": "宿松县",
              "pinyin": "ssx",
              "pinyin_first": "S",
              "code": "340826"
            },
            {
              "value": 407,
              "label": "望江县",
              "text": "望江县",
              "pinyin": "wjx",
              "pinyin_first": "W",
              "code": "340827"
            },
            {
              "value": 408,
              "label": "岳西县",
              "text": "岳西县",
              "pinyin": "yxx",
              "pinyin_first": "Y",
              "code": "340828"
            },
            {
              "value": 3826,
              "label": "安徽安庆经济开发区",
              "text": "安徽安庆经济开发区",
              "pinyin": "ahaqjjkfq",
              "pinyin_first": "A",
              "code": "340871"
            },
            {
              "value": 3831,
              "label": "潜山市",
              "text": "潜山市",
              "pinyin": "qss",
              "pinyin_first": "Q",
              "code": "340882"
            }
          ]
        },
        {
          "value": 37,
          "label": "蚌埠市",
          "text": "蚌埠市",
          "pinyin": "bbs",
          "pinyin_first": "B",
          "code": "3403",
          "children": [
            {
              "value": 413,
              "label": "怀远县",
              "text": "怀远县",
              "pinyin": "hyx",
              "pinyin_first": "H",
              "code": "340321"
            },
            {
              "value": 414,
              "label": "五河县",
              "text": "五河县",
              "pinyin": "whx",
              "pinyin_first": "W",
              "code": "340322"
            },
            {
              "value": 415,
              "label": "固镇县",
              "text": "固镇县",
              "pinyin": "gzx",
              "pinyin_first": "G",
              "code": "340323"
            },
            {
              "value": 433,
              "label": "蚌山区",
              "text": "蚌山区",
              "pinyin": "bsq",
              "pinyin_first": "B",
              "code": "340303"
            },
            {
              "value": 434,
              "label": "龙子湖区",
              "text": "龙子湖区",
              "pinyin": "lzhq",
              "pinyin_first": "L",
              "code": "340302"
            },
            {
              "value": 435,
              "label": "禹会区",
              "text": "禹会区",
              "pinyin": "yhq",
              "pinyin_first": "Y",
              "code": "340304"
            }
          ]
        }
      ]
    }
  ]
}
```

