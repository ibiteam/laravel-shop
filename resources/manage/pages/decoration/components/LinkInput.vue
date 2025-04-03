<template>
    <div>
        <el-input v-model="link.name" :readonly="readOnly" clearable @clear="emit('clear', LinkDataItemField())" @input="handleInput">
            <template #suffix>
                <Icon name="link-o" class="link-btn" @click.stop="handleOpenLink"/>
            </template>
        </el-input>
        <el-input v-model="link.value" style="display: none;">
        </el-input>
    </div>
</template>

<script setup>
import { ref, reactive, watch, defineEmits } from 'vue';
import { Icon } from 'vant';
import { LinkDataItemField } from '@/dataField/Index.js'
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
const handleOpenLink = () => {
    console.log(1)
}

const handleInput = () => {
    link.value = link.name
    emit('input', link)
}


</script>

<style lang='scss' scoped>
.link-btn {
    padding-left: 10px;
    cursor: pointer;
}
</style>