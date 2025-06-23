<template>
    <el-table-column :label="label" :prop="prop">
        <template #default="scope">
            <el-switch
                v-model="scope.row[prop]"
                :active-value="activeValue"
                :inactive-value="inactiveValue"
                @change="handleFieldChange(scope)"
            />
        </template>
    </el-table-column>
</template>

<script setup lang="ts">
import { ElMessage } from 'element-plus';
import Http from '@/utils/http';
import { isSuccess } from '@/utils/constants';

const props = defineProps<{
    label: string;
    prop: string;
    url: string;
    activeValue?: any;
    inactiveValue?: any;
}>();
// 默认值处理
const activeValue = props.activeValue ?? 1;
const inactiveValue = props.inactiveValue ?? 0;
const handleFieldChange = (scope: any) => {
    Http.doPost(props.url, {
        id: scope.row.id,
        field: props.prop,
        name: props.label
    }).then((res: any) => {
        if (isSuccess(res.code)) {
            ElMessage.success(res.message);
        } else {
            ElMessage.error(res.message);
        }
    }).catch(() => {
        ElMessage.error('请求失败');
    });
};
</script>
