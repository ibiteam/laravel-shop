<template>
    <el-header>
        <el-form :inline="true" :model="model" class="search-form" :label-width="`${labelWidth}px`">
            <slot></slot>
        </el-form>
    </el-header>
</template>

<script setup lang="ts">
import { defineProps, withDefaults } from 'vue';

withDefaults(defineProps<{
    model: Record<string, any>;
    labelWidth?: number; // 注意这里使用可选属性
}>(), {
    labelWidth: 100
});
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
