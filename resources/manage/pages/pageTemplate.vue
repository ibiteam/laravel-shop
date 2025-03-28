<template>
    <div class="manage-public-wrap">
        <div class="manage-public-cont">
            <div class="manage-public-title">块级内容标题</div>
            <div>
                内容区域内容区域
            </div>
        </div>
        <div class="manage-public-cont">
            <div class="manage-public-title">块级内容标题</div>
            <div>
                内容区域内容区域
            </div>
        </div>
    </div>
</template>

<script setup>
    import {ref, onMounted, getCurrentInstance } from 'vue'
    import { useRouter , useRoute } from 'vue-router'
    const router = useRouter()
    const route = useRoute()
    const cns = getCurrentInstance().appContext.config.globalProperties

    onMounted( () => {
        // 初始化加载数据
    })
</script>

<style scoped lang="scss">

</style>
