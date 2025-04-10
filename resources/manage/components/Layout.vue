<template>
    <div class='seller-layout' v-if="pageLoad">
        <el-container>
            <el-aside :class="{'left-hidden':!leftShow}">
                <div class='layout-left-header s-flex ai-ct jc-bt' @click="router.push({name:'manage.home.index'})">
                    <div class='seller-picture s-flex ai-ct jc-ct flex-1'>
                        <img :src="commonStore.shopConfig.shop_logo" v-if="commonStore.shopConfig.shop_logo" alt=''>
                    </div>
                </div>
                <div class='menu-tree' v-if="menus.length>0">
                    <el-tree
                        ref="menuTreeRef"
                        style="max-width: 200px"
                        :data="menus[menuIndex].children"
                        :icon='ArrowRight'
                        :props="{
                              children: 'children',
                              label: 'label',
                            }"
                        node-key="index"
                        :highlight-current="true"
                        @node-click="(e,data,el) => openMenu(e)">
                        <template #default="{ node, data }">
                            <div class="custom-tree-node s-flex ai-ct">
                                <el-icon v-if='data.icon'>
                                    <component :is="data.icon"/>
                                </el-icon>
                                <span class="ml-10">{{ data.title }}</span>
                            </div>
                        </template>
                    </el-tree>
                </div>
            </el-aside>
            <el-container>
                <el-header>
                    <div class='seller-header s-flex jc-bt ai-ct'>
                        <div class='header-left s-flex ai-ct'>
                            <div class='flow-menu s-flex'>
                                <div class='menu-box'>
                                    <div class='s-flex'>
                                        <div class='menu-list s-flex jc-ct ai-ct' :class='{actived:index === menuIndex}' :key="item.index" v-for='(item,index) in menus'  @click="leftShow = true,menuIndex = index">
                                            <el-icon v-if="item.icon" :size="20">
                                                <component :is="item.icon"/>
                                            </el-icon>
                                            <div class="menu-first-name co-666"><span>{{item.title}}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='header-right s-flex ai-ct'>
                            <el-select
                                v-model="menuValue"
                                class="menu-select"
                                filterable
                                remote
                                placeholder="搜索/快速搜索"
                                popper-class="menu-option-box"
                                :remote-method="remoteMethod"
                                @visible-change="changeSelect"
                                :loading="menuLoading">
                                <template #prefix>
                                    <el-icon style="color: #ffffff" :size="20">
                                        <Search></Search>
                                    </el-icon>
                                </template>
                                <el-option
                                    v-for="(item,index) in menuOptions"
                                    :key="index"
                                    :label="item.title"
                                    :value="item.index">
                                    <template #default>
                                        <div class="menu-option-model" @click="toOption(item)">
                                            <div class="s_flex">
                                                <template v-for="(ite,index) in item.title.split(menuQuery)">
                                                    <span>{{ite}}</span>
                                                    <span style="color: #F41313" v-if="index < item.title.split(menuQuery).length - 1">{{menuQuery}}</span>
                                                </template>
                                            </div>
                                            <div class="s_flex" style="margin-top: 9px" v-if="item.source">
                                                <template v-for="(ite,index) in item.source.split(menuQuery)">
                                                    <span>{{ite}}</span>
                                                    <span style="color: #F41313" v-if="index < item.source.split(menuQuery).length - 1">{{menuQuery}}</span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </el-option>
                            </el-select>
                            <div class='user-info'>
                                <el-dropdown>
                                    <div class='el-dropdown-link s-flex ai-ct'>
                                        <span class='co-fff'>{{ commonStore.adminUser.user_name }}</span>
                                        <el-icon class='el-icon--right co-fff'>
                                            <arrow-down />
                                        </el-icon>
                                    </div>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item @click="router.push({name:'manage.shop_config.index'})">设置</el-dropdown-item>
                                            <el-dropdown-item @click="logOut">退出登录</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </div>
                        </div>
                    </div>
                </el-header>
                <el-main style='height: 100%' class='s-flex flex-dir'>
                    <div class='router-tabs s-flex ai-ct jc-bt'>
                        <el-tabs
                            v-model="routerActived"
                            closable
                            @tab-remove="tabRemove"
                            @tab-change="tabChange"
                        >
                            <el-tab-pane
                                v-for="item in Array.from(commonStore.visitedViews)"
                                :key="item.name"
                                :label="item.title"
                                :name="item.name">
                                <template #label>
                                    <div class="s-flex ai-ct jc-ct flex-1" style="height: 100%" @contextmenu.prevent.native="openTabMenu(item, $event)">{{ item.title }}</div>
                                </template>
                            </el-tab-pane>
                        </el-tabs>
                        <div class="refresh-btn s-flex ai-ct" @click="dropRefresh"><el-icon><Refresh /></el-icon><div>刷新</div></div>
                        <ul
                            class="contextmenu"
                            v-show="visible"
                            :style="{ left: left + 'px', top: top + 'px' }"
                        >
                            <li @click="refresh(selectedTag)">刷新</li>
                            <li @click="closeSelectedTag(selectedTag)">关闭</li>
                            <li @click="closeOthersTags(selectedTag)">关闭其他</li>
                            <li @click="closeAllTags">关闭所有</li>
                        </ul>
                    </div>
                    <div class='flex-1' id="shopLayoutView" style='height: 0;background: var(--page-bg-color);padding: 16px;overflow-y: auto;'>
                        <router-view v-slot="{ Component }" v-if="isRendered">
                            <transition name="fade" mode="out-in">
                                <keep-alive :include="cachedViews">
                                    <div :key="route.path" style="height: 100%;">
                                        <component :is="Component"></component>
                                    </div>
                                </keep-alive>
                            </transition>
                        </router-view>
                        <div v-else v-loading="!isRendered" class="bg-fff" style="width: 100%;height: 100%;"></div>
                    </div>
                </el-main>
            </el-container>
            <div class="narrow-box" :class="{'narrow-launch' : !leftShow}">
                <svg v-if="leftShow" @click="leftShow = !leftShow" t="1741575361343" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2301" width="16" height="16"><path d="M935.27 12.34H89.78c-41.38 0-75.23 33.85-75.23 75.23S48.4 162.8 89.78 162.8h845.49c41.38 0 75.23-33.85 75.23-75.23 0-41.37-33.85-75.23-75.23-75.23zM935.27 295.17H496.64c-41.38 0-75.23 33.85-75.23 75.23s33.85 75.23 75.23 75.23h438.63c41.38 0 75.23-33.85 75.23-75.23 0-41.37-33.85-75.23-75.23-75.23zM935.27 578.27H496.64c-41.38 0-75.23 33.85-75.23 75.23s33.85 75.23 75.23 75.23h438.63c41.38 0 75.23-33.85 75.23-75.23 0-41.37-33.85-75.23-75.23-75.23zM935.27 861.64H89.78c-41.38 0-75.23 33.85-75.23 75.23s33.85 75.23 75.23 75.23h845.49c41.38 0 75.23-33.85 75.23-75.23 0-41.37-33.85-75.23-75.23-75.23zM26.95 544.85l235.2 176.67c25.2 18.93 61.19 0.95 61.19-30.57V332.96c0-31.74-36.44-49.65-61.57-30.28L26.57 484.01c-20.01 15.42-19.82 45.67 0.38 60.84z" fill="#2c2c2c" p-id="2302"></path></svg>
                <svg v-else @click="leftShow = !leftShow" t="1741574477397" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2610" width="16" height="16"><path d="M51.2 819.2l921.601 0 0 102.4-921.601-1e-8 0-102.39999999z" p-id="2611"></path><path d="M358.4 563.199l614.39899999 0 1e-8 103.253-614.399 0 0-103.253z" p-id="2612"></path><path d="M358.4 358.4l614.39899999 0 1e-8 103.253-614.399 0 0-103.253z" p-id="2613"></path><path d="M51.2 102.40000001l921.601-1e-8 0 103.253-921.601-1e-8c0 0 0-103.253 0-103.25299998z" p-id="2614"></path><path d="M51.2 332.8l205.653 205.653-205.653 153.6 0-359.253z" p-id="2615"></path></svg>
            </div>
        </el-container>
    </div>
</template>

<script setup>
import {nextTick, onUnmounted, ref, onMounted, getCurrentInstance,watch,computed} from 'vue';
const cns = getCurrentInstance().appContext.config.globalProperties
import { useRoute,useRouter } from 'vue-router';
import { ArrowRight } from '@element-plus/icons-vue'
import {getConfigAxios} from "../api/home.js";

import { useCommonStore } from '@/store'
import {accountLogout} from "../api/user.js";
const commonStore = useCommonStore()

const route = useRoute()
const router =  useRouter()

const pageLoad = ref(false)
const menus = ref([])
const menuIndex = ref(0)

const leftShow = ref(true)

const menuList = ref([])
const menuValue = ref('')//快速搜索
const menuQuery = ref('')
const menuOptions = ref([])
const menuLoading = ref(false)
const menuTreeRef =ref(null)

const routerActived = ref('')

const visible =ref(false)
const top = ref(0)
const left = ref(0)
const selectedTag = ref({})
const isRendered = ref(true)

watch(() => route.path,(to, from) => {
    for (var index in commonStore.visitedViews) {
        var view = commonStore.visitedViews[index]
        if (view.name === to.name && view.path !== to.path) {
            commonStore.delVisitedViews(view).then((views) => {
                router.push({name:to.name,query:to.query})
            })
            break
        }
    }
    addViewTags()
    moveToCurrentTag()
})

watch(() => visible.value,(value) => {
    if (value) {
        document.body.addEventListener('click', closeMenu)
    } else {
        document.body.removeEventListener('click', closeMenu)
    }
})

const getConfig = () => {
    getConfigAxios().then(res => {
        if (cns.$successCode(res.code)) {
            menus.value = res.data.menus
            formatMenu()
            commonStore.updateShopConfig(res.data.shop_config)
            commonStore.updateAdminUser(res.data.admin_user)
            const root = document.documentElement;
            root.style.setProperty('--manage-color', res.data.shop_config.manage_color);
            root.style.setProperty('--manage-color-over', res.data.shop_config.mouse_move_color);
            pageLoad.value = true
            nextTick(()=>{
                checkMenuActive()
            })
        } else {
            cns.$message.error(res.message);
        }
    });
}
const checkMenuActive = () => {
    menus.value.forEach((item,index) => {
        if (item.children && item.children.length>0){
            item.children.forEach(ite => {
                if (ite.children && ite.children.length>0){
                    ite.children.forEach(it => {
                        if (it.children && it.children.length>0){
                            it.children.forEach(its => {
                                if (its.name == route.name){
                                    menuIndex.value = index
                                    menuTreeRef.value.setCurrentKey(Number(its.index))
                                }
                            })
                        }else{
                            if (it.name == route.name){
                                menuIndex.value = index
                                menuTreeRef.value.setCurrentKey(Number(it.index))
                            }
                        }
                    })
                }else{
                    if (ite.name == route.name){
                        menuIndex.value = index
                        menuTreeRef.value.setCurrentKey(Number(ite.index))
                    }
                }
            })
        }
    })
}
const formatMenu = () => {
    let menuListCopy = []
    menus.value.forEach(item => {
        if(item.children && item.children.length > 0){
            item.children.forEach(ite => {
                if(ite.children && ite.children.length > 0){
                    ite.children.forEach(it => {
                        if (it.children && it.children.length > 0){
                            it.children.forEach(its => {
                                its.source = item.title + '-' + ite.title + '-' + it.title
                                menuListCopy.push(its)
                            })
                        }else{
                            it.source = item.title + '-' + ite.title
                            menuListCopy.push(it)
                        }
                    })
                }else{
                    ite.source = item.title
                    menuListCopy.push(ite)
                }
            })
        }else{
            menuListCopy.push(item)
        }
    })
    menuList.value = menuListCopy
}

const openMenu = (e) => {
    if (typeof (e.children) === 'undefined' || e.children.length === 0) {
        router.push({name:e.name})
    } else {
        return false
    }
}

const addViewTags = () => {
    const add_route = generateRoute()
    if (!add_route) {
        return false
    }
    let routeFileter = commonStore.visitedViews.filter(item => item.name == add_route.name)
    if (routeFileter.length>0){
        return false
    }
    commonStore.addVisitedViews(add_route)
}

const generateRoute = () => {
    if (route.name) {
        return route
    }
    return false
}
const isActive = (view) => {
    return view.path === route.path
}
const moveToCurrentTag = () => {
    routerActived.value = route.name
}

const tabChange = (name) =>{
    const view = commonStore.visitedViews.filter((ite) => ite.name == name)
    router.push({ name: view[0].name , query: view[0].query})
}

const tabRemove = (name) =>{
    const view = commonStore.visitedViews.filter((ite) => ite.name == name)
    if (name == 'manage.home.index' && commonStore.visitedViews.length == 1) return
    commonStore.delVisitedViews(view[0]).then((views) => {
        if (isActive(view[0])) {
            const latestView = views.slice(-1)[0]
            if (latestView) {
                router.push({name: latestView.name,query:latestView.query})
            } else {
                router.push({name: 'manage.home.index'})
            }
        }
    })

}

let cachedViews = computed(() => {
    if (route.meta && route.meta.keepAlive) {
        commonStore.addCachedViews(route)
    }
    return commonStore.cachedViews
})

const openTabMenu = (tag, e) => {
    visible.value = true
    selectedTag.value = tag
    left.value = e.clientX
    top.value = e.clientY
}

const closeMenu = () =>{
    visible.value = false
}

const refresh = (view) => {
    commonStore.refreshQuery(view).then(() => {
        nextTick(() => {
            router.replace({
                name: 'manage.refresh.index',
            })
        });
    })
}

const closeSelectedTag = (view) => {
    if (route.meta.keepAlive) {
        route.meta.keepAlive = false
    }
    if (view.name == 'manage.home.index' && commonStore.visitedViews.length == 1) return
    commonStore.delVisitedViews(view).then((views) => {
        if (isActive(view)) {
            const latestView = views.slice(-1)[0]
            if (latestView) {
                router.push({name: latestView.name,query:latestView.query})
            } else {
                router.push({name: 'manage.home.index'})
            }
        }
    })
}

const closeOthersTags = (view) =>{
    router.push({name: view.name,query:view.query})
    commonStore.delOthersViews(view).then(() => {
        moveToCurrentTag()
    })
}

const closeAllTags = () => {
    if (route.name == 'manage.home.index' && commonStore.visitedViews.length == 1) return
    commonStore.delAllViews()
    router.push({name: 'manage.home.index'})
}

const remoteMethod = (query) => {
    menuQuery.value = query
    if (query !== '') {
        menuLoading.value = true;
        setTimeout(() => {
            menuLoading.value = false;
            menuOptions.value = menuList.value.filter(item => {
                return item.title.toLowerCase().indexOf(query.toLowerCase()) > -1 || (item.source && item.source.toLowerCase().indexOf(query.toLowerCase()) > -1);
            });
        }, 200);
    } else {
        menuOptions.value = [];
    }
}

const changeSelect = (event) => {
    if(!event){
        menuOptions.value = [];
        menuValue.value = ''
    }
}
const toOption = (item) => {
    menuValue.value = ''
    router.push({name:item.name})
}
const dropRefresh = () => {
    isRendered.value = false;
    setTimeout(() => {
        isRendered.value = true;
    }, 400);
}
const logOut = () =>{
    cns.$confirm('确定要退出登录?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        accountLogout().then(res => {
            if(cns.$successCode(res.code)){
                cns.$cookies.remove('manage-token')
                router.push({name:'login'})
                commonStore.resetVisitedViews()
            } else {
                cns.$message.error(res.message);
            }
        })
    });
}

onMounted(() => {
    getConfig()
    addViewTags()
    routerActived.value = route.name
})

onUnmounted(() => {

})

</script>

<style scoped lang='scss'>
.seller-layout{
    &,.el-container{
        width: 100vw;
        height: 100vh;
        overflow: hidden;
    }
    .el-aside{
        width: 200px;
        opacity: 1;
        transition: width 0.5s ease-in-out, opacity 0.5s ease-in-out;
        box-shadow: 0px 0px 3px 0px rgba(16, 43, 76, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        &.left-hidden{
            width: 0;
            opacity: 0;
        }
        .layout-left-header{
            height: 60px;
            padding: 0 15px;
            background: var(--manage-color);
            cursor: pointer;
            .seller-picture{
                height: 40px;
                width: 100%;
            }
        }
        .menu-tree{
            width: 200px;
            padding: 20px 0;
            user-select: none;
            flex: 1;
            overflow:auto;
            &::-webkit-scrollbar {
                display: none;
            }
            :deep(.el-tree){
                .el-tree-node{
                    .el-tree-node__content{
                        padding: 0 !important;
                        height: 50px !important;
                        width: 100%;
                        position: relative;
                        box-sizing: border-box;
                        .custom-tree-node{
                            width: 100%;
                            height: 100%;
                            display: flex;
                            align-items: center;
                            padding: 0 6px 0 6px;
                            margin: 0 10px 0 8px;
                            border-radius: 6px;
                            &.custom-tree-node-select.actived{
                                background-color: #F6FAFF;
                                span{
                                    color: #077FFF;
                                }
                            }
                            &:hover{
                                background-color: #F4F4F4 !important;
                            }
                        }
                        .custom-tree-node span{
                            font-weight: 600;
                            font-size: 14px;
                            color: #333333;
                        }
                        .custom-tree-node.custom-tree-node-select span {
                            font-weight: 400;
                            font-size: 14px;
                            color: #333;
                        }
                        .el-tree-node__expand-icon{
                            position: absolute;
                            right: 8px;
                            transform: rotate(270deg);
                            color: #31373D;
                        }

                    }
                    .el-tree-node__children{
                        padding-left: 5px;
                        .custom-tree-node span{
                            font-size: 14px;
                            color: #333333;
                            font-weight: 400;
                        }
                    }
                    &:focus{
                        > .el-tree-node__content{
                            background-color: transparent;
                            .custom-tree-node {
                                background-color: #F6FAFF;
                                span{
                                    color: #077FFF;
                                }
                            }
                        }
                    }
                    :hover{
                        background: unset;
                    }
                }
            }
            :deep(.el-tree--highlight-current .el-tree-node.is-current>.el-tree-node__content){
                background: #ffffff;
                .custom-tree-node{
                    background-color: #F6FAFF;
                    span{
                        color: #077FFF;
                    }
                }
            }
        }
    }
    .el-container{
        transition: flex 0.5s ease-in;
        .el-header{
            padding: 0;
            box-shadow: 0px 1px 6px 0px rgba(16, 43, 76, 0.08);
            .seller-header{
                height: 60px;
                padding: 0 10px 0 0;
                background: var(--manage-color);
                .header-left{
                    flex: 1 1 0%;
                    width: 0px;
                    .flow-menu{
                        overflow-x: auto;
                        .menu-box{
                            user-select: none;
                            .menu-list{
                                width: 120px;
                                height: 60px;
                                cursor: pointer;
                                .el-icon{
                                    color: #ffffff;
                                    svg{
                                        width: 20px;
                                        height: 20px;
                                    }
                                }
                                .menu-first-name{
                                    margin-left: 5px;
                                    span{
                                        font-weight: normal;
                                        font-size: 16px;
                                        color: #ffffff;
                                    }
                                }
                                &.actived , &:hover{
                                    background: var(--manage-color-over);
                                }
                            }
                        }
                    }
                }
                .header-right{
                    .menu-select{
                        background: var(--manage-color);
                        margin-right: 30px;
                        width: 210px;
                        height:40px;
                        :deep(.el-select__wrapper){
                            background: var(--manage-color);
                            height:40px;
                            box-shadow: none;
                            border-radius: 20px;
                            border: 1px solid #bbbbbb;
                        }
                        :deep(.el-select__input){
                            color: #ffffff;
                        }
                        :deep(.el-input__prefix){
                            left: 10px;
                        }
                        :deep(.el-select__placeholder.is-transparent){
                            color: #ffffff;
                        }

                    }
                    .user-info{
                        margin-right:20px;
                        :deep(.el-dropdown){
                            cursor: pointer;
                            .el-dropdown-link:focus-visible{
                                outline: none;
                            }
                            span{
                                font-size: 16px;
                                font-weight: normal;
                                margin: 0 5px;
                            }
                        }

                    }
                }

            }
        }
        .el-main{
            padding: 0;
            .router-tabs{
                :deep(.el-tabs){
                    padding: 4px 77px 0 7px;
                    height: 34px;
                    .el-tabs__header{
                        margin-bottom: 0;
                    }
                    .el-tabs__nav-wrap{
                        &::after{
                            content: none;
                        }
                    }
                    .el-tabs__active-bar{
                        visibility: hidden;
                    }
                    .el-tabs__item{
                        font-weight: 400;
                        font-size: 12px;
                        color: #888888;
                        padding: 0 20px !important;
                        margin-right: 4px;
                        border: solid 1px #D8D8D8;
                        border-radius: 8px 8px 0px 0px;
                        border-bottom: none;
                        height: 30px;
                        line-height: 30px;
                        user-select: none;
                        &:last-child{
                            margin-right: 0;
                        }

                        &.is-active{
                            background: #F6FAFF;
                            color: #077FFF;
                        }
                        .el-icon{
                            margin-right: 5px;
                        }
                    }
                }
                .refresh-btn{
                    font-size: 14px;
                    color: #666666;
                    margin-right: 20px;
                    cursor: pointer;
                    .el-icon{
                        margin-right: 5px;
                        width: 18px;
                        height: 18px;
                        svg{
                            width: 18px;
                            height: 18px;
                        }
                    }
                }
                .contextmenu {
                    margin: 0;
                    background: #fff;
                    z-index: 100;
                    position: absolute;
                    list-style-type: none;
                    padding: 5px 0;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: 400;
                    color: #333;
                    box-shadow: 2px 2px 3px 0 rgba(0, 0, 0, 0.3);

                    li {
                        margin: 0;
                        padding: 7px 16px;
                        cursor: pointer;

                        &:hover {
                            background: #eee;
                        }
                    }
                }
            }
        }
    }
}
img{
    max-width: 100%;
    max-height: 100%;
}
.narrow-box {
    position: fixed;
    bottom: 33px;
    left: 0;
    transition: transform 0.3s ease;
    transform: translateX(167px);
}
.narrow-box.narrow-launch{
    transform: translateX(8px);
}
.narrow-box .icon{
    cursor: pointer;
}
</style>
<style>
.menu-option-box{
    max-height: 440px;
    background: #EEEEEE;
}
.menu-option-box .el-select-dropdown__wrap{
    max-height: 440px;
    background: #EEEEEE;
}
.menu-option-box .el-select-dropdown__list{
    background: #EEEEEE;
    padding:0 5px 5px;
}
.menu-option-box .el-select-dropdown__item{
    width: 250px;
    height: 50px;
    border-radius: 5px;
    background: #ffffff;
    margin-top: 5px;
    padding-left: 18px;
}
.menu-option-box .el-select-dropdown__item:hover{
    background: #DEDCDC;
}
.menu-option-box .menu-option-model{
    font-size: 12px;
    color: #1D2129;
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1;
    height: 50px;
    width: 100%;
}
</style>
