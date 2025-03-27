<template>
    <div>
        <Editor v-model="content" @change="handleChange" />
    </div>
</template>

<script setup>
import Editor from '@/components/good/Editor.vue'
import { ref } from 'vue'

const content = ref('')

const handleChange = (value) => {
    console.log(value)
}
</script>

<style scoped lang="scss">

</style>
