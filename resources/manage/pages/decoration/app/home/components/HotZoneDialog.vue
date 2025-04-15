<!-- <template>
    <el-drawer
        header-class="custom-drawer-header"
        body-class="custom-drawer-body"
        v-model="hotZoneDialog.drawer"
        :show-close="false"
        :append-to="targetElement"
        :modal="false"
        direction="rtl"
        size="100%"
    >
        <template #header>
            <div class="hot-zone-header s-flex ai-ct jc-bt">
                <p>{{ hotZoneDialog.title }}</p>
                <div>
                    <el-button type="primary" @click="handleSave(formRef)">保存</el-button>
                    <el-button @click="handleClose">返回</el-button>
                </div>
            </div>
        </template>
        <div class="hot-zone-body s-flex">
            <FreeZoneSelect
                style="margin: 0 auto;"
                :backgroundImage="background_image"
                :data="hotZoneDialog.data"
                @update="handleAreaUpdate"
            >
            </FreeZoneSelect>
            <setting-bar v-bind="{name: '热区'}" v-if="hotZoneDialog.drawer">
                <template #content>
                    <div class="setting-bar-item">
                        <el-form :model="hotZoneDialog" label-width="auto" ref="formRef">
                            <el-form-item :label="`热区${index+1}`" :prop="['data', index, 'url', 'name']" v-for="(area, index) in hotZoneDialog.data" :key="index"
                                :rules="{ required: true, message: '请输入热区链接', trigger: 'change' }"
                            >
                                <div class="s-flex ai-ct">
                                    <LinkInput
                                        :name="area.url.name"
                                        :value="area.url.value"
                                        @select="handleOpenLink(['areas', index, 'url'])"
                                        @input="(res) => {
                                            hotZoneDialog.data[index].url = res
                                        }"
                                        @clear="(res) => {
                                            hotZoneDialog.data[index].url = res
                                        }"
                                    />
                                    <em class="iconfont icon-shanchu" style="margin-left: 10px;" @click.stop="hotZoneDialog.data.splice(index, 1)" title="删除热区"></em>
                                </div>
                            </el-form-item>
                        </el-form>
                    </div>
                </template>
            </setting-bar>
        </div>
    </el-drawer>
</template> -->

<template>
    <teleport to="#decorationAppMain">
        <div class="hot-zone-dialog-wrapper" :style="{width: clientWidth + 'px',height: clientHeight+ 'px', right: computedStyle?.getPropertyValue('padding-right'), bottom: computedStyle?.getPropertyValue('padding-bottom')}">
            <div class="hot-zone-header s-flex ai-ct jc-bt">
                <p>{{ hotZoneDialog.title }}</p>
                <p>编辑热区</p>
                <div>
                    <el-button type="primary" @click="handleSave(formRef)">保存</el-button>
                    <el-button @click="handleClose">返回</el-button>
                </div>
            </div>
            <div class="hot-zone-body s-flex">
                <FreeZoneSelect
                    style="margin: 0 auto;"
                    :backgroundImage="background_image"
                    :data="hotZoneDialog.data"
                    @update="handleAreaUpdate"
                >
                </FreeZoneSelect>
                <setting-bar v-bind="{name: '热区'}" v-if="hotZoneDialog.drawer">
                    <template #content>
                        <div class="setting-bar-item">
                            <el-form :model="hotZoneDialog" label-width="auto" ref="formRef">
                                <el-form-item :label="`热区${index+1}`" :prop="['data', index, 'url', 'name']" v-for="(area, index) in hotZoneDialog.data" :key="index"
                                    :rules="{ required: true, message: '请输入热区链接', trigger: 'change' }"
                                >
                                    <div class="s-flex ai-ct">
                                        <LinkInput
                                            :name="area.url?.name"
                                            :value="area.url?.value"
                                            @select="handleOpenLink([index, 'url'])"
                                            @input="(res) => {
                                                hotZoneDialog.data[index].url = res
                                            }"
                                            @clear="(res) => {
                                                hotZoneDialog.data[index].url = res
                                            }"
                                        />
                                        <em class="iconfont icon-shanchu" style="margin-left: 10px;" @click.stop="hotZoneDialog.data.splice(index, 1)" title="删除热区"></em>
                                    </div>
                                </el-form-item>
                            </el-form>
                        </div>
                    </template>
                </setting-bar>
            </div>
        </div>
    </teleport>
</template>

<script setup>
import { ref, reactive, watch, defineEmits, getCurrentInstance, onMounted, nextTick } from 'vue'
import FreeZoneSelect from '@/pages/decoration/components/FreeZoneSelect.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import LinkInput from '@/pages/decoration/components/LinkInput.vue'
import { updateNested } from '@/pages/decoration/utils/common.js'

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    show: {
        type: Boolean,
        default: true
    },
    title: {
        type: String,
        default: '编辑热区'
    },
    data: {
        type: Array,
        default: () => []
    },
    temp_index: {
        type: [Number, String],
        default: ''
    },
    background_image: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['save', 'close'])

const hotZoneDialog = reactive({
    drawer: false,
    title: '',
    data: []
})
const formRef = ref(null)
const clientWidth = ref(0)
const clientHeight = ref(0)
const computedStyle = ref(null)

// 通知打开选择路由弹窗
const handleOpenLink = (keys) => {
    cns.$bus.emit('openLinkDialog', {temp_index: props.temp_index, keys, show: true})
}


// 获取选区数据
const handleAreaUpdate = (data) => {
    console.log(data)
}

const handleSave = (formEl) => {
    if (!formEl) return
    if (hotZoneDialog.data.length === 0) {
        cns.$message.warning('请设置热区')
        return
    }
    formEl.validate((valid) => {
        if (valid) {
            emit('save', hotZoneDialog.data)
            hotZoneDialog.drawer = false
        }
    })
}

const handleClose = (done) => {
    hotZoneDialog.drawer = false
    emit('close')
    // done()
}

// 更新选择路由数据
const updateLinkComponentData = (res) => {
    hotZoneDialog.data = updateNested(hotZoneDialog.data, res.keys, res.link)
}

defineExpose({
    updateLinkComponentData
})

watch(() => props, (newVal) => {
    if (newVal) {
        hotZoneDialog.drawer = newVal.show
        hotZoneDialog.title = newVal.title
        hotZoneDialog.data = newVal.data
    }
}, {
    immediate: true,
    deep: true
})

onMounted(() => {
    nextTick(() => {
        const element = document.querySelector('.decoration-layout-container')
        clientWidth.value = element.clientWidth
        clientHeight.value = element.clientHeight
        computedStyle.value = window.getComputedStyle(document.getElementById('shopLayoutView'))
    })
})
</script>

<style lang='scss'>
// .custom-drawer-header {
//     height: 50px;
//     padding-bottom: 0;
//     padding-top: 0;
//     margin-bottom: 0;
//     color: #333;
// }
// .custom-drawer-body {
//     padding: 0;
// }
</style>
<style lang='scss' scoped>
.hot-zone-dialog-wrapper{
    overflow: hidden;
    position: fixed;
    z-index: 1000;
    .hot-zone-header {
        height: 50px;
        padding-bottom: 0;
        padding-top: 0;
        margin-bottom: 0;
        color: #333;
        background-color: #fff;
    }
    .hot-zone-body {
        width: 100%;
        height: 100%;
        padding: 20px 420px 20px 20px;
        background: var(--page-bg-color);
        box-sizing: border-box;
        overflow: hidden;
    }
}
</style>