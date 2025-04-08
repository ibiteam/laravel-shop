<template>
    <el-container class="decoration-layout-container">
        <el-aside class="decoration-layout-aside" width="300px">
            <slot name="aside-content"></slot>
        </el-aside>
        <el-main class="decoration-layout-main">
            <el-header class="decoration-layout-header s-flex ai-ct jc-bt" height="50px">
                <div class="header-left s-flex ai-ct">
                    <!-- <el-link :underline="false" class="header-left-back"><em class="iconfont icon-to_left"></em>返回</el-link>
                    <el-divider direction="vertical" /> -->
                    <span v-if="pageName" class="fs16 fw-b">{{ pageName }}装修</span>
                    <el-divider v-if="time" direction="vertical" />
                    <span v-if="time" class="co-999 fs12">发布时间：{{ time }}</span>
                </div>
                <div class="header-right">
                    <el-button text @click.stop="emit('pageSetting')">历史记录</el-button>
                    <el-button text @click.stop="emit('pageSetting')">页面配置</el-button>
                    <el-button @click.stop="emit('pageSave', {button_type: 1})" type="primary">保存草稿</el-button>
                    <el-button plain @click.stop="emit('pageSave', {button_type: 2})">预览</el-button>
                    <el-button @click.stop="emit('pageSave', {button_type: 3})" type="danger">发布</el-button>
                </div>
            </el-header>
            <div class="main-content-wrapper">
                <slot name="main-content"></slot>
            </div>
        </el-main>
    </el-container>
</template>

<script setup>
const emit = defineEmits(['pageSetting', 'pageSave'])

const props = defineProps({
    pageName: {
        type: String,
        default: ''
    },
    time: {
        type: [String, null],
        default: null
    }
})
</script>

<style lang='scss' scoped>
.decoration-layout-container {
    width: 100%;
    height: 100%;
    overflow: hidden;
    .decoration-layout-aside {
        position: relative;
        border-right: 1px solid #D8D8D8;
        box-sizing: border-box;
    }
    .decoration-layout-header {
        background: #fff;
        border-bottom: 1px solid #D8D8D8;
        box-sizing: border-box;
        user-select: none;
        .header-left,.header-left-back {
            color: #333;
        }
    }
    .decoration-layout-main{
        padding: 0;
        .main-content-wrapper {
            position: relative;
            height: calc(100% - 50px);
        }
    }
}
</style>