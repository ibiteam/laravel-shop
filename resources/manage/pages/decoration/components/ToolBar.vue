<template>
    <aside class="toolbar-wrapper">
        <el-collapse v-model="active.name">
            <el-collapse-item :title="active.title[itsKey]" :name="itsKey" v-for="(its, itsKey) in active.components" :key="itsKey">
                <VueDraggable
                    class="module-group s-flex flex-wrap"
                    v-model="active.components[itsKey]"
                    :animation="1000"
                    filter=".disabled"
                    :group="{name: 'decoration', pull: 'clone', put: false}"
                    :sort="false"
                >
                    <div class="module-item s-flex ai-ct jc-ct" :class="{'disabled': item.limit == 0}" v-for="item in its" :key="item.component_name">
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
    components: {
        type: Object,
        default: () => {
            return {}
        }
    },
    // 组件原始数据
    original: {
        type: Array,
        default: () => {
            return []
        }
    }
})
const active = reactive({
    name: [],
    title: {
        advertisement_component: '广告组件',
        data_component: '数据组件'
    },
    value: [],
    components: {}
})

watch(() => props, (newVal) => {
    if (newVal && newVal.components) {
        active.name = Object.keys(newVal.components)
        active.components = newVal.components
        active.value = Object.values(newVal.components).flat()
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
        }
    }
}
</style>