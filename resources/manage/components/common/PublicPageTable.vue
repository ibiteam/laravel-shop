<template>
    <div>
        <el-table
            :data="data"
            border
            stripe
            :style="{width, minHeight}">
            <slot></slot>
        </el-table>
        <Pagination :pageInfo="pageInfo" @sizeChange="(value) => emit('sizeChange', value)" @currentChange="(value) => emit('currentChange', value)"></Pagination>
    </div>
</template>

<script setup>
import { ref, onMounted, defineEmits } from 'vue';
import Pagination from './Pagination.vue'

const emit = defineEmits(['sizeChange', 'currentChange'])

defineProps({
    data: {
        type: Array,
        default: () => []
    },
    pageInfo: {
        type: Object,
        default: () => {
            return {
                total: 0,
                per_page: 10,
                current_page: 1
            }
        }
    },
    width: {
        type: String,
        default: '100%'
    },
    minHeight: {
        type: String,
        default: '700px'
    }
})

</script>

<style scoped lang="scss">
.el-table--border:before,.el-table--border:after{
    display: none;
}
</style>
