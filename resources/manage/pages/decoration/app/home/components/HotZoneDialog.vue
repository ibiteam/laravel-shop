<template>
    <el-drawer
        header-class="custom-drawer-header"
        v-model="hotZoneDialog.drawer"
        :title="hotZoneDialog.title"
        :append-to-body="true"
        direction="rtl"
        size="100%"
        :before-close="handleClose"
    >
        <div class="hot-zone-body s-flex">
            <FreeZoneSelect
                backgroundImage="https://cdn.toodudu.com/2025/02/24/WsUjqeUNqgzY0wyHm2hvEc7aBPXamQ3t080ehmUe.jpg"
                :data="hotZoneDialog.data"
                @update="handleAreaUpdate"
            >
            </FreeZoneSelect>
            <div class="setting-wrapper flex-1">
                <p class="fs18 fw-b MB20">图片热区</p>
                <el-alert class="MB20" title="框选热区范围，双击设置热区信息" type="warning" show-icon :closable="false"/>
                <el-form :model="hotZoneDialog" label-width="auto" ref="formRef">
                    <el-form-item :label="`热区${index+1}`" :prop="'data.' + index + '.url'" v-for="(area, index) in hotZoneDialog.data" :key="index"
                        :rules="{ required: true, message: '请输入热区链接', trigger: 'blur' }"
                    >
                        <el-input v-model="area.url" style="width: 240px">
                            <template #suffix>
                                <Icon name="link-o" />
                            </template>
                        </el-input>
                        <Icon name="delete-o" class="remove-btn" @click.stop="hotZoneDialog.data.splice(index, 1)"/>
                    </el-form-item>
                    <el-form-item>
                        <div class="s-flex jc-fe" style="width: 100%;">
                            <el-button type="primary" @click="handleSave(formRef)">保存</el-button>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </el-drawer>
</template>

<script setup>
import { ref, reactive, watch, defineEmits, getCurrentInstance } from 'vue'
import FreeZoneSelect from '@/pages/decoration/components/FreeZoneSelect.vue'
import { Icon } from 'vant'

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
    done()
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
    padding-bottom: 20px;
    margin-bottom: 0;
    color: #333;
    border-bottom: 1px solid #d8d8d8;
}
</style>
<style lang='scss' scoped>
.hot-zone-body {
    height: 100%;
    width: 100%;
    padding: 0 100px;
    overflow: hidden;
    .setting-wrapper {
        padding-left: 50px;
        .remove-btn {
            margin-left: 10px;
            cursor: pointer;
        }
    }
    .MB20 {
        margin-bottom: 20px;
    }
}
</style>