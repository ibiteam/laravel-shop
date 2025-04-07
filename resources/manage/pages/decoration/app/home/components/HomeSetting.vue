<template>
    <setting-bar>
        <template #content>
            <el-form :model="form" label-width="auto" label-position="top" ref="appWebsiteformRef">
                <div class="setting-bar-item">
                    <div class="item-title">基础设置</div>
                    <el-form-item label="名称" >
                        <el-input disabled v-model="form.name"></el-input>
                    </el-form-item>
                </div>
                <div class="setting-bar-item">
                    <div class="item-title">TDK设置</div>
                    <el-form-item label="标题" :prop="['title']" :rules="{required: true, message: '请填写标题', trigger: 'blur'}">
                        <el-input v-model="form.title"></el-input>
                    </el-form-item>
                    <el-form-item label="关键词" :prop="['keywords']" :rules="{required: true, message: '请填写关键词', trigger: 'blur'}">
                        <el-input v-model="form.keywords"></el-input>
                    </el-form-item>
                    <el-form-item label="描述" :prop="['description']" :rules="{required: true, message: '请填写描述', trigger: 'blur'}">
                        <el-input v-model="form.description"></el-input>
                    </el-form-item>
                </div>
            </el-form>
            <!-- <el-form label-width="auto" label-position="top">
                <div class="setting-bar-item">
                    <div class="item-title">弹屏广告</div>
                    <el-form-item label="图片">
                        <div>
                            <ImageUpload @material="handleOpenUpload(index)" @local="(image) => { item.image = image }" @remove="item.image = ''" />
                            <p class="item-title-info" style="margin-bottom: 0;">建议尺寸：750 *750</p>
                        </div>
                    </el-form-item>
                    <el-form-item label="链接">
                        <LinkInput
                            :name="item.url.name"
                            :value="item.url.value"
                            @clear="(res) => form.content.data[index].url = res "
                            @select=""
                            @input="(res) => item.url = res"
                        />
                    </el-form-item>
                    <el-form-item label="有效期">
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
                            @clear="() => {
                                item.time = [];
                            }"
                        >
                        </el-date-picker>
                    </el-form-item>
                    <el-form-item label="是否显示" style="margin-bottom: 0;">
                        <el-switch v-model="is_show" :active-value="1" :inactive-value="0"/>
                    </el-form-item>
                </div>
            </el-form>
            <el-form label-width="auto" label-position="top">
                <div class="setting-bar-item">
                    <div class="item-title">悬浮广告</div>
                    <el-form-item label="图片">
                        <div>
                            <ImageUpload @material="handleOpenUpload(index)" @local="(image) => { item.image = image }" @remove="item.image = ''" />
                            <p class="item-title-info" style="margin-bottom: 0;">建议尺寸：200 *200</p>
                        </div>
                    </el-form-item>
                    <el-form-item label="描述">
                        <el-input></el-input>
                    </el-form-item>
                    <el-form-item label="链接">
                        <LinkInput
                            :name="item.url.name"
                            :value="item.url.value"
                            @clear="(res) => form.content.data[index].url = res "
                            @select=""
                            @input="(res) => item.url = res"
                        />
                    </el-form-item>
                    <el-form-item label="有效期">
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
                            @clear="() => {
                                item.time = [];
                            }"
                        >
                        </el-date-picker>
                    </el-form-item>
                    <el-form-item label="是否显示" style="margin-bottom: 0;">
                        <el-switch v-model="is_show" :active-value="1" :inactive-value="0"/>
                    </el-form-item>
                </div>
            </el-form> -->
        </template>
    </setting-bar>
</template>

<script setup>
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import ImageUpload from '@/pages/decoration/components/ImageUpload.vue'
import LinkInput from '@/pages/decoration/components/LinkInput.vue'
import { ref, reactive, watch, getCurrentInstance, defineExpose } from 'vue'

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    data: {
        type: [Object, null],
        default: () => {
            return null
        }
    },

})

const form = reactive({
    name: '',
    title: '',
    keywords: '',
    description: '',
    data: []
})

watch(() => props, (newValue) => {
    if (newValue.data) {
        form.name = newValue.data?.name
        form.title = newValue.data?.title
        form.keywords = newValue.data?.keywords
        form.description = newValue.data?.description
    }
},{
    immediate: true,
    deep: true,
})
</script>

<style lang='scss' scoped>
</style>