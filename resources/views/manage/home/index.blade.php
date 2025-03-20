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
	<link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2024/04/29/element2-15-14.css">
	<script src="https://cdn.toodudu.com/uploads/2023/05/31/vue2.6.10.min.js"></script>
	<script src="https://cdn.toodudu.com/uploads/2023/05/30/element-2.4.js"></script>
	<script src="https://cdn.toodudu.com/uploads/2023/05/30/pubsub.js?t=1685520761"></script>
	<script src="https://cdn.toodudu.com/uploads/2023/05/30/nprogress.js?t=1685520761"></script>
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

        .el-tabs{width:100%;height:100%;display:flex;flex-direction:column;}
        .el-tabs__content{flex:1 1;display:flex;flex-direction:column;}
        .el-tab-pane{flex:1 1;display:flex;flex-direction:column;position: relative;}
        .ifram-con{height:100%;}

        /*tab*/
        /*.el-tabs__nav-wrap{box-shadow:0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 0 3px 0 rgba(0, 0, 0, 0.04);}*/
        /*.el-tabs--card>.el-tabs__header{border:none !important;margin:0;}*/
        /*.el-tabs__nav-scroll{display:flex;align-items:center;font-size:12px;padding: 0 40px}*/
        /*.el-tabs--card>.el-tabs__header .el-tabs__item{border-left:none !important;border-right:1px solid #e6e6e6 !important;font-size:12px;color:#666;padding:0 25px !important;background: transparent;font-weight: 400;font-size: 16px;color: #999999;}*/
        /*.el-tabs--card>.el-tabs__header .el-tabs__nav{border:none !important;border-radius:0px !important;color:#666;font-size:12px;}*/
        .el-tabs__item:focus.is-active.is-focus:not(:active){-webkit-box-shadow: none !important;box-shadow: none !important;}
        .el-tabs__content{box-sizing:border-box;padding: 20px 20px 0 20px;}
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
                    <em class="el-icon-s-fold" @click="leftShow = false"></em>
                </div>
            </div>
            <div class="layout-left-content">
                <div class="menu-treeBox">
                    <el-tree ref="treeNode" v-if="menus.length>0" :data="menus[menuIndex].children" :props="defaultProps"
                             icon-class="el-icon-arrow-down" node-key="index" :highlight-current="false"
                             :default-expanded-keys="[expandedKeys]"
                             @node-click="handleNodeClick">
                        <div :class="{'custom-tree-node' : true,'custom-tree-node-select' : !data.children,'actived':expandedKeys == data.index}" slot-scope="{ node, data }">
                            <div class="tree-icon" style="margin-right: 8px;">
                                <i v-if="data.level == 1" class="iconfont" :class="data.icon"></i>
                            </div>
                            <span>@{{ node.label }}</span>
                        </div>
                    </el-tree>
                </div>
            </div>
        </div>
		<div class="layout-right" :class="{'layout-right-full': !leftShow}">
			<div class="layout-right-header">
				<div class="left-logo-menu" style="flex: 1;width: 0;">
                    <div class="indentation">
                        <em class="el-icon-s-unfold" @click="leftShow = true"></em>
                    </div>
                    <div style="overflow-x: auto;width: 1400px;">
                        <div style="display: flex">
                            <div class="menu-first">
                                <div class="menu-first-list" v-if="menus.length>0" :class="{'actived':index === menuIndex}" :key="item.index"
                                     @click="leftShow = true,menuIndex = index" v-for="(item,index) in menus">
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
							<i class="el-icon-search" @click="openSearchShow()"></i>
						</div>
						<div class="position-search" v-if="searchShow">
							<div class="search-list-box" :class="searchMenuArr.length?'':'nomore'">
								<template v-if="!!searchMenuArr.length">
									<div class="search-list" v-for="(item,index) in searchMenuArr" :key="item.index">
										<div class="search-list-parent">
											<span>@{{item.title}}</span>
										</div>
										<div class="search-list-child" v-for="(its,ids) in item.children" :key="its.index">
											<template v-if="!!its.children">
												<div class="search-list-parent" style="padding: 0 15px;">
													<span>@{{its.title}}</span>
												</div>
												<div class="childsUl">
													<template v-for="(itas,idas) in its.children">
														<div class="search-list-child" v-if="!!itas.children" :key="itas.index">
															<div class="search-list-parent" style="padding: 0 30px;">
																<span>@{{itas.title}}</span>
															</div>
															<div class="list-search-show" style="padding: 0 45px;"
																 v-for="(itds,itdd) in itas.children"
																 @click="chooseSearch(item,itds)">
																<span>@{{itds.title}}</span>
															</div>
														</div>
														<div class="list-search-show" style="padding: 0 30px;" v-else
															 :key="itas.index" @click="chooseSearch(item,itas)">
															<span>@{{itas.title}}</span>
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
														<span>@{{its.title}}</span>
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
								<span v-else>{{ $admin_user->generateAvatarText()  }}</span>
							</div>
							<i class="el-icon-caret-bottom"></i>
						</div>
						<el-dropdown-menu slot="dropdown">
							<el-dropdown-item command='account_setting'>
								账号设置
							</el-dropdown-item>
							@if($admin_user->is_special_admin==1)
								<el-dropdown-item command='system_config'>系统配置</el-dropdown-item>
							@endif
							<el-dropdown-item command='refresh'>刷新</el-dropdown-item>
							<el-dropdown-item command='logout' divided>退出登录</el-dropdown-item>
						</el-dropdown-menu>
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
							<em class="el-icon-refresh" style="font-size: 18px"></em>
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
<script src="https://cdn-files.ibisaas.com/static/js/jquery-3.7.1.min.js"></script>
<script>
    $('.el-menu--horizontal.el-menu').css({'height':'50px','white-space':'nowrap','overflow':'hidden','min-height':'50px'});
    var flag = false;
    var admin = '{{ $admin_user['user_name'] }}';
    $('.el-menu--horizontal.el-menu').hover(function(){
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
        vm.removeTab('close', vm.tabValue);
        //跳转的处理
        if(title && redirect_url){
            var src_index = vm.fetchSrcIndex(redirect_url);
            var flag = false;
            vm.tabArr.forEach(function (tab, index) {
                if (tab.name == src_index) {
                    vm.tabValue = src_index;
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
		vm.removeTab('close', vm.tabValue);
		//跳转的处理
		if(title && redirect_url){
			var src_index = vm.fetchSrcIndex(redirect_url);
			vm.removeTab('close', src_index);
			openTab(title,redirect_url);
		}
    }

    var vm = new Vue({
        el: '#app',
        data: {
            admin_user: @json($admin_user),
            tabArr: [{
                title: '首页',
                name: '1',
                src: ''
            }],
            menus:@json($menus),
            menuIndex: 0,
            expandedKeys:'',
            defaultProps: {
                children: 'children',
                label: 'title',
                isLeaf: 'isLeaf',
                checked: 'checked',
                id: 'id'
            },
            tabValue: '1',
            tabSrc:'',//激活下的iframe的src
            isvisible:false,//是否显示操作卡
            top:'',//操作卡top
            left:'',//操作卡left
            operaTabVal:'',
            flag: false, // 控制菜单的显示隐藏
            flagChild: false, // 控制子菜单

            leftShow:true,

            searchHide: true, // 是否显示搜索input
            searchVal: '', // 搜索内容


			// 搜索
			searchShow:false,
            searchtools:'',
            searchMenuArr:[],

			//
            visiblePhone:false,
        },
        watch:{
            /** 默认如果全部关闭会打开第一个tab **/
            tabArr: function(newV){
                if(newV.length == 0){
                    this.tabArr.push({
                        title: '首页',
                        name: '1',
                        src: ''

                    })
                    this.tabValue = '1';
                    this.creatIframe(this.tabArr[0].name, this.tabArr[0].src);
                }
            },
            /** 给当前tab添加动画类名 **/
            tabValue: function(newV,oldV){
                //删除动画类名
                this.deleAnim();
                //添加动画类名
                var clNm = "ifr-"+newV;
                let tab = this.tabArr.find(a => a.name === String(newV))
				if (!tab){return false}
                var timers = setInterval(function(){
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
                let obj = this.tabArr.find(a => a.name === newV)
                if(obj.indexArr && obj.indexArr.length){
                    this.menuIndex = obj.indexArr[0]
                    this.expandedKeys = obj.indexArr[1]
                }else{
                    this.menuIndex = 0
                    this.expandedKeys = ''
				}
            },

        },
        methods: {
            toFocus(){
                let rect = event.target.getBoundingClientRect();
                let positionSvg = document.querySelector(".positionSvg");
                positionSvg.style.left = rect.left + 'px'
                positionSvg.style.top = (rect.top+50) + 'px'
            },

            //解析标签的地址
            fetchSrcIndex(src){
                // return src.replace('index.php?','').replace('\?','').replace(/=/g,'_').replace(/&/g,'_').replace(/http:\/\//g,'_').replace('_a_index','');
                // 新版 src转成hash值
                const hash = this.hashCode(src)
                return 'template_'+hash
            },
            hashCode(str) {
                let hash = 0;
                if (str.length === 0) {
                    return hash;
                }
                for (let i = 0; i < str.length; i++) {
                    let char = str.charCodeAt(i);
                    hash = ((hash << 5) - hash) + char;
                    hash = hash & hash; // Convert to 32bit integer
                }
                return Math.abs(hash); // 取绝对值以确保返回正数
            },
            /** 控制菜单显示 **/
            foldMenu: function(){
                if(this.flag){
                    $('.el-menu--horizontal.el-menu').css({'height':'','overflow': '','transition':'all 0.5s'});
                }else{
                    $('.el-menu--horizontal.el-menu').css({'height':'50px','overflow': 'hidden','transition':'all 0.5s'});
                }
            },
            /** 菜单样式初始化 **/
            initMenu: function() {
                $('.el-menu--horizontal.el-menu').css({'height':'50px','white-space':'nowrap','overflow':'hidden','min-height':'50px'});
                $('.el-menu--horizontal.el-menu').hover(function(){
                    vm.flag = !vm.flag;
                    vm.foldMenu();
                })

                $('.el-menu--horizontal').hover(function(){
                    vm.flagChild = !vm.flagChild
                    $('.el-menu--horizontal.el-menu').css({'height':'','overflow': '','transition':'all 0.5s'});
                },function () {
                    vm.flagChild = !vm.flagChild
                    if (!vm.flagChild) {
                        $('.el-menu--horizontal.el-menu').css({'height':'50px','overflow': 'hidden','transition':'all 0.5s'});
                    }
                })
            },
            // refresh: function(){
            //     var nm = $('.el-tabs__nav .is-active').attr('id').slice(4);
            //     this.tabArr.forEach(function (item,index) {
            //         if(item.name == nm){
            //             vm.tabSrc = item.src;
            //             vm.operaTabVal = item.name;
            //         }
            //     })
            //     PubSub.publish('operats', 0)
            // },
            clearCache: function(){
                axios.post('?c=home&a=noauth_clear').then(function (res) {
                    vm.$message.success('缓存清除成功');
                });
            },
            updatePower(){
                axios.post('?c=home&a=noauth_update_power').then(function (res) {
                    vm.$message.success('更新权限成功');
                });
            },
            /** 删除tab **/
            removeTab: function(type, targetName) {

                var tabs = this.tabArr;
                var activeName = this.tabValue;
                if (activeName === targetName) {
                    NProgress.done();
                    tabs.forEach(function (tab, index) {
                        if (tab.name === targetName) {
                            var nextTab = type === 'close' ? tabs[index] : tabs[index + 1] || tabs[index - 1];
                            if (nextTab) {
                                activeName = nextTab.name;
                            }
                        }
                    });
                }
                this.tabValue = activeName;
                this.tabArr = tabs.filter(function (tab) { return tab.name !== targetName });
            },
            /** 选中tab **/
            clickTab: function(e){
                this.tabValue = e.name;
            },
			chooseIndex(e){
                const findIndexRecursively = (items) => {
                    let found = false;
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
                    return found;
                };

                let index = this.menus.findIndex(a => findIndexRecursively(a.children));
                return index !== -1 ? index : -1;
			},
            /** 监听菜单点击事件 和 操作卡点击事件 **/
            pubSub: function(){
                PubSub.subscribe("opentab", function (event, e) {
                    if(!e.children){
                        var src_index = vm.fetchSrcIndex(e.src);
                        var tabs = vm.tabArr;
                        var flag = 0;
                        // 帮助文档辨别标识
                        tabs.forEach(function (tab, index) {
                            if (tab.name == src_index) {
                                vm.tabValue = src_index;
                                flag = 1;
                            }
                        });
                        if(!flag){
							let index = vm.chooseIndex(e)
                            tabs.push({
                                title: e.title,
                                name: src_index,
                                src: e.src,
                                indexArr:index !== -1 ?[index,e.index]:[vm.menuIndex,vm.expandedKeys]
                            });
                            vm.tabValue = src_index;
                            vm.creatIframe(src_index, e.src);
                        }
                    }
                });
                PubSub.subscribe('operats', function (event, e) {
                    if(e == 0){ //刷新
                        vm.tabValue = vm.operaTabVal;
                        vm.creatIframe(vm.tabValue ,vm.tabSrc)
                    }else{
                        vm.closeTab(e);
                    }
                })
            },
            /** 关闭tab--操作卡 **/
            closeTab: function(e){
                var tabV = this.operaTabVal;
                var actTabV = this.tabValue;
                var len = this.tabArr.length;
                var tabIndex;
                if(e==1){
                    if(tabV==actTabV){
                        this.removeTab('click', tabV);
                    }else{
                        this.tabArr.forEach(function (item,index) {
                            if(item.name == tabV){
                                tabIndex = index;
                            }
                        })
                        this.tabArr.splice(tabIndex,1);
                    }
                }else{
                    this.tabArr.forEach(function (item,index) {
                        if(item.name == tabV){
                            tabIndex = index;
                        }
                    })
                    if(e==3){ //  删除全部
                        this.tabArr = [];
                    }else{ //
                        if(tabIndex == 0){
                            this.tabArr.splice(1,len-1);
                        }else{
                            this.tabArr.splice(0,tabIndex);
                            var len2 = this.tabArr.length;
                            this.tabArr.splice(1,len2-1);
                        }
                        if(tabV!=actTabV){
                            this.tabValue = tabV;
                        }
                    }
                }
            },
            /** 创建iframe标签 **/
            creatIframe: function(idN, newSrc){
                NProgress.start();
                // var sName = '#'+'pane-'+idN+' .ifram-con';
                var sName = '#'+'pane-'+idN;
				// let divDom = document.createElement("div")
                // divDom.id = 'pane-'+idN;
                // divDom.className = 'pane-list';
                // document.querySelector(".iframe-box").appendChild(divDom);
                this.check(idN,sName,newSrc);
            },
            check: function(idN,sName,newSrc){
                var that = this;
                var timer = setInterval(function(){
                    var dom = document.querySelectorAll(sName)[0];
                    if(dom){
                        var clNm = "ifr-"+idN;
                        //删除iframe
                        var im = document.getElementsByClassName(clNm)[0];
                        if(im){
                            im.parentNode.removeChild(im);
                        }
                        var iframe = document.createElement("iframe");
                        iframe.src = newSrc;
                        iframe.className = clNm;
                        iframe.id = clNm;
                        // iframe.style.width = "100%";
                        iframe.style.flex = 1
                        iframe.style.height = "100%";
                        iframe.scrolling = 'auto';
                        iframe.setAttribute("frameborder", "0", 0);
                        const divDom = document.querySelector("#divDom"+idN)
                        if(divDom){
                            dom.insertBefore(iframe,divDom);
                        }else{
                            dom.appendChild(iframe);
                        }
						let htmlLoading = document.createElement('div')
						htmlLoading.className = 'iframe-loading loading-'+ idN
						htmlLoading.style.width = '100%'
						htmlLoading.style.height = '100%'
						htmlLoading.style.display = 'flex'
						htmlLoading.style.background = '#fff'
						htmlLoading.style.borderRadius = '15px'
						htmlLoading.style.justifyContent = 'center'
						htmlLoading.style.alignItems = 'center'
						htmlLoading.style.position = 'absolute'
						htmlLoading.style.left = '0'
						htmlLoading.style.top = '0'
						htmlLoading.innerHTML = '<div class="loader" style="width:auto;height:50px;text-align:center;"><img src="{{ shop_config('page_loading_img') }}" alt="" style="height:50px;"></div>'
						dom.appendChild(htmlLoading)
                        // iframe事件穿透增加响应键盘事件
                        iframe.addEventListener('load',function(){
                            vm.iframeObload("#ifr-"+idN,this)
							dom.removeChild(htmlLoading)
                            $('.loading-'+idN).remove()
                        })
                        // iframe.contentWindow.addEventListener('keydown',(e) => {
                        //     that.getKeyCode(e)
                        // })
                        that.loadIframe(iframe);
                        if(timer){
                            clearInterval(timer);
                        }
                    }
                },16)
            },
            /** iframe加载完成事件 **/
            loadIframe: function(iframe){
                if(iframe.readyState){ //IE
                    iframe.onreadystatechange = function(){
                        if(iframe.readyState == 'loaded' || iframe.readyState == 'complete'){
                            NProgress.done();
                        }
                    }
                }else{ //其他浏览器
                    iframe.onload = function(){
                        NProgress.done();
                    }
                }
            },
            iframeObload(id,obj){
                this.$nextTick(() => {
                    obj.contentWindow.removeEventListener('keydown',this.getKeyCode)
                    obj.contentWindow.addEventListener('keydown',this.getKeyCode)
                })
            },
            /** tab右击事件 **/
            conmenuEvent: function(){
                var that = this;
                $('.el-tabs__nav').on('contextmenu','.el-tabs__item',function(e){
                    e.preventDefault();
                    var nm = $(e.currentTarget).attr('id').slice(4);
                    that.tabArr.forEach(function(item,index){
                        if(item.name == nm){
                            that.tabSrc = item.src;
                            that.operaTabVal = item.name;
                            that.left = (e.clientX + 5) + 'px';
                            that.top = e.clientY + 'px';
                            that.isvisible = true;
                        }
                    })
                })
            },
            /** 点击页面其他地方关闭操作卡 **/
            closeOpe: function(){
                var that = this;
                $('html').on('click',function(){
                    that.isvisible = false;
                })
            },
            /** 删除iframe动画类名 **/
            deleAnim: function(){
                var ims = document.getElementsByTagName('iframe');
                if(ims.length>0){
                    var imsArr = Array.from(ims);
                    imsArr.forEach(function(item){
                        item.classList.remove('ifr_active');
                        item.classList.remove('ifr_leave');
                    })
                }
            },
            handleCommand: function(command) {
                switch (command){
                    case 'account_setting' :
                        parent.openTab('账号设置','');
                        break;
                    case 'system_config':
                        parent.openTab('系统配置','');
                        break;
                    case 'data_export':
                        parent.openTab('数据导出','');
                        break;
                    case 'refresh':
                        // window.location.reload();
                        this.tabArr.forEach(function (item,index) {
                            if(item.name == vm.tabValue){
                                vm.tabSrc = item.src;
                                vm.operaTabVal = item.name;
                            }
                        })
                        PubSub.publish('operats', 0)
                        break;
                    case 'logout':
                        window.location.href = '退出';
                }


                // <el-dropdown-item command='system_config'>系统配置</el-dropdown-item>
                // <el-dropdown-item command='update_authority'>更新权限</el-dropdown-item>
                // <el-dropdown-item command='data_export'>数据导出</el-dropdown-item>
                // <el-dropdown-item command='refresh'>刷新</el-dropdown-item>
                // <el-dropdown-item command='logout' divided>退出登录</el-dropdown-item>
            },
            dingLogin() {
                this.$nextTick(() => {
                    var obj = DDLogin({
                        id: "ding-login",
                        goto: this.http_url,
                        style: "border:none;background-color:#FFFFFF;",
                        width: "300", // 二维码的宽度
                        height: "300" // 二维码的高度
                    })
                    // 重置扫码登录框的样式，让登录框居中
                    let box = document.getElementById('ding-login')
                    box.querySelector('iframe').style.top = '0'
                    box.querySelector('iframe').style.bottom = '0'
                    box.querySelector('iframe').style.left = '0'
                    box.querySelector('iframe').style.right = '0'
                    box.querySelector('iframe').style.margin = 'auto'
                })
            },
            debounce (func, wait) {//
                if (this.timeout) clearTimeout(this.timeout)
                this.timeout = setTimeout(() => {
                    func()
                }, wait)
            },
            filterMenuData(arr, searchTerm) {
                return arr
                    .filter(item => item.title) // 过滤掉没有标题的节点
                    .map(item => {
                        if (item.title.includes(searchTerm)) {
                            return { ...item }; // 匹配直接返回
                        } else if (item.children) {
                            // 递归过滤子节点
                            const filteredChildren = this.filterMenuData(item.children, searchTerm);
                            if (filteredChildren.length > 0) {
                                return { ...item, children: filteredChildren }; // 子节点有匹配内容
                            }
                        }
                        return null; // 过滤掉不匹配的节点
                    })
                    .filter(item => item !== null); // 移除空节点
            },
            handleChange(){ //查询事件
                if (!this.searchtools) {
                    this.searchMenuArr = []; // 搜索词为空时，清空结果
                } else {
                    this.searchMenuArr = this.filterMenuData(this.menus, this.searchtools);
                }
            },
			openSearchShow(){
                this.searchtools = ''
                this.searchShow = true;
                this.searchMenuArr=[]
                this.$nextTick(() => {
					this.$refs['searchtool'].focus()
				})
			},
            closeSearch(){
                setTimeout(() => {
                    this.searchtools = ''
                    this.searchShow = false;
                    this.searchMenuArr=[]
				},50)
			},
            getKeyCode(event){
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
                            console.warn('无法访问 iframe 的内容，可能是跨域问题', error);
                        }
                    }
                    event.preventDefault()
                    this.openSearchShow()
                }else if([38,40].includes(event.keyCode) && this.searchShow && this.searchMenuArr.length){
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
                }else if(event.code === 'Enter' && this.searchShow && this.searchMenuArr.length && !!document.querySelector(".search-list-box .list-search-show.active")){
                    document.querySelector(".search-list-box .list-search-show.active").click()
                } else if(this.searchShow && event.key === 'Escape'){
                    event.preventDefault()
					this.closeSearch()
				}
            },
            chooseSearch(item,its){
                PubSub.publish('opentab', its)
                // this.isCollapse = false
                this.closeSearch()
                this.searchMenuArr=[]
                this.visiblePhone = false
            },
            handleNodeClick(data){
                if(data.src !== '/' && !data.children){
                    PubSub.publish('opentab', data)
                    this.visiblePhone = false
                }
            },
        },
        mounted: function () {
            let root = document.querySelector(":root")
            let color_number = 10
            root.style.setProperty("--shop-color", "{{$admin_user['theme_color']}}")
            while (color_number < 100) {
                root.style.setProperty(`--shop-color-${color_number}`, "{{$admin_user['theme_color']}}" + color_number)
                color_number += 10
            }
            root.style.setProperty("--mouse-color", "{{$admin_user['mouse_color']}}")


            /** 监听菜单点击事件 和 操作卡点击事件  **/
            this.pubSub();
            /** 创建iframe标签（默认的第一个tab） **/
            this.creatIframe(this.tabArr[0].name, this.tabArr[0].src);
            /** tab右击事件 **/
            this.conmenuEvent();
            /** 点击页面其他地方关闭操作卡 **/
            this.closeOpe();
            /** 菜单样式初始化 **/
            this.initMenu()

            document.addEventListener('keydown',(e) => {
                this.getKeyCode(e)
            })
        },
    })

</script>
</body>
</html>
