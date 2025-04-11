<template>
    <el-dialog
        class="custom-dialog"
        header-class="custom-dialog-header"
        body-class="custom-dialog-body"
        v-model="dialogVisible"
        width="1000"
        :close-on-click-modal="false"
        :before-close="handleClose">
        <template #header>
            <p class="custom-dialog-title fs16">选择链接</p>
        </template>
        <div class="custom-dialog-content s-flex jc-bt">
            <div class="left-wrapper">
                <div class="tree-content">
                    <el-tree
                        style="max-width: 190px"
                        :data="treeData"
                        :props="{value: 'id', label: 'name', children: 'children'}"
                        node-key="id"
                        highlight-current
                        default-expand-all
                        :indent="6"
                        :expand-on-click-node="false"
                    >
                        <template #default="{ node, data }">
                            <div class="custom-tree-node" @click="checkTree(data)">
                                <span>{{ data.name }}</span>
                            </div>
                        </template>
                    </el-tree>
                </div>
                <el-link class="link-button" :underline="false" href="/manage/set/router">前往访问地址</el-link>
            </div>
            <div class="right-wrapper">
                <div class="search-wrapper s-flex ai-ct" v-if="searchForm.page_name != 'manage.category.index'">
                    <el-input v-model="searchForm.name" :placeholder="searchForm.page_name == 'manage.goods.index'?'搜索商品名称/货号/ID':'搜索名称/别名'" style="width: 200px" @change="handleCurrentChange(1)"/>
                </div>
                <div class="table-wrapper" v-loading="tableLoading" :class="searchForm.page_name == 'manage.category.index'?'table-wrapper-cover':''">
                    <el-table
                        :data="tableData"
                        ref="tableRef"
                        style="width: 100%" height="100%"
                        row-key="id"
                        :tree-props="{ children: 'all_children', hasChildren: true,checkStrictly:true }"
                        @select="handleSelect">
                        <el-table-column fixed type="selection" width="55" v-if="searchForm.page_name"/>
                        <template v-if="searchForm.page_name == 'manage.router.index'">
                            <el-table-column property="name" label="名称" />
                            <el-table-column property="alias" label="别名"/>
                            <el-table-column property="h5_url" label="h5链接" />
                        </template>
                        <template v-if="searchForm.page_name == 'manage.goods.index'">
                            <el-table-column property="id" label="ID" width="100"/>
                            <el-table-column label="商品" align="center" width="250">
                                <template #default="scope">
                                    <div class="s-flex ai-ct">
                                        <el-image style="width: 35px; height: 35px; margin-right: 5px;cursor: pointer;" :src="scope.row.image" fit="scale-down" @click="handleClickViewer(scope,'image')">
                                            <template #error>
                                                <img src="@/assets/images/decoration/app-nopic.png" width="35" height="35"/>
                                            </template>
                                        </el-image>
                                        <p class="elli-1" style="max-width: 70%;">{{scope.row.name}}</p>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column property="no" label="货号" align="center" />
                        </template>
                        <template v-if="searchForm.page_name == 'manage.category.index'">
                            <el-table-column property="id" label="ID" align="center" width="100"/>
                            <el-table-column label="分类名" align="center">
                                <template #default="scope">
                                    <div class="s-flex ai-ct">
                                        <el-image style="width: 35px; height: 35px; margin-right: 5px;cursor: pointer;" :src="scope.row.logo" fit="scale-down" @click="handleClickViewer(scope,'logo')">
                                            <template #error>
                                                <img src="@/assets/images/decoration/app-nopic.png" width="35" height="35"/>
                                            </template>
                                        </el-image>
                                        <p class="elli-1" style="max-width: 70%;">{{scope.row.title}}</p>
                                    </div>
                                </template>
                            </el-table-column>
                        </template>
                    </el-table>
                </div>
                <div class="pagination-wrapper s-flex ai-ct jc-ct" v-if="searchForm.page_name != 'manage.category.index'">
                    <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />
                </div>
                <div class="button-wrapper s-flex ai-ct jc-ct">
                    <el-button type="primary" @click="handleConfirm">确定</el-button>
                    <el-button @click="handleClose">取消</el-button>
                </div>
            </div>
        </div>
        <ImageViewer v-bind="{...viewerData}" @close="() => { viewerData.show = false }" />
    </el-dialog>
</template>

<script setup>
import { ref, reactive , getCurrentInstance, defineEmits, onMounted, watch } from 'vue'
import Page from '@/components/common/Pagination.vue'
import ImageViewer from '@/components/common/ImageViewer.vue'
import {linkTableData, linkTreeList} from '@/api/link.js';

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
})
const emit = defineEmits(['confirm', 'close'])

const dialogVisible = ref(false)
// 左侧层级树
const treeData = ref([])

const searchForm = reactive({
    name: '',
    page_name: '',
    id:0
})
const pageInfo = reactive({
    page: 1,
    number: 10,
    total: 0,
    current_page: 1,
    layout: 'total, sizes, prev, pager, next',
})
const tableRef = ref(null)
const tableLoading = ref(false)
const tableData = ref([])
// 预览数据
const viewerData = reactive({
    show: false,
    index: 0,
    srcList: [],

})
// 勾选数据
const check_group = ref([])

// 表格单选
const handleSelect = (selection, row) => {
    if (tableRef.value) {
        tableRef.value.clearSelection()
        tableRef.value.toggleRowSelection(row)
        check_group.value = [row]
        return
    }
    check_group.value = selection
}
// 打开预览图片
const handleClickViewer = (list,name) => {
    viewerData.index = list.$index
    viewerData.srcList = []
    tableData.value.map((item,idx) => {
        viewerData.srcList.push(item[name])
    })
    viewerData.show = true
}
// 层级树选择
const checkTree = (data) => {
    if (!data.page_name) return
    searchForm.page_name = data.page_name
    searchForm.id = data.id
    getLinkTableData()
}
const handleSizeChange = (val) => {
    //切换一页显示数据条数
    getLinkTableData({page: pageInfo.current_page, number: val})
}

const handleCurrentChange = (val) => {
    //切换页码
    getLinkTableData({page: val, number: pageInfo.per_page})
}


const handleClose = () => {
    dialogVisible.value = false
    emit('close')
}
const handleConfirm = () => {
    dialogVisible.value = false
    emit('confirm', check_group.value)
}

const searchInfoData = () => {
    let searchInfo = {
        name:searchForm.name,
        page_name:searchForm.page_name
    }
    if (searchForm.page_name == 'manage.router.index'){//基础连接,自定义链接
        searchInfo.url = 'manage/set/router'
        searchInfo.is_show = -1
        searchInfo.router_category_id = searchForm.id
    }else if(searchForm.page_name == 'manage.goods.index'){//商品列表
        searchInfo.url = 'manage/goods/info'
        searchInfo.status = -1
    }else if(searchForm.page_name == 'manage.category.index'){//商品分类
        searchInfo.url = 'manage/goods/category'
    }
    return searchInfo
}
// 获取文件树
const getLinkTreeData = () => {
    linkTreeList().then(res => {
        if (res.code === 200) {
            treeData.value = res.data;
            searchForm.page_name = res.data[0].page_name
            searchForm.id = res.data[0].id
            getLinkTableData()
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {})
}
// 获取文件列表
const getLinkTableData = (params = {page: 1, number: 10}) => {
    const {page, number} = params;
    let searchInfo = searchInfoData()
    searchInfo.page = page;
    searchInfo.number = number;
    tableLoading.value = true
    linkTableData(searchInfo).then(res => {
        if (res.code === 200) {
            if (searchForm.page_name == 'manage.category.index'){
                tableData.value = res.data;
            }else{
                tableData.value = res.data.list;
                // 更新分页信息
                pageInfo.total = res.data.meta.total;
                pageInfo.per_page = Number(res.data.meta.per_page);
                pageInfo.current_page = res.data.meta.current_page;
            }
        } else {
            cns.$message.error(res.message)
        }
        tableLoading.value = false
    }).catch(() => {
        tableLoading.value = false
    })
}

watch(() => props, (newVal) => {
    if (newVal) {
        dialogVisible.value = newVal.show
        dialogVisible.value && getLinkTreeData()
    }
}, {
    immediate: true,
    deep: true
})

onMounted(() => {
})
</script>

<style lang='scss' scoped>
.custom-dialog-title {
    height: 54px;
    line-height: 54px;
    padding: 0 50px 0 20px;
    border-bottom: 1px solid #d8d8d8;
    box-sizing: border-box;
}
.custom-dialog-content{
    width: 100%;
    height: 750px;
    .left-wrapper {
        width: 210px;
        height: 100%;
        padding: 14px 10px 14px;
        text-align: center;
        border-right: 1px solid #d8d8d8;
        box-sizing: border-box;
        .tree-content {
            width: 100%;
            height: calc(100% - 30px);
        }
        .link-button {
            margin: 0 auto;
            text-align: center;
            height: 30px;
            color: var(--main-color);
        }
    }
    .right-wrapper {
        width: calc(100% - 210px);
        height: 100%;
        padding: 14px 0;
        box-sizing: border-box;
        .search-wrapper {
            height: 50px;
            padding: 0 10px;
            gap: 10px;
        }
        .table-wrapper {
            height: calc(100% - 150px);
            padding: 0 10px;
            overflow: hidden auto;
        }
        .table-wrapper.table-wrapper-cover{
            height: calc(100% - 50px);
        }
        :deep(.el-table__header-wrapper .el-table-column--selection>.cell){
            display: none;
        }
        .pagination-wrapper {
            height: 50px;
            padding: 0 10px;
            border-bottom: 1px solid #d8d8d8;
            .common-pagination{
                margin-top: 0;
            }
        }
        .button-wrapper {
            height: 50px;
            padding: 0 10px;
        }
        .grid-wrapper {
            width: 100%;
            height: calc(100% - 150px);
            padding: 0 10px;
            overflow: hidden auto;
            .grid-wrapper-content {
                display: flex;
                align-items: flex-start;
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 10px;
                text-align: center;
                .grid-item-mask {
                    width: 144px;
                    height: 144px;
                    border: 2px solid transparent;
                    box-sizing: border-box;
                    cursor: pointer;
                    position: relative;
                    &.select {
                        border:2px solid var(--main-color);
                    }
                }
            }
        }
    }
}
.custom-tree-node{
    width: 100%;
    text-align: left;
}

</style>
<style>
.custom-dialog {
    padding: 0!important;
}
.custom-dialog-header {
    padding: 0;
    position: relative;
}
.custom-dialog-header.el-dialog__header.show-close{
    padding: 0;
}
.custom-dialog-header .el-dialog__headerbtn {
    bottom: 0;
    margin: auto 0;
}
</style>
