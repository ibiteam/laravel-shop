<template>
    <el-header>
        <el-form :inline="true" :model="model" class="search-form">
            <slot></slot>
        </el-form>
    </el-header>
</template>

<script setup lang="ts">
import { defineProps } from 'vue'

defineProps<{
    model: Record<string, any>
}>()
</script>

<style scoped lang="scss">
.search-form {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 10px;

    :deep(.el-select),
    :deep(.el-input) {
        width: 200px;
    }
}
</style>
