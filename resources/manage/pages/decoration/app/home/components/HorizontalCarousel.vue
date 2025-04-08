<template>
    <section>
        <drag-wrapper v-bind="{component: form, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="ad-wrapper" @click="handleChooseDragItem">
                    <div class="ad-item"  v-if="!form.content.data || form.content.data.length == 0">
                        <image-wrapper v-bind="{width: '100%', height: '100px', radius: '10px'}" />
                    </div>
                    <div class="ad-swiper">
                        <swiper v-if="form.content.style == 2" v-bind="{
                            slidesPerView: 3,
                            spaceBetween: 0,
                            centeredSlides: true,
                            autoplay: {
                                'delay': (form.content.interval || 3) * 1000,
                                'disableOnInteraction': false,
                                'waitForTransition': true,
                            },
                            loop: true,
                            pagination: {
                                'el': '.swiper-pagination.decoration-swiper-pagination',
                                'clickable': false,
                            },
                            modules: swiperModules
                        }" class="cards-swiper">
                            <template v-if="form.content.data.length <= 3">
                                <swiper-slide class="scroll-item" v-slot="{ isActive }" v-for="(item, index) in [...form.content.data, ...form.content.data, ...form.content.data, ...form.content.data]" :key="index">
                                    <image-wrapper v-bind="{fit: isActive ? 'fill' : 'cover', src: item.image, width: '100%', height: (form.content.height ? form.content.height / 2 : 100) + 'px', radius: '0'}" />
                                </swiper-slide>
                            </template>
                            <swiper-slide v-else class="scroll-item" v-slot="{ isActive }" v-for="(item, index) in form.content.data" :key="index">
                                <image-wrapper v-bind="{fit: isActive ? 'fill' : 'cover', src: item.image, width: '100%', height: (form.content.height ? form.content.height / 2 : 100) + 'px', radius: '0'}" />
                            </swiper-slide>
                        </swiper>
                        <!-- 平铺 -->
                        <swiper v-if="form.content.style == 1" v-bind="{
                            slidesPerView: 1,
                            autoplay: {
                                'delay': (form.content.interval || 3) * 1000,
                            },
                            loop: true,
                            pagination: {
                                'el': '.swiper-pagination.decoration-swiper-pagination',
                                'clickable': false,
                            },
                            modules: swiperModules
                        }" class="scroll-wrapper">
                            <swiper-slide class="scroll-item" v-for="(item, index) in form.content.data" :key="index" :style="{width: form.content.width / 2 + 'px', height: (form.content.height ? form.content.height / 2 : 100) + 'px'}">
                                <image-wrapper
                                    v-bind="{src: item.image, width: form.content.width / 2 + 'px', height: (form.content.height ? form.content.height / 2 : 100) + 'px', radius: '10px'}" 
                                />
                            </swiper-slide>
                        </swiper>
                        <div class="swiper-pagination decoration-swiper-pagination"></div>
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
                            <el-form-item label="显示样式" label-position="top" :prop="'style'" required>
                                <el-radio-group v-model="form.content.style" fill="var(--main-color)">
                                    <el-radio v-for="style in StyleOption" :value="style.value" :key="style.value">{{style.label}}</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="图片宽高" label-position="top" :prop="'height'" :rules="
                                [
                                    { required: true, message: '请输入图片高度', trigger: 'blur' },
                                    { validator: (rule, value, callback) => {
                                        if (value < 200 || value > 350) {
                                            callback(new Error('图片高度范围是200px~350px'));
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
                                <p class="item-title-info" style="margin-bottom: 0;">高度范围：200px~350px</p>
                            </el-form-item>
                            <el-form-item label="切换时间(秒)" label-position="top" :prop="'interval'">
                                <el-input v-model="form.content.interval" style="width: 100px;"></el-input>
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
    
                                <div class="form-group-item s-flex ai-ct jc-bt" v-for="(item, index) in form.content.data" :key="index">
                                    <em class="iconfont icon-bars" style="font-size:20px"></em>
                                    <div class="group-content s-flex ai-fs jc-bt">
                                        <el-form-item class="not-required" label="" :prop="['data', index, 'image']" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                            <ImageUpload 
                                                :src="item.image"
                                                @material="handleOpenUpload(index)"
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
                                                    @select="handleOpenLink(index)"
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
                                                    :editable="false"
                                                    :default-time="[new Date(2000, 1, 1, 0, 0, 0), new Date(2000, 2, 1, 23, 59, 59)]"
                                                    :range-separator="item.time.length == 0 ? '长期' : '~'"
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
                                                        console.log(item.date_type, item.time)
                                                    }"
                                                    @clear="() => {
                                                        item.date_type = 1;
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
import { TempField, TempContentDataItemField, StyleOption, MaxItemLength } from '@/pages/decoration/app/dataField/HorizontalCarousel.js'
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay,Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
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
const swiperModules = [Autoplay, Pagination]
const swiperSlideWidth = (style, width) => {
    // return style == 1 ? width / 2 + 'px' : '233px'
    return width / 2 + 'px'
}

const handleChooseDragItem = () => {
    cns.$bus.emit('chooseDragItem', {temp_index: form.id})
}
// 通知打开选择图片弹窗
const handleOpenUpload = (index) => {
    cns.$bus.emit('openUploadDialog', {temp_index: form.id, form_index: index, show: true, dir_type: 1, multiple: false})
}
// 更新上传图片数据
const updateUploadComponentData = (res) => {
    form.content.data[res.form_index].image = res.file[0].file_path
}

// 添加图片数据
const handleClickAddImageData = () => {
    if (form.content.data.length >= MaxItemLength) return
    form.content.data.push(TempContentDataItemField())
}

// 通知打开选择路由弹窗
const handleOpenLink = (index) => {
    cns.$bus.emit('openLinkDialog', {temp_index: form.id, form_index: index, show: true})
}

// 更新选择路由数据
const updateLinkComponentData = (res) => {
    form.content.data[res.form_index].url = res.link
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
        border-radius: 10px;
    }
    .ad-swiper {
        width: 100%;
    }
    .cards-swiper {
        width: 100%;
        .swiper-slide {
            transition: transform 0.3s ease;
            overflow: hidden;
        }
        .swiper-slide-active {
            transform: scale(2.5,1);
            z-index: 1;
        }
        .swiper-slide-prev,
        .swiper-slide-next {
            transform: scale(0.9);
            opacity: 0.3;
        }
    }
}
.remove-btn {
    cursor: pointer
}
</style>