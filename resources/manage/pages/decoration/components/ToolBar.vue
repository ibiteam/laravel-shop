<template>
    <aside class="toolbar-wrapper">
        <el-collapse v-model="active.name">
            <el-collapse-item :title="active.title[itsKey]" :name="itsKey" v-for="(its, itsKey) in active.components" :key="itsKey">
                <VueDraggable
                    class="module-group s-flex flex-wrap"
                    v-model="active.components[itsKey]"
                    :animation="1000"
                    filter=".disabled"
                    :group="{name: 'decoration', pull: 'clone', put: false, revertClone: true}"
                    :sort="false"
                    :clone="handleDragClone"
                    @move="handleDragMove"
                    @end="handleDragEnd"
                >
                    <div class="module-item s-flex ai-ct jc-ct" :class="{'disabled': computedTempIsExist({component_name: item.component_name, limit: item.limit})}" v-for="item in its" :key="item.component_name">
                        <em :class="`decoration-svg ${active.svg[item.component_name]}`"></em>
                        <p>{{ item.name }}</p>
                    </div>
                </VueDraggable>
            </el-collapse-item>
        </el-collapse>
    </aside>
</template>

<script setup>
import { VueDraggable } from 'vue-draggable-plus'
import { ref, reactive, watch, defineEmits, nextTick } from 'vue'

const props = defineProps({
    // 组件拖拽数据
    // advertisement_component: 广告组件
    // data_component: 数据组件
    component_icon: {
        type: Object,
        default: () => {
            return {}
        }
    },
    // 组件原始数据
    component_value: {
        type: Array,
        default: () => {
            return []
        }
    },
    // 当前装修数据
    component_data: {
        type: Array,
        default: []
    },
})

const emit = defineEmits(['updateDragPlaceholder']);

const active = reactive({
    name: [],
    title: {
        advertisement_component: '广告组件',
        data_component: '数据组件'
    },
    components: {},
    svg: {
        'advertising_one': 'advertising',
        'advertising_two': 'advertising',
        'advertising_three': 'advertising',
        "theme_advertising": "theme-advertising", // 主题广告
        "quick_link": "quick-link", // 金刚区
        "brand_choice": "brand-choice", // 品牌精选
        "channel_square": "channel-square", // 频道广场
        "news": "news", // 新闻
        "hot_list": "hot-list", // 热力榜
        "flash_sale": "flash-sale", // 限时抢购
        "recommend_seller": "recommend-seller", // 推荐商家
        "recommend_theme": "recommend-theme", // 为您推荐
        "hot_sale_good": "hot-sale-good", // 热销商品
    }
})


// 查询组件是否达到添加次数
const computedTempIsExist = (params = {component_name: '', limit: 0}) => {
    const { component_name, limit } = params
    const existList = props.component_data.filter(item => item && item.component_name == component_name)
    if (limit > 0 && existList.length >= limit) {
        return true
    } else {
        return false
    }
}

// 拖拽克隆赋值组件初始数据
const handleDragClone = (item) => {
    function generateUUID() {
        return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }
    let {component_name} = item
    let component = props.component_value.find(item => item.component_name === component_name)
    component.id = generateUUID() + '-add'
    component.data = {}
    return component
}

const handleDragEnd = (e) => {
    console.log('handleDragEnd')
    emit('updateDragPlaceholder', null);
}

const handleDragMove = (event) => {
    console.log('handleDragMove')
    if (event.related && event.related.parentNode === document.querySelector('.app-wrapper-content')) {
        // 获取目标列表中拖拽项的索引
        const targetIndex = Array.prototype.indexOf.call(event.related.parentNode.children, event.related);
        emit('updateDragPlaceholder', targetIndex);
    }
}


watch(() => props, (newVal) => {
    if (newVal) {
        if (newVal.component_icon) {
            active.name = Object.keys(newVal.component_icon)
            active.components = newVal.component_icon
        }
    }
}, {
    immediate: true,
    deep: true
})

</script>
<style lang='scss' scoped>
@import '@/assets/css/decoration-svg-icon.css';
.toolbar-wrapper{
    width: 100%;
    height: 100%;
    padding: 10px;
    box-sizing: border-box;
    overflow: hidden auto;
    background-color: #fff;
    // position: absolute;
    // left: 0;
    // top: 0;
    // z-index: 2;
    user-select: none;
    :deep(.el-collapse),
    :deep(.el-collapse-item__header),
    :deep(.el-collapse-item__wrap) {
        border: none;
        border-bottom: none;
    }
    :deep(.el-collapse-item__wrap) {
        overflow: visible;
    }
    .module-group {
        gap: 10px;
    }
    .module-item {
        flex: 0 0 calc(33.333% - 10px);
        height: 66px;
        margin-bottom: 10px;
        border-radius: 5px;
        font-size: 12px;
        color: #666;
        flex-direction: column;
        cursor: pointer;
        &:hover {
            box-shadow: 0 0 5px 0 var(--main-color-30);
            transform: scale(1.1);
            transition: all .2s;
        }
        &.disabled{
            cursor: no-drop;
            &:hover {
                box-shadow: none;
                transform: scale(1);
            }
        }
    }
}
</style>