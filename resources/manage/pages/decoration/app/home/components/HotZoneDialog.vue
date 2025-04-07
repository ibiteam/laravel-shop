<template>
    <el-drawer
        header-class="custom-drawer-header"
        body-class="custom-drawer-body"
        v-model="hotZoneDialog.drawer"
        :show-close="false"
        :append-to-body="true"
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
                backgroundImage="https://cdn.toodudu.com/2025/02/24/WsUjqeUNqgzY0wyHm2hvEc7aBPXamQ3t080ehmUe.jpg"
                :data="hotZoneDialog.data"
                @update="handleAreaUpdate"
            >
            </FreeZoneSelect>
            <setting-bar v-bind="{name: '热区'}" v-if="hotZoneDialog.drawer">
                <template #content>
                    <div class="setting-bar-item">
                        <el-form :model="hotZoneDialog" label-width="auto" ref="formRef">
                            <el-form-item :label="`热区${index+1}`" :prop="['data', index, 'url', 'name']" v-for="(area, index) in hotZoneDialog.data" :key="index"
                                :rules="{ required: true, message: '请输入热区链接', trigger: 'blur' }"
                            >
                                <div class="s-flex ai-ct">
                                    <LinkInput
                                        :name="area.url.name"
                                        :value="area.url.value"
                                        @clear="(res) => hotZoneDialog.data[index].url = res"
                                        @select=""
                                        @input=""
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
</template>

<script setup>
import { ref, reactive, watch, defineEmits, getCurrentInstance } from 'vue'
import FreeZoneSelect from '@/pages/decoration/components/FreeZoneSelect.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import LinkInput from '@/pages/decoration/components/LinkInput.vue'

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
        default: () => [{
            x: 89,
            y: 86,
            width: 100,
            height: 100,
            url: ''
        }]
    }
})

const emit = defineEmits(['save', 'close'])

const hotZoneDialog = reactive({
    drawer: false,
    title: '',
    data: []
})
const formRef = ref(null)


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

</script>

<style lang='scss'>
.custom-drawer-header {
    height: 50px;
    padding-bottom: 0;
    padding-top: 0;
    margin-bottom: 0;
    color: #333;
}
.custom-drawer-body {
    padding: 0;
}
</style>
<style lang='scss' scoped>
.hot-zone-body {
    width: 100%;
    height: 100%;
    padding: 20px 420px 20px 20px;
    background: var(--page-bg-color);
    box-sizing: border-box;
    overflow: hidden;
}
</style>