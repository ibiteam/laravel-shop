<template>
    <el-form-item :label="label" :prop="prop">
        <div class="check-all-group">
            <el-checkbox
                :indeterminate="isIndeterminate"
                v-model="checkAll"
                @change="onCheckAllChange"
            >
                {{ checkAllLabel }}
            </el-checkbox>

            <el-checkbox-group
                :model-value="modelValue"
                @change="onGroupChange"
            >
                <el-checkbox
                    v-for="item in options"
                    :key="item.value"
                    :value="item.value"
                >
                    {{ item.label }}
                </el-checkbox>
            </el-checkbox-group>
        </div>
    </el-form-item>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps({
    label: String,
    prop: String,
    modelValue: {
        type: Array,
        required: true
    },
    options: {
        type: Array,
        required: true
    },
    checkAllLabel: {
        type: String,
        default: '全选'
    }
});

const emit = defineEmits(['update:modelValue']);

const checkAll = ref(false);
const isIndeterminate = ref(false);
const onCheckAllChange = (value) => {
    const newValue = value ? props.options.map(opt => opt.value) : [];
    emit('update:modelValue', newValue);
    isIndeterminate.value = false;
};

const onGroupChange = (value) => {
    const total = props.options.length;
    const checked = value.length;
    checkAll.value = checked === total;
    isIndeterminate.value = checked > 0 && checked < total;
    emit('update:modelValue', value);
};

watch(
    () => props.modelValue,
    (val) => {
        const total = props.options.length;
        const checked = val.length;
        checkAll.value = checked === total;
        isIndeterminate.value = checked > 0 && checked < total;
    },
    { immediate: true }
);
</script>

<style scoped>
.check-all-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
</style>
