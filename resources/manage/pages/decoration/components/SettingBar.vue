<template>
    <el-drawer
        class="setting-drawer"
        body-class="setting-drawer-body"
        modal-class="setting-drawer-modal"
        direction="rtl"
        :model-value="true"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :show-close="false"
        :with-header="false"
        :modal="false"
        append-to=".decoration-layout-main"
        size="400px"
        :before-close="handleCancle"
    >
        <aside class="setting-bar-wrapper s-flex">
            <div class="setting-bar-header s-flex ai-ct jc-bt">
                <p class="fs16 fw-b">{{ name }}</p>
                <el-radio-group v-model="formType" v-if="show_radio">
                    <el-radio-button label="内容" :value="0" />
                    <el-radio-button label="样式" :value="1" />
                </el-radio-group>
            </div>
            <div class="setting-bar-content">
                <slot name="content" :type="formType"></slot>
            </div>
            <div class="setting-bar-footer s-flex ai-ct jc-ct">
                <el-button @click="handleCancle">取消</el-button>
                <el-button type="primary">保存</el-button>
            </div>
        </aside>
    </el-drawer>
</template>

<script setup>
import { ref, getCurrentInstance, defineEmits } from 'vue'

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    name: {
        type: String,
        default: ''
    },
    show_radio: {
        type: Boolean,
        default: true
    }
})

const formType = ref(0)

const emit = defineEmits(['close'])
const handleCancle = () => {
    emit('close')
    // cns.$bus.emit('chooseDragItem', {temp_index: ''})
}

</script>
<style lang="scss">
.setting-bar-item,
.setting-bar-header,
.setting-bar-footer {
    width: 100%;
    padding: 20px;
    box-sizing: border-box;
}
.setting-bar-item {
    border-top: 4px solid var(--page-bg-color);
    font-size: 14px;
    color: #333;
    .item-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: bold;
    }
    .item-title-info {
        color: #999;
        line-height: 30px;
        font-size: 12px;
    }
}
.setting-drawer{
    width: 400px;
    height: calc(100% - 50px)!important;
    position: fixed;
    top: auto!important;
    right: 0;
    bottom: 0!important;
    box-shadow: none;
}
.setting-drawer-body {
    padding: 0!important;
}
.setting-drawer-modal {
    width: 400px;
    height: calc(100% - 50px);
    top: auto!important;
    right: 0!important;
    bottom: 0!important;
    inset: auto!important;
}
</style>

<style lang='scss' scoped>
.setting-bar-wrapper{
    width: 400px;
    height: 100%;
    overflow: hidden;
    background-color: #fff;
    flex-direction: column;
    position: absolute;
    right: 0;
    bottom: 0;
    z-index: 3;
    .setting-bar-content{
        flex: 1;
        overflow: hidden auto;
    }
}
</style>