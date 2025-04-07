<template>
    <section>
        <drag-wrapper v-bind="{component: form, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="ad-wrapper" @click="handleChooseDragItem">
                    <div class="ad-item s-flex ai-ct jc-bt"  v-if="!form.content.data || form.content.data.length == 0">
                        <image-wrapper v-bind="{width: ColumnWidthHeight['2'].width / 2 + 'px', height: ColumnWidthHeight['2'].maxHeight / 2 + 'px'}" />
                        <image-wrapper v-bind="{width: ColumnWidthHeight['2'].width / 2 + 'px', height: ColumnWidthHeight['2'].maxHeight / 2 + 'px'}" />
                    </div>
                </div>
            </template>
        </drag-wrapper>
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
                        <el-form-item label="背景色" label-position="top" :prop="'background'" required>
                            <div>
                                <div>
                                    <el-radio-group v-model="form.content.background" fill="var(--main-color)">
                                        <el-radio v-for="bg in BackgroundOption" :value="bg.value" :key="bg.value">{{bg.label}}</el-radio>
                                    </el-radio-group>
                                </div>
                                <div>
                                    <el-color-picker v-model="form.content.background_color" v-if="form.content.background" />
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
                            <el-color-picker v-model="form.content.title.color" />
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
                            handle=".icon-Bars"
                            >

                            <div class="form-group-item s-flex ai-ct jc-bt" v-for="(item, index) in form.content.data" :key="index">
                                <em class="iconfont icon-Bars" style="font-size:20px"></em>
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
                                        <el-form-item label="链接">
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
                                        <el-form-item label="时间">
                                            <el-date-picker
                                                :class="item.time.length == 0 ? 'time-long' : 'time-range'"
                                                v-model="item.time"
                                                value-format="YYYY-MM-DD HH:mm:ss"
                                                type="datetimerange"
                                                size="large"
                                                :teleported="false"
                                                :editable="false"
                                                :default-time="[new Date(2000, 1, 1, 0, 0, 0), new Date(2000, 2, 1, 23, 59, 59)]"
                                                :range-separator="item.time.length == 0 ? '长期' : '~'"
                                                :disabled-date="(time) => {
                                                    const today = new Date();
                                                    today.setHours(0, 0, 0, 0);
                                                    return time.getTime() < today.getTime();
                                                }"
                                                @clear="() => {
                                                    item.time = [];
                                                }"
                                            >
                                            </el-date-picker>
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
                        <el-button type="primary" style="width: 100%;" :disabled="form.content.data.length >= MaxItemLength" @click="handleClickAddImageData">添加({{form.content.data.length}}/10)</el-button>
                    </div>
                </el-form>
            </template>
        </setting-bar>
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
import { Icon } from 'vant';
import { TempField, TempContentDataItemField, ColumnOption, BackgroundOption, MaxItemLength, ColumnWidthHeight, TitleAlignOption } from '@/pages/decoration/app/dataField/AdvertisingBanner.js'
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
.ad-wrapper{
    padding: 5px 10px 5px;
    .ad-item {
        background: #fff;
        border-radius: 10px;
        padding: 10px;
        box-sizing: border-box;
    }
}
.remove-btn {
    cursor: pointer
}
</style>