<template>
    <div class='seller-layout' v-if="pageLoad">
        <el-container>
            <el-aside :class="{'left-hidden':!leftShow}">
                <div class='layout-left-header s-flex ai-ct jc-bt'>
                    <div class='seller-picture s-flex ai-ct jc-ct'>
                        <img :src="commonStore.shopConfig.log?commonStore.shopConfig.log:'https://fastly.jsdelivr.net/npm/@vant/assets/logo.png'" alt=''>
                    </div>
                    <div class='s-flex jc-bt ai-ct' style="font-size: 20px;cursor: pointer;" @click="leftShow = false">
                        <Fold style="width: 1.5em; height: 1.5em;" />
                    </div>
                </div>
                <div class='menu-tree' v-if="menus.length>0">
                    <el-tree
                        style="max-width: 200px"
                        :data="menus[menuIndex].children"
                        :icon='ArrowRight'
                        :props="{
                              children: 'children',
                              label: 'label',
                            }"
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
                        <div class='indentation s-flex jc-bt ai-ct' :class="{'indentation-show' : !leftShow}" style="font-size: 20px;cursor: pointer;" @click="leftShow = !leftShow">
                            <Expand style="width: 1.5em; height: 1.5em;" />
                        </div>
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
                                            <div class="s_flex" style="margin-top: 9px">
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
                                        <div class='user-picture'>
                                            <img src='https://fastly.jsdelivr.net/npm/@vant/assets/logo.png' alt=''>
                                        </div>
                                        <span class='co-fff'>张三</span>
                                        <el-icon class='el-icon--right co-fff'>
                                            <arrow-down />
                                        </el-icon>
                                    </div>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item>账号设置</el-dropdown-item>
                                            <el-dropdown-item>刷新</el-dropdown-item>
                                            <el-dropdown-item>退出登录</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </div>
                        </div>
                    </div>
                </el-header>
                <el-main style='height: 100%' class='s-flex flex-dir'>
                    <div class='router-tabs'>
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
                        <router-view v-slot="{ Component }">
                            <transition name="fade" mode="out-in">
                                <keep-alive :include="cachedViews">
                                    <div :key="route.path" style="height: 100%;">
                                        <component :is="Component"></component>
                                    </div>
                                </keep-alive>
                            </transition>
                        </router-view>
                    </div>
                </el-main>
            </el-container>
        </el-container>
    </div>
</template>

<script setup>
import {nextTick, onUnmounted, ref, onMounted, getCurrentInstance,watch,computed} from 'vue';
const cns = getCurrentInstance().appContext.config.globalProperties
import { useRoute,useRouter } from 'vue-router';
import $public from '@/utils/public'
import { ArrowRight } from '@element-plus/icons-vue'
import {getConfigAxios} from "../api/home.js";

import { useCommonStore } from '@/store'
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

const routerActived = ref('')

const visible =ref(false)
const top = ref(0)
const left = ref(0)
const selectedTag = ref({})

watch(() => route.path,(to, from) => {
    for (var index in commonStore.visitedViews) {
        var view = commonStore.visitedViews[index]
        if (view.name === to.name && view.path !== to.path) {
            commonStore.delVisitedViews(view).then((views) => {
                router.push(to.path)
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

const getMenu = () => {
    getConfigAxios().then(res => {
        if (cns.$successCode(res.code)) {
            menus.value = res.data.menus
            formatMenu()
            commonStore.updateShopConfig(res.data.shop_config)
            const root = document.documentElement;
            root.style.setProperty('--manage-color', res.data.shop_config.manage_color);
            root.style.setProperty('--manage-color-over', res.data.shop_config.mouse_move_color);
            pageLoad.value = true
        } else {
            cns.$message.error(res.message);
        }
    });
}

const formatMenu = () => {
    let menuListCopy = []
    menus.value.forEach(item => {
        if(item.children && item.children.length > 0){
            item.children.forEach(ite => {
                if(ite.children && ite.children.length > 0){
                    ite.children.forEach(it => {
                        it.source = item.title + '-' + ite.title
                        menuListCopy.push(it)
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
    router.push(view[0].path)
}

const tabRemove = (name) =>{
    const view = commonStore.visitedViews.filter((ite) => ite.name == name)
    commonStore.delVisitedViews(view[0]).then((views) => {
        if (isActive(view[0])) {
            const latestView = views.slice(-1)[0]
            if (latestView) {
                router.push(latestView.path)
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
    commonStore.refreshQuery(view)
    router.replace({
        name: 'manage.refresh.index',
    })
}

const closeSelectedTag = (view) => {
    if (route.meta.keepAlive) {
        route.meta.keepAlive = false
    }
    commonStore.delVisitedViews(view).then((views) => {
        if (isActive(view)) {
            const latestView = views.slice(-1)[0]
            if (latestView) {
                router.push(latestView.path)
            } else {
                router.push({name: 'manage.home.index'})
            }
        }
    })
}

const closeOthersTags = (view) =>{
    router.push(view.path)
    commonStore.delOthersViews(view).then(() => {
        moveToCurrentTag()
    })
}

const closeAllTags = () => {
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

onMounted(() => {
    getMenu()
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
        &.left-hidden{
            width: 0;
            opacity: 0;
        }
        .layout-left-header{
            height: 60px;
            padding: 0 15px;
            background: var(--manage-color);
            .seller-picture{
                width: 120px;
                height: 40px;
                margin-right: 20px;
            }
            svg{
                color: #ffffff;
            }
        }
        .menu-tree{
            width: 200px;
            padding: 20px 0;
            :deep(.el-tree){
                .el-tree-node{
                    .el-tree-node__content{
                        width: 200px;
                        height: 40px;
                        padding-left: 18px !important;
                        .el-tree-node__expand-icon{
                            position: absolute;
                            right: 10px;
                            color: #31373D;
                            font-size: 14px;
                        }
                        .custom-tree-node{
                            .icons{
                                width: 20px;
                                height: 20px;
                            }
                        }
                        .custom-tree-node span{
                            font-weight: 600;
                            font-size: 14px;
                            color: #31373D;
                        }
                    }
                    .el-tree-node__children{
                        .custom-tree-node span{
                            font-weight: 400;
                            font-size: 14px;
                            color: #333;
                        }
                    }
                    &.is-current{
                        > .el-tree-node__content{
                            background: #F6FAFF;
                            .custom-tree-node{
                                color: #077FFF;
                                span{
                                    color: #077FFF;
                                }
                            }
                        }
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
                .indentation{
                    opacity: 0;
                    width: 0;
                    transition: opacity 0.5s ease-in-out, width 0.5s ease-in-out;
                    color: #ffffff;
                    &.indentation-show{
                        opacity: 1;
                        width: 30px;
                    }
                }
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
                                border-radius: 4px;
                                .el-icon{
                                    color: #ffffff;
                                }
                                .menu-first-name{
                                    margin-left: 5px;
                                    span{
                                        font-weight: normal;
                                        font-size: 18px;
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
                        :deep(.el-select__wrapper){
                            background: var(--manage-color);
                        }
                        :deep(.el-select__input){
                            color: #ffffff;
                        }
                        :deep(.el-input__prefix){
                            left: 10px;
                        }
                        input::placeholder{
                            color: #ffffff;
                        }
                    }
                    .user-info{
                        :deep(.el-dropdown){
                            cursor: pointer;
                            .el-dropdown-link:focus-visible{
                                outline: none;
                            }
                            .user-picture{
                                width: 32px;
                                height: 32px;
                                img {

                                }
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
                        width: 122px;
                        height: 30px;
                        border-radius: 8px 8px 0px 0px;
                        border: solid 1px #D8D8D8;
                        border-bottom: none;
                        padding: 0;
                        margin-right: 4px;

                        display: flex;
                        justify-content: space-around;

                        font-size: 12px;
                        color: #888888;
                        &:last-child{
                            margin-right: 0;
                        }

                        &.is-active{
                            background: #F6FAFF;
                            border: none;
                            color: #077FFF;
                        }
                        .el-icon{
                            margin-right: 5px;
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
