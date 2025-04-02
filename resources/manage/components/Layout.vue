<template>
    <div class='seller-layout' v-if="pageLoad">
        <el-container>
            <el-aside :class="{'left-hidden':!leftShow}">
                <div class='layout-left-header s-flex ai-ct jc-bt'>
                    <div class='seller-picture s-flex ai-ct jc-ct'>
                        <img src='https://fastly.jsdelivr.net/npm/@vant/assets/logo.png' alt=''>
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
                            <div class='search-box'>
                                <div class="header-search" :class="{'show':searchShow,'border-half':searchMenuArr.length}">
                                    <input type="text" v-model="searchtools" @input="debounceSearch" ref="searchtoolRef" class="search-input" placeholder="输入 / 快速搜索">
                                    <el-icon @click="openSearchShow()" :size="20"><Search /></el-icon>
                                </div>
                                <div class="position-search" v-if="searchShow">
                                    <div class="search-list-box" :class="searchMenuArr.length?'':'nomore'">
                                        <template v-if="!!searchMenuArr.length">
                                            <div class="search-list" v-for="(item,index) in searchMenuArr" :key="item.index">
                                                <div class="search-list-parent">
                                                    <span>{{item.title}}</span>
                                                </div>
                                                <div class="search-list-child" v-for="(its,ids) in item.children" :key="its.index">
                                                    <template v-if="!!its.children">
                                                        <div class="search-list-parent" style="padding: 0 15px;">
                                                            <span>{{its.title}}</span>
                                                        </div>
                                                        <div class="childsUl">
                                                            <template v-for="(itas,idas) in its.children">
                                                                <div class="search-list-child" v-if="!!itas.children" :key="itas.index">
                                                                    <div class="search-list-parent" style="padding: 0 30px;">
                                                                        <span>{{itas.title}}</span>
                                                                    </div>
                                                                    <div class="list-search-show" style="padding: 0 45px;"
                                                                         v-for="(itds,itdd) in itas.children"
                                                                         @click="chooseSearch(item,itds)">
                                                                        <span>{{itds.title}}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="list-search-show" style="padding: 0 30px;" v-else
                                                                     :key="itas.index" @click="chooseSearch(item,itas)">
                                                                    <span>{{itas.title}}</span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="childsUl">
                                                            <div class="list-search-show" style="padding: 0 8px;"
                                                                 @click="chooseSearch(item,its)">
                                                                <svg width="20" height="20" viewBox="0 0 20 20">
                                                                    <path d="M17 6v12c0 .52-.2 1-1 1H4c-.7 0-1-.33-1-1V2c0-.55.42-1 1-1h8l5 5zM14 8h-3.13c-.51 0-.87-.34-.87-.87V4"
                                                                          stroke="currentColor" fill="none" fill-rule="evenodd"
                                                                          stroke-linejoin="round"></path>
                                                                </svg>
                                                                <span>{{its.title}}</span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class='more-func s-flex ai-ct'>
                                <div class='circle s-flex ai-ct jc-ct'>
                                    <el-icon :size="20"><Bell /></el-icon>
                                </div>
                                <div class='circle s-flex ai-ct jc-ct'>
                                    <el-icon :size="20"><Setting /></el-icon>
                                </div>
                            </div>
                            <div class='user-info'>
                                <el-dropdown>
                                    <div class='el-dropdown-link s-flex ai-ct'>
                                        <div class='user-picture'>
                                            <img src='https://fastly.jsdelivr.net/npm/@vant/assets/logo.png' alt=''>
                                        </div>
                                        <span class='co-3D'>张三</span>
                                        <el-icon class='el-icon--right'>
                                            <arrow-down />
                                        </el-icon>
                                    </div>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item>Action 1</el-dropdown-item>
                                            <el-dropdown-item>Action 2</el-dropdown-item>
                                            <el-dropdown-item>Action 3</el-dropdown-item>
                                            <el-dropdown-item disabled>Action 4</el-dropdown-item>
                                            <el-dropdown-item divided>Action 5</el-dropdown-item>
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
                                v-for="item in Array.from(tabsStore.visitedViews)"
                                :key="item.name"
                                :label="item.title"
                                :name="item.name"
                            />
                        </el-tabs>
                    </div>
                    <div class='flex-1' style='height: 0;background: var(--page-bg-color);padding: 16px;overflow-y: auto;'>
                        <router-view></router-view>
                    </div>
                </el-main>
            </el-container>
        </el-container>
        <Transition name="fade">
            <div class="mask-search" v-if="searchShow" @click="closeSearch()"></div>
        </Transition>
    </div>
</template>

<script setup>
import {nextTick, onUnmounted, ref, onMounted, getCurrentInstance,watch} from 'vue';
const cns = getCurrentInstance().appContext.config.globalProperties
import { useRoute,useRouter } from 'vue-router';
import $public from '@/utils/public'
import { ArrowRight } from '@element-plus/icons-vue'
import {getMenuAxios} from "../api/home.js";

import { useTabsStore } from '@/store'
const tabsStore = useTabsStore()

const route = useRoute()
const router =  useRouter()
watch(() => route.path,(to, from) => {
    for (var index in tabsStore.visitedViews) {
        var view = tabsStore.visitedViews[index]
        if (view.name === to.name && view.path !== to.path) {
            tabsStore.delVisitedViews(view).then((views) => {
                router.push(to.path)
            })
            break
        }
    }
    addViewTags()
    moveToCurrentTag()
})

const pageLoad = ref(false)
const menus = ref([])
const menuIndex = ref(0)

const leftShow = ref(true)

const searchtoolRef = ref(null)
const searchShow = ref(false)
const searchtools = ref('')
const searchMenuArr = ref([])

const routerActived = ref('')


const openSearchShow = () => {
    searchtools.value = ''
    searchShow.value = true
    nextTick(() => {
        searchtoolRef.value.focus()
    })
}
const closeSearch = () => {
    setTimeout(() => {
        searchtools.value = ''
        searchShow.value = false;
        searchMenuArr.value = []
    },50)
}

const filterMenuData = (arr, searchTerm) => {
    return arr
        .filter(item => item.title) // 过滤掉没有标题的节点
        .map(item => {
            if (item.title.includes(searchTerm)) {
                return { ...item }; // 匹配直接返回
            } else if (item.children) {
                // 递归过滤子节点
                const filteredChildren = filterMenuData(item.children, searchTerm);
                if (filteredChildren.length > 0) {
                    return { ...item, children: filteredChildren }; // 子节点有匹配内容
                }
            }
            return null; // 过滤掉不匹配的节点
        })
        .filter(item => item !== null); // 移除空节点
}

const  handleChange = () => { //查询事件
    if (!searchtools.value) {
        searchMenuArr.value = []; // 搜索词为空时，清空结果
    } else {
        searchMenuArr.value = filterMenuData(menus.value, searchtools.value);
    }
}
const debounceSearch = $public.debounce(handleChange,600)

const chooseSearch = (item,its) => {
    closeSearch()
    searchMenuArr.value = []
}
const getKeyCode = (event) => {
    if(event.key === '/'){
        const activeElement = document.activeElement;

        // 如果活动元素是输入框或可编辑区域，拦截操作
        if (
            activeElement.tagName === 'INPUT' ||
            activeElement.tagName === 'TEXTAREA' ||
            activeElement.isContentEditable
        ) {
            return true; // 应该拦截
        }

        // 如果活动元素是 iframe，深入检查 iframe 内部的活动元素
        if (activeElement.tagName === 'IFRAME') {
            try {
                const iframeDoc = activeElement.contentDocument || activeElement.contentWindow.document;
                const iframeActiveElement = iframeDoc.activeElement;

                if (
                    iframeActiveElement.tagName === 'INPUT' ||
                    iframeActiveElement.tagName === 'TEXTAREA' ||
                    iframeActiveElement.isContentEditable
                ) {
                    return true; // 应该拦截
                }
            } catch (error) {
                console.warn('', error);
            }
        }
        event.preventDefault()
        openSearchShow()
    }else if([38,40].includes(event.keyCode) && searchShow.value && searchMenuArr.value.length){
        event.preventDefault()
        let listsearchshow = document.querySelectorAll(".search-list-box .list-search-show")
        let searchlistbox = document.querySelector(".search-list-box")
        let flag = -1
        for(let i=0;i<listsearchshow.length;i++){
            if(listsearchshow[i].className.indexOf('active') > -1){
                flag = i
                break
            }
        }
        if(flag !== -1){
            if(event.keyCode === 38){
                if(flag === 0){
                    listsearchshow[flag].classList.remove('active')
                    listsearchshow[listsearchshow.length-1].classList.add('active')
                    searchlistbox.scrollTop = listsearchshow[listsearchshow.length-1].offsetTop-searchlistbox.offsetHeight+40
                }else if(flag-1 < listsearchshow.length){
                    listsearchshow[flag].classList.remove('active')
                    listsearchshow[flag-1].classList.add('active')
                    if(listsearchshow[flag-1].getBoundingClientRect().top - document.querySelector(".position-search").getBoundingClientRect().top < 100){
                        searchlistbox.scrollTop = listsearchshow[flag-1].offsetTop-64+30
                    }
                }
            }else{
                if(flag+1 < listsearchshow.length){
                    listsearchshow[flag].classList.remove('active')
                    listsearchshow[flag+1].classList.add('active')
                    if(listsearchshow[flag+1].getBoundingClientRect().top - document.querySelector(".position-search").getBoundingClientRect().top > searchlistbox.offsetHeight){
                        searchlistbox.scrollTop = listsearchshow[flag+1].offsetTop-searchlistbox.offsetHeight+30
                    }
                }else if(flag+1 === listsearchshow.length){
                    listsearchshow[flag].classList.remove('active')
                    listsearchshow[0].classList.add('active')
                    searchlistbox.scrollTop = 0
                }
            }
        }else{
            if(event.keyCode === 40){
                listsearchshow[0].classList.add('active')
            }else{
                listsearchshow[listsearchshow.length-1].classList.add('active')
                searchlistbox.scrollTop = listsearchshow[listsearchshow.length-1].offsetTop-searchlistbox.offsetHeight+30
            }
        }
    }else if(event.code === 'Enter' && searchShow.value && searchMenuArr.value.length && !!document.querySelector(".search-list-box .list-search-show.active")){
        document.querySelector(".search-list-box .list-search-show.active").click()
    } else if(searchShow.value && event.key === 'Escape'){
        event.preventDefault()
        closeSearch()
    }
}

const getMenu = () => {
    getMenuAxios().then(res => {
        if (res.code === 200) {
            menus.value = res.data
            pageLoad.value = true
        } else {
            cns.$message.error(res.message);
        }
    });
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
    tabsStore.addVisitedViews(add_route)
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
    const view = tabsStore.visitedViews.filter((ite) => ite.name == name)
    router.push(view[0].path)
}

const tabRemove = (name) =>{
    const view = tabsStore.visitedViews.filter((ite) => ite.name == name)
    tabsStore.delVisitedViews(view[0]).then((views) => {
        if (isActive(view[0])) {
            const latestView = views.slice(-1)[0]
            if (latestView) {
                router.push(latestView.path)
            } else {
                router.push('/home')
            }
        }
    })
}

onMounted(() => {
    getMenu()
    addViewTags()
    routerActived.value = route.name
    document.addEventListener('keydown',getKeyCode)
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
            .seller-picture{
                width: 91px;
                height: 41px;
                margin-right: 30px;
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
                    }
                    &.is-current{
                        > .el-tree-node__content{
                            background: #F6FAFF;
                            .custom-tree-node{
                                color: #077FFF;
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
                padding: 0 10px;
                .indentation{
                    opacity: 0;
                    width: 0;
                    transition: opacity 0.5s ease-in-out, width 0.5s ease-in-out;
                    &.indentation-show{
                        opacity: 1;
                        //margin-right: 10px;
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
                                width: 100px;
                                height: 32px;
                                cursor: pointer;
                                border-radius: 4px;
                                margin-right: 16px;
                                .icon-imgs{
                                    i{
                                        font-size: 18px;
                                    }
                                }
                                .menu-first-name{
                                    margin-left: 8px;
                                    span{
                                        font-weight: normal;
                                        font-size: 18px;
                                    }
                                }
                                &.actived , &:hover{
                                    background: var(--main-color-10);
                                }
                            }
                        }
                    }
                }
                .header-right{
                    .search-box{
                        display: flex;
                        align-items: center;
                        cursor: pointer;
                        position: relative;
                        z-index: 2000;
                        .header-search{
                            position: relative;
                            width: 32px; /* 初始宽度 */
                            height: 32px;
                            overflow: hidden;
                            transition: width 0.3s ease, background-color 0.3s ease; /* 平滑宽度变化 */
                            border-radius: 20px;
                            margin-right: 16px;

                            border: 1px solid #F2F3F5;
                            &.show {
                                width: 350px;
                                background: rgba(51, 51, 51, 0.05);
                                background: #fff;
                                box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;

                                .search-input {
                                    opacity: 1; /* 显示输入框 */
                                }
                            }

                            &.border-half {
                                border-radius: 0;
                                border-top-right-radius: 20px;
                                border-top-left-radius: 20px;
                                border-bottom: 0;
                            }

                            .search-input {
                                position: absolute;
                                left: 10px; /* 输入框左内边距 */
                                top: 50%;
                                transform: translateY(-50%);
                                width: calc(100% - 50px); /* 留出右侧图标空间 */
                                border: none;
                                outline: none;
                                font-size: 14px;
                                opacity: 0; /* 初始不可见 */
                                background: transparent;
                                transition: opacity 0.3s ease; /* 渐显动画 */
                                color: #333;
                            }

                            .el-icon {
                                position: absolute;
                                right: 5px; /* 图标始终贴靠右侧 */
                                top: 50%;
                                transform: translateY(-50%);
                                color: #888;
                                cursor: pointer;
                                z-index: 10;
                            }
                        }
                        .position-search{
                            background: #fff;
                            position: absolute;
                            width: 350px;
                            /*height: 600px;*/
                            top: 32px;
                            left: 0;

                            border-bottom-right-radius: 20px;
                            border-bottom-left-radius: 20px;
                            box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;
                            border: 1px solid #F2F3F5;
                            border-top: none;
                            /*search样式*/
                            .search-list-box{
                                max-height: 430px;
                                margin: 20px;
                                overflow-y: auto;
                                position: relative;
                                box-sizing: border-box;
                            }
                            .search-list-box.nomore{
                                margin: 0;
                            }
                            .search-list-box .search-list{margin: 0 0 16px;}
                            .search-list-box .search-list .search-list-parent{
                                color: #666666;
                                margin: 13px 0 11px;
                                font-weight: 400;
                                font-size: 16px;
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                            }
                            .search-list-box .search-list .search-list-child > .search-list-parent{
                                position: relative;
                            }
                            .search-list-box .search-list .search-list-child > .search-list-parent::before{
                                content: '';
                                position: absolute;
                                width: 4px;
                                height: 4px;
                                background: #999999;
                                left: 0px;
                                border-radius: 50%;
                            }
                            .search-list-box .search-list .search-list-child > .childsUl > .search-list-child > .search-list-parent::before{
                                left: 15px !important;
                            }
                            .search-list-box .search-list .search-list-child > .search-list-parent > span{
                                font-size: 14px;
                                font-weight: 500;
                                color: #666666;
                            }
                            .search-list-box .search-list .list-search-show{
                                cursor: pointer;
                                padding: 0 16px;
                                /*min-height: 40px;*/
                                border-radius: 4px;
                                line-height: 30px;
                                display: flex;
                                align-items: center;
                            }
                            .search-list-box .search-list .list-search-show:hover span,.search-list-box .search-list .list-search-show.active span{
                                color: #278FF0;
                            }
                            .search-list-box .search-list .list-search-show span{
                                margin-left: 8px;
                                font-size: 14px;
                                font-family: Source Han Sans CN;
                                font-weight: 400;
                                color: #333333;
                            }
                            .search-tips {
                                text-align: center;
                                font-size: 20px;
                                font-weight: 600;
                                position: absolute;
                                left: 50%;
                                top: 50%;
                                transform: translate(-50%,-50%);
                            }
                        }
                    }

                    .more-func{
                        .circle{
                            width: 32px;
                            height: 32px;
                            margin-right: 16px;
                            border-radius: 50%;
                            border: 1px solid #F2F3F5;
                            cursor: pointer;

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
                    }
                }
            }
        }
    }
}
.mask-search{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* background-color: rgba(0, 0, 0, 0.5); */
    z-index: 2999;
}
img{
    max-width: 100%;
    max-height: 100%;
}


</style>
