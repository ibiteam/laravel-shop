<template>
    <aside class="setting-bar-wrapper s-flex" :style="{height: (clientHeight - 53)+ 'px', right: computedStyle?.getPropertyValue('padding-right'), bottom: computedStyle?.getPropertyValue('padding-bottom')}">
        <div class="setting-bar-header s-flex ai-ct jc-bt" v-if="name">
            <p class="fs16 fw-b">{{ name }}</p>
        </div>
        <div class="setting-bar-content">
            <slot name="content" :type="formType"></slot>
        </div>
    </aside>
</template>

<script setup>
import { ref, getCurrentInstance, defineEmits, onMounted, nextTick, onUnmounted } from 'vue'

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
const clientHeight = ref(0)
const computedStyle = ref(null)

const emit = defineEmits(['close', 'submit'])
const handleCancle = () => {
    emit('close')
    // cns.$bus.emit('chooseDragItem', {temp_index: ''})
}

const resizeHandler = () => {
    nextTick(() => {
        const element = document.querySelector('.decoration-layout-container')
        clientHeight.value = element.clientHeight
        computedStyle.value = window.getComputedStyle(document.getElementById('shopLayoutView'))
    })
}

onMounted(() => {
    resizeHandler()
    window.addEventListener('resize', resizeHandler)
})

onUnmounted(() => {
    window.removeEventListener('resize', resizeHandler)
})

</script>
<style lang="scss">
.setting-bar-item,
.setting-bar-header,
.setting-bar-footer {
    width: 100%;
    padding: 20px;
    box-sizing: border-box;
}
.setting-bar-header {
    border-top: 4px solid var(--page-bg-color);
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
        margin-bottom: 12px;
    }
    .item-title-info {
        color: #999;
        line-height: 24px;
        font-size: 12px;
        margin-bottom: 12px;
    }
    .form-group-item {
        width: 100%;
        padding: 16px 20px 0 0;
        margin-bottom: 15px;
        border-radius: 10px;
        background-color: #f9f9f9;
        box-sizing: border-box;
        position: relative;
        .icon-drag {
            padding: 10px;
            cursor: move;
            font-size: 20px;
            color: #999;
        }
        .group-content {
            width: calc(100% - 40px);
            position: relative;
        }
    }
    // 长期时间
    .time-long {
        .el-range-input{
            display: none;
            width: 30%;
            margin: 0 4.5%;
        }
    }
    // 范围时间
    .time-range {
        display: block!important;
        width: 100%;
        position: relative;
        .el-range-input {
            display: block;
            height: 20px;
            width: calc(100% - 40px);
            margin: 0 10px 0 20px;
        }
        .el-range-separator {
            display: none;
        }
        .el-range__close-icon {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 10px;
            margin: auto 0;
        }
    }
    .not-required{
        .el-form-item__label:before {
            display: none;
        }
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
    position: fixed;
    // right: 16px;
    // bottom: 16px;
    z-index: 3;
    user-select: none;
    .setting-bar-content{
        flex: 1;
        overflow: hidden auto;
    }
}
</style>