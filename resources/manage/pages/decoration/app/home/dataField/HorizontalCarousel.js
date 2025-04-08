import { LinkDataItemField } from "./Index.js"

/**
 * 轮播图 数据字段定义
 * @returns 
 */
export const TempComponentName = 'horizontal_carousel'
export const TempComponentNameZh = '轮播图'

export function TempField() {
    return {
        component_name: TempComponentName,
        // 表单数据
        content: {
            ...TempContentField(),
            data: [TempContentDataItemField()]
        },
        // 渲染数据
        data: {
            component_name: TempComponentName,
            ...TempContentField(),
            items: [TempDataItemField()],
        },
        id: '', // 组件id
        is_show: 1, // 组件是否显示
        name: TempComponentNameZh,  // 组件名
    }
}

// 基础数据
export function TempContentField() {
    return {
        width: '710', // 长度：默认710 不可修改
        height: '200', // 宽度：默认200
        style: 1, // 显示样式：默认1-平铺 2-过渡
        interval: 3, // 切换时间，默认3秒切换
    }
}
// 表单-单个轮播图数据
export function TempContentDataItemField() {
    return {
        url: LinkDataItemField(),
        date_type: 1, // 0:自定义  1:长期
        time: [], // 显示时间
        image: '',// 图片链接
        is_show: 1, // 是否显示
    }
}
// 组件渲染 单个轮播图数据
export function TempDataItemField() {
    return {
        image: '',
        url: '',
    }
}

// 显示样式
export const StyleOption = [{
    value: 1,
    label: '平铺'
}, {
    value: 2,
    label: '过渡'
}]

// 添加数据最大长度
export const MaxItemLength = 10
