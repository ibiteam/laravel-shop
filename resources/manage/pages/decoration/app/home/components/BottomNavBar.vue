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
                        <tabbar-item v-for="(tab, index) in form.content.data"
                            :key="index"
                            :name="tab.alias"
                            :icon="index == 0 ? tab.selection_image : tab.default_image"
                            :badge="tab.is_show_number && tab.number ? tab.number : ''"
                            :to="tab.url.value"
                        >
                            <template #icon>
                                <image-wrapper v-bind="{ src: index == 0 ? tab.selection_image : tab.default_image, width: '22px', height: '22px', radius: '0' }"/>
                            </template>
                            <span :style="{color: index == 0 ? form.content.font_selection_color : form.content.font_default_color}" v-if="(index == 0 && tab.selection_title) || (index != 0 && tab.default_title)">{{ index == 0 ? tab.selection_title : tab.default_title }}</span>
                        </tabbar-item>
                    </tabbar>
                </div>
            </template>
        </drag-wrapper>
        <setting-bar v-bind="{name: form.name}" v-if="temp_index == form.component_name">
            <template #content>
                <el-form :model="form.content" label-width="auto">
                    <div class="setting-bar-item">
                        <div class="item-title">颜色设置</div>
                        <el-form-item label="文字默认颜色" label-position="top" :prop="['font_default_color']">
                            <div>
                                <el-color-picker v-model="form.content.font_default_color" @change="() => {
                                    if (!form.content.font_default_color) {
                                        form.content.font_default_color = '#333333'
                                    }
                                }"/>
                                <p class="item-title-info" style="margin-bottom: 0;">用于改变标签栏中文字的默认颜色</p>
                            </div>
                        </el-form-item>
                        <el-form-item label="文字选中颜色" label-position="top" :prop="['font_selection_color']">
                            <div>
                                <el-color-picker v-model="form.content.font_selection_color" @change="() => {
                                    if (!form.content.font_selection_color) {
                                        form.content.font_selection_color = '#f71111'
                                    }
                                }"/>
                                <p class="item-title-info" style="margin-bottom: 0;">用于改变标签栏中文字的选中颜色</p>
                            </div>
                        </el-form-item>
                    </div>
                    <div class="setting-bar-item">
                        <div class="item-title">标签设置</div>
                        <VueDraggable
                            class="group-dragable"
                            v-model="form.content.data"
                            :animation="1000"
                            :group="{name: form.id, pull: true, put: true}"
                            handle=".icon-bars"
                            >
                            <div class="form-group-item s-flex ai-ct jc-bt" v-for="(item, index) in form.content.data" :key="index">
                                <em class="iconfont icon-bars" style="font-size:20px"></em>
                                <div class="group-content s-flex ai-fs jc-bt">
                                    <div>
                                        <el-form-item class="not-required" label="" label-position="top" :prop="['data', index, 'default_image']" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                            <ImageUpload 
                                                :src="item.image"
                                                @material="handleOpenUpload([index, 'default_image'], form.component_name)"
                                                @local="(image) => {
                                                    item.default_image = image
                                                }"
                                                @remove="() => {
                                                    item.default_image = ''
                                                }" 
                                            >
                                                <template #upload-placeholder>
                                                    <div class="upload-placeholder co-999" style="line-height: 0.5">默认图</div>
                                                </template>
                                            </ImageUpload>
                                        </el-form-item>
                                        <el-form-item class="not-required" label="" label-position="top" :prop="['data', index, 'selection_image']" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                            <ImageUpload 
                                                :src="item.image"
                                                @material="handleOpenUpload([index, 'selection_image'], form.component_name)"
                                                @local="(image) => {
                                                    item.selection_image = image
                                                }"
                                                @remove="() => {
                                                    item.selection_image = ''
                                                }" 
                                            >
                                                <template #upload-placeholder>
                                                    <div class="upload-placeholder co-999" style="line-height: 0.5">选中图</div>
                                                </template>
                                            </ImageUpload>
                                        </el-form-item>
                                    </div>
                                    <div style="width: calc(100% - 70px);">
                                        <el-form-item label="默认名称">
                                            <el-input v-model="item.default_title" maxlength='4'></el-input>
                                        </el-form-item>
                                        <el-form-item label="选中名称">
                                            <el-input v-model="item.selection_title" maxlength='4'></el-input>
                                        </el-form-item>
                                        <el-form-item label="链接">
                                            <LinkInput
                                                :name="item.url.name"
                                                :value="item.url.value"
                                                @select="handleOpenLink([index, 'url'], form.component_name)"
                                                @input="(res) => {
                                                    item.url = res
                                                }"
                                                @clear="(res) => {
                                                    item.url = res
                                                }"
                                            />
                                        </el-form-item>
                                        <div class="s-flex ai-ct jc-bt">
                                            <el-form-item label="" style="margin-bottom: 0;">
                                                <el-switch v-model="item.is_show" :active-value="1" :inactive-value="0" active-text="显示" inactive-text="隐藏"/>
                                            </el-form-item>
                                            <em class="iconfont icon-shanchu remove-btn" @click.stop="handleClickDeleteData(index, `data`)" title="删除"></em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </VueDraggable>
                        <el-button type="primary" style="width: 100%;" :disabled="form.content.data.length >= TempFixedField[form.component_name]['MaxItemLength']" @click="handleClickAddImageData">添加({{form.content.data.length}}/{{ TempFixedField[form.component_name]['MaxItemLength'] }})</el-button>
                    </div>
                </el-form>
            </template>
        </setting-bar>
    </div>
</template>

<script setup>
import DragWrapper from '@/pages/decoration/components/app/DragWrapper.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import ImageWrapper from '@/pages/decoration/components/app/ImageWrapper.vue'
import ImageUpload from '@/pages/decoration/components/ImageUpload.vue'
import LinkInput from '@/pages/decoration/components/LinkInput.vue'
import { Tabbar, TabbarItem } from 'vant';
import { ref, reactive, watch, getCurrentInstance } from 'vue'
import { VueDraggable } from 'vue-draggable-plus'
import { TempFixedField } from '@/pages/decoration/app/home/dataField/Index.js'
import { updateNested } from '@/pages/decoration/utils/common.js'

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
// 通知打开选择图片弹窗
const handleOpenUpload = (keys, not_for_data) => {
    cns.$bus.emit('openUploadDialog', {keys, not_for_data, show: true, dir_type: 1, multiple: false})
}
// 更新上传图片数据
const updateUploadComponentData = (res) => {
    const { keys, not_for_data, file } = res
    form.content.data = updateNested(form.content.data, keys, file[0].file_path)
}

// 添加图片数据
const handleClickAddImageData = () => {
    if (form.content.data.length >= TempFixedField[form.component_name]['MaxItemLength']) return
    const item = JSON.parse(JSON.stringify(TempFixedField[form.component_name]['TempContentDataItemField']))
    form.content.data.push(item)
}

// 通知打开选择路由弹窗
const handleOpenLink = (keys, not_for_data) => {
    cns.$bus.emit('openLinkDialog', {keys, not_for_data, show: true})
}

// 更新选择路由数据
const updateLinkComponentData = (res) => {
    const { keys, not_for_data, link } = res
    form.content.data = updateNested(form.content.data, keys, link)
}

// 删除
const handleClickDeleteData = (index, target) => {
    if (!form.content[target][index]) return
    form.content[target].splice(index, 1)
    cns.$message.success('删除成功！')
}

defineExpose({
    getComponentData() {
        return form
    },
    updateUploadComponentData,
    updateLinkComponentData
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
.bottom-navbar-wrapper{
    height: 50px;
    background-color: #fff;
    :deep(.van-tabbar-item) {
        cursor: default;
    }
}
</style>