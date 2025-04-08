<template>
    <setting-bar>
        <template #content>
            <el-form :model="appWebsiteData" label-width="auto" label-position="top" ref="appWebsiteformRef">
                <div class="setting-bar-item">
                    <div class="item-title">基础设置</div>
                    <el-form-item label="名称" >
                        <el-input disabled v-model="appWebsiteData.name"></el-input>
                    </el-form-item>
                </div>
                <div class="setting-bar-item">
                    <div class="item-title">TDK设置</div>
                    <el-form-item label="标题" :prop="['title']" :rules="{required: true, message: '请填写标题', trigger: 'blur'}">
                        <el-input v-model="appWebsiteData.title"></el-input>
                    </el-form-item>
                    <el-form-item label="关键词" :prop="['keywords']" :rules="{required: true, message: '请填写关键词', trigger: 'blur'}">
                        <el-input v-model="appWebsiteData.keywords"></el-input>
                    </el-form-item>
                    <el-form-item label="描述" :prop="['description']" :rules="{required: true, message: '请填写描述', trigger: 'blur'}">
                        <el-input v-model="appWebsiteData.description"></el-input>
                    </el-form-item>
                </div>
            </el-form>
            <el-form :model="popUp.content" label-width="auto" label-position="top" v-if="popUp.content">
                <div class="setting-bar-item">
                    <div class="item-title">弹屏广告</div>
                    <el-form-item label="图片">
                        <div>
                            <ImageUpload 
                                :src="popUp.content.image"
                                @material="() => {
                                    handleOpenUpload(['image'], 'danping_advertisement')
                                }"
                                @local="(image) => {
                                    popUp.content.image = image
                                }"
                                @remove="() => {
                                    popUp.content.image = ''
                                }" 
                            />
                            <p class="item-title-info" style="margin-bottom: 0;">建议尺寸：750 *750</p>
                        </div>
                    </el-form-item>
                    <el-form-item label="链接">
                        <LinkInput
                            style="width: 70%;"
                            :name="popUp.content.url.name"
                            :value="popUp.content.url.value"
                            @select="handleOpenLink(['url'], 'danping_advertisement')"
                            @input="(res) => {
                                popUp.content.url = res
                            }"
                            @clear="(res) => {
                                popUp.content.url = res
                            }"
                        />
                    </el-form-item>
                    <el-form-item label="有效期">
                        <div style="width: 70%;">
                            <el-date-picker
                                :class="popUp.content.time?.length == 0 ? 'time-long' : 'time-range'"
                                v-model="popUp.content.time"
                                value-format="YYYY-MM-DD HH:mm:ss"
                                type="datetimerange"
                                size="large"
                                :editable="false"
                                :default-time="[new Date(2000, 1, 1, 0, 0, 0), new Date(2000, 2, 1, 23, 59, 59)]"
                                :range-separator="popUp.content.time?.length == 0 ? '长期' : '~'"
                                :disabled-date="(time) => {
                                    const today = new Date();
                                    today.setHours(0, 0, 0, 0);
                                    return time.getTime() < today.getTime();
                                }"
                                @change="() => {
                                    if (popUp.content.time) {
                                        popUp.content.date_type = 0
                                    } else {
                                        popUp.content.date_type = 1;
                                        popUp.content.time = []
                                    }
                                }"
                                @clear="() => {
                                    popUp.content.date_type = 1;
                                    popUp.content.time = [];
                                }"
                            >
                            </el-date-picker>
                        </div>
                    </el-form-item>
                    <el-form-item label="是否显示" style="margin-bottom: 0;">
                        <el-switch v-model="popUp.content.is_show" :active-value="1" :inactive-value="0"/>
                    </el-form-item>
                </div>
            </el-form>
            <el-form :model="suspended.content" label-width="auto" label-position="top" v-if="suspended.content">
                <div class="setting-bar-item">
                    <div class="item-title">悬浮广告</div>
                    <el-form-item label="图片">
                        <div>
                            <ImageUpload 
                                :src="suspended.content.image"
                                @material="() => {
                                    handleOpenUpload(['image'], 'suspended_advertisement')
                                }"
                                @local="(image) => {
                                    suspended.content.image = image
                                }"
                                @remove="() => {
                                    suspended.content.image = ''
                                }" 
                            />
                            <p class="item-title-info" style="margin-bottom: 0;">建议尺寸：200 *200</p>
                        </div>
                    </el-form-item>
                    <el-form-item label="描述">
                        <el-input v-model="suspended.content.describe"></el-input>
                    </el-form-item>
                    <el-form-item label="链接">
                        <LinkInput
                            style="width: 70%;"
                            :name="suspended.content.url.name"
                            :value="suspended.content.url.value"
                            @select="handleOpenLink(['url'], 'suspended_advertisement')"
                            @input="(res) => {
                                suspended.content.url = res
                            }"
                            @clear="(res) => {
                                suspended.content.url = res
                            }"
                        />
                    </el-form-item>
                    <el-form-item label="有效期">
                        <div style="width: 70%;">
                            <el-date-picker
                                :class="suspended.content.time?.length == 0 ? 'time-long' : 'time-range'"
                                v-model="suspended.content.time"
                                value-format="YYYY-MM-DD HH:mm:ss"
                                type="datetimerange"
                                size="large"
                                :editable="false"
                                :default-time="[new Date(2000, 1, 1, 0, 0, 0), new Date(2000, 2, 1, 23, 59, 59)]"
                                :range-separator="suspended.content.time?.length == 0 ? '长期' : '~'"
                                :disabled-date="(time) => {
                                    const today = new Date();
                                    today.setHours(0, 0, 0, 0);
                                    return time.getTime() < today.getTime();
                                }"
                                @change="() => {
                                    if (suspended.content.time) {
                                        suspended.content.date_type = 0
                                    } else {
                                        suspended.content.date_type = 1;
                                        suspended.content.time = []
                                    }
                                }"
                                @clear="() => {
                                    suspended.content.date_type = 1;
                                    suspended.content.time = [];
                                }"
                            >
                            </el-date-picker>
                        </div>
                    </el-form-item>
                    <el-form-item label="是否显示" style="margin-bottom: 0;">
                        <el-switch v-model="suspended.content.is_show" :active-value="1" :inactive-value="0"/>
                    </el-form-item>
                </div>
            </el-form>
        </template>
    </setting-bar>
</template>

<script setup>
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import ImageUpload from '@/pages/decoration/components/ImageUpload.vue'
import LinkInput from '@/pages/decoration/components/LinkInput.vue'
import { ref, reactive, watch, getCurrentInstance, defineExpose } from 'vue'
import { updateNested } from '@/pages/decoration/utils/common.js'

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    app_website_data: {
        type: [Object, null],
        default: () => {
            return null
        }
    },
    // 弹屏广告
    danping_advertisement: {
        type: [Object, null],
        default: () => {
            return null
        }
    },
    // 悬浮广告
    suspended_advertisement: {
        type: [Object, null],
        default: () => {
            return null
        }
    },
})
// 网站设置
const appWebsiteData = reactive({})
// 弹屏广告设置
const popUp = reactive({})
// 悬浮广告设置
const suspended = reactive({})


// 通知打开选择图片弹窗
const handleOpenUpload = (keys, not_for_data) => {
    cns.$bus.emit('openUploadDialog', {not_for_data, keys, show: true, dir_type: 1, multiple: false})
}
// 更新上传图片数据
const updateUploadComponentData = (res) => {
    const { keys, not_for_data, file } = res
    if (not_for_data == 'danping_advertisement') {
        // 弹屏
        popUp.content = updateNested(popUp.content, keys, file[0].file_path)
    } else if (not_for_data == 'suspended_advertisement') {
        // 悬浮
        suspended.content = updateNested(suspended.content, keys, file[0].file_path)
    }
}

// 通知打开选择路由弹窗
const handleOpenLink = (keys, not_for_data) => {
    cns.$bus.emit('openLinkDialog', {not_for_data, keys, show: true})
}

// 更新选择路由数据
const updateLinkComponentData = (res) => {
    const { keys, not_for_data, link } = res
    if (not_for_data == 'danping_advertisement') {
        // 弹屏
        popUp.content = updateNested(popUp.content, keys, link)
    } else if (not_for_data == 'suspended_advertisement') {
        // 悬浮
        suspended.content = updateNested(suspended.content, keys, link)
    }
}

defineExpose({
    getComponentData() {
        return {
            app_website_data: appWebsiteData,
            danping_advertisement: popUp,
            suspended_advertisement: suspended
        }
    },
    updateUploadComponentData,
    updateLinkComponentData
})

watch(() => props, (newValue) => {
    if (newValue.app_website_data) {
        Object.keys(newValue.app_website_data).forEach(key => {
            appWebsiteData[key] = newValue.app_website_data[key]
        })
    }
    if (newValue.danping_advertisement) {
        Object.keys(newValue.danping_advertisement).forEach(key => {
            popUp[key] = newValue.danping_advertisement[key]
        })
    }
    if (newValue.suspended_advertisement) {
        Object.keys(newValue.suspended_advertisement).forEach(key => {
            suspended[key] = newValue.suspended_advertisement[key]
        })
    }
},{
    immediate: true,
    deep: true,
})
</script>

<style lang='scss' scoped>
:deep(.el-date-editor.el-input__wrapper){
    flex-grow: unset;
    width: 100%;
}
</style>