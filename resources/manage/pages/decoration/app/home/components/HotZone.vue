<template>
    <section>
        <drag-wrapper v-bind="{component: form, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="hot-zone-wrapper" @click="handleChooseDragItem">
                    <template v-if="!form.data.image">
                        <image-wrapper v-bind="{width: '100%', height: '100px', radius: '10px'}" />
                    </template>
                    <template v-else>
                        <image-wrapper v-bind="{src: form.data.image, width: '100%', height: 'auto', radius: '10px'}" />
                    </template>
                </div>
            </template>
        </drag-wrapper>
        <teleport to="#decorationAppMain">
            <setting-bar v-bind="{name: form.name, show_radio: false,}" v-if="temp_index == form.id">
                <template #content="slotProps">
                    <div class="setting-bar-item" v-if="slotProps.type == 0">
                        <div class="item-title">内容设置 </div>
                        <p class="item-title-info">建议：请先选择图片，图片宽度750px，高度不限</p>
                        <div class="placeholder s-flex ai-ct jc-ct" v-if="!form.content.image">
                            <span class="fs12">750*高度不限</span>
                        </div>
                        <ImageUpload v-else width="100%" height="100%" :src="form.content.image" @material="handleOpenUpload" @local="(image) => form.content.image = image" @remove="form.content.image = ''" />
                        <!--<div class="placeholder s-flex ai-ct jc-ct">
                            <image-wrapper v-bind="{src: form.content.image, width: '100%', height: '100%'}" />
                            <div class="add-btn s-flex ai-ct jc-ct" @click="showDialog = true">
                                <Icon name="plus" size="24px" />
                                <p>添加热区</p>
                            </div>
                        </div>-->
                        <el-button type="primary" style="width: 100%; margin-top: 20px;" :disabled="form.content.areas.length >= MaxItemLength" @click="showDialog = true">编辑热区({{form.content.areas.length}}/10)</el-button>
                    </div>
                </template>
            </setting-bar>
        </teleport>
        <HotZoneDialog v-bind="{show: showDialog, title: '编辑热区', data: form.content.areas, temp_index}" @close="showDialog = false" />
    </section>
</template>
<script setup>
import DragWrapper from '@/pages/decoration/components/app/DragWrapper.vue'
import ImageWrapper from '@/pages/decoration/components/app/ImageWrapper.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import ImageUpload from '@/pages/decoration/components/ImageUpload.vue'
import { ref, reactive, watch, getCurrentInstance, defineExpose } from 'vue'
import HotZoneDialog from './HotZoneDialog.vue'
import { MaxItemLength } from '@/pages/decoration/app/home/dataField/HotZone.js'


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

const showDialog = ref(false)

// 通知打开选择图片弹窗
const handleOpenUpload = (index) => {
    cns.$bus.emit('openUploadDialog', {temp_index: form.id, show: true, dir_type: 1, multiple: false})
}
// 更新上传图片数据
const updateUploadComponentData = (res) => {
    form.content.image = res.file[0].file_path
}

const handleChooseDragItem = () => {
    cns.$bus.emit('chooseDragItem', {temp_index: form.id})
}

defineExpose({
    getComponentData() {
        return form
    },
    updateUploadComponentData
})

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
.hot-zone-wrapper{
    padding: 5px 10px 5px;
}
.setting-bar-item{
    // .placeholder{
    //     width: 100%;
    //     height: 170px;
    //     background: #f2f2f2;
    //     color: #999;
    //     font-size: 12px;
    //     position: relative;
    //     img {
    //         width: 100%;
    //         height: auto;
    //     }
    //     .add-btn {
    //         width: 100%;
    //         height: 100%;
    //         background-color: rgba(0,0,0, .5);
    //         color: #fff;
    //         flex-direction: column;
    //         font-weight: bold;
    //         position: absolute;
    //         top: 0;
    //         left: 0;
    //         z-index: 2;
    //         cursor: pointer;
    //     }
    // }
}
</style>