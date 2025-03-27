<template>
    <div class='seller-layout'>
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
                <div class='menu-tree'>
                    <el-tree
                        style="max-width: 200px"
                        :data="data"
                        :icon='ArrowRight'
                        :props="{
                              children: 'children',
                              label: 'label',
                            }">
                        <template #default="{ node, data }">
                            <div class="custom-tree-node s-flex ai-bs">
                                <div class='icons'>
                                    <el-icon v-if='data.children'><Setting /></el-icon>
                                </div>
                                <span>{{ node.label }}</span>
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
                                        <div class='menu-list s-flex jc-ct ai-ct' :class='{actived:item === 1}' v-for='item in 11'>
                                            <div class="icon-imgs"><i class="iconfont dingdan_pc_tb co-666"></i></div>
                                            <div class="menu-first-name co-666"><span>订单</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='header-right s-flex ai-ct'>
                            <div class="header-search" :class="{'show':searchShow}">
                                <input type="text" v-model="searchtools" ref="searchtoolRef" class="search-input" placeholder="输入 / 快速搜索">
                                <el-icon @click="openSearchShow()" :size="20"><Search /></el-icon>
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
                        >
                            <el-tab-pane
                                v-for="item in editableTabs"
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
import { nextTick, ref } from 'vue';
import { Search , Bell , Setting , ArrowRight , Fold , Expand } from '@element-plus/icons-vue'

const data = [
    {
        label: '基础设置',
    },
    {
        label: '权限管理',
        children: [
            {
                label: '商品管理',

            },
            {
                label: '商品编辑',
            },
        ],
    },
]
const editableTabs = [
    {
        title: '基础设置',
        name: '1',
    },
    {
        title: '商品管理',
        name: '2',
    },
]

const leftShow = ref(true)

const searchtoolRef = ref(null)
const searchShow = ref(false)
const searchtools = ref('')

const routerActived = ref('1')

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
    },50)
}
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
                .indentation{
                    opacity: 0;
                    width: 0;
                    transition: opacity 0.5s ease-in-out, width 0.5s ease-in-out;
                    &.indentation-show{
                        opacity: 1;
                        margin: 0 10px;
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
                                height: 40px;
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
                                &.actived{
                                    background: var(--main-color-10);
                                }
                            }
                        }
                    }
                }
                .header-right{
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
