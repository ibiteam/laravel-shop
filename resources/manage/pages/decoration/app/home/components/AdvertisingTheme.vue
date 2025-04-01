<template>
    <section>
        <drag-wrapper v-bind="{component: form, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="ad-content-wrapper" v-if="!form.data.items || form.data.items.length == 0" @click="handleChooseDragItem">
                    <div class="decoration-title-wrapper">
                        <div class="decoration-title">主题广告</div>
                        <div class="decoration-title-right">
                            更多<Icon name="arrow" />
                        </div>
                    </div>
                    <div class="ad-wrapper s-flex ai-ct jc-bt">
                        <image-wrapper
                            class="ad-item"
                            v-bind="{width: '165px', height: '75px', radius: '10px'}" 
                            v-for="item in 4" :key="item"
                        />
                    </div>
                </div>
                <div class="ad-content-wrapper" v-else @click="handleChooseDragItem">
                    <div class="decoration-title-wrapper">
                        <div class="decoration-title">{{ form.data.name }}</div>
                        <div class="decoration-title-right" v-if="form.data.url">
                            更多<Icon name="arrow" />
                        </div>
                    </div>
                    <div class="ad-wrapper s-flex ai-ct jc-bt">
                        <image-wrapper
                            class="ad-item"
                            v-bind="{src: item.icon, width: form.data.width / 2 + 'px', height: form.data.height / 2 + 'px', radius: '10px'}" 
                            v-for="(item, idx) in form.data.items" :key="idx"
                        />
                    </div>
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
        console.log(form)
    }
}, {
    immediate: true,
    deep: true
})
</script>

<style lang='scss' scoped>
.ad-content-wrapper{
    padding: 5px 10px 5px;
    background: #f2f2f2;
    .decoration-title-wrapper {
        border-radius: 10px 10px 0 0;
    }
    .ad-wrapper {
        flex-wrap: wrap;
        padding: 0 7.5px;
        background: #fff;
        border-radius: 0 0 10px 10px;
        .ad-item {
            margin-bottom: 5px;
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