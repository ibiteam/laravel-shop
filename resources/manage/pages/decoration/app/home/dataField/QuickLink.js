import { LinkDataItemField } from "./Index.js"

/**
 * 金刚区 数据字段定义
 * @returns 
 */
export const TempComponentName = 'quick_link'
export const TempComponentNameZh = '金刚区'

export function TempField() {
    return {
        component_name: TempComponentName,
        // 表单数据
        content: {
            ...TempContentField(),
            data: [TempContentDataItemField()]
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
        row: 2, // 板块行数 1,2,3 默认2行
        column: 4, // 每行显示： 3,4,5 默认显示4个
    }
}
// 表单-单个数据
export function TempContentDataItemField() {
    return {
        url: LinkDataItemField(),
        title: '', // 名称
        image: '',// 图片链接
        is_show: 1, // 是否显示
    }
}
// 组件渲染 单个数据
export function TempDataItemField() {
    return {
        image: '',
        url: '',
        title: '',
    }
}

// 每行显示
export const RowOption = [{
    value: 1,
    label: '1行'
}, {
    value: 2,
    label: '2行'
}, {
    value: 3,
    label: '3行'
}]

// 每行显示
export const ColumnOption = [{
    value: 3,
    label: '3个'
}, {
    value: 4,
    label: '4个'
}, {
    value: 5,
    label: '5个'
}]

export const MaxItemLength = 30
