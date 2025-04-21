<template>
    <el-pagination
        class="common-pagination"
        :current-page.sync="pageInfo.current_page"
        :page-sizes="pageInfo.page_sizes ? pageInfo.page_sizes :[10, 20, 50, 100]"
        :page-size="pageInfo.per_page"
        :layout="pageInfo.layout ? pageInfo.layout : 'total, sizes, prev, pager, next, jumper'"
        :total="pageInfo.total"
        @size-change="(value) => $emit('sizeChange', value)"
        @current-change="(value) => $emit('currentChange', value)"
        />
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
defineProps({
    pageInfo: {
        type: Object,
        default: () => {
            return {
                current_page: 1,
                per_page: 10,
                total: 0,
                page_sizes: [10, 20, 50, 100],
                layout: "total, sizes, prev, pager, next, jumper",
            }
        }
    }
})

const emit = defineEmits(['sizeChange', 'currentChange'])

</script>

<style lang="scss" scoped>

.common-pagination {
    display: flex;
    justify-content: center;
    margin-top: 15px;
}
</style>
