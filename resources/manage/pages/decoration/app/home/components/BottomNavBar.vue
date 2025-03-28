<template>
    <div>
        <drag-wrapper v-bind="{component, select: temp_index == form.component_name, show_tooltip: false}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="bottom-navbar-wrapper" @click="handleChooseDragItem">
                    <tabbar
                        :fixed="false"
                        safe-area-inset-bottom
                        :before-change="() => false"
                        >
                        <tabbar-item v-for="(tab, index) in form.data.items"
                            :key="tab.alias"
                            :name="tab.alias"
                            :icon="active == tab.alias ? tab.selection_image : tab.default_image"
                            :badge="tab.is_show_number && tab.number ? tab.number : ''"
                            :to="tab.url"
                        >
                            <template #icon>
                                <img width='22' height='22' :src="index == 0 ? tab.selection_image : tab.default_image" />
                            </template>
                            <span :style="{color: index == 0 ? form.data.font_selection_color : form.data.font_default_color}">{{ index == 0 ? tab.check_title : tab.title }}</span>
                        </tabbar-item>
                    </tabbar>
                </div>
            </template>
        </drag-wrapper>
        <setting-bar v-bind="{name: form.name}" v-if="temp_index == form.component_name">
            <template #content="slotProps">
                {{slotProps.type}}
                <div class="setting-bar-item"></div>
            </template>
        </setting-bar>
    </div>
</template>

<script setup>
import DragWrapper from '@/pages/decoration/components/app/DragWrapper.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import { Tabbar, TabbarItem } from 'vant';
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

const active = ref('')

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
.bottom-navbar-wrapper{
    height: 50px;
    background-color: #fff;
    // position: absolute;
    // bottom: 0;
    // left: 0;
    // right: 0;
    :deep(.van-tabbar-item) {
        cursor: default;
    }
}
</style>