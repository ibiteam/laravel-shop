<template>
    <div>
        <el-input v-model="link.name" :readonly="readOnly" clearable @clear="emit('clear', LinkDataItemField())" @input="handleInput">
            <template #suffix>
                <em class="iconfont icon-link link-btn" @click.stop="emit('select')" title="路由中心"></em>
                <em class="iconfont icon-guanbi link-btn" v-if="readOnly" @click.stop="handleClear" title="清空"></em>
            </template>
        </el-input>
        <el-input v-model="link.value" style="display: none;">
        </el-input>
    </div>
</template>

<script setup>
import { ref, reactive, watch, defineEmits } from 'vue';
import { LinkDataItemField } from '@/pages/decoration/app/home/dataField/Index.js'
const props = defineProps({
    name: '',
    value: '',
})

const link = reactive({
    name: '',
    value: '',
})

const readOnly = ref(false)

const emit = defineEmits(['select', 'clear', 'input'])

const handleInput = () => {
    link.value = link.name
    emit('input', link)
}

const handleClear = () => {
    link.name = ''
    link.value = ''
    emit('clear', LinkDataItemField())
}

watch(() => props, (newValue, oldValue) => {
    if (newValue) {
        link.name = newValue.name
        link.value = newValue.value
        readOnly.value = newValue.name != newValue.value
    }
}, {
    immediate: true,
    deep: true,
})
</script>

<style lang='scss' scoped>
.link-btn {
    padding-left: 10px;
    cursor: pointer;
    font-size: 14px;
}
</style>