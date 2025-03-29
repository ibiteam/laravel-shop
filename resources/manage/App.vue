<template>
    <div style="width: inherit; height: inherit;">
        <el-config-provider :locale="zhCn">
            <router-view></router-view>
        </el-config-provider>
    </div>
</template>

<script lang="ts" setup>
import {onMounted} from "vue";
import zhCn from 'element-plus/dist/locale/zh-cn.mjs'
onMounted(() => {
    function getCssVariableValue(variableName) {
        return getComputedStyle(document.documentElement).getPropertyValue(variableName).trim();
    }
    const mainColor = getCssVariableValue('--main-color')
    let root = document.querySelector(":root") as HTMLElement
    let color_number = 10
    root.style.setProperty("--main-color", mainColor)
    while (color_number < 100) {
        root.style.setProperty(`--main-color-${color_number}`, mainColor + color_number)
        color_number += 10
    }
})
</script>
