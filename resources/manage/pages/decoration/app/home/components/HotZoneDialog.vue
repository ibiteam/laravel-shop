<template>
    <el-drawer
        header-class="custom-drawer-header"
        v-model="hotZoneDialog.drawer"
        :title="hotZoneDialog.title"
        direction="rtl"
        size="100%"
        :before-close="handleClose"
    >
        <div class="hot-zone-body s-flex">
            <FreeZoneSelect v-bind="{width: '750px', }"></FreeZoneSelect>
            <div class="setting-wrapper flex-1">
                <p class="fs18 fw-b MB20">图片热区</p>
                <el-alert class="MB20" title="框选热区范围，双击设置热区信息" type="warning" show-icon :closable="false"/>
            </div>
        </div>
    </el-drawer>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, nextTick, getCurrentInstance, watch } from 'vue'
import FreeZoneSelect from '@/pages/decoration/components/FreeZoneSelect.vue'


const props = defineProps({
    show: {
        type: Boolean,
        default: true
    },
    title: {
        type: String,
        default: '编辑热区'
    },
})

const hotZoneDialog = reactive({
    drawer: false,
    title: '',
})

const handleClose = (done) => {
    hotZoneDialog.drawer = false
    done()
}

watch(() => props, (newVal) => {
    if (newVal) {
        hotZoneDialog.drawer = newVal.show
        hotZoneDialog.title = newVal.title
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
    }
    .MB20 {
        margin-bottom: 20px;
    }
}
</style>