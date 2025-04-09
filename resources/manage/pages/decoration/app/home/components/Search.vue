<template>
    <section>
        <drag-wrapper v-bind="{component, select: temp_index == form.component_name}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="search-wrapper s-flex ai-ct jc-bt" @click="handleChooseDragItem">
                    <div class="search-logo" v-if="form.content.logo">
                        <image-wrapper v-bind="{ src: form.content.logo, width: '80px', height: '30px', radius: '0' }"/>
                    </div>
                    <div class="search-input s-flex ai-ct jc-bt flex-1">
                        <swiper v-if="form.content.data?.length" v-bind="{
                            direction: 'vertical',
                            autoplay: {
                                'delay': (form.content.interval || 3) * 1000,
                            },
                            loop: form.content.data.length >= 3 ? true : false,
                            modules: swiperModules
                        }" class="search-swiper">
                            <swiper-slide class="scroll-item" v-for="(item, index) in form.content.data" :key="index">
                                <div class="search-placeholder" > {{ item.title }} </div>
                            </swiper-slide>
                        </swiper>
                        <div class="search-placeholder" v-else> {{ form.content.keywords }} </div>
                        <div class="search-btn" :style="{color: '#fff', backgroundColor: form.content.button_color}">搜索</div>
                    </div>
                </div>
            </template>
        </drag-wrapper>
        <setting-bar v-bind="{name: form.name}" v-if="temp_index == form.component_name">
            <template #content>
                <el-form :model="form.content" label-width="auto">
                    <div class="setting-bar-item">
                        <div class="item-title">搜索设置</div>
                        <el-form-item label="logo" label-position="top" :prop="['logo']">
                            <div>
                                <ImageUpload 
                                    :src="form.content.logo"
                                    @material="() => {
                                        handleOpenUpload(['logo'], form.component_name)
                                    }"
                                    @local="(image) => {
                                        form.content.logo = image
                                    }"
                                    @remove="() => {
                                        form.content.logo = ''
                                    }" 
                                />
                                <p class="item-title-info" style="margin-bottom: 0;">建议尺寸：140 * 60</p>
                            </div>
                        </el-form-item>
                        <el-form-item label="关键词" label-position="top" :prop="['keywords']">
                            <div>
                                <el-input v-model="form.content.keywords"></el-input>
                                <p class="item-title-info" style="margin-bottom: 0;">搜索框默认文字</p>
                            </div>
                        </el-form-item>
                        <el-form-item label="按钮色值" label-position="top" :prop="['button_color']">
                            <el-color-picker v-model="form.content.button_color" @change="() => {
                                if (!form.content.button_color) {
                                    form.content.button_color = '#f71111'
                                }
                            }"/>
                        </el-form-item>
                        <el-form-item label="切换时间(秒)" label-position="top" :prop="['interval']" :rules="
                                [
                                    { validator: (rule, value, callback) => {
                                        if (isNaN(value)) {
                                            callback(new Error('请输入数字'));
                                        } else if (!Number.isInteger(value * 1)) {
                                            callback(new Error('请输入整数'));
                                        } else if (value < 1 || value > 10) {
                                            callback('切换时间为1-10秒')
                                        } else { callback(); }
                                    }, trigger: 'blur' },
                                ]
                            ">
                            <div>
                                <el-input v-model="form.content.interval"></el-input>
                            </div>
                        </el-form-item>
                    </div>
                    <div class="setting-bar-item">
                        <div class="item-title">搜索推荐</div>
                        <p class="item-title-info">设置后搜索框将拿推荐的数据</p>
                        <VueDraggable
                            class="group-dragable"
                            v-model="form.content.data"
                            :animation="1000"
                            :group="{name: form.id, pull: true, put: true}"
                            handle=".icon-bars"
                            >
                            <div class="form-group-item s-flex ai-ct jc-bt" v-for="(item, index) in form.content.data" :key="item">
                                <em class="iconfont icon-bars" style="font-size:20px"></em>
                                <div class="group-content" style="width: 70%;">
                                    <el-form-item label="名称">
                                        <el-input v-model="item.title"></el-input>
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
                                </div>
                                <em class="iconfont icon-shanchu remove-btn" @click.stop="handleClickDeleteData(index, `data`)" title="删除"></em>
                            </div>
                        </VueDraggable>
                        <el-button type="primary" style="width: 100%;" :disabled="form.content.data.length >= TempFixedField[form.component_name]['MaxItemLength']" @click="handleClickAddImageData">添加({{form.content.data.length}}/{{ TempFixedField[form.component_name]['MaxItemLength'] }})</el-button>
                    </div>
                </el-form>
            </template>
        </setting-bar>
    </section>
</template>

<script setup>
import DragWrapper from '@/pages/decoration/components/app/DragWrapper.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import ImageWrapper from '@/pages/decoration/components/app/ImageWrapper.vue'
import ImageUpload from '@/pages/decoration/components/ImageUpload.vue'
import LinkInput from '@/pages/decoration/components/LinkInput.vue'
import { ref, reactive, watch, getCurrentInstance } from 'vue'
import { VueDraggable } from 'vue-draggable-plus'
import { TempFixedField } from '@/pages/decoration/app/home/dataField/Index.js'
import { updateNested } from '@/pages/decoration/utils/common.js'
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/autoplay';


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

const swiperModules = [Autoplay]

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
    form.content.logo = file[0].file_path
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
.search-wrapper{
    padding: 10px 10px 5px;
    background: #f2f2f2;
    .search-logo {
        max-width: 80px;
        max-height: 30px;
        margin-right: 4px;
        overflow: hidden;
    }
    .search-input {
        height: 33px;
        padding: 0 4px 0 8px;
        box-sizing: border-box;
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
    }
    .search-swiper {
        flex: 1;
        height: 33px;
        border-radius: 15px;
    }
    .search-placeholder {
        color: #c1c1c1;
        line-height: 33px;
    }
    .search-btn {
        width: 60px;
        height: 28px;
        line-height: 28px;
        text-align: center;
        color: #fff;
        border-radius: 15px;
    }
}
</style>