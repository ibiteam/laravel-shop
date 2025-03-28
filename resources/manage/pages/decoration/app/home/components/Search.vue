<template>
    <section>
        <drag-wrapper v-bind="{component, select: temp_index == form.component_name}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="search-wrapper s-flex ai-ct jc-bt" @click="handleChooseDragItem">
                    <el-carousel
                        class="search-swiper"
                        height="33px"
                        direction="vertical"
                        :pause-on-hover="false"
                        indicator-position="none"
                    >
                        <el-carousel-item v-for="item in form.data.search_data.items" :key="item.keywords">
                            {{ item.keywords }}
                        </el-carousel-item>
                    </el-carousel>
                    <div class="search-btn" :style="{color: form.data.search_data.search_font_color, backgroundColor: form.data.search_data.button_color}">搜索</div>
                </div>
            </template>
        </drag-wrapper>
        <setting-bar v-bind="{name: form.name}" v-if="temp_index == form.component_name">
            <template #content="slotProps">
                {{slotProps.type}}
                <div class="setting-bar-item"></div>
            </template>
        </setting-bar>
    </section>
</template>

<script setup>
import DragWrapper from '@/pages/decoration/components/app/DragWrapper.vue'
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
})

const handleChooseDragItem = () => {
    cns.$bus.emit('chooseDragItem', {temp_index: form.component_name})
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
.search-wrapper{
    padding: 10px 10px 5px;
    .search-swiper {
        flex: 1;
        padding-left: 10px;
        border-radius: 15px;
        background: #fff;
        color: #c1c1c1;
        :deep(.el-carousel__item) {
            line-height: 33px;
        }
    }
    .search-btn {
        width: 60px;
        height: 30px;
        margin-left: 8px;
        line-height: 30px;
        text-align: center;
        color: #fff;
        background-color: var(--main-color);
        border-radius: 25px;
    }
}
</style>