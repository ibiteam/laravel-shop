<template>
    <el-pagination 
        :current-page.sync="pageInfo.currentPage"
        :page-sizes="pageInfo.pageSizes ? pageInfo.pageSizes :[10, 20, 50, 100]"
        :page-size="pageInfo.number"
        :layout="pageInfo.layout ? pageInfo.layout : 'total, sizes, prev, pager, next, jumper'"
        :total="pageInfo.total" 
        @size-change="(value) => emit('sizeChange', value)"
        @current-change="(value) => emit('currentChange', value)" 
        />
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
defineProps({
    pageInfo: {
        type: Object,
        default: () => {
            return {
                currentPage: 1,
                number: 10,
                total: 0,
                pageSizes: [10, 20, 50, 100],
                layout: "total, sizes, prev, pager, next, jumper",
                handleSizeChange: (val) => { },
                handleCurrentChange: (val) => { }
            }
        }
    }
})

const emit = defineEmits(['sizeChange', 'currentChange'])

</script>

<style lang="scss" scoped>
</style>
