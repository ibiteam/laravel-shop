// 链接选择数据
export function LinkDataItemField() {
    return {
        name: '',
        value: '',
    }
}

// 拖拽组件模版数据
export function DragTempItemField() {
    return {
        // 轮播图
        'horizontal_carousel': {
            icon: 'iconfont icon-lunbotu',
        },
        // 广告图
        'advertising_banner': {
            icon: 'iconfont icon-guanggaotu'
        },
        // 热区
        'hot_zone': {
            icon: 'iconfont icon-chuangjianrequ'
        },
        // 金刚区
        'quick_link': {
            icon: 'iconfont icon-jingangqu'
        },
        // 为您推荐
        'recommend': {
            icon: 'iconfont icon-weinintuijian'
        },
        // 商品推荐
        'goods_recommend': {
            icon: 'iconfont icon-shangpintuijian'
        }
    }
}

// 固定组件相关数据定义

export const TempFixedField = {
    // 顶部搜索栏
    'home_search': {
        MaxItemLength: 10,
        TempContentDataItemField: {
            title: '', // 搜索提示词
            url: LinkDataItemField()
        }
    },
    // 底部导航栏
    'label': {
        MaxItemLength: 5,
        TempContentDataItemField: {
            default_title: '', // 标签默认名称
			selection_title: '', // 标签选中名称
			default_image: '', // 标签默认图标
			selection_image: '', // 标签选中图标
			is_show: 1, // 是否显示
            url: LinkDataItemField()
        }
    }
}