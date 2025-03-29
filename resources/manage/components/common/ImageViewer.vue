<template>
    <el-image-viewer
        v-if="showPreview"
        :url-list="srcList"
        show-progress
        :initial-index="index"
        @close="$emit('close')"
    />
</template>

<script setup>
import { defineProps, defineEmits, watch, ref } from 'vue'
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    srcList: {
        type: Array,
        default: () => []
    },
    index: {
        type: Number,
        default: 0,
    }

})
defineEmits(['close'])

const showPreview = ref(false)

watch(() => props.show, (bool) => {
    showPreview.value = bool
})


</script>

<style lang="scss" scoped>
</style>
