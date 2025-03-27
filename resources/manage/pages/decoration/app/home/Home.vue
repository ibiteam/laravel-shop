<template>
    <div class="decoration-app-container">
        <tool-bar :components="decoration.component_icon" :original="decoration.component_value" ></tool-bar>
        <main class="decoration-app-main">
            <div class="app-wrapper">
                <VueDraggable
                    class="app-wrapper-content"
                    v-model="decoration.data"
                    :animation="1000"
                    filter=".fixed"
                    handle=".drag-item"
                    group="decoration"
                    :forceFallback="false"
                    @add="handleDragAdd"
                    @choose="handleDragChoose"
                >
                    <bottom-nav-bar :component="findNotForData('label')"></bottom-nav-bar>
                </VueDraggable>
            </div>
        </main>
    </div>
</template>

<script setup>
import 'vant/lib/index.css';
import { VueDraggable } from 'vue-draggable-plus'
import ToolBar from './../../components/ToolBar.vue'
import BottomNavBar from './components/BottomNavBar.vue'
import DataExample from './DataExample'
import { ref, reactive } from 'vue'


const decoration = reactive({
    // 组件拖拽数据
    // advertisement_component: 广告组件
    // data_component: 数据组件
    component_icon: DataExample.component_icon,
    // 组件原始数据
    component_value: DataExample.component_value,
    // 装修数据
    data: DataExample.data,
    // 不可拖拽的数据
    not_for_data: DataExample.not_for_data
})
// 查找不可拖拽的数据
const findNotForData = (component_name) => {
    return decoration.not_for_data.find(item => item.component_name === component_name)
}
console.log(DataExample)

// 拖拽至列表中添加
const handleDragAdd = (e) => {
    console.log(e)
}

const handleDragChoose = (e) => {
    console.log(e)   
}

</script>

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
        .app-wrapper{
            height: 720px;
            padding-top: 20px;
            box-sizing: border-box;
            position: relative;
            &::before {
                content: '';
                display: block;
                width: 375px;
                height: 20px;
                background: #fff url('@/assets/images/decoration/app-header.png') top center no-repeat;
                background-size: 375px 20px;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                margin: 0 auto;
            }
            .app-wrapper-content{
                width: 375px;
                height: 650px;
                margin: 0 auto;
                padding-bottom: 50px;
                box-sizing: border-box;
                position: relative;
            }
        }
    }
}
</style>