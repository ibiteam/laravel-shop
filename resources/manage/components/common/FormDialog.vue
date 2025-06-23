<template>
    <el-dialog
        :title="title"
        :width="width"
        :center="center"
        :model-value="visibleModel"
        @update:modelValue="val => visibleModel = val"
        :before-close="handleCancel"
    >
        <div class="s-flex jc-ct">
            <el-form
                ref="formRef"
                :model="modelValue"
                :rules="rules"
                :label-width="labelWidth"
                :size="size"
                style="width: 80%"
            >
                <slot />
            </el-form>
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="handleCancel">取消</el-button>
                <el-button type="primary" :loading="loading" @click="handleSubmit">提交</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import { ref,watch } from 'vue';
import { ElMessage } from 'element-plus';
import  Http  from '@/utils/http';
import { isSuccess } from '@/utils/constants';

const props = withDefaults(defineProps<{
    title: string;
    url: string;
    modelValue: any;
    rules: any;
    visible: boolean;
    width?: string;
    center?: boolean;
    labelWidth?: string;
    size?: 'default' | 'small' | 'large';
}>(), {
    center: true,
    width: '900',
    labelWidth:'auto'
});

const emit = defineEmits<{
    (e: 'update:visible', value: boolean): void;
    (e: 'submitSuccess', res: any): void;
}>();

const visibleModel = ref(props.visible);
watch(() => props.visible, val => (visibleModel.value = val));
watch(visibleModel, val => emit('update:visible', val));

const formRef = ref();
const loading = ref(false);

const handleCancel = () => {
    emit('update:visible', false);
    formRef.value.clearValidate();
};

const handleSubmit = () => {
    formRef.value?.validate((valid: boolean) => {
        if (!valid) {
            ElMessage.error('表单验证失败');
            return;
        }

        loading.value = true;
        Http.doPost(props.url, props.modelValue).then((res: any) => {
            loading.value = false;
            if (isSuccess(res.code)) {
                ElMessage.success(res.message || '操作成功');
                emit('update:visible', false);
                emit('submitSuccess', res);
            } else {
                ElMessage.error(res.message || '操作失败');
            }
        }).catch(() => {
            loading.value = false;
            ElMessage.error('请求失败');
        });
    });
};
</script>
