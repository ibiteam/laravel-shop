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
                >
                    <div class="module-item s-flex ai-ct jc-ct" :class="{'disabled': computedTempIsExist({component_name: item.component_name, limit: item.limit})}" v-for="item in its" :key="item.component_name">
                        <em class="iconfont" >{{ item.icon }}</em>
                        <p>{{ item.name }}</p>
                    </div>
                </VueDraggable>
            </el-collapse-item>
        </el-collapse>
    </aside>
</template>

<script setup>
import { VueDraggable } from 'vue-draggable-plus'
import { ref, reactive, watch } from 'vue'

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
    }
})

const active = reactive({
    name: [],
    title: {
        advertisement_component: '广告组件',
        data_component: '数据组件'
    },
    components: {}
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
    let {component_name} = item
    let component = props.component_value.find(item => item.component_name === component_name)
    component.id = Math.round(new Date() / 1000) + 'add'
    component.data = {}
    return component
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
.toolbar-wrapper{
    width: 300px;
    height: inherit;
    padding: 10px;
    box-sizing: border-box;
    overflow: hidden auto;
    background-color: #fff;
    position: absolute;
    left: 0;
    top: 0;
    z-index: 2;
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