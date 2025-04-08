<template>
    <section>
        <drag-wrapper v-bind="{component: form, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="quick-link-wrapper" @click="handleChooseDragItem">
                    <swiper v-bind="{
                        slidesPerView: form.content.column,
                        slidesPerGroup: form.content.column,
                        pagination: {
                            'el': '.swiper-pagination.decoration-swiper-pagination',
                            'clickable': false,
                        },
                        grid: {
                            'fill': 'row',
                            'rows': form.content.row
                        },
                        modules: swiperModules
                    }" class="scroll-wrapper">
                        <swiper-slide class="scroll-item" v-for="(item, index) in form.content.data" :key="index">
                            <div class="link-item s-flex ai-ct jc-ct flex-dir">
                                <image-wrapper
                                    v-bind="{src: item.image, width: '45px', height: '45px'}" 
                                />
                                <p class="elli-1 title">{{item.title}}</p>
                            </div>
                        </swiper-slide>
                    </swiper>
                    <div class="swiper-pagination decoration-swiper-pagination"></div>
                </div>
            </template>
        </drag-wrapper>
        <teleport to="#decorationAppMain">

            <setting-bar v-bind="{name: form.name}" v-if="temp_index == form.id">
                <template #content>
                    <el-form lable-width="auto" :model="form.content" ref="templateSetForm">
                        <div class="setting-bar-item">
                            <div class="item-title">显示设置</div>
                            <el-form-item label="每行显示" label-position="top" :prop="'column'" required>
                                <el-radio-group v-model="form.content.column" fill="var(--main-color)">
                                    <el-radio v-for="column in ColumnOption" :value="column.value" :key="column.value">{{column.label}}</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="板块行数" label-position="top" :prop="'row'" required>
                                <el-radio-group v-model="form.content.row" fill="var(--main-color)">
                                    <el-radio v-for="row in RowOption" :value="row.value" :key="row.value">{{row.label}}</el-radio>
                                </el-radio-group>
                            </el-form-item>
                        </div>
                        <div class="setting-bar-item">
                            <div class="item-title">内容设置</div>
                            <p class="item-title-info" style="margin-bottom: 0;">建议尺寸：90 * 90</p>
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
                                        <el-form-item class="not-required" label="" :prop="['data', index, 'image']" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                            <ImageUpload 
                                                :src="item.image"
                                                @material="() => {
                                                    handleOpenUpload(['data', index, 'image'])
                                                }"
                                                @local="(image) => {
                                                    item.image = image
                                                }"
                                                @remove="() => {
                                                    item.image = ''
                                                }" 
                                            />
                                        </el-form-item>
                                        <div style="width: calc(100% - 70px);">
                                            <el-form-item label="名称" :prop="['data', index, 'title']" :rules="{ required: true, message: '请填写名称', trigger: 'blur' }">
                                                <el-input v-model="item.title"/>
                                            </el-form-item>
                                            <el-form-item label="链接" :prop="['data', index, 'value']" :rules="{ required: true, message: '请填写链接', trigger: 'blur' }">
                                                <LinkInput
                                                    :name="item.url.name"
                                                    :value="item.url.value"
                                                    @select="handleOpenLink(['data', index, 'url'])"
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
                            <el-button type="primary" style="width: 100%;" :disabled="form.content.data.length >= MaxItemLength" @click="handleClickAddImageData">添加({{form.content.data.length}}/{{ MaxItemLength }})</el-button>
                        </div>
                    </el-form>
                </template>
            </setting-bar>
        </teleport>
    </section>
</template>
<script setup>
import DragWrapper from '@/pages/decoration/components/app/DragWrapper.vue'
import ImageWrapper from '@/pages/decoration/components/app/ImageWrapper.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import ImageUpload from '@/pages/decoration/components/ImageUpload.vue'
import LinkInput from '@/pages/decoration/components/LinkInput.vue'
import { ref, reactive, watch, getCurrentInstance, onMounted, nextTick } from 'vue'
import { VueDraggable } from 'vue-draggable-plus'
import { TempField, TempContentDataItemField, ColumnOption, RowOption, MaxItemLength} from '@/pages/decoration/app/home/dataField/QuickLink.js'
import { updateNested } from '@/pages/decoration/utils/common.js'
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Pagination, Grid } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/grid';

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

const form = reactive(TempField())
const templateSetForm = ref(null)
const swiperModules = [Pagination, Grid]

const handleChooseDragItem = () => {
    cns.$bus.emit('chooseDragItem', {temp_index: form.id})
}
// 通知打开选择图片弹窗
const handleOpenUpload = (keys) => {
    cns.$bus.emit('openUploadDialog', {temp_index: form.id, keys, show: true, dir_type: 1, multiple: false})
}
// 更新上传图片数据
const updateUploadComponentData = (res) => {
    form.content = updateNested(form.content, res.keys, res.file[0].file_path)
}

// 添加图片数据
const handleClickAddImageData = () => {
    if (form.content.data.length >= MaxItemLength) return
    form.content.data.push(TempContentDataItemField())
}

// 通知打开选择路由弹窗
const handleOpenLink = (keys) => {
    cns.$bus.emit('openLinkDialog', {temp_index: form.id, keys, show: true})
}

// 更新选择路由数据
const updateLinkComponentData = (res) => {
    form.content = updateNested(form.content, res.keys, res.link)
}

// 删除
const handleClickDeleteData = (index, target) => {
    if (!form.content[target][index]) return
    form.content[target].splice(index, 1)
    cns.$message.success('删除成功！')
}

// 保存
const handleTempFormSubmit = () => {
    templateSetForm.value.validate((valid) => {
        if (valid) {

        }
    })
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
        let temp = JSON.parse(JSON.stringify(newValue[0]))
        Object.keys(temp).forEach(key => {
            form[key] = temp[key]

        })
        console.log(form)
    }
}, {
    immediate: true,
    deep: true
})

</script>

<style lang='scss' scoped>
.quick-link-wrapper{
    padding: 5px 10px 5px;
    .scroll-wrapper {
        width: 100%;
        background: #fff;
        border-radius: 10px;
        padding-top: 10px;
        .link-item {
            margin-bottom: 10px;
            .title {
                width: 100%;
                height: 20px;
                line-height: 20px;
                text-align: center;
            }
        }
    }
}
.remove-btn {
    cursor: pointer
}
</style>