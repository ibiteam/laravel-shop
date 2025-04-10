import { LinkDataItemField } from "./Index.js"

/**
 * 广告图 数据字段定义
 * @returns 
 */
export const TempComponentName = 'goods_recommend'
export const TempComponentNameZh = '商品推荐'

export function TempField() {
    return {
        component_name: TempComponentName,
        // 表单数据
        content: {
            ...TempContentField(),
        },
        // 渲染数据
        // data: {
        //     component_name: TempComponentName,
        //     ...TempContentField(),
        //     items: [TempDataItemField()],
        // },
        id: '', // 组件id
        is_show: 1, // 组件是否显示
        name: TempComponentNameZh,  // 组件名
    }
}

// 基础数据
export function TempContentField() {
    return {
        layout: 2, // 商品布局： 1,2,3 默认显示2列
        title: {
			image: '', // 标题小图标，默认空
			name: '', // 标题名称，默认空
			align: 'left', // 标题对齐，默认左对齐，left-左侧，center-居中
			suffix: '', // 标题右侧文案
            color: '#333333',  // 标题颜色
			// 标题右侧文案链接
            url: LinkDataItemField()
		},
        goods: {
            "rule": 1,// 推荐规则 1、智能推荐 2、手动推荐
            "sort_type": 1,//排序类型 1、销量 2、好评 3、低价 4、新品
            "number": 3,//数量限制 1 ~ 20
            "goods_nos": [] // 商品集合
        }
    }
}

// 商品布局
export const LayoutOption = [{
    value: 1,
    label: '单列'
}, {
    value: 2,
    label: '双列'
}, {
    value: 3,
    label: '三列'
}]


// 对齐方式
export const TitleAlignOption = [{
    value: 'left',
    label: '左对齐',
    icon: 'iconfont icon-zuoduiqi'
}, {
    value: 'center',
    label: '居中对齐',
    icon: 'iconfont icon-juzhongduiqi'
}]

export const MaxGoodsNumber = 20
export const MinGoodsNumber = 1