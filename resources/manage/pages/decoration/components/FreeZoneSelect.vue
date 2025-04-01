<template>
    <div 
        class="free-zone-container"
        ref="containerRef"
        @mousedown="startSelection"
    >
        <img class="background-image" :src="backgroundImage" ondragstart="return false;" oncontextmenu="return false;" onselect="document.selection.empty();"  alt="热区">
        <div class="area" v-for="(area, index) in areas" :key="index" :style="getAreaStyle(area)" @mousedown.stop="startDrag(index, $event)">
            <div class="area-content" v-if="currentAreaIndex != index || (currentAreaIndex == index && !isSelecting)">
                <span class="co-fff">热区{{ index+1 }}</span>
                <span :style="{color: area.url ? 'var(--main-color)' : 'var(--red-color)'}">{{ area.url ? '(已设置)' : '(未设置)' }}</span>
                <Icon name="cross" class="remove" @click.stop="removeArea(index)" color="#fff" />
                <Icon name="expand-o" class="resize" @mousedown.stop="startResize(index, $event)" color="#fff" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, getCurrentInstance, watch, defineEmits } from 'vue'
import { Icon } from 'vant'

const props = defineProps({
    backgroundImage: {
        type: String,
        default: 'https://cdn.toodudu.com/2025/02/24/WsUjqeUNqgzY0wyHm2hvEc7aBPXamQ3t080ehmUe.jpg'
    },
    data: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['update'])

const containerRef = ref(null);
const areas = ref([]);
let isSelecting = ref(false);
let startX = ref(0);
let startY = ref(0);
let offsetX = ref(0); // 鼠标相对于区域左上角的X偏移量
let offsetY = ref(0); // 鼠标相对于区域左上角的Y偏移量
let currentAreaIndex = ref(-1);

// 创建选区
const startSelection = (e) => {
    if (e.target.classList.contains('zone')) return;
    isSelecting.value = true;
    startX.value = e.clientX;
    startY.value = e.clientY;
    const containerRect = containerRef.value.getBoundingClientRect();
    areas.value.push({
        x: startX.value - containerRect.left,
        y: startY.value - containerRect.top,
        width: 0,
        height: 0,
        url: ''
    });
    currentAreaIndex.value = areas.value.length - 1;
    document.addEventListener('mousemove', updateSelection);
    document.addEventListener('mouseup', endSelection);
};

// 鼠标移动-更新选区参数
const updateSelection = (e) => {
    if (isSelecting.value) {
        const containerRect = containerRef.value.getBoundingClientRect();
        const width = e.clientX - startX.value;
        const height = e.clientY - startY.value;
        const x = Math.min(startX.value - containerRect.left, e.clientX - containerRect.left);
        const y = Math.min(startY.value - containerRect.top, e.clientY - containerRect.top);
        const finalWidth = Math.abs(width);
        const finalHeight = Math.abs(height);

        areas.value[currentAreaIndex.value] = {
            ...areas.value[currentAreaIndex.value],
            x,
            y,
            width: finalWidth,
            height: finalHeight
        };
    }
};

// 选区创建完成
const endSelection = () => {
    let currentArea = areas.value[currentAreaIndex.value];
    if (currentArea.width <= 10 && currentArea.height <= 10) {
        areas.value.splice(currentAreaIndex.value, 1);
    }
    isSelecting.value = false;
    document.removeEventListener('mousemove', updateSelection);
    document.removeEventListener('mouseup', endSelection);
    emit('update', areas.value);
};

// 移动选区开始
const startDrag = (index, e) => {
    currentAreaIndex.value = index;
    startX.value = e.clientX;
    startY.value = e.clientY;

    // 计算鼠标相对于区域左上角的偏移量
    const area = areas.value[index];
    const areaRect = e.target.getBoundingClientRect();
    offsetX.value = e.clientX - areaRect.left;
    offsetY.value = e.clientY - areaRect.top;

    document.addEventListener('mousemove', dragArea);
    document.addEventListener('mouseup', endDrag);
};

// 更新选区移动参数
const dragArea = (e) => {
    if (currentAreaIndex.value !== -1) {
        const containerRect = containerRef.value.getBoundingClientRect();
        const containerWidth = containerRect.width;
        const containerHeight = containerRect.height;

        // 计算新的位置
        const newX = e.clientX - containerRect.left - offsetX.value;
        const newY = e.clientY - containerRect.top - offsetY.value;

        // 获取当前区域的宽度和高度
        const area = areas.value[currentAreaIndex.value];
        const maxX = containerWidth - area.width;
        const maxY = containerHeight - area.height;

        // 更新区域位置
        areas.value[currentAreaIndex.value] = {
            ...area,
            x: Math.max(0, Math.min(newX, maxX)),
            y: Math.max(0, Math.min(newY, maxY))
        };
    }
};

// 选区移动完成
const endDrag = () => {
    document.removeEventListener('mousemove', dragArea);
    document.removeEventListener('mouseup', endDrag);
    emit('update', areas.value);
};

// 删除选区
const removeArea = (index) => {
    areas.value.splice(index, 1);
    emit('update', areas.value);
};

let currentResizeIndex = ref(-1);
let initialWidth = ref(0);
let initialHeight = ref(0);
let initialClientX = ref(0);
let initialClientY = ref(0);

// 开始调整大小
const startResize = (index, event) => {
    currentResizeIndex.value = index;
    const area = areas.value[index];
    
    initialWidth.value = area.width;
    initialHeight.value = area.height;
    initialClientX.value = event.clientX;
    initialClientY.value = event.clientY;

    // 添加全局鼠标移动和释放事件
    document.addEventListener('mousemove', handleResize);
    document.addEventListener('mouseup', stopResize);
};

// 调整大小中
const handleResize = (event) => {
    if (currentResizeIndex.value === -1) return;

    const area = areas.value[currentResizeIndex.value];
    const deltaX = event.clientX - initialClientX.value;
    const deltaY = event.clientY - initialClientY.value;

    // 更新宽高
    area.width = Math.max(30, initialWidth.value + deltaX);
    area.height = Math.max(30, initialHeight.value + deltaY);

    const containerRect = containerRef.value.getBoundingClientRect();

    // 限制区域右边界和下边界不超过父容器
    area.x = Math.min(containerRect.width - area.width, area.x);
    area.y = Math.min(containerRect.height - area.height, area.y);
};

// 停止调整大小
const stopResize = () => {
    document.removeEventListener('mousemove', handleResize);
    document.removeEventListener('mouseup', stopResize);
    currentResizeIndex.value = -1;
    emit('update', areas.value);
};

// 获取选区样式
const getAreaStyle = (area) => {
    return {
        left: `${area.x}px`,
        top: `${area.y}px`,
        width: `${area.width}px`,
        height: `${area.height}px`
    };
};

watch(() => props, (newVal) => {
    if (newVal) {
        areas.value = newVal.data
    }
}, {
    immediate: true,
    deep: true
})


</script>

<style lang='scss' scoped>
.free-zone-container {
    width: 750px;
    height: fit-content;
    border: 1px solid #d8d8d8;
    box-sizing: border;
    position: relative;
    cursor: crosshair;
    .background-image {
        width: 100%;
        height: auto;
        vertical-align: bottom;
        user-select: none;
    }
    .area {
        position: absolute;
        border: 1px dashed var(--main-color);
        background-color: var(--main-color-80);
        box-sizing: border-box;
        user-select: none;
        cursor: move;
        .area-content {
            width: inherit;
            height: inherit;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            .remove {
                background-color: var(--main-color);
                cursor: pointer;
                position: absolute;
                top: 0;
                right: 1px;
            }
            .resize {
                background-color: var(--main-color);
                cursor: nw-resize;
                position: absolute;
                bottom: 1px;
                right: 1px;
            }
        }
    }
}
</style>