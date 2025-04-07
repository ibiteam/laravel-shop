<template>
    <el-container class="decoration-layout-container">
        <el-header class="decoration-layout-header s-flex ai-ct jc-bt" height="50px">
            <div class="header-left s-flex ai-ct">
                <el-link :underline="false" class="header-left-back"><em class="iconfont icon-to_left"></em>返回</el-link>
                <el-divider direction="vertical" />
                <span>当前页面：首页</span>
            </div>
            <div class="header-right">
                <el-button plain @click="emit('pageSetting')">页面配置</el-button>
                <el-button plain>预览</el-button>
                <el-button plain>保存</el-button>
                <el-button plain>保存并关闭</el-button>
            </div>
        </el-header>
        <el-main class="decoration-layout-main">
            <slot name="main-content"></slot>
        </el-main>
    </el-container>
</template>

<script setup>
const emit = defineEmits(['pageSetting'])
</script>

<style lang='scss' scoped>
.decoration-layout-container {
    width: 100%;
    height: 100%;
    overflow: hidden;
    .decoration-layout-header {
        background: var(--main-color);
        .header-left,.header-left-back {
            color: #fff;
        }
    }
    .decoration-layout-main{
        height: calc(100% - 50px);
        padding: 0;
        position: relative;
    }
}
</style>