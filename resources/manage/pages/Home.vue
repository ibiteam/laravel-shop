<template>
    <div class='seller-home'>
        <div class="home-container">
            <div class="home-content s-flex jc-bt">
                <div class="home-left">
                    <div class="information">
                        <div class="welcome">
                            <em class=""></em>
                            <span><em style="font-size: 30px;margin-right: 5px;">👏</em>上午好，张三</span>
                        </div>
                        <div class="quick-view-box s-flex">
                            <div class="quick-view s-flex ai-ct">
                                <div class="fonts s-flex ai-ct jc-ct">
                                    <img src="@/assets/images/home/user.png" alt="">
                                </div>
                                <div class="s-flex flex-dir ai-fs jc-bt quick-view-info">
                                    <span class="fs12">用户数</span>
                                    <span class="fs22" style="color: #551A8B">{{ number_data.user_number?number_data.user_number:'--' }}</span>
                                </div>
                            </div>
                            <div class="quick-view s-flex ai-ct">
                                <div class="fonts s-flex ai-ct jc-ct">
                                    <img src="@/assets/images/home/order.png" alt="">
                                </div>
                                <div class="s-flex flex-dir ai-fs jc-bt quick-view-info">
                                    <span class="fs12">订单数</span>
                                    <span class="fs22" style="color: #551A8B">{{ number_data.order_number?number_data.order_number:'--' }}</span>
                                </div>
                            </div>
                            <div class="quick-view s-flex ai-ct">
                                <div class="fonts s-flex ai-ct jc-ct">
                                    <img src="@/assets/images/home/transcation.png" alt="">
                                </div>
                                <div class="s-flex flex-dir ai-fs jc-bt quick-view-info">
                                    <span class="fs12">总交易额</span>
                                    <span class="fs22" style="color: #551A8B">{{ number_data.total_transaction_value?number_data.total_transaction_value:'--' }}</span>
                                </div>
                            </div>
                            <div class="quick-view s-flex ai-ct">
                                <div class="fonts s-flex ai-ct jc-ct">
                                    <img src="@/assets/images/home/log.png" alt="">
                                </div>
                                <div class="s-flex flex-dir ai-fs jc-bt quick-view-info">
                                    <span class="fs12">操作日志</span>
                                    <span class="fs22" style="color: #551A8B">日志</span>
                                </div>
                            </div>
                        </div>
                        <div class="access-data">
                            <div class="access-data-header s-flex jc-bt ai-ct">
                                <div class="data-header">
                                    <span>销售数据</span>
                                    <span style="font-size: 12px;color: #4E5969">（近7日）</span>
                                </div>
                                <div class="mores" style="margin-right: 50px">
                                    <span>查看更多</span>
                                </div>
                            </div>
                            <div style="width: 100%;height:400px;" id="access-data"></div>
                        </div>
                    </div>
                    <div class="module-main" v-for="item in todo_list">
                        <div class="module-title">{{ item.group_name }}</div>
                        <div class="module-content s-flex flex-wrap">
                            <div class="module-model s-flex ai-ct jc-ct" v-for="ite in item.list">
                                <el-icon v-if="ite.icon" :size="32">
                                    <component :is="ite.icon"/>
                                </el-icon>
                                <div class="module-text">{{ ite.title }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="home-right">
                    <div class="shortcut">
                        <div class="collection">
                            <div class="access-data-header s-flex jc-bt ai-ct">
                                <div class="data-header">
                                    <span>我的收藏</span>
                                </div>
                                <div class="mores" @click="openCollect">
                                    <span>管理</span>
                                </div>
                            </div>
                            <div class="more-opt s-flex flex-wrap" v-if="my_collect.length">
                                <div class="opt-list" v-for="item in my_collect" :key="item.value">
                                    <div class="icon">
                                        <el-icon v-if="item.icon" :size="20">
                                            <component :is="item.icon"/>
                                        </el-icon>
                                    </div>
                                    <div class="titles">
                                        <span>{{item.title}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="no-data" v-else style="padding: 20px 20px 0 20px;height: 100%;">
                                <span>暂无收藏</span>
                            </div>
                        </div>
                        <div class="line" style="border-bottom: solid 1px #F2F3F5;margin: 20px 0;"></div>
                        <div class="recently-visited">
                            <div class="access-data-header s-flex jc-bt ai-ct">
                                <div class="data-header">
                                    <span>最近访问</span>
                                </div>
                            </div>
                            <div class="more-opt s-flex flex-wrap" v-if="access_record.length > 0">
                                <div class="opt-list" v-for="item in access_record">
                                    <div class="icon">
                                        <el-icon v-if="item.icon" :size="20">
                                            <component :is="item.icon"/>
                                        </el-icon>
                                    </div>
                                    <div class="titles">
                                        <span>{{item.title}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="no-data" v-else style="padding: 20px 20px 0 20px;height: 100%;">
                                <span>暂无访问记录</span>
                            </div>
                        </div>
                        <div class="line" style="border-bottom: solid 1px #F2F3F5;margin: 20px 0;"></div>
                        <div class="support-main">
                            <div class="access-data-header s-flex jc-bt ai-ct">
                                <div class="data-header">
                                    <span>辅助功能</span>
                                </div>
                            </div>
                            <div class="more-opt s-flex flex-wrap">
                                <div class="opt-list" @click="clearCache">
                                    <div class="icon">
                                        <el-icon :size="20"><DeleteFilled /></el-icon>
                                    </div>
                                    <div class="titles">
                                        <span>清除缓存</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <el-drawer
            class="collection-drawer"
            v-model="collectionVisible"
            @close='closeCollect'
            size="80%"
            :append-to-body='false'
            direction="rtl">
            <div class="collection-box s-flex jc-bt">
                <div class="right-collection flex-1">
                    <template v-if="my_collect.length">
                        <div class="menu-listRouter" v-for="(item,index) in my_collect" :key="item.value">
                            <span>{{ item.title }}</span>
                            <i class="el-icon-error" style="display: none;color: #ccc;"></i>
                        </div>
                    </template>
                    <div class="no-data" v-else style="padding: 20px 20px 0 20px;height: 100%;">
                        <span>暂无收藏</span>
                    </div>
                </div>
                <div class="left-collection">
                    <div class="search-menu">
                        <el-input placeholder="请输入关键词" v-model="searchtools" ref='searchtoolsRef' @input='searchMenusFnc'>
                            <template #prefix>
                                <div style='font-size: 20px' class='s-flex ai-ct jc-ct'>
                                    <Search style='width: 1.5em;height: 1.5em;color: var(--main-color)' />
                                </div>
                            </template>
                        </el-input>
                    </div>
                    <div class='menu-overflow'>
                        <div class="menu-scroll">
                            <div class="menu-list" v-for="(item,index) in searchMenus" :key="item.value">
                                <div class="menu-title"><span>{{ item.title }}</span></div>
                                <div class="menu-second" v-for="(its,ids) in item.children" :key="its.value">
                                    <div class="menu-title"><span>{{ its.title }}</span></div>
                                    <div class="menu-listRouter s-flex jc-bt ai-ct" v-for="(itas,idas) in its.children" :key="itas.value" style="cursor: pointer;" @click="collectionFnc(itas,index,ids,idas)">
                                        <span>{{ itas.title }}</span>
                                        <el-icon style="color: #ff6a00" v-if="itas.is_collection"><StarFilled /></el-icon>
                                        <el-icon style="color: rgb(204, 204, 204);display: none" v-else><StarFilled /></el-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </el-drawer>
    </div>
</template>

<script setup lang='ts'>
import { nextTick, onMounted, ref, reactive, onUnmounted, computed,getCurrentInstance } from 'vue';
const cns = getCurrentInstance().appContext.config.globalProperties
import * as echarts from 'echarts'
import $public from '@/utils/public'
import {getMenuAxios,clearCacheAxios, getHomeDashboardAxios, homeCollectMenuAxios} from "../api/home.js";



let lineRef = null

const searchtoolsRef = ref(null)
const searchtools = ref('')

const number_data = ref({})
const access_record = ref([])
const my_collect = ref([])
const access_statistic = ref({})
const todo_list = ref([])
const collectionVisible = ref<boolean>(false)
const menus = ref([])
const searchMenus = ref([])

const getUvChartOption = (item) => {
    let xAxisData = [];
    let pc_uv = 0;
    let app_uv = 0;
    if (item?.pc?.statistic_date && Array.isArray(item.pc.statistic_date)) {
        xAxisData = item.pc.statistic_date;
        pc_uv = item.pc.uv_number;
    }
    if (item?.ibi?.statistic_date && Array.isArray(item.ibi.statistic_date)) {
        xAxisData = item.ibi.statistic_date;
        app_uv = item.ibi.uv_number;
    }
    return {
        title: {
            text: '',
            subtextStyle: {
                color: '#333333'
            }
        },
        legend: {},
        tooltip: {
            trigger: 'axis',
            padding: [10, 20, 10, 10]
        },
        grid: {
            left: '3%',
            right: '6%',
            bottom: '3%',
            containLabel: true
        },
        toolbox: {
            feature: {
                // saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: xAxisData,
            axisLabel: {
                showMaxLabel: true
            }
        },
        yAxis: {
            type: 'value',
            axisLine: {
                show: false // 隐藏 Y 轴的边框线
            }

        },
        series: [
            {
                name: '订单数',
                type: 'line',
                data: pc_uv,
                lineStyle: {
                    color: '#4081ff'
                },
                itemStyle: {
                    color: '#4081ff'
                },
                label: {
                    show: true // 显示数据值

                },
                smooth: true

            },
            {
                name: '销售额',
                type: 'line',
                data: app_uv,
                lineStyle: {
                    color: '#1EE7FF'
                },
                itemStyle: {
                    color: '#1EE7FF'
                },
                label: {
                    show: true // 显示数据值

                },
                smooth: true

            }

        ]
    };
}

const resizeFnc = () => {
    lineRef.resize()
}

const debounceResize = $public.debounce(resizeFnc,100)

const closeCollect = () => {
    searchMenus.value = []
    searchtools.value = ''
}
const openCollect = () => {
    collectionVisible.value = true
    searchMenus.value = menus.value
    setTimeout(() => {
        searchtoolsRef.value.focus()
    },500)
}
const mapData = (arrs,newArr) => {
    for(let i=0;i<arrs.length;i++){
        if(arrs[i].hasOwnProperty('children')){ // 下拉节点
            if(arrs[i].title.indexOf(searchtools.value) > -1){
                newArr.push(arrs[i])
            }else{
                let newData = mapData(arrs[i].children,[])
                if(newData.length>0){
                    newArr.push({...arrs[i],children:newData})
                }else{
                    continue;
                }
            }
        }else{ // 最后一级
            if(arrs[i].title.indexOf(searchtools.value) > -1){
                newArr.push(arrs[i])
            }
            continue;
        }
    }
    return newArr
}
const searchMenusFnc = $public.debounce(() => {
    let arrs = menus.value
    let newArr = new Array()
    for(let i=0;i<arrs.length;i++){
        if(arrs[i].title.indexOf(searchtools.value) > -1){ // 如果顶级标题包含查询内容
            newArr.push({...arrs[i]})
            continue;
        }else if(arrs[i].hasOwnProperty('children')){
            let mapDatas = mapData(arrs[i].children,[])
            if(mapDatas.length>0){
                newArr.push({...arrs[i],children:mapDatas})
            }
        }
    }
    searchMenus.value = newArr
},500)

const collectionFnc = (itas,index,ids,idas) =>{
    let is_collect = itas.is_collection
    homeCollectMenuAxios(itas.index).then(res => {
        if (res.code === 200) {
            if(is_collect){
                let collectIndex = my_collect.value.findIndex(a => a.name === itas.name)
                my_collect.value.splice(collectIndex,1)
            }else{
                my_collect.value.push(itas)
            }
            searchMenus.value[index].children[ids].children[idas].is_collection = !is_collect
        } else {
            cns.$message.error(res.message);
        }
    });
}

const getMenu = () => {
    getMenuAxios().then(res => {
        if (res.code === 200) {
            menus.value = res.data
        } else {
            cns.$message.error(res.message);
        }
    });
}

const getData = () => {
    getHomeDashboardAxios().then(res => {
        if (res.code === 200) {
            number_data.value = res.data.number_data
            my_collect.value = res.data.my_collect
            access_record.value = res.data.access_record
            access_statistic.value = res.data.access_statistic
            todo_list.value = res.data.todo_list
            nextTick(() => {
                lineRef = echarts.init(document.getElementById('access-data'));
                lineRef.setOption(getUvChartOption(access_statistic));
            });
        } else {
            cns.$message.error(res.message);
        }
    });
}

const clearCache = () => {
    clearCacheAxios().then(res => {
        if (res.code === 200) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
        }
    });
}

onMounted(() => {
    getMenu()
    getData()


    window.addEventListener('resize', debounceResize)
});
onUnmounted(() => {
    window.removeEventListener('resize', debounceResize)
});
</script>

<style scoped lang='scss'>
.seller-home {
    transform: translate(0, 0);
    .container.member {
        overflow-y: hidden;
    }

    .el-card {
        background: transparent;
    }

    .el-card__body {
        background: transparent;
    }

    .home-container {
        width: 100%;
        height: 100%;

        .home-content {

            .home-left {
                width: 0;
                flex: 1;
                margin-right: 50px;
            }

            .home-left .information {
                padding: 10px 20px;
                box-sizing: border-box;
                background: #fff;
                border-radius: 4px;
            }

            .home-left .information .welcome {
                padding: 10px 0 20px 0;
                border-bottom: 1px #f2f3f5 solid;
            }

            .home-left .information .welcome span {
                font-size: 20px;
                color: #1D2129;
                font-weight: 400;
            }

            .home-left .information .quick-view-box {
                padding: 20px 0;
                border-bottom: 1px #f2f3f5 solid;
            }

            .home-left .information .quick-view-box .fonts {
                width: 54px;
                height: 54px;
                background: #f6f7fb;
                border-radius: 50px;
            }
            .home-left .information .quick-view-box .fonts img{
                width: 32px;
                height: 32px;
            }

            .home-left .information .quick-view-box .quick-view {
                flex: 1;
                position: relative;
                cursor: pointer;
            }

            .home-left .information .quick-view-box .quick-view &::after {
                position: absolute;
                content: '';
                border-right: solid 1px #f2f3f5;
                height: 100%;
                right: 30px;
                top: 0;
            }

            .home-left .information .quick-view-box .quick-view &:last-child &::after {
                content: none;
            }

            .home-left .information .quick-view-box .quick-view > div {
                margin-right: 10px;
                text-align: center;
            }

            .home-left .information .quick-view-box .quick-view .fonts em {
                font-size: 20px;
                color: red;
            }

            .home-left .information .quick-view-box .quick-view > div &:last-child {
                margin-right: 0px;
            }

            .home-left .information .quick-view-box .quick-view > div span &:first-child {
                font-size: 16px;
                color: #333;
                font-weight: 500;
            }

            .home-left .information .quick-view-box .quick-view > div span &:last-child {
                font-size: 22px;
                color: #551a8a;
                margin-top: 15px;
                font-weight: 500;
            }
            .home-left .information .quick-view-box .quick-view .quick-view-info{
                height: 100%;
            }

            .module-main{
                padding: 30px 40px;
                background: #ffffff;
                margin-top: 15px;
                border-radius: 4px;
                box-sizing: border-box;
            }
            .module-main .module-title{
                font-size: 16px;
                color: #333333;
            }
            .module-main .module-content{

            }
            .module-main .module-content .module-model{
                margin: 20px 30px 0;
                width: 176px;
                height: 70px;
                border-radius: 10px;
                border: 1px dashed rgba(0, 0, 0, 0.1);
            }
            .module-main .module-content .module-model>.el-icon svg{
                width: 20px;
                height: 20px;
            }
            .module-main .module-content .module-model>.module-text{
                margin-left: 10px;
                font-size: 14px;
                color: #333333;
            }

            .information .access-data {
                margin-top: 20px;
            }

            .access-data-header {
                padding: 10px 0 20px 0;
            }

            .access-data-header .data-header span {
                font-size: 18px;
                color: #000;
                font-weight: 400;
            }

            .access-data-header .mores {
                color: #165DFF;
                font-size: 12px;
                cursor: pointer;
            }

            .home-right {
                width: 280px;
            }

            .home-right .shortcut {
                width: 100%;
                background: #fff;
                padding: 10px 20px;
                box-sizing: border-box;
            }

            .home-right .shortcut .more-opt {
                /*column-gap: 6%;*/
                display: grid;
                justify-content: space-between;
                grid-template-columns:repeat(auto-fill, 70px);
                min-height: 60px;
            }

            .home-right .shortcut .opt-list {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 10px;
                cursor: pointer;
            }

            .home-right .shortcut .opt-list .icon {
                width: 40px;
                height: 40px;
                border-radius: 5px;
                background: #f7f8fa;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .home-right .shortcut .opt-list .icon svg{
                width: 30px;
                height: 30px;
            }

            .home-right .shortcut .opt-list .titles {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                width: 85px;
                text-align: center;
                margin-top: 5px;
            }

            .home-right .shortcut .opt-list span {
                font-size: 14px;
                color: #666666;
                font-weight: 400;
            }
        }
    }

    .table-title {
        -webkit-line-clamp: 2;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
        word-break: break-all;
        overflow: hidden;
    }

}
/*  首页收藏弹窗  */
:deep(.collection-drawer) {
    .collection-box {
        width: 100%;
        height: 100%;
        padding: 20px;
        box-sizing: border-box;
        display: flex;
        padding-left: 0;
    }

    &.el-drawer {
        border-radius: 20px 0 0 0;
    }

    .el-drawer__header span {
        font-weight: 500;
        font-size: 20px;
        color: #333333;
    }

    .el-drawer__body {
        height: 0;
        padding: 0;
        overflow: hidden !important;
    }

    .collection-box .left-collection {
        width: 85%;
        height: 100%;
        overflow: hidden;
        margin-left: 20px;
        display: flex;
        flex-direction: column;

        .menu-overflow {
            height: 0;
            flex: 1;
            overflow-y: auto;
            margin-top: 74px;
        }

        .menu-scroll {
            columns: 4;
            column-gap: 20px;
        }

        .menu-list {
            display: inline-block;
            width: 100%;
            break-inside: avoid;

            .menu-title span, .menu-title i {
                color: #ff822c;
                font-size: 18px;
                line-height: 3;
            }

            .menu-second .menu-title span {
                font-size: 16px;
                color: #333;
                line-height: 3;
                font-weight: 500;
            }
        }

        .search-menu .el-input__wrapper {
            height: 36px;
            background: #F5F7FA;
            border-radius: 10px;
            border: none;
            outline: none;
            box-shadow: none;
        }

        .search-menu .el-input__prefix {
            display: flex;
            align-items: center;
            padding-left: 10px;
        }
    }

    .menu-list .menu-second .menu-title span {
        font-size: 16px;
        color: #333;
        line-height: 3;
        font-weight: 500;
    }

    .collection-box .menu-listRouter {
        height: 30px;
        border-radius: 10px;
        padding: 0 10px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
    }

    .collection-box .menu-listRouter {
        &:hover {
            background-color: #f4f6f7;
            .el-icon {
                display: block !important;
            }
        }
    }

    .collection-box .menu-listRouter span,
    .collection-box .menu-listRouter i {
        cursor: pointer;
        color: #333;
        font-size: 14px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .collection-box .right-collection {
        width: 0;
        height: 100%;
        overflow: hidden;
        overflow-y: auto;
        padding: 20px;
        flex: 1;
        box-shadow: 1px 0px 6px 0px rgba(16, 43, 76, 0.08);
        border-radius: 0px 20px 20px 0px;
        scrollbar-width: none;
        -ms-overflow-style: none;

        &::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        &::-webkit-scrollbar-track {
            display: none;
        }
    }

    .menu-overflow .menu-listRouter {
        padding: 0 20px;
        border-radius: 10px;
        height: 30px;

        &:hover {
            background-color: #f4f6f7;
        }

        .hideIcon {
            display: none;
        }

        &:hover .hideIcon {
            display: block;
        }
    }

    .remind {
        background: #E90013;
        border-radius: 60px;
        height: 20px;
        min-width: 20px;
        text-align: center;
        line-height: 1;
        padding: 0 5px;
        margin-left: 10px;

        span {
            font-weight: 500;
            font-size: 12px;
            color: #FFFFFF;
        }
    }
}


/**/
.no-data{
    display: flex;align-items: center;justify-content: center;
}
.no-data span{
    font-size: 16px;color: #ccc;font-weight: 400;
}
</style>
