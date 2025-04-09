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
            icon: 'iconfont icon-changjianrequ'
        },
        // 金刚区
        'quick_link': {
            icon: 'iconfont icon-jingangqu'
        }
        // 为您推荐
        // 商品推荐
    }
}

// 固定组件相关数据定义

export const TempFixedField = {
    'home_search': {
        MaxItemLength: 10,
        TempContentDataItemField: {
            title: '',
            url: LinkDataItemField()
        }
    }
}