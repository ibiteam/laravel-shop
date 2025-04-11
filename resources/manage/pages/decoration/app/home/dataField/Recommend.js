/**
 * 广告图 数据字段定义
 * @returns 
 */
export const TempComponentName = 'recommend'
export const TempComponentNameZh = '为您推荐'

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
        title: '', // 标题
        goods: {
            goods_data: [],
        }
    }
}
