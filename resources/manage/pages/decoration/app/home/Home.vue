<template>
    <div class="decoration-app-container">
        <tool-bar
            v-bind="{
                component_icon: decoration.component_icon,
                component_value: decoration.component_value,
                component_data: decoration.data,
            }"
            @updateDragPlaceholder="updateDragPlaceholder"
        ></tool-bar>
        <main class="decoration-app-main">
            <div class="app-wrapper">
                <search v-bind="{component: findNotForData('home_nav'), temp_index: decoration.temp_index}"></search>
                <VueDraggable
                    class="app-wrapper-content"
                    v-model="decoration.data"
                    :animation="1000"
                    filter=".fixed.setting-bar-wrapper"
                    handle=".drag-item"
                    dragClass=".drag-item"
                    :group="{name: 'decoration', pull: true, put: true}"
                    :forceFallback="false"
                    @add="handleDragAdd">
                    <template v-for="(temp, index) in decoration.data">
                        <div class="drag-placeholder" v-if="dragPlaceholderIndex == index">释放鼠标将组件添加至此处</div>
                        <HorizontalCarousel v-if="temp.component_name == 'horizontal_carousel'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}" />
                        <AdvertisingTwo v-else-if="temp.component_name == 'advertising_two'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}" ></AdvertisingTwo>
                        <AdvertisingThree v-else-if="temp.component_name == 'advertising_three'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}"></AdvertisingThree>
                        <AdvertisingTheme v-else-if="temp.component_name == 'theme_advertising'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}"></AdvertisingTheme>
                        <HotZone v-else-if="temp.component_name == 'hot_zone'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}"></HotZone>
                        <div class="drag-item" style="height: 100px;margin: 0 auto;" v-else ref="tempRefs" :key="temp.id">{{ temp.component_name }}{{ temp.name }}</div>
                    </template>
                </VueDraggable>
                <bottom-nav-bar v-bind="{component: findNotForData('label'), temp_index: decoration.temp_index}"></bottom-nav-bar>
            </div>
        </main>
        <MaterialCenterDialog v-if="materialCenterDialogData.show" v-bind="{...materialCenterDialogData}" @close="handlematerialCenterDialogClose" @confirm="handlematerialCenterDialogConfirm"/>
    </div>
</template>

<script setup>
import 'vant/lib/index.css';
import { VueDraggable } from 'vue-draggable-plus'
import ToolBar from './../../components/ToolBar.vue'
import BottomNavBar from './components/BottomNavBar.vue'
import Search from './components/Search.vue'
import HorizontalCarousel from './components/HorizontalCarousel.vue'
import AdvertisingTwo from './components/AdvertisingTwo.vue'
import AdvertisingThree from './components/AdvertisingThree.vue'
import AdvertisingTheme from './components/AdvertisingTheme.vue'
import HotZone from './components/HotZone.vue';
import MaterialCenterDialog from '@/components/MaterialCenter/Dialog.vue'
import DataExample from './DataExample'
import { ref, reactive, onMounted, onUnmounted, nextTick, getCurrentInstance, watch } from 'vue'

const cns = getCurrentInstance().appContext.config.globalProperties
const decoration = reactive({
    // 组件拖拽数据
    // advertisement_component: 广告组件
    // data_component: 数据组件
    component_icon: DataExample.component_icon,
    // 组件原始数据
    component_value: DataExample.component_value,
    // 装修数据
    data: [],
    // 不可拖拽的数据
    not_for_data: DataExample.not_for_data,
    // 当前选中拖拽的索引
    temp_index: ''
})
// 拖拽占位 下标显示位置
const dragPlaceholderIndex = ref(null)
// 组件refs
const tempRefs = ref([])
const materialCenterDialogData = reactive({
    show: false,
    dir_type: 1,
    multiple: false
})

// 查找不可拖拽的数据
const findNotForData = (component_name) => {
    return decoration.not_for_data.find(item => item.component_name === component_name)
}
console.log(DataExample)
// 拖拽克隆完成
const handleDragAdd = (e) => {
    console.log('handleDragAdd:')
    console.log(e)
    const { clonedData } = e
    decoration.temp_index = clonedData.id
}
// 拖拽过程中获取占位下标
const updateDragPlaceholder = (index) => {
    dragPlaceholderIndex.value = index
}
const handleDragChoose = (e) => {
    console.log(e)   
}

// 装修组件顺序控制
const sortDecorationData = (id, direction) => {
    const index = decoration.data.findIndex(item => item.id === id);
    if (direction === 'up') {
        if (index == 0) return
        const preItem = decoration.data[index - 1];
        if (!preItem) return
        const temp = decoration.data[index];
        decoration.data[index] = preItem;
        decoration.data[index - 1] = temp;
    }
    if (direction === 'down') {
        if (index == decoration.data.length - 1) return
        const nextItem = decoration.data[index + 1];
        if (!nextItem) return
        const temp = decoration.data[index];
        decoration.data[index] = nextItem;
        decoration.data[index + 1] = temp;
    }
}

// 关闭素材中心弹窗
const handlematerialCenterDialogClose = () => {
    materialCenterDialogData.show = false
}

// 接收素材中心弹窗数据
const handlematerialCenterDialogConfirm = (res) => {
    materialCenterDialogData.show = false
    const index = decoration.data.findIndex(item => item.id === materialCenterDialogData.temp_index)
    tempRefs.value[index].updateUploadComponentData({form_index: materialCenterDialogData.form_index, file: res})
}

onMounted(() => {
    decoration.data.unshift({
        component_name: 'hot_zone',
        content: {
            image: 'https://cdn.toodudu.com/2025/02/24/WsUjqeUNqgzY0wyHm2hvEc7aBPXamQ3t080ehmUe.jpg',
            areas: [{
                x: 89,
                y: 86,
                width: 100,
                height: 100,
                url: {
                    name: '',
                    value: '',
                }
            }]
        },
        data: {
            component_name: 'hot_zone',
            image: 'https://cdn.toodudu.com/2025/02/24/WsUjqeUNqgzY0wyHm2hvEc7aBPXamQ3t080ehmUe.jpg',
            items: [{
                x: 89,
                y: 86,
                width: 100,
                height: 100,
                url: 'https://h5.toodudu.com/'
            }]
        },
        id: 999,
        is_show: 1,
        name: '热区',
    }, {
        component_name: 'horizontal_carousel',
        content: {
            width: '710',
            height: '200',
            style: 2, // 1-平铺 2-过渡
            interval: 3, // 默认3秒切换
            data: [{
                url: {
                    name: '',
                    value: '',
                },
                date_type: 1, // 0:自定义  1:长期
                time: ['2025-10-10 00:00:00', '2025-10-10 23:59:59'],
                image: '',
                is_show: 1, // 是否显示
            }]
        },
        data: {
            component_name: 'horizontal_carousel',
            width: '710',
            height: '200',
            style: 2,
            interval: 3,
            items: [{
                image: 'https://cdn.toodudu.com/2024/12/25/S9T0mbYiJRkRFqKMZ7NeJnFDBHeeRkhLRx8tvgzC.jpg',
                url: '',
            },{
                image: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
                url: '',
            },{
                image: 'https://cdn.toodudu.com/2024/12/25/Q8DPTcVi9OJWtJLeLfQDfUvbanUR8IfjG2xlxhgU.jpg',
                url: '',
            },{
                image: 'https://cdn.toodudu.com/2024/08/02/AjOkTg03f0JOBEnQuGgatRol1abaiAsYRWDWoe13.jpg',
                url: '',
            },{
                image: 'https://cdn.toodudu.com/2024/10/17/GEEoTIAnkTSSDGAn5qNBN4sBgm2YXMkfjM6Zxkeb.jpg',
                url: '',
            },],
        },
        id: '',
        is_show: 1,
        name: '轮播图',
    })
    nextTick(() => {
        cns.$bus.on('chooseDragItem', (res) => {
            decoration.temp_index = res.temp_index
        })
        // 更新组件状态
        cns.$bus.on('updateComponentData', (res) => {
            // 切换显示
            if (res.type == 'show') {
                decoration.data.forEach((item, index) => {
                    if (item.id == res.component.id) {
                        decoration.data[index].is_show = !decoration.data[index].is_show ? 1 : 0
                    }
                })
            }
            // 删除
            if (res.type == 'remove') {
                decoration.data = decoration.data.filter(item => item.id != res.component.id)
            }
            // 升序
            if (res.type == 'sort_up') {
                sortDecorationData(res.component.id, 'up')
            }
            // 降序
            if (res.type == 'sort_down') {
                sortDecorationData(res.component.id, 'down')
            }
        })
        // 打开素材中心弹窗
        cns.$bus.on('openUploadDialog', (params = {show: false, dir_type: 1, multiple: false, temp_index: ''}) => {
            Object.keys(params).forEach(key => {
                materialCenterDialogData[key] = params[key]
            })
        })
    })
})

onUnmounted(() => {
    cns.$bus.off('chooseDragItem')
    cns.$bus.off('updateComponentData')
    cns.$bus.off('openUploadDialog')
})

</script>
<style lang="scss">
.drag-item {
    cursor: move;
}
// 公用装修组件标题
.decoration-title-wrapper{
    width: 100%;
    height: 46px;
    padding: 0 10px;
    box-sizing: border-box;
    background: #fff;
    display: flex;
    align-items: center;
    position: relative;
    .decoration-title {
        width: 70%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-weight: bold;
        font-size: 15px;
        color: #010101;
    }
    .decoration-title-right {
        color: #666;
        display: flex;
        align-items: center;
        position: absolute;
        top: 0;
        bottom: 0;
        right: 10px;
        margin: auto 0;
    }
}
</style>
<style lang='scss' scoped>
.decoration-app-container{
    width: 100%;
    height: 100%;
    background: var(--page-bg-color);
    overflow: hidden;
    position: relative;
    .decoration-app-main{
        width: 100%;
        height: 100%;
        padding: 20px 400px 20px 300px;
        box-sizing: border-box;
        overflow: hidden;
        position: relative;
        z-index: 1;
        background: #F4F4FA;
        .app-wrapper{
            // height: 780px;
            padding-top: 20px;
            box-sizing: border-box;
            overflow: hidden;
            position: relative;
            &::before {
                content: '';
                display: block;
                width: 375px;
                height: 20px;
                background: #f2f2f2 url('@/assets/images/decoration/app-header.png') top center no-repeat;
                background-size: 375px 20px;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                margin: 0 auto;
            }
            .app-wrapper-content{
                // width: 375px;
                height: 650px;
                margin: 0 auto;
                // padding-bottom: 50px;
                box-sizing: border-box;
                position: relative;
                overflow-y: auto;
                &::-webkit-scrollbar{
                    display: none;
                }
                &::-webkit-scrollbar-track-piece {
                    display: none;
                }
                &::-webkit-scrollbar-thumb:vertical {
                    height: 5px;
                    background-color: rgba(125, 125, 125, 0.2);
                    -webkit-border-radius: 6px;
                    display: none;
                    
                }
                &:hover {
                    &::-webkit-scrollbar-thumb:vertical {
                        display: block;
                    }
                }
                .drag-placeholder {
                    width: 375px;
                    height: 44px;
                    line-height: 44px;
                    margin: 0 auto;
                    border: 2px dotted var(--main-color);
                    color: var(--main-color);
                    text-align: center;
                    box-sizing: border-box;
                    background-color: var(--main-color-20);
                    user-select: none;
                }
            }
        }
    }
}
</style>