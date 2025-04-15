import { LinkDataItemField } from "./Index.js"

/**
 * 广告图 数据字段定义
 * @returns 
 */
export const TempComponentName = 'advertising_banner'
export const TempComponentNameZh = '广告图'

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
        column: 2, // 每行显示： 2,3,4 默认显示2个
		background: 1, // 是否有背景色， 1-有 0-无 默认1
		background_color: '#ffffff', // 背景色，默认为白色:#ffffff
        width: ColumnWidthHeight['2'].width, // 宽度：默认330 不可修改 2个330,3个220,4个160
        height: ColumnWidthHeight['2'].maxHeight, // 高度：默认240（最高250）；2个240,3个150，4个150
        title: {
			image: '', // 标题小图标，默认空
			name: '', // 标题名称，默认空
			align: 'left', // 标题对齐，默认左对齐，left-左侧，center-居中
			suffix: '', // 标题右侧文案
            color: '#333333',  // 标题颜色
			// 标题右侧文案链接
            url: LinkDataItemField()
		},
    }
}
// 表单-单个广告图数据
export function TempContentDataItemField() {
    return {
        url: LinkDataItemField(),
        date_type: 1, // 0:自定义  1:长期
        time: [], // 显示时间
        image: '',// 图片链接
        is_show: 1, // 是否显示
    }
}
// 组件渲染 单个广告图数据
export function TempDataItemField() {
    return {
        image: '',
        url: '',
    }
}

// 每行显示
export const ColumnOption = [{
    value: 2,
    label: '2个'
}, {
    value: 3,
    label: '3个'
}, {
    value: 4,
    label: '4个'
}]

// 背景色
export const BackgroundOption = [{
    value: 1,
    label: '有'
}, {
    value: 0,
    label: '无'
}]

// 默认最大宽，最大高度，最小高度，最大添加长度
export const ColumnWidthHeight = {
    "2": {
        maxWidth: 350,
        minWidth: 340,
        maxHeight: 250,
        minHeight: 190,
        maxItemLength: 8,
    },
    "3": {
        maxWidth: 230,
        minWidth: 220,
        maxHeight: 400,
        minHeight: 280,
        maxItemLength: 12,
    },
    "4": {
        maxWidth: 170,
        minWidth: 160,
        maxHeight: 350,
        minHeight: 220,
        maxItemLength: 16,
    },
}

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

