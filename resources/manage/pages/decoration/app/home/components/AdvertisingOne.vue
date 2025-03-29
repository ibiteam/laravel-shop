<template>
    <section>
        <drag-wrapper v-bind="{component: form, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="ad-wrapper" @click="handleChooseDragItem">
                    <div class="ad-item"  v-if="!form.data.items || form.data.items.length == 0">
                        <image-wrapper v-bind="{width: '100%', height: '100px', radius: '10px'}" />
                    </div>
                    <el-carousel
                        v-else
                        class="ad-swiper"
                        :height="(form.data.height ? form.data.height / 2 : 100) + 'px'"
                        :pause-on-hover="false"
                        trigger="click"
                        arrow="never">
                        <el-carousel-item class="ad-item" v-for="item in form.data.items" :key="item.url">
                            <image-wrapper v-bind="{src: item.icon, width: '100%', height: (form.data.height ? form.data.height / 2 : 100) + 'px', radius: '10px'}" />
                        </el-carousel-item>
                    </el-carousel>
                </div>
            </template>
        </drag-wrapper>
        <setting-bar v-bind="{name: form.name}" v-if="temp_index == form.id">
            <template #content="slotProps">
                <div class="setting-bar-item" v-if="slotProps.type == 0">
                    <div class="item-title">内容设置
                        <el-button size="small">添加({{form.content.data.length}}/20)</el-button>
                    </div>
                    <p class="item-title-info">支持jpg、jpeg、png、gif格式；建议大小：2M内。</p>
                    <VueDraggable
                        class="group-dragable"
                        v-model="form.content.data"
                        :animation="1000"
                        :group="{name: form.component_name, pull: true, put: true}"
                        handle=".icon-drag"
                        >
                        <div class="group-item s-flex ai-ct jc-bt" v-for="(item, index) in form.content.data" :key="index">
                            <icon name="bars" class="icon-drag" size="20px"/>
                        </div>
                    </VueDraggable>
                </div>
            </template>
        </setting-bar>
    </section>
</template>
<script setup>
import DragWrapper from '@/pages/decoration/components/app/DragWrapper.vue'
import ImageWrapper from '@/pages/decoration/components/app/ImageWrapper.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import { ref, reactive, watch, getCurrentInstance } from 'vue'
import { VueDraggable } from 'vue-draggable-plus'
import { Icon } from 'vant';

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    component: {
        type: Object,
        default: () => {
            return {}
        }
    },
    temp_index: {
        type: [Number, String],
        default: ''
    },
    parent: {
        type: Array,
        default: []
    },
    parent_index: {
        type: Number,
        default: 0
    }
})

const form = reactive({
    component_name: '',
    content: {},
    data: {},
    id: '',
    is_show: 1,
    name: '',
    sort: 0,
    showDrawer: false
})

const handleChooseDragItem = () => {
    cns.$bus.emit('chooseDragItem', {temp_index: form.id})
}

watch([() => props.component], (newValue) => {
    if (newValue[0]) {
        Object.keys(newValue[0]).forEach(key => {
            form[key] = newValue[0][key]
        })
    }
}, {
    immediate: true,
    deep: true
})
</script>

<style lang='scss' scoped>
.ad-wrapper{
    padding: 5px 10px 5px;
    .ad-item {
        border-radius: 10px;
    }
    .ad-swiper {
        :deep(.el-carousel__button) {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            box-shadow: 0 0 2px rgba(0, 0, 0, 0.1);
        }
        :deep(.is-active .el-carousel__button) {
            width: 13px;
            border-radius: 20px;
            background-color: var(--main-color);
        }
    }
}
.group-item {
    width: 100%;
    padding: 16px 20px 16px 0;
    margin-bottom: 15px;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-sizing: border-box;
    position: relative;
    .icon-drag {
        padding: 10px;
        cursor: move;
    }
}
</style>