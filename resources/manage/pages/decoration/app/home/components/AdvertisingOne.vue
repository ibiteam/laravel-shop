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
                {{slotProps.type}}
                <div class="setting-bar-item"></div>
            </template>
        </setting-bar>
    </section>
</template>

<script setup>
import DragWrapper from '@/pages/decoration/components/app/DragWrapper.vue'
import ImageWrapper from '@/pages/decoration/components/app/ImageWrapper.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import { ref, reactive, watch, getCurrentInstance } from 'vue'

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
</style>