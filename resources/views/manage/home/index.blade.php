<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ shop_config('wap_logo_color') }}">
    <title>{{ shop_config('shop_name') }}-后台管理中心</title>
    <link rel="stylesheet" href="/css/fonts/iconfont.css?time=1998">
    {{--new element css--}}
    <link rel="stylesheet" href="/css/element-plus-index.css">
    <script src="/js/jquery-3.7.1.min.js"></script>
    <script src="/js/vue-3.js"></script>
    <script src="/js/element-plus-index.full.js"></script>
    <script src="/js/element-plus-icons-vue.js"></script>
    <script src="https://cdn.toodudu.com/uploads/2023/05/30/pubsub.js?t=1685520761"></script>
    <script src="https://cdn.toodudu.com/uploads/2023/05/30/nprogress.js?t=1685520761"></script>
    <script src="{{ url('/js/axios.js') }}"></script>
    <script src="https://cstaticdun.126.net/load.min.js?t={$time}"></script>
    <script src="/js/jsencrypt.min.js"></script>
    <style>
        body {
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        #app {
            width: 100vw;
            height: 100vh;
        }

        .layout-container {
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: row;
        }

        .layout-container .layout-right{
            display: flex;
            flex-direction: column;
            width: 0;
            flex: 1;
            transition: flex 0.5s ease-in;
        }
        .layout-container .layout-right .layout-right-content{
            flex: 1;
            height: 0;
        }

        .layout-container .layout-right .layout-right-header {
            width: 100%;
            height: 60px;
            background: #FFFFFF;
            /*background: #333;*/
            /*box-shadow: 0px 1px 6px 0px rgba(16,43,76,0.08);*/
            /*box-shadow: 0px 1px 6px 0px rgba(16,43,76,0.08);*/
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            padding-right: 10px;
            transition: padding 0.5s ease-in;
        }
        .layout-container .layout-right.layout-right-full .layout-right-header{
            padding: 0 10px;
        }
        .mask-search {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2999; /* 确保遮罩层在最上层 */
        }
        /* 渐变效果：进入和离开时的过渡动画 */
        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 0.3s ease;
        }
        .fade-enter,
        .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
            opacity: 0;
        }
        .fade-enter-to {
            opacity: 1;
        }
        .layout-container .layout-left{
            display: flex;
            flex-direction: column;
            width: 184px;
            transition: width 0.5s ease-in-out,opacity 0.5s ease-in-out;
            opacity: 1;
        }
        .layout-container .layout-left.layout-left-hidden{
            width: 0;
            opacity: 0;
        }
        .layout-container .layout-left .layout-left-header{
            padding: 0 15px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .layout-container .logo-imgs {
            width: 113px;
            height: 40px;
        }
        .layout-container .logo-imgs img {
            max-width: 100%;
            max-height: 100%;
        }
        .indentation{
        }
        .indentation em{
            font-size: 22px;
            cursor: pointer;
            color: rgba(51, 51, 51, 0.5);
            transform: rotate(0) !important;
        }
        .layout-container .left-logo-menu {
            display: flex;
            flex-direction: row;
            align-items: center;
            height: 100%;
        }

        .layout-container .layout-right .left-logo-menu .indentation{
            opacity: 0;
            width: 0;
            transition: opacity 0.5s ease-in-out,width 0.5s ease-in-out;
        }

        .layout-container .layout-right.layout-right-full .left-logo-menu .indentation{
            opacity: 1;
            width: 30px;
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first {
            display: flex;
            user-select: none;
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list {
            width: 100px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 4px;
            margin-right: 16px;
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list:last-child {
            margin-right: 0;
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list .icon-imgs {
            /*width: 18px;*/
            /*height: 18px;*/
            line-height: 1;
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list .icon-imgs i {
            font-size: 18px;
            color: #666666;
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list .icon-imgs img {
            width: 100%;
            height: 100%;
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list .menu-first-name {
            margin-left: 8px;
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list .menu-first-name span {
            font-weight: normal;
            font-size: 18px;
            color: #666666;
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list.actived{
            background: var(--shop-color-10);
        }
        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list:hover{
            background: var(--mouse-color);
        }

        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list.actived .icon-imgs i,
        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list.actived .menu-first-name span,
        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list:hover .icon-imgs i,
        .layout-container .layout-right .layout-right-header .left-logo-menu .menu-first .menu-first-list:hover .menu-first-name span{
            color: var(--shop-color);
        }

        .layout-container .layout-right .layout-right-header .right-menuBox {
            display: flex;
            align-items: center;
            height: 100%;
            padding-left: 10px;
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .search-box {
            display: flex;
            align-items: center;
            margin-right: 33px;
            cursor: pointer;
            position: relative;
            z-index: 3000;

        }

        .layout-container .layout-right .layout-right-header .right-menuBox .search-box .small-search {
            position: relative;
            width: 40px; /* 初始宽度 */
            height: 40px;
            overflow: hidden;
            transition: width 0.3s ease ,background-color 0.3s ease; /* 平滑宽度变化 */
            border-radius: 20px;
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .search-box .small-search.show{
            width: 350px;
            background: rgba(51, 51, 51, 0.05);
            background: #fff;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .search-box .small-search.border-half{
            border-radius: 0;
            border-top-right-radius: 20px;
            border-top-left-radius: 20px;
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .search-box .small-search .search-input{
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
            padding-left: 40px;
        }
        .layout-container .layout-right .layout-right-header .right-menuBox .search-box .small-search .el-icon{
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        /* 搜索框展开后显示输入框 */
        .layout-container .layout-right .layout-right-header .right-menuBox .search-box .small-search.show .search-input {
            opacity: 1; /* 显示输入框 */
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .search-box .small-search .el-icon-search{
            position: absolute;
            right: 10px; /* 图标始终贴靠右侧 */
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: #888;
            cursor: pointer;
            z-index: 10;
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .search-box .position-search{
            background: #fff;
            position: absolute;
            width: 350px;
            /*height: 600px;*/
            top: 40px;
            left: 0;

            border-bottom-right-radius: 20px;
            border-bottom-left-radius: 20px;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;
        }


        .layout-container .layout-right .layout-right-header .right-menuBox .user-box {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .user-box .user-headerPicture {
            width: 36px;
            height: 36px;
            background: rgb(124, 161, 230);
            border-radius: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: solid 1px #fff;
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .user-box .user-headerPicture span {
            color: #fff;
            font-size: 12px;
            font-weight: bold;
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .user-box .user-headerPicture img{
            max-width: 100%;
            max-height: 100%;
            border-radius: 5px;
        }

        .layout-container .layout-right .layout-right-header .right-menuBox .user-box i {
            color: rgba(51, 51, 51, 0.3);
            font-size: 14px;
        }


        /* 内容部分 */
        .layout-container .layout-left-content , .layout-container .layout-right-content {
            height: 0;
            flex: 1;
        }

        /* tree */
        .menu-treeBox{
            height: 100%;
            transition: width 0.3s ease-out;
            user-select: none;
        }
        .menu-treeBox .el-tree {
            width: 100%;
            box-sizing: border-box;
            overflow-x: hidden;
            overflow-y: auto;
            height: 100%;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .menu-treeBox .el-tree::-webkit-scrollbar {
            width: 0;
            height: 0;
        }
        .menu-treeBox .el-tree::-webkit-scrollbar-track {
            display: none;
        }

        .menu-treeBox .el-tree-node__content {
            padding: 0 !important;
            height: 45px !important;
            width: 100%;
            position: relative;
            box-sizing: border-box;
        }

        .menu-treeBox .el-tree-node__content .custom-tree-node {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            padding: 0 15px;
            margin: 0 5px;
            border-radius: 4px;
        }
        .menu-treeBox .el-tree-node__content .custom-tree-node span{
            font-weight: bold;
            font-size: 14px;
            color: #666666;

            display: inline-block;
            max-width: calc(8* 1em);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .menu-treeBox .el-tree-node__content .custom-tree-node.custom-tree-node-select span{
            font-weight: 400;
            font-size: 14px;
            color: #666666;
        }

        .el-tree-node__content .custom-tree-node.custom-tree-node-select.actived,
        .el-tree-node:focus>.el-tree-node__content .custom-tree-node{
            background-color: var(--shop-color-10);
        }
        .menu-treeBox .el-tree-node__content .custom-tree-node.custom-tree-node-select.actived span,
        .el-tree-node:focus>.el-tree-node__content .custom-tree-node span,
        .el-tree-node:focus>.el-tree-node__content .custom-tree-node .tree-icon i{
            color: var(--shop-color);
        }

        .menu-treeBox .el-tree-node__content .custom-tree-node .tree-icon{
            /*margin-right: 8px;*/
            width: 16px;
        }
        .menu-treeBox .el-tree-node__content .custom-tree-node .tree-icon i{
            font-size: 16px;
            color: #31373D;
        }


        .menu-treeBox .el-tree-node__content .el-tree-node__expand-icon {
            position: absolute;
            right: 10px;
            transform: rotate(270deg);
            color: #31373D;
            font-size: 14px;
        }

        .menu-treeBox .el-tree-node__content .el-tree-node__expand-icon.expanded{
            transform: rotate(360deg);
        }

        .el-tree-node__expand-icon.is-leaf{
            display: none;
        }

        .el-tree-node:focus>.el-tree-node__content{
            background-color: transparent;
        }

        .el-tree-node__content:hover, .el-upload-list__item:hover{
            background-color: transparent;
        }

        .el-tree-node__content .custom-tree-node:hover, .el-upload-list__item .custom-tree-node:hover{
            background-color: var(--mouse-color) !important;
        }
        .el-tree-node__content .custom-tree-node:hover span, .el-upload-list__item .custom-tree-node:hover span{
            color: var(--shop-color) !important;
        }
        .el-tree-node{
            margin: 2px 0;
        }

        /* tree */
        .layout-container .content-iframe {
            width: 100%;
            height: 100%;
            background: #F5F6FA;
            display: flex;
            flex-direction: column;
            /*position: relative;*/
        }
        .layout-container .content-iframe .open-menuBox{
            width: 100%;
            height: 0;
            flex: 1;
            /*background: #FFFFFF;*/
            /*display: flex;*/
            box-sizing: border-box;
        }

        .el-tabs{width:100%;height:100%;display:flex;flex-direction:column-reverse;}
        .el-tabs__content{flex:1 1;display:flex;flex-direction:column;}
        .el-tab-pane{flex:1 1;display:flex;flex-direction:column;position: relative;}
        .ifram-con{height:100%;}

        /*tab*/
        .el-tabs__item:focus.is-active.is-focus:not(:active){-webkit-box-shadow: none !important;box-shadow: none !important;}
        .el-tabs__content{box-sizing:border-box;padding: 20px;}
        .el-tabs__nav-wrap::after{
            background-color: #E5E5E5;
            height: 1px;
        }
        .el-tabs__header{
            margin: 0 !important;
        }
        .el-tabs__nav-wrap{
            background: #fff;
            /*padding: 4px 4px 0 4px;*/
        }
        .el-tabs__nav-wrap::after{
            content: none;
        }
        .el-tabs__item{
            font-weight: 400;
            font-size: 12px;
            color: #888888;
            padding: 0 20px !important;
            margin-right: 4px;
            border: solid 1px rgba(0, 0, 0, 0.05);
            border-radius: 8px 8px 0px 0px;
            border-bottom: none;
            height: 30px;
            line-height: 30px;
        }
        .el-tabs__item:last-child{
            margin-right: 0;
        }

        .el-tabs__item.is-active,.el-tabs__item:hover{background-color: var(--shop-color-10); color: var(--shop-color);}

        .el-tabs__item .el-icon-close{
            color: #D8D8D8;
            font-size: 14px;
            line-height: 1;
        }
        .el-tabs__item.is-active .el-icon-close,.el-tabs__item:hover .el-icon-close{
            color: var(--shop-color);
        }
        .el-tabs__nav-prev,.el-tabs__nav-next{
            font-size: 18px;
            height: 30px;
            line-height: 30px;
        }
        .el-tabs__nav-next{
            right: 60px !important;
        }
        .el-tabs__nav-prev.is-disabled,.el-tabs__nav-next.is-disabled{
            color: #333;

        }
        .el-tabs__active-bar{
            display: none;
        }
        .el-tabs__nav-wrap.is-scrollable{
            padding: 4px 80px 0 20px !important;
        }

        /* refresh_tabs */
        .refresh_tabs{
            position: absolute;
            top: 69px;
            z-index: 999;
            right: 9px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        /*菜单动画*/
        .el-menu--horizontal.el-menu li{-webkit-transition:all .5s linear;-o-transition:all .5s linear;-moz-transition:all .5s linear;transition:all .5s linear;}
        .el-menu--horizontal.el-menu{z-index:10;}

        /*iframe切换动画*/
        .ifr_active{-webkit-animation:ifranim 0.8s cubic-bezier(.645,.045,.355,1);-o-animation:ifranim 0.8s cubic-bezier(.645,.045,.355,1);-moz-animation:ifranim 0.8s cubic-bezier(.645,.045,.355,1);animation:ifranim 0.8s cubic-bezier(.645,.045,.355,1);}
        @keyframes ifranim{
            0%{ opacity: 0; transform: translate(800px, 0) }
            100%{ opacity: 1; transform: translate(0, 0) }
        }
        /*进度条*/
        #nprogress .bar{top:0px !important;overflow:hidden; }
        #nprogress .spinner{top:0px !important;}

        [v-cloak] { display: none; }

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

        /*移动端tree*/
        .phone-tree .el-drawer{
            width: 40% !important;
        }
        .phone-tree .el-drawer__body{
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .phone-tree .el-drawer__body::-webkit-scrollbar{
            width: 0;
            height: 0;
        }
        .phone-tree .el-drawer__body::-webkit-scrollbar-track{
            display: none;
        }

        .phone-tree .menu-treeBox .el-tree-node__content{
            height: 40px !important;
        }
        .phone-tree .drawer-search{
            width: 90%;
            height: 40px;
            border-radius: 50px;
            background: rgba(51, 51, 51, 0.05);
            /* text-align: center; */
            margin: 0 auto;
            padding: 0 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .phone-tree .drawer-search span{
            font-size: 14px;
            color: rgba(51, 51, 51, 0.5);
        }
        .phone-tree .drawer-search i{
            font-size: 18px;
            color: #333;
        }
        .phone-route-push{
            position: fixed;
            width: 50px;
            height: 50px;
            background: #fff;
            z-index: 99;
            bottom: 4%;
            right: 4%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .phone-route-push span{
            font-size: 10px;
            color: #333;
        }

    </style>
</head>
<body>
<div id="app" v-cloak>
    <div class="layout-container">
        <div class="layout-left" :class="{'layout-left-hidden':!leftShow}">
            <div class="layout-left-header">
                <div class="logo-imgs">
                    <img src="{{ shop_config('shop_logo') }}" alt="">
                </div>
                <div class="indentation">
                    <el-icon @click="leftShow = false"><Fold /></el-icon>
                </div>
            </div>
            <div class="layout-left-content">
                <div class="menu-treeBox">
                    <el-tree ref="treeNode" v-if="menus.length > 0" :data="menus[menuIndex].children" :props="defaultProps"
                             icon="el-icon-arrow-down" node-key="index" :highlight-current="false"
                             :default-expanded-keys="[expandedKeys]"
                             @node-click="handleNodeClick">
                        <template #default="{ node, data }">
                            <div :class="{'custom-tree-node' : true,'custom-tree-node-select' : !data.children,'actived':expandedKeys == data.index}">
                                <div class="tree-icon" style="margin-right: 8px;">
                                    <i v-if="data.level == 1" class="iconfont" :class="data.icon"></i>
                                </div>
                                <span>@{{ node.label }}</span>
                            </div>
                        </template>
                    </el-tree>
                </div>
            </div>
        </div>
        <div class="layout-right" :class="{'layout-right-full': !leftShow}">
            <div class="layout-right-header">
                <div class="left-logo-menu" style="flex: 1;width: 0;">
                    <div class="indentation">
                        <el-icon @click="leftShow = true"><Expand /></el-icon>
                    </div>
                    <div style="overflow-x: auto;width: 1400px;">
                        <div style="display: flex">
                            <div class="menu-first">
                                <div class="menu-first-list" v-if="menus.length > 0" :class="{'actived':index === menuIndex}" :key="item.index"
                                     @click="leftShow = true; menuIndex = index" v-for="(item,index) in menus">
                                    <div class="icon-imgs">
                                        <i class="iconfont" :class="item.icon"></i>
                                    </div>
                                    <div class="menu-first-name">
                                        <span>@{{ item.title }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right-menuBox">
                    <div class="search-box">
                        <div class="small-search" :class="{'show':searchShow,'border-half':searchMenuArr.length}">
                            <input type="text" @input="debounce(handleChange, 600)" v-model="searchtools" ref="searchtool" class="search-input" placeholder="输入 / 快速搜索">
                            <el-icon @click="openSearchShow()"><Search /></el-icon>
                        </div>
                        <div class="position-search" v-if="searchShow">
                            <div class="search-list-box" :class="searchMenuArr.length ? '' : 'nomore'">
                                <template v-if="!!searchMenuArr.length">
                                    <div class="search-list" v-for="(item,index) in searchMenuArr" :key="item.index">
                                        <div class="search-list-parent">
                                            <span>@{{ item.title }}</span>
                                        </div>
                                        <div class="search-list-child" v-for="(its,ids) in item.children" :key="its.index">
                                            <template v-if="!!its.children">
                                                <div class="search-list-parent" style="padding: 0 15px;">
                                                    <span>@{{ its.title }}</span>
                                                </div>
                                                <div class="childsUl">
                                                    <div v-for="(itas,idas) in its.children" :key="itas.index">
                                                        <div class="search-list-child" v-if="!!itas.children">
                                                            <div class="search-list-parent" style="padding: 0 30px;">
                                                                <span>@{{ itas.title }}</span>
                                                            </div>
                                                            <div class="list-search-show" style="padding: 0 45px;" @click="chooseSearch(item,itds)" v-for="(itds,itdd) in itas.children">
                                                                <span>@{{ itds.title }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="list-search-show" style="padding: 0 30px;" v-else @click="chooseSearch(item,itas)">
                                                            <span>@{{ itas.title }}</span>
                                                        </div>
                                                    </div>
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
                                                        <span>@{{ its.title }}</span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <Transition name="fade">
                        <el-dropdown @command="handleCommand">
                            <div class="user-box">
                                <div class="user-headerPicture" :style="{backgroundColor: admin_user.avatar ? 'transparent':'rgb(124, 161, 230)'}">
                                    <img v-if="admin_user.avatar" :src="admin_user.avatar" alt="">
                                    <span v-else>{{ $admin_user->generateAvatarText() }}</span>
                                </div>
                            </div>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item command="account_setting">
                                        账号设置
                                    </el-dropdown-item>
                                    @if($admin_user->is_special_admin == 1)
                                    <el-dropdown-item command="system_config">系统配置</el-dropdown-item>
                                    @endif
                                    <el-dropdown-item command="refresh">刷新</el-dropdown-item>
                                    <el-dropdown-item command="logout" divided>退出登录</el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </Transition>
                </div>
            </div>
            <div class="layout-right-content">
                <div class="content-iframe">
                    <div class="open-menuBox">
                        <el-tabs v-model="tabValue" closable @tab-remove="value => { removeTab('click', value) }" @tab-click="clickTab">
                            <el-tab-pane
                                v-for="(item, index) in tabArr"
                                :key="item.name"
                                :label="item.title"
                                :name="item.name"
                            >
                            </el-tab-pane>
                        </el-tabs>
                        <card-cmp :is-visible="isvisible" :tops="top" :lefts="left"></card-cmp>

                        <div class="refresh_tabs" @click="handleCommand('refresh')">
                            <el-icon style="font-size: 18px"><Refresh /></el-icon>
                            <span style="font-size: 12px;color: #636b6f;">刷新</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Transition name="fade">
        <div class="mask-search" v-if="searchShow" @click="closeSearch()"></div>
    </Transition>
</div>
@include('manage.components.card-components')

<script>
    $('.el-menu--horizontal.el-menu').css({'height':'50px','white-space':'nowrap','overflow':'hidden','min-height':'50px'});
    var flag = false;
    var admin = '{{ $admin_user['user_name'] }}';
    $('.el-menu--horizontal.el-menu').hover(function() {
        flag = !flag;
        foldMenu();
    })
    //顶部导航栏展开折叠

    function openTab(title,src) {
        var index = 1;
        if(src.indexOf('c=home')<0){
            index = src.replace('index\.php?','').replace('?','').replace(/=/g,'_').replace(/&/g,'_').replace(/http:\/\//g,'_').replace('_a_index','');
        }
        var params = {
            src:src,
            title:title,
            index:index
        }
        PubSub.publish('opentab', params)
    }

    /**
     * 关闭自己
     * @param title 页面标题
     * @param redirect_url 跳转的页面
     */
    function closeSelf(title,redirect_url){
        //移除自己
        app.removeTab('close', app.tabValue);
        //跳转的处理
        if(title && redirect_url){
            var src_index = app.fetchSrcIndex(redirect_url);
            var flag = false;
            app.tabArr.forEach(function (tab, index) {
                if (tab.name == src_index) {
                    app.tabValue = src_index;
                    flag = true;
                }
            });
            if(!flag){
                openTab(title,redirect_url);
            }
        }
    }

    /**
     * 关闭当前标签并刷新指定页面
     * @param title
     * @param redirect_url
     */
    function refresh(title,redirect_url) {
        //移除自己
        app.removeTab('close', app.tabValue);
        //跳转的处理
        if(title && redirect_url){
            var src_index = app.fetchSrcIndex(redirect_url);
            app.removeTab('close', src_index);
            openTab(title,redirect_url);
        }
    }

    const { createApp, ref, onMounted, watch, defineComponent,nextTick } = Vue;
    const { ElMessage } = ElementPlus;

    const { Fold,Expand,Search,Refresh } = ElementPlusIconsVue;

    const app = createApp({
        setup() {
            const admin_user = ref(@json($admin_user))
            const tabArr = ref([{ title: '首页', name: '1', src: '' }])
            const menus = ref(@json($menus))
            const menuIndex =  ref(0)
            const expandedKeys = ref('')
            const defaultProps = ref({
                children: 'children',
                label: 'title',
                isLeaf: 'isLeaf',
                checked: 'checked',
                id: 'id'
            })
            const tabValue = ref('1')
            const tabSrc = ref('') //激活下的iframe的src
            const isvisible = ref(false) //是否显示操作卡
            const top = ref('') //操作卡top
            const left = ref('') //操作卡left
            const operaTabVal = ref('')
            const flag = ref(false) // 控制菜单的显示隐藏
            const flagChild = ref(false) // 控制子菜单
            const leftShow = ref(true)
            const searchHide = ref(true) // 是否显示搜索input
            const searchVal = ref('') // 搜索内容
            const searchShow = ref(false)
            const searchtools = ref('')
            const searchMenuArr = ref([])
            const visiblePhone = ref(false)

            // 监听 tabArr 的变化
            watch(tabArr, (newV) => {
                /** 默认如果全部关闭会打开第一个tab **/
                if(newV.length == 0){
                    tabArr.value.push({
                        title: '首页',
                        name: '1',
                        src: ''
                    })
                    tabValue.value = '1';
                    creatIframe(tabArr.value[0].name, tabArr.value[0].src);
                }
            }, { deep: true });

            // 监听 tabValue 的变化
            watch(tabValue, (newV, oldV) => {
                /** 给当前tab添加动画类名 **/
                //删除动画类名
                deleAnim();
                //添加动画类名
                var clNm = "ifr-"+newV;
                let tab = tabArr.value.find(a => a.name === String(newV))
                if (!tab){return false}
                var timers = setInterval(function() {
                    var dom = document.getElementsByClassName(clNm)[0];
                    if(dom){
                        dom.classList.add('ifr_active');
                        if(timers){
                            clearInterval(timers);
                        }
                    }
                    var vm = document.getElementById(clNm).contentWindow.vm;
                    if(vm){
                        if (vm._self.getData && vm.list_is_refresh) {
                            page = vm._self.form ? vm._self.form.page : 1;
                            vm._self.getData(page);
                        }
                    }
                },16)

                // 打开对应位置
                let obj = tabArr.value.find(a => a.name === newV)
                if(obj.indexArr && obj.indexArr.length){
                    menuIndex.value = obj.indexArr[0]
                    expandedKeys.value = obj.indexArr[1]
                }else{
                    menuIndex.value = 0
                    expandedKeys.value = ''
                }
            });

            const toFocus = () => {
                const rect = event.target.getBoundingClientRect();
                const positionSvg = document.querySelector(".positionSvg");
                if (positionSvg) {
                    positionSvg.style.left = rect.left + 'px';
                    positionSvg.style.top = (rect.top + 50) + 'px';
                }
            };

            // 解析标签的地址
            const fetchSrcIndex = (src) => {
                const hash = hashCode(src);
                return 'template_' + hash;
            };

            const hashCode = (str) => {
                let hash = 0;
                if (str.length === 0) return hash;
                for (let i = 0; i < str.length; i++) {
                    const char = str.charCodeAt(i);
                    hash = ((hash << 5) - hash) + char;
                    hash = hash & hash; // Convert to 32bit integer
                }
                return Math.abs(hash); // 取绝对值以确保返回正数
            };

            /** 控制菜单显示 **/
            const foldMenu = () => {
                if (flag.value) {
                    $('.el-menu--horizontal.el-menu').css({
                        'height': '',
                        'overflow': '',
                        'transition': 'all 0.5s'
                    });
                } else {
                    $('.el-menu--horizontal.el-menu').css({
                        'height': '50px',
                        'overflow': 'hidden',
                        'transition': 'all 0.5s'
                    });
                }
            };

            /** 菜单样式初始化 **/
            const initMenu = () => {
                $('.el-menu--horizontal.el-menu').css({
                    'height': '50px',
                    'white-space': 'nowrap',
                    'overflow': 'hidden',
                    'min-height': '50px'
                });
                $('.el-menu--horizontal.el-menu').hover(() => {
                    flag.value = !flag.value;
                    foldMenu();
                });

                $('.el-menu--horizontal').hover(() => {
                    flagChild.value = !flagChild.value;
                    $('.el-menu--horizontal.el-menu').css({
                        'height': '',
                        'overflow': '',
                        'transition': 'all 0.5s'
                    });
                }, () => {
                    flagChild.value = !flagChild.value;
                    if (!flagChild.value) {
                        $('.el-menu--horizontal.el-menu').css({
                            'height': '50px',
                            'overflow': 'hidden',
                            'transition': 'all 0.5s'
                        });
                    }
                });
            };

            const clearCache = () => {
                axios.post('?c=home&a=noauth_clear').then(res => {
                    ElMessage.success('缓存清除成功');
                });
            };

            const updatePower = () => {
                axios.post('?c=home&a=noauth_update_power').then(res => {
                    ElMessage.success('更新权限成功');
                });
            };

            /** 删除tab **/
            const removeTab = (type, targetName) => {
                let tabs = tabArr.value;
                let activeName = tabValue.value;
                if (activeName === targetName) {
                    NProgress.done();
                    tabs.forEach((tab, index) => {
                        if (tab.name === targetName) {
                            const nextTab = type === 'close' ? tabs[index] : tabs[index + 1] || tabs[index - 1];
                            if (nextTab) {
                                activeName = nextTab.name;
                            }
                        }
                    });
                }
                tabValue.value = activeName;
                tabArr.value = tabs.filter(tab => tab.name !== targetName);
            };

            /** 选中tab **/
            const clickTab = (e) => {
                tabValue.value = e.name;
            };

            const chooseIndex = (e) => {
                const findIndexRecursively = (items) => {
                    let found = false;
                    if(items && items.length > 0){
                        items.some(item => {
                            if (item.children) {
                                found = findIndexRecursively(item.children); // 递归调用自身处理更深层级的 children
                            }
                            if (found) return true; // 如果在子层级找到了目标索引，立即停止遍历
                            if (item.index === e.index) {
                                found = true;
                                return true; // 找到目标索引，立即停止遍历
                            }
                            return false; // 继续遍历
                        });
                    }
                    return found;
                };
                const index = menus.value.findIndex(a => findIndexRecursively(a.children));
                return index !== -1 ? index : -1;
            };

            /** 监听菜单点击事件 和 操作卡点击事件 **/
            const pubSub = () => {
                PubSub.subscribe("opentab", (event, e) => {
                    if (!e.children) {
                        const src_index = fetchSrcIndex(e.src);
                        const tabs = tabArr.value;
                        let flag = 0;
                        // 帮助文档辨别标识
                        tabs.forEach((tab, index) => {
                            if (tab.name === src_index) {
                                tabValue.value = src_index;
                                flag = 1;
                            }
                        });
                        if (!flag) {
                            const index = chooseIndex(e);
                            tabs.push({
                                title: e.title,
                                name: src_index,
                                src: e.src,
                                indexArr: index !== -1 ? [index, e.index] : [menuIndex.value, expandedKeys.value]
                            });
                            tabValue.value = src_index;
                            creatIframe(src_index, e.src);
                        }
                    }
                });
                PubSub.subscribe('operats', (event, e) => {
                    if (e === 0) { // 刷新
                        tabValue.value = operaTabVal.value;
                        creatIframe(tabValue.value, tabSrc.value);
                    } else {
                        closeTab(e);
                    }
                });
            };

            /** 关闭tab--操作卡 **/
            const closeTab = (e) => {
                const tabV = operaTabVal.value;
                const actTabV = tabValue.value;
                const len = tabArr.value.length;
                let tabIndex;
                if (e === 1) {
                    if (tabV === actTabV) {
                        removeTab('click', tabV);
                    } else {
                        tabArr.value.forEach((item, index) => {
                            if (item.name === tabV) {
                                tabIndex = index;
                            }
                        });
                        if (tabIndex !== undefined) {
                            tabArr.value.splice(tabIndex, 1);
                        }
                    }
                } else {
                    tabArr.value.forEach((item, index) => {
                        if (item.name === tabV) {
                            tabIndex = index;
                        }
                    });
                    if (tabIndex !== undefined) {
                        if (e === 3) { // 删除全部
                            tabArr.value = [];
                        } else { //
                            if (tabIndex === 0) {
                                tabArr.value.splice(1, len - 1);
                            } else {
                                tabArr.value.splice(0, tabIndex);
                                const len2 = tabArr.value.length;
                                tabArr.value.splice(1, len2 - 1);
                            }
                            if (tabV !== actTabV) {
                                tabValue.value = tabV;
                            }
                        }
                    }
                }
            };

            /** 创建iframe标签 **/
            const creatIframe = (idN, newSrc) => {
                NProgress.start();
                const sName = '#' + 'pane-' + idN;
                check(idN, sName, newSrc);
            };

            const check = (idN, sName, newSrc) => {
                const timer = setInterval(() => {
                    const dom = document.querySelectorAll(sName)[0];
                    if (dom) {
                        const clNm = "ifr-" + idN;
                        // 删除iframe
                        const im = document.getElementsByClassName(clNm)[0];
                        if (im) {
                            im.parentNode.removeChild(im);
                        }
                        const iframe = document.createElement("iframe");
                        iframe.src = newSrc;
                        iframe.className = clNm;
                        iframe.id = clNm;
                        iframe.style.flex = 1;
                        iframe.style.height = "100%";
                        iframe.scrolling = 'auto';
                        iframe.setAttribute("frameborder", "0", 0);
                        const divDom = document.querySelector("#divDom" + idN);
                        if (divDom) {
                            dom.insertBefore(iframe, divDom);
                        } else {
                            dom.appendChild(iframe);
                        }
                        const htmlLoading = document.createElement('div');
                        htmlLoading.className = 'iframe-loading loading-' + idN;
                        htmlLoading.style.width = '100%';
                        htmlLoading.style.height = '100%';
                        htmlLoading.style.display = 'flex';
                        htmlLoading.style.background = '#fff';
                        htmlLoading.style.borderRadius = '15px';
                        htmlLoading.style.justifyContent = 'center';
                        htmlLoading.style.alignItems = 'center';
                        htmlLoading.style.position = 'absolute';
                        htmlLoading.style.left = '0';
                        htmlLoading.style.top = '0';
                        htmlLoading.innerHTML = '<div class="loader" style="width:auto;height:50px;text-align:center;"><img src="{{ shop_config('page_loading_img') }}" alt="" style="height:50px;"></div>';
                        dom.appendChild(htmlLoading);
                        // iframe事件穿透增加响应键盘事件
                        iframe.addEventListener('load', () => {
                            iframeObload("#ifr-" + idN, iframe);
                            dom.removeChild(htmlLoading);
                            $('.loading-' + idN).remove();
                        });
                        loadIframe(iframe);
                        clearInterval(timer);
                    }
                }, 16);
            };

            /** iframe加载完成事件 **/
            const loadIframe = (iframe) => {
                if (iframe.readyState) { // IE
                    iframe.onreadystatechange = () => {
                        if (iframe.readyState === 'loaded' || iframe.readyState === 'complete') {
                            NProgress.done();
                        }
                    };
                } else { // 其他浏览器
                    iframe.onload = () => {
                        NProgress.done();
                    };
                }
            };

            const iframeObload = (id, obj) => {
                nextTick(() => {
                    obj.contentWindow.removeEventListener('keydown', getKeyCode);
                    obj.contentWindow.addEventListener('keydown', getKeyCode);
                });
            };

            /** tab右击事件 **/
            const conmenuEvent = () => {
                $('.el-tabs__nav').on('contextmenu', '.el-tabs__item', (e) => {
                    e.preventDefault();
                    const nm = $(e.currentTarget).attr('id').slice(4);
                    tabArr.value.forEach((item, index) => {
                        if (item.name === nm) {
                            tabSrc.value = item.src;
                            operaTabVal.value = item.name;
                            left.value = (e.clientX + 5) + 'px';
                            top.value = e.clientY + 'px';
                            isvisible.value = true;
                        }
                    });
                });
            };

            /** 点击页面其他地方关闭操作卡 **/
            const closeOpe = () => {
                $('html').on('click', () => {
                    isvisible.value = false;
                });
            };

            /** 删除iframe动画类名 **/
            const deleAnim = () => {
                const ims = document.getElementsByTagName('iframe');
                if (ims.length > 0) {
                    const imsArr = Array.from(ims);
                    imsArr.forEach(item => {
                        item.classList.remove('ifr_active');
                        item.classList.remove('ifr_leave');
                    });
                }
            };

            const handleCommand = (command) => {
                switch (command) {
                    case 'account_setting':
                        parent.openTab('账号设置', '');
                        break;
                    case 'system_config':
                        parent.openTab('系统配置', '');
                        break;
                    case 'data_export':
                        parent.openTab('数据导出', '');
                        break;
                    case 'refresh':
                        // window.location.reload();
                        tabArr.value.forEach((item, index) => {
                            if (item.name === tabValue.value) {
                                tabSrc.value = item.src;
                                operaTabVal.value = item.name;
                            }
                        });
                        PubSub.publish('operats', 0);
                        break;
                    case 'logout':
                        window.location.href = '退出';
                        break;
                }
            };

            const dingLogin = () => {
                nextTick(() => {
                    const obj = DDLogin({
                        id: "ding-login",
                        goto: window.location.href,
                        style: "border:none;background-color:#FFFFFF;",
                        width: "300", // 二维码的宽度
                        height: "300" // 二维码的高度
                    });
                    // 重置扫码登录框的样式，让登录框居中
                    const box = document.getElementById('ding-login');
                    if (box) {
                        box.querySelector('iframe').style.top = '0';
                        box.querySelector('iframe').style.bottom = '0';
                        box.querySelector('iframe').style.left = '0';
                        box.querySelector('iframe').style.right = '0';
                        box.querySelector('iframe').style.margin = 'auto';
                    }
                });
            };

            const debounce = (func, wait) => {
                if (this.timeout) clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    func();
                }, wait);
            };

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
            };

            const handleChange = () => { // 查询事件
                if (!searchtools.value) {
                    searchMenuArr.value = []; // 搜索词为空时，清空结果
                } else {
                    searchMenuArr.value = filterMenuData(menus.value, searchtools.value);
                }
            };

            const openSearchShow = () => {
                searchtools.value = '';
                searchShow.value = true;
                searchMenuArr.value = [];
                nextTick(() => {
                    const searchtool = document.querySelector('.search-input');
                    if (searchtool) {
                        searchtool.focus();
                    }
                });
            };
            const closeSearch = () => {
                setTimeout(() => {
                    searchtools.value = '';
                    searchShow.value = false;
                    searchMenuArr.value = [];
                }, 50);
            };

            const getKeyCode = (event) => {
                if (event.key === '/') {
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
                            console.warn('无法访问 iframe 的内容，可能是跨域问题', error);
                        }
                    }
                    event.preventDefault();
                    openSearchShow();
                } else if ([38, 40].includes(event.keyCode) && searchShow.value && searchMenuArr.value.length) {
                    event.preventDefault();
                    let listsearchshow = document.querySelectorAll(".search-list-box .list-search-show");
                    let searchlistbox = document.querySelector(".search-list-box");
                    let flag = -1;
                    for (let i = 0; i < listsearchshow.length; i++) {
                        if (listsearchshow[i].className.indexOf('active') > -1) {
                            flag = i;
                            break;
                        }
                    }
                    if (flag !== -1) {
                        if (event.keyCode === 38) {
                            if (flag === 0) {
                                listsearchshow[flag].classList.remove('active');
                                listsearchshow[listsearchshow.length - 1].classList.add('active');
                                searchlistbox.scrollTop = listsearchshow[listsearchshow.length - 1].offsetTop - searchlistbox.offsetHeight + 40;
                            } else if (flag - 1 < listsearchshow.length) {
                                listsearchshow[flag].classList.remove('active');
                                listsearchshow[flag - 1].classList.add('active');
                                if (listsearchshow[flag - 1].getBoundingClientRect().top - document.querySelector(".position-search").getBoundingClientRect().top < 100) {
                                    searchlistbox.scrollTop = listsearchshow[flag - 1].offsetTop - 64 + 30;
                                }
                            }
                        } else {
                            if (flag + 1 < listsearchshow.length) {
                                listsearchshow[flag].classList.remove('active');
                                listsearchshow[flag + 1].classList.add('active');
                                if (listsearchshow[flag + 1].getBoundingClientRect().top - document.querySelector(".position-search").getBoundingClientRect().top > searchlistbox.offsetHeight) {
                                    searchlistbox.scrollTop = listsearchshow[flag + 1].offsetTop - searchlistbox.offsetHeight + 30;
                                }
                            } else if (flag + 1 === listsearchshow.length) {
                                listsearchshow[flag].classList.remove('active');
                                listsearchshow[0].classList.add('active');
                                searchlistbox.scrollTop = 0;
                            }
                        }
                    } else {
                        if (event.keyCode === 40) {
                            listsearchshow[0].classList.add('active');
                        } else {
                            listsearchshow[listsearchshow.length - 1].classList.add('active');
                            searchlistbox.scrollTop = listsearchshow[listsearchshow.length - 1].offsetTop - searchlistbox.offsetHeight + 30;
                        }
                    }
                } else if (event.code === 'Enter' && searchShow.value && searchMenuArr.value.length && !!document.querySelector(".search-list-box .list-search-show.active")) {
                    document.querySelector(".search-list-box .list-search-show.active").click();
                } else if (searchShow.value && event.key === 'Escape') {
                    event.preventDefault();
                    closeSearch();
                }
            };

            const chooseSearch = (item, its) => {
                PubSub.publish('opentab', its);
                closeSearch();
                searchMenuArr.value = [];
                visiblePhone.value = false;
            };

            const handleNodeClick = (data) => {
                if (data.src !== '/' && !data.children) {
                    PubSub.publish('opentab', data);
                    visiblePhone.value = false;
                }
            };

            onMounted(() => {
                let root = document.querySelector(":root");
                let color_number = 10;
                root.style.setProperty("--shop-color", admin_user.value.theme_color);
                while (color_number < 100) {
                    root.style.setProperty(`--shop-color-${color_number}`, admin_user.value.theme_color + color_number);
                    color_number += 10;
                }
                root.style.setProperty("--mouse-color", admin_user.value.mouse_color);

                /** 监听菜单点击事件 和 操作卡点击事件  **/
                pubSub();
                /** 创建iframe标签（默认的第一个tab） **/
                creatIframe(tabArr.value[0].name, tabArr.value[0].src);
                /** tab右击事件 **/
                conmenuEvent();
                /** 点击页面其他地方关闭操作卡 **/
                closeOpe();
                /** 菜单样式初始化 **/
                initMenu();

                document.addEventListener('keydown', (e) => {
                    getKeyCode(e);
                });
            });

            return {
                admin_user,
                tabArr,
                menus,
                menuIndex,
                expandedKeys,
                defaultProps,
                tabValue,
                tabSrc,
                isvisible,
                top,
                left,
                operaTabVal,
                flag,
                flagChild,
                leftShow,
                searchHide,
                searchVal,
                searchShow,
                searchtools,
                searchMenuArr,
                visiblePhone,
                foldMenu,
                initMenu,
                clearCache,
                updatePower,
                removeTab,
                clickTab,
                chooseIndex,
                pubSub,
                closeTab,
                creatIframe,
                check,
                loadIframe,
                iframeObload,
                conmenuEvent,
                closeOpe,
                deleAnim,
                handleCommand,
                dingLogin,
                debounce,
                filterMenuData,
                handleChange,
                openSearchShow,
                closeSearch,
                getKeyCode,
                chooseSearch,
                handleNodeClick
            };
        }
    })

    // 注册 Element Plus
    app.use(ElementPlus);
    // 注册必要的图标组件
    app.component('Fold', Fold);
    app.component('Expand', Expand);
    app.component('Search', Search);
    app.component('Refresh', Refresh);
    app.component('cardCmp', cardCmp);
    app.mount('#app');
</script>
</body>
</html>
