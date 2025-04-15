<template>
    <DecorationLayout 
        :pageName="decoration.app_website_data?.name"
        :time="decoration.app_website_data?.release_time"
        :id="decoration.app_website_data?.id"
        @pageSetting="openPageSetting"
        @pageSave="decorationSave"
    >
        <template #aside-content>
            <tool-bar
                v-bind="{
                    component_icon: decoration.component_icon,
                    component_value: decoration.component_value,
                    component_data: decoration.data,
                }"
                @updateDragPlaceholder="updateDragPlaceholder"
            ></tool-bar>
        </template>
        <template #main-content>
            <div class="decoration-app-container">
                <main class="decoration-app-main" id="decorationAppMain">
                    <div class="app-wrapper">
                        <search v-if="findNotForData('home_nav')" ref="homeSearchRef" v-bind="{component: findNotForData('home_nav'), temp_index: decoration.temp_index}" ></search>
                        <VueDraggable
                            class="app-wrapper-content"
                            v-model="decoration.data"
                            :animation="1000"
                            filter=".fixed.setting-bar-wrapper"
                            handle=".drag-item"
                            dragClass=".drag-item"
                            :scroll="true"
                            :group="{name: 'decoration', pull: true, put: true}"
                            :forceFallback="false"
                            @add="handleDragAdd">
                            <template v-for="(temp, index) in decoration.data">
                                <div class="drag-placeholder" v-if="dragData.placeholderIndex == index"></div>
                                <HorizontalCarousel v-if="temp.component_name == 'horizontal_carousel'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}" />
                                <HotZone v-if="temp.component_name == 'hot_zone'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}" ></HotZone>
                                <AdvertisingBanner v-if="temp.component_name == 'advertising_banner'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}"></AdvertisingBanner>
                                <QuickLink v-if="temp.component_name == 'quick_link'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}"></QuickLink>
                                <GoodsRecommend v-if="temp.component_name == 'goods_recommend'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index,}"></GoodsRecommend>
                                <Recommend v-if="temp.component_name == 'recommend'" ref="tempRefs" :key="temp.id" v-bind="{component: temp, temp_index: decoration.temp_index, parent: decoration.data, parent_index: index, default_data: defaultRecommendData}"></Recommend>
                            </template>
                        </VueDraggable>
                        <!-- <bottom-nav-bar v-if="findNotForData('label')" ref="homeLabelRef" v-bind="{component: findNotForData('label'), temp_index: decoration.temp_index}" ></bottom-nav-bar> -->
                    </div>
                </main>
                <HomeSetting v-show="decoration.app_website_data && pageSetting" ref="pageSettingRef" v-bind="{app_website_data: decoration.app_website_data, danping_advertisement: findNotForData('danping_advertisement'), suspended_advertisement: findNotForData('suspended_advertisement')}"></HomeSetting>
                <MaterialCenterDialog v-if="materialCenterDialogData.show" v-bind="{...materialCenterDialogData}" @close="handlematerialCenterDialogClose" @confirm="handlematerialCenterDialogConfirm"/>
                <LinkCenterDialog v-if="linkCenterDialogData.show" v-bind="{...linkCenterDialogData}" @close="handleLinkCenterDialogClose" @confirm="handleLinkCenterDialogConfirm"></LinkCenterDialog>
                <GoodsSelectDialog v-if="goodsDialogData.show" v-bind="{...goodsDialogData}" @close="handleGoodsDialogClose" @confirm="handleGoodsDialogConfirm"></GoodsSelectDialog>
            </div>
        </template>
    </DecorationLayout>
</template>
<script setup>
import 'vant/lib/index.css';
import { VueDraggable } from 'vue-draggable-plus'
import DecorationLayout from '@/pages/decoration/DecorationLayout.vue'; 
import ToolBar from './../../components/ToolBar.vue'
// import BottomNavBar from './components/BottomNavBar.vue'
import HomeSetting from './components/HomeSetting.vue'
import Search from './components/Search.vue'
import HorizontalCarousel from './components/HorizontalCarousel.vue'
import HotZone from './components/HotZone.vue';
import AdvertisingBanner from './components/AdvertisingBanner.vue'
import QuickLink from './components/QuickLink.vue'
import GoodsRecommend from './components/GoodsRecommend.vue';
import Recommend from './components/Recommend.vue';
import MaterialCenterDialog from '@/components/MaterialCenter/Dialog.vue'
import LinkCenterDialog from '@/components/LinkCenter/Dialog.vue'
import GoodsSelectDialog from '@/components/good/SelectDialog.vue'
import { ref, reactive, onMounted, onUnmounted, nextTick, getCurrentInstance, watch } from 'vue'
import { appDecorationInit, appDecorationSave, decorationRecommendData } from '@/api/decoration.js'

const cns = getCurrentInstance().appContext.config.globalProperties
const decoration = reactive({
    // 页面配置数据
    app_website_data: null,
    // 组件拖拽数据
    // advertisement_component: 广告组件
    // data_component: 数据组件
    component_icon: [],
    // 组件原始数据
    component_value: [],
    // 装修数据
    data: [],
    // 不可拖拽的数据
    not_for_data: [],
    // 当前选中拖拽的索引
    temp_index: ''
})
// 拖拽过程 true-拖拽中 false-拖拽结束
const dragMove = ref(false)
const dragData = reactive({
    placeholderIndex: null, // 拖拽占位 下标显示位置
    move: false, // 拖拽过程 true-拖拽中 false-拖拽结束
    temp_index: '', // 拖拽元素id
})
// 组件refs
const tempRefs = ref([])
// 固定头部搜索ref
const homeSearchRef = ref(null)
// 固定底部导航栏ref
const homeLabelRef = ref(null)
// 素材中心弹窗
const materialCenterDialogData = reactive({
    show: false,
    dir_type: 1,
    multiple: false
})
// 路由中心弹窗
const linkCenterDialogData = reactive({
    show: false,
})
// 商品弹窗
const goodsDialogData = reactive({
    show: false,
})
// 为您推荐默认数据
const defaultRecommendData = ref([])
// 页面基础设置
const pageSetting = ref(true)
const pageSettingRef = ref(null)

// 查找不可拖拽的数据
const findNotForData = (component_name) => {
    return decoration.not_for_data.find(item => item.component_name === component_name)
}
// 拖拽克隆完成
const handleDragAdd = (e) => {
    console.log('handleDragAdd:')
    console.log(e)
    const { clonedData } = e
    decoration.temp_index = clonedData.id
    pageSetting.value = false
}
// 拖拽过程中获取占位下标
const updateDragPlaceholder = (index) => {
    dragData.placeholderIndex = index
}
const handleDragChoose = (e) => {
    console.log(e)   
}

// 装修组件顺序控制
const sortDecorationData = (params = {component: {}, direction: 'up'}) => {
    // id, direction
    const {component, direction} = params
    const id = component.id
    const index = decoration.data.findIndex(item => item.id === id);
    if (direction === 'up') {
        if (index == 0) return
        const preItem = decoration.data[index - 1];
        if (!preItem) return
        // const temp = decoration.data[index];
        decoration.data[index] = preItem;
        decoration.data[index - 1] = component;
    }
    if (direction === 'down') {
        if (index == decoration.data.length - 1) return
        const nextItem = decoration.data[index + 1];
        if (!nextItem) return
        // const temp = decoration.data[index];
        decoration.data[index] = nextItem;
        decoration.data[index + 1] = component;
    }
}

// 关闭素材中心弹窗
const handlematerialCenterDialogClose = () => {
    materialCenterDialogData.show = false
}

// 接收素材中心弹窗数据
const handlematerialCenterDialogConfirm = (res) => {
    materialCenterDialogData.show = false
    const updateData = {...materialCenterDialogData, file: res}
    if (materialCenterDialogData.temp_index) {
        const index = decoration.data.findIndex(item => item.id === materialCenterDialogData.temp_index)
        tempRefs.value[index].updateUploadComponentData(updateData)
        return
    }
    if (materialCenterDialogData.not_for_data) {
        const not_for_data = materialCenterDialogData.not_for_data
        if (['danping_advertisement', 'suspended_advertisement'].includes(not_for_data)) {
            pageSettingRef.value.updateUploadComponentData(updateData)
        }
        if (not_for_data == 'home_nav') {
            homeSearchRef.value.updateUploadComponentData(updateData)
        }
        // if (not_for_data == 'label') {
        //     homeLabelRef.value.updateUploadComponentData(updateData)
        // }
        return
    }
}

// 接收路由中心弹窗数据
const handleLinkCenterDialogConfirm = (res) => {
    linkCenterDialogData.show = false
    const updateData = {...linkCenterDialogData, link: {
        name: res[0]?.name,
        value: res[0]?.h5_url
    }}
    if (linkCenterDialogData.temp_index) {
        const index = decoration.data.findIndex(item => item.id === linkCenterDialogData.temp_index)
        tempRefs.value[index].updateLinkComponentData(updateData)
        return
    }
    if (linkCenterDialogData.not_for_data) {
        const not_for_data = linkCenterDialogData.not_for_data
        if (['danping_advertisement', 'suspended_advertisement'].includes(not_for_data)) {
            pageSettingRef.value.updateLinkComponentData(updateData)
        }
        if (not_for_data == 'home_nav') {
            homeSearchRef.value.updateLinkComponentData(updateData)
        }
        // if (not_for_data == 'label') {
        //     homeLabelRef.value.updateLinkComponentData(updateData)
        // }
        return
    }
}

// 关闭路由中心弹窗
const handleLinkCenterDialogClose = () => {
    linkCenterDialogData.show = false
}

// 接收商品弹窗数据
const handleGoodsDialogConfirm = (res) => {
    goodsDialogData.show = false
    const updateData = {
        ...goodsDialogData,
        goods: res
    }
    if (goodsDialogData.temp_index) {
        const index = decoration.data.findIndex(item => item.id === goodsDialogData.temp_index)
        tempRefs.value[index].updateGoodsComponentData(updateData)
        return
    }
}

// 关闭路由中心弹窗
const handleGoodsDialogClose = () => {
    goodsDialogData.show = false
}

// 保存装修
const decorationSave = (params) => {
    try {
        const { button_type } = params
        const pageSettingData = pageSettingRef.value.getComponentData()
        const { app_website_data, danping_advertisement, suspended_advertisement } = pageSettingData
        if (!app_website_data.title || !app_website_data.keywords || !app_website_data.description) {
            cns.$message.error('请设置TDK')
            decoration.temp_index = ''
            pageSetting.value = true
            return
        }
        let decoration_data = []
        tempRefs.value.map(item => {
            let temp_data = JSON.parse(JSON.stringify(item.getComponentData()))
            decoration_data.push(temp_data)
        })
        const save_decoration_data = [
            homeSearchRef.value.getComponentData(),
            danping_advertisement,
            suspended_advertisement,
            ...decoration_data
        ]
        const saveData = {
            button_type,
            id: decoration.app_website_data.id,
            title: app_website_data.title,
            keywords: app_website_data.keywords,
            description: app_website_data.description,
            data: JSON.stringify(save_decoration_data)
        }
        console.log(save_decoration_data)
        console.log(saveData)
        appDecorationSave((saveData)).then(res => {
            if (cns.$successCode(res.code)) {
                cns.$message.success('保存成功')
            } else if (res.code == 4006) {
                if (res.data.id) {
                    decoration.temp_index = res.data.id
                    pageSetting.value = false
                } else {
                    decoration.temp_index = ''
                    pageSetting.value = true
                }
                cns.$message.error(res.message)
            } else {
                cns.$message.error(res.message)
            }
        })
    } catch (error) {
        console.log(error)
    }

}

// 获取首页装修数据
const getDecorationHome = () => {
    appDecorationInit({id: cns.$route.query.id}).then(res => {
        if (cns.$successCode(res.code)) {
            decoration.app_website_data = res.data.app_website_data
            decoration.component_icon = res.data.component_icon
            decoration.component_value = res.data.component_value
            decoration.data = res.data.data
            decoration.not_for_data = res.data.not_for_data

            
        } else {
            cns.$message.error(res.message)
        }
    })
    getDecorationRecommendData()
}

// 获取为您推荐数据
const getDecorationRecommendData = () => {
    decorationRecommendData().then(res => {
        if (cns.$successCode(res.code)) {
            defaultRecommendData.value = res.data.list
        }
    })
}

const openPageSetting = () => {
    pageSetting.value = true
    decoration.temp_index = ''
}


onMounted(() => {
    getDecorationHome()
    nextTick(() => {
        cns.$bus.on('chooseDragItem', (res) => {
            pageSetting.value = false
            decoration.temp_index = res.temp_index
        })
        // 更新组件状态
        cns.$bus.on('updateComponentData', (res) => {
            // 切换显示
            if (res.type == 'show') {
                decoration.data.forEach((item, index) => {
                    if (item.id == res.component.id) {
                        decoration.data[index] = {
                            ...tempRefs.value[index].getComponentData(),
                            is_show: !decoration.data[index].is_show ? 1 : 0
                        } 
                    }
                })
            }
            // 删除
            if (res.type == 'remove') {
                cns.$confirm('确定删除吗？', '删除组件',{
                    center: true,
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                }).then(() => {
                    cns.$message.success('删除成功')
                    decoration.data = decoration.data.filter(item => item.id != res.component.id)
                })
            }
            // 升序
            if (res.type == 'sort_up') {
                sortDecorationData({component: res.component, direction: 'up'})
            }
            // 降序
            if (res.type == 'sort_down') {
                sortDecorationData({component: res.component, direction: 'down'})
            }
        })
        // 打开素材中心弹窗
        cns.$bus.on('openUploadDialog', (params = {show: false, dir_type: 1, multiple: false}) => {
            Object.keys(params).forEach(key => {
                materialCenterDialogData[key] = params[key]
            })
        })
        // 打开路由中心弹窗
        cns.$bus.on('openLinkDialog', (params = {show: false}) => {
            Object.keys(params).forEach(key => {
                linkCenterDialogData[key] = params[key]
            })
        })
        // 打开商品弹窗
        cns.$bus.on('openGoodsDialog', (params = {show: false}) => {
            Object.keys(params).forEach(key => {
                goodsDialogData[key] = params[key]
            })
        })
    })
})

onUnmounted(() => {
    cns.$bus.off('chooseDragItem')
    cns.$bus.off('updateComponentData')
    cns.$bus.off('openUploadDialog')
    cns.$bus.off('openLinkDialog')
    cns.$bus.off('openGoodsDialog')
})

</script>

<!-- <script>
import vClickOutside from '@/utils/clickOutside.ts'; // 导入自定义指令
// 局部注册自定义指令
export default {
    directives: {
        'click-outside': vClickOutside,
    },
};
</script> -->


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
// 公用swiper分页样式
.swiper-pagination.decoration-swiper-pagination,
.swiper-pagination{
    .swiper-pagination-bullet{
        width: 6px;
        height: 6px;
        border-radius: 50%;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.1);
        background: #E2E2E2;
        opacity: 1;
    }
    .swiper-pagination-bullet-active {
        width: 13px;
        border-radius: 20px;
        background-color: var(--main-color);
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
        padding: 20px 400px 20px 20px;
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
                    text-align: center;
                    box-sizing: border-box;
                    background-color: var(--main-color-20);
                    user-select: none;
                    &::after {
                        content: '释放鼠标将组件添加至此处';
                        display: block;
                        width: 375px;
                        height: 44px;
                        line-height: 44px;
                        color: var(--main-color);
                    }
                }
            }
        }
    }
}
</style>