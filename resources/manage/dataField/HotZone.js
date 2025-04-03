import { LinkDataItemField } from "./Index.js"

/**
 * 热区 数据字段定义
 * @returns 
 */
export const TempComponentName = 'hot_zone'
export const TempComponentNameZh = '热区'

export function TempField() {
    return {
        component_name: TempComponentName,
        // 表单数据
        content: {
            ...TempContentField(),
            areas: [TempContentAreaItemField()]
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
        image: '', // 背景图
    }
}
// 表单-单个热区定位数据
export function TempContentAreaItemField() {
    return {
        url: LinkDataItemField(),
        x: '', // x坐标
        y: '', // y坐标
        width: '', // 宽度
        height: '', // 高度
    }
}
// 组件渲染 单个轮播图数据
export function TempDataItemField() {
    return {
        x: '', // x坐标
        y: '', // y坐标
        width: '', // 宽度
        height: '', // 高度
        url: '',
    }
}
