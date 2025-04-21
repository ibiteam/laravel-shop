<template>
    <div>
        <el-table
            :data="data.list"
            border
            stripe
            :max-height="maxHeight"
            :style="{width, minHeight}">
            <slot></slot>
        </el-table>
        <el-pagination
            v-if="data.meta"
            class="common-pagination"
            :current-page="data.meta.current_page"
            :page-size="data.meta.per_page"
            :page-sizes="data.meta.page_sizes? data.meta.page_sizes :[10, 20, 50, 100]"
            :layout="layout"
            :total="Number(data.meta?.total ?? 0)"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
        />

    </div>
</template>

<script lang="ts" setup>
import { defineEmits,computed } from 'vue';

const emit = defineEmits<{
    (e: 'change', page: number, per_page: number): void
}>()

const props = defineProps({
    data: {
        type: Object,
        default: () => ({
            list: [],
            meta: {}
        })
    },
    width: {
        type: String,
        default: '100%'
    },
    minHeight: {
        type: String,
        default: '600px'
    },
    maxHeight: {
        type: String,
        default: '100%'
    }
})
const layout = computed(() => {
    const defaultLayout = 'total, sizes, prev, pager, next, jumper'
    return props.data.meta.layout ?? defaultLayout
})
const handleSizeChange = (per_page: number) => {
    emit('change',1,per_page)
};

const handleCurrentChange = (page: number) => {
    emit('change',page,props.data.meta.per_page)
};
</script>

<style scoped lang="scss">
.el-table--border:before,.el-table--border:after{
    display: none;
}
.common-pagination {
    display: flex;
    justify-content: center;
    margin-top: 15px;
}
</style>
