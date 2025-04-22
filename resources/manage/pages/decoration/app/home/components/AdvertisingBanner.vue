<template>
    <section>
        <drag-wrapper v-bind="{component: form, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="ad-wrapper" @click="handleChooseDragItem">
                    <div class="ad-item s-flex ai-ct jc-bt" :style="{ backgroundColor: '#fff', padding: '10px', borderRadius: '10px'}"  v-if="!form.content.data || form.content.data.length == 0">
                        <image-wrapper v-bind="{width: ColumnWidthHeight['2'].minWidth / 2 + 'px', height: ColumnWidthHeight['2'].maxHeight / 2 + 'px'}" />
                        <image-wrapper v-bind="{width: ColumnWidthHeight['2'].minWidth / 2 + 'px', height: ColumnWidthHeight['2'].maxHeight / 2 + 'px'}" />
                    </div>
                    <div class="ad-item"
                        v-else
                        :style="{
                            minHeight: ColumnWidthHeight[form.content.column].minHeight / 2 + 'px', 
                            backgroundColor: form.content.background && form.content.background_color ? form.content.background_color : '',
                            padding: form.content.background ? '10px 10px 0 10px' : '',
                            borderRadius: form.content.background ? '10px' : '',
                        }"  
                    >
                        <div class="ad-title-wrapper s-flex" v-if="form.content.title.name || form.content.title.image || form.content.title.suffix || form.content.title.url.value" :class="form.content.title.align == 'center' ? 'jc-ct' : 'jc-fs'" style="margin-bottom: 10px;">
                            <div class="ad-title s-flex ai-ct jc-ct" >
                                <image-wrapper v-if="form.content.title.image" v-bind="{ src: form.content.title.image, width: '16px', height: '16px', radius: '0' }" style="margin-right: 6px;"/>
                                <span class="fs14 fw-b" :style="{color: form.content.title.color}">{{form.content.title.name}}</span>
                            </div>
                            <div class="ad-title-link s-flex ai-ct fs12">
                                {{ form.content.title.suffix }}<em class="iconfont icon-gengduo"  v-if="form.content.title.url.value" style="font-size: 12px;"></em>
                            </div>
                        </div>
                        <div class="ad-banner-wrapper s-flex ai-ct jc-bt flex-wrap">
                            <div v-for="(item, index) in form.content.data" :key="index" 
                                :style="{
                                    width: (form.content.width / 2 - (form.content.background ? '5' : 0)) + 'px',
                                    marginBottom: (form.content.background ? '10px' : 0)
                                }"
                            >
                                <image-wrapper 
                                    v-bind="{
                                        src: item.image,
                                        width: '100%',
                                        height: (form.content.height ? form.content.height / 2 : 100) + 'px', 
                                        radius: '0'
                                    }" 
                                />
                            </div>
                        </div>
                    </div>
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
                                <el-radio-group v-model="form.content.column" fill="var(--main-color)" @change="() => {
                                    form.content.width = ColumnWidthHeight[form.content.column].width
                                    if (form.content.column == 2) {
                                        form.content.width = form.content.background ? ColumnWidthHeight['2'].minWidth : ColumnWidthHeight['2'].maxWidth
                                        form.content.height = ColumnWidthHeight['2'].maxHeight
                                    } else if (form.content.column == 3) {
                                        form.content.width = form.content.background ? ColumnWidthHeight['3'].minWidth : ColumnWidthHeight['3'].maxWidth
                                        form.content.height = ColumnWidthHeight['3'].maxHeight
                                    } else if (form.content.column == 4) {
                                        form.content.width = form.content.background ? ColumnWidthHeight['4'].minWidth : ColumnWidthHeight['4'].maxWidth
                                        form.content.height = ColumnWidthHeight['4'].maxHeight
                                    }
                                }">
                                    <el-radio v-for="column in ColumnOption" :value="column.value" :key="column.value">{{column.label}}</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="背景色" label-position="top" :prop="'background'" required>
                                <div>
                                    <div>
                                        <el-radio-group v-model="form.content.background" fill="var(--main-color)" @change="() => {
                                            form.content.width = form.content.background ? ColumnWidthHeight[form.content.column].minWidth : ColumnWidthHeight[form.content.column].maxWidth
                                        }">
                                            <el-radio v-for="bg in BackgroundOption" :value="bg.value" :key="bg.value">{{bg.label}}</el-radio>
                                        </el-radio-group>
                                    </div>
                                    <div>
                                        <el-color-picker v-model="form.content.background_color" v-if="form.content.background" @change="() => {
                                            if (!form.content.background_color) {
                                                form.content.background_color = '#ffffff'
                                            }
                                        }"/>
                                    </div>
                                </div>
                            </el-form-item>
                            <el-form-item label="图片宽高" label-position="top" :prop="'height'" :rules="
                                [
                                    { required: true, message: '请输入图片高度', trigger: 'blur' },
                                    { validator: (rule, value, callback) => {
                                        if (value < ColumnWidthHeight[form.content.column].minHeight || value > ColumnWidthHeight[form.content.column].maxHeight) {
                                            callback(new Error(`图片高度范围是${ColumnWidthHeight[form.content.column].minHeight}px~${ColumnWidthHeight[form.content.column].maxHeight}px`));
                                        } else if (isNaN(value)) {
                                            callback(new Error('请输入数字'));
                                        } else if (!Number.isInteger(value * 1)) {
                                            callback(new Error('请输入整数'));
                                        } else { callback(); }
                                    }, trigger: 'blur' },
                                ]
                            ">
                                <div class="s-flex ai-ct" style="width: 100%;">
                                    <el-input v-model="form.content.width" disabled style="width: 100px;"></el-input>&nbsp;&nbsp;
                                    <p style="margin: 0 10px;">~</p>
                                    <el-input v-model="form.content.height" style="width: 100px;"></el-input>&nbsp;&nbsp;
                                </div>
                                <p class="item-title-info" style="margin-bottom: 0;">{{ `高度范围：${ColumnWidthHeight[form.content.column].minHeight}px~${ColumnWidthHeight[form.content.column].maxHeight}px` }}</p>
                            </el-form-item>
                        </div>
                        <div class="setting-bar-item">
                            <div class="item-title">标题设置</div>
                            <el-form-item label="小图标" label-position="top" :prop="['title', 'image']">
                                <div>
                                    <ImageUpload 
                                        :src="form.content.title.image"
                                        @material="() => {
                                            handleOpenUpload(['title', 'image'])
                                        }"
                                        @local="(image) => {
                                            form.content.title.image = image
                                        }"
                                        @remove="() => {
                                            form.content.title.image = ''
                                        }" 
                                    />
                                    <p class="item-title-info" style="margin-bottom: 0;">建议尺寸：32 * 32</p>
                                </div>
                            </el-form-item>
                            <el-form-item label="标题" label-position="top" :prop="['title', 'name']">
                                <el-input v-model="form.content.title.name" style="width: 70%;"></el-input>
                                <el-color-picker v-model="form.content.title.color" @change="() => {
                                    if (!form.content.title.color) {
                                        form.content.title.color = '#333333'
                                    }
                                }"/>
                            </el-form-item>
                            <el-form-item label="对齐方式" label-position="top" :prop="['title', 'align']">
                                <el-radio-group v-model="form.content.title.align">
                                    <el-tooltip
                                        v-for="align in TitleAlignOption"
                                        :key="align.value"
                                        effect="dark"
                                        :content="align.label"
                                        placement="bottom"
                                    >
                                        <el-radio-button  :value="align.value" size="small">
                                            <template #default>
                                                <em :class="align.icon"></em>
                                            </template>
                                        </el-radio-button>
                                    </el-tooltip>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="右侧文字" label-position="top" :prop="['title', 'suffix']">
                                <el-input v-model="form.content.title.suffix" style="width: 70%;" placeholder="如：更多"></el-input>
                            </el-form-item>
                            <el-form-item label="链接" label-position="top" :prop="['title', 'url', 'value']">
                                <LinkInput
                                    style="width: 70%;"
                                    :name="form.content.title.url.name"
                                    :value="form.content.title.url.value"
                                    @select="handleOpenLink(['title', 'url'])"
                                    @input="(res) => {
                                        form.content.title.url = res
                                    }"
                                    @clear="(res) => {
                                        form.content.title.url = res
                                    }"
                                />
                            </el-form-item>
                        </div>
                        <div class="setting-bar-item">
                            <div class="item-title">内容设置</div>
                            <VueDraggable
                                class="group-dragable"
                                v-model="form.content.data"
                                :animation="1000"
                                :group="{name: form.id, pull: true, put: true}"
                                handle=".icon-bars"
                                >
    
                                <div class="form-group-item s-flex ai-fs jc-bt" v-for="(item, index) in form.content.data" :key="index">
                                    <em class="iconfont icon-drag" style="font-size:20px;margin-top: 10px;"></em>
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
                                            <el-form-item label="链接" style="margin-bottom:5px;">
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
                                            <el-form-item label="时间" style="margin-bottom:5px;">
                                                <el-date-picker
                                                    :class="item.time?.length == 0 ? 'time-long' : 'time-range'"
                                                    v-model="item.time"
                                                    value-format="YYYY-MM-DD HH:mm:ss"
                                                    type="datetimerange"
                                                    size="large"
                                                    :editable="false"
                                                    :default-time="[new Date(2000, 1, 1, 0, 0, 0), new Date(2000, 2, 1, 23, 59, 59)]"
                                                    :range-separator="item.time?.length == 0 ? '长期' : '~'"
                                                    :disabled-date="(time) => {
                                                        const today = new Date();
                                                        today.setHours(0, 0, 0, 0);
                                                        return time.getTime() < today.getTime();
                                                    }"
                                                    @change="() => {
                                                        if (item.time) {
                                                            item.date_type = 0
                                                        } else {
                                                            item.date_type = 1;
                                                            item.time = []
                                                        }
                                                    }"
                                                    @clear="() => {
                                                        item.date_type = 1;
                                                        item.time = [];
                                                    }"
                                                >
                                                </el-date-picker>
                                            </el-form-item>
                                            <div class="s-flex ai-ct jc-fe">
                                                <el-switch v-model="item.is_show" :active-value="1" :inactive-value="0"/>
                                                <em class="iconfont icon-shanchu remove-btn ml-10" @click.stop="handleClickDeleteData(index, `data`)" title="删除"></em>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </VueDraggable>
                            <el-button type="primary" style="width: 100%;" :disabled="form.content.data.length >= ColumnWidthHeight[form.content.column].maxItemLength" @click="handleClickAddImageData">添加({{form.content.data.length}}/{{ColumnWidthHeight[form.content.column].maxItemLength}})</el-button>
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
import { TempField, TempContentDataItemField, ColumnOption, BackgroundOption, ColumnWidthHeight, TitleAlignOption } from '@/pages/decoration/app/home/dataField/AdvertisingBanner.js'
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
    if (form.content.data.length >= ColumnWidthHeight[form.content.column].maxItemLength) return
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
        // border-radius: 10px;
        // padding: 10px;
        box-sizing: border-box;
    }
    .ad-title-wrapper {
        position: relative;
        .ad-title-link {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto 0;
        }
    }
}
.remove-btn {
    cursor: pointer
}
</style>