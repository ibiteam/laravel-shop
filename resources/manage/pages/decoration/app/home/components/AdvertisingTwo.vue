<template>
    <section>
        <drag-wrapper v-bind="{component, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="ad-wrapper s-flex ai-ct jc-bt" v-if="!form.data.items || form.data.items.length == 0" @click="handleChooseDragItem">
                    <image-wrapper
                        v-bind="{width: '175px', height: '80px', radius: '10px'}" 
                        v-for="item in 2" :key="item"
                    />
                </div>
                <div class="ad-wrapper s-flex ai-ct jc-bt" v-else @click="handleChooseDragItem">
                    <image-wrapper
                        v-bind="{src: item.icon, width: '175px', height: (form.data.height ? form.data.height / 2 : 80) + 'px', radius: '10px'}" 
                        v-for="item in form.data.items" :key="item.url"
                    />
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
}
</style>