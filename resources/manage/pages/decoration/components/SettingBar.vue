<template>
    <aside class="setting-bar-wrapper s-flex">
        <div class="setting-bar-header s-flex ai-ct jc-bt">
            <p class="fs16 fw-b">{{ name }}</p>
            <el-radio-group v-model="formType">
                <el-radio-button label="内容" :value="0" />
                <el-radio-button label="样式" :value="1" />
            </el-radio-group>
        </div>
        <div class="setting-bar-content">
            <slot name="content" :type="formType"></slot>
        </div>
        <div class="setting-bar-footer s-flex ai-ct jc-ct">
            <el-button>取消</el-button>
            <el-button type="primary">保存</el-button>
        </div>
    </aside>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    name: {
        type: String,
        default: ''
    }
})

const formType = ref(0)

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
}
</style>
<style lang='scss' scoped>
.setting-bar-wrapper{
    width: 400px;
    height: calc(100% - 50px);
    overflow: hidden;
    background-color: #fff;
    flex-direction: column;
    position: fixed;
    right: 0;
    bottom: 0;
    z-index: 2;
    .setting-bar-content{
        flex: 1;
        overflow: hidden auto;
    }
}
</style>