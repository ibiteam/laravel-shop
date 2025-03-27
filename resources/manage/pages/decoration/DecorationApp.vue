<template>
    <div class="decoration-app-container s-flex jc-bt">
        <tool-bar></tool-bar>
        <main class="decoration-app-main"></main>
        <!-- 375*667 -->
        <setting-bar></setting-bar>
    </div>
</template>

<script setup>
import ToolBar from './components/ToolBar.vue'
import SettingBar from './components/SettingBar.vue'
</script>

<style lang='scss' scoped>
.decoration-app-container{
    width: 100%;
    height: 100%;
    background: var(--page-bg-color);
    
}
</style>