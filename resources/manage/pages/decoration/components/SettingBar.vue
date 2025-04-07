<template>
    <!-- <el-drawer
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
    > -->
        <aside class="setting-bar-wrapper s-flex" :style="{height: (clientHeight - 50)+ 'px', right: computedStyle.getPropertyValue('padding-right'), bottom: computedStyle.getPropertyValue('padding-bottom')}">
            <div class="setting-bar-header s-flex ai-ct jc-bt" v-if="name">
                <p class="fs16 fw-b">{{ name }}</p>
                <!-- <el-radio-group v-model="formType" v-if="show_radio">
                    <el-radio-button label="内容" :value="0" />
                    <el-radio-button label="样式" :value="1" />
                </el-radio-group> -->
            </div>
            <div class="setting-bar-content">
                <slot name="content" :type="formType"></slot>
            </div>
            <!-- <div class="setting-bar-footer s-flex ai-ct jc-ct">
                <el-button @click="handleCancle">取消</el-button>
                <el-button type="primary" @click="emit('submit')">保存</el-button>
            </div> -->
        </aside>
    <!-- </el-drawer> -->
</template>

<script setup>
import { ref, getCurrentInstance, defineEmits, onMounted, nextTick } from 'vue'

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
const computedStyle = window.getComputedStyle(document.getElementById('shopLayoutView'))

const emit = defineEmits(['close', 'submit'])
const handleCancle = () => {
    emit('close')
    // cns.$bus.emit('chooseDragItem', {temp_index: ''})
}

onMounted(() => {
    nextTick(() => {
        const element = document.querySelector('.decoration-layout-container')
        clientHeight.value = element.clientHeight
    })
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
        padding: 16px 20px 16px 0;
        margin-bottom: 15px;
        border-radius: 10px;
        background-color: #f9f9f9;
        box-sizing: border-box;
        position: relative;
        .icon-Bars {
            padding: 10px;
            cursor: move;
            font-size: 20px;
        }
        .group-content {
            width: calc(100% - 40px);
            position: relative;
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
    .setting-bar-content{
        flex: 1;
        overflow: hidden auto;
    }
}
</style>