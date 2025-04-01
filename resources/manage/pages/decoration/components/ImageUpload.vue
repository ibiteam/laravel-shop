<template>
    <el-image v-if="src" :src="src" :fit="fit" class="upload-btn">
    </el-image>
    <div v-else class="upload-btn s-flex ai-ct jc-ct">
        <Icon name="plus" size="32" color="#999"/>
    </div>
</template>

<script setup>
import { Icon } from 'vant';
const props = defineProps({
    src: {
        type: String,
        default: '',
    },
    fit: {
        type: String,
        default: 'fill', // cover, contain, fill, none, scale-down
    }
})

</script>

<style lang='scss' scoped>
.upload-btn {
    width: 64px;
    height: 64px;
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
    border: 1px solid #ddd;
    box-sizing: border-box;
}
</style>