<template>
    <el-image :src="src" :fit="fit" :style="{ width, height, borderRadius: `${radius}`}">
        <template #placeholder>
            <div class="image-item" :style="{ width, height, borderRadius: `${radius}` }">
                <img src="@/assets/images/decoration/app-nopic.png" />
            </div>
        </template>
        <template #error>
            <div class="image-item" :style="{ width, height, borderRadius: `${radius}`}">
                <img src="@/assets/images/decoration/app-nopic.png" />
            </div>
        </template>
    </el-image>
</template>

<script setup>
const props = defineProps({
    src: {
        type: String,
        default: '',
    },
    width: {
        type: String,
        default: ''
    },
    height: {
        type: String,
        default: ''
    },
    radius: {
        type: String,
        default: '0'
    },
    fit: {
        type: String,
        default: 'fill', // cover, contain, fill, none, scale-down
    }
})

</script>

<style lang='scss' scoped>
.image-item {
    text-align: center;
    background-color: #fff;
    img {
        width: auto;
        height: 100%;
        max-height: 100%;
        margin: 0 auto;
    }
}
</style>