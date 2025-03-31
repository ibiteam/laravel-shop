<template>
    <el-dialog
        class="custom-dialog"
        header-class="custom-dialog-header"
        body-class="custom-dialog-body"
        v-model="dialogVisible"
        width="1000"
        :before-close="handleClose">
        <template #header>
            <p class="custom-dialog-title fs16">选择图片</p>
        </template>
        <div class="custom-dialog-content s-flex jc-bt">
            <div class="left-wrapper">
                <div class="tree-content">
                    <!-- <tabs v-model:active="treeMenuData.active" title-active-color="var(--main-color)">
                        <tab v-for="menu in treeMenuData.menu" :key="menu.dir_type" :title="menu.name" :name="menu.dir_type"></tab>
                    </tabs> -->
                    <el-tree-v2
                        style="max-width: 190px"
                        :data="treeMenuData.tree"
                        :props="{value: 'id', label: 'name', children: 'children'}"
                        highlight-current
                        check-on-click-node
                        indent="6"
                    />
                </div>
                <el-link class="link-button" :underline="false">前往素材中心</el-link>
            </div>
            <div class="right-wrapper">
                <div class="search-wrapper s-flex ai-ct">
                    <el-radio-group v-model="viewType" size="small">
                        <el-radio-button :value="0">
                            <template #default>
                                <svg t="1743232052088" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="20672" width="18" height="18"><path d="M196.277 225.916c0 12.675-10.264 22.951-22.952 22.951H87.254c-12.687 0-22.954-10.277-22.954-22.95v-86.07c0-12.675 10.267-22.952 22.954-22.952h86.071c12.688 0 22.952 10.276 22.952 22.951v86.07z m763.173 0c0 12.675-10.265 22.951-22.951 22.951H311.04c-12.687 0-22.953-10.276-22.953-22.95v-86.07c0-12.675 10.266-22.95 22.953-22.95h625.458c12.685 0 22.951 10.275 22.951 22.95v86.07zM196.277 532.894c0 12.675-10.264 22.952-22.952 22.952H87.254c-12.687 0-22.954-10.277-22.954-22.952v-86.07c0-12.674 10.266-22.951 22.953-22.951h86.071c12.688 0 22.952 10.276 22.952 22.952v86.069z m763.173 0c0 12.675-10.265 22.952-22.951 22.952H311.04c-12.687 0-22.953-10.277-22.953-22.952v-86.07c0-12.674 10.266-22.951 22.953-22.951h625.458c12.685 0 22.951 10.276 22.951 22.952v86.069zM196.277 839.871c0 12.675-10.264 22.952-22.952 22.952H87.254c-12.687 0-22.954-10.277-22.954-22.952v-86.068c0-12.676 10.266-22.953 22.953-22.953h86.071c12.688 0 22.952 10.277 22.952 22.953v86.068z m763.173 0c0 12.675-10.265 22.952-22.951 22.952H311.04c-12.687 0-22.953-10.277-22.953-22.952v-86.068c0-12.676 10.266-22.953 22.953-22.953h625.458c12.685 0 22.951 10.277 22.951 22.953v86.068z" :fill="!viewType ? '#fff' : '#333'" p-id="20673"></path></svg>
                            </template>
                        </el-radio-button>
                        <el-radio-button :value="1">
                            <template #default>
                                <svg t="1743231975541" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="20511" width="18" height="18"><path d="M341.888 552.32c70.229333 0 126.976 57.258667 126.976 127.829333v130.688A127.402667 127.402667 0 0 1 341.888 938.666667H212.309333A127.402667 127.402667 0 0 1 85.333333 810.837333v-130.688c0-70.570667 56.746667-127.829333 126.976-127.829333z m469.845333 0c70.144 0 126.933333 57.301333 126.933334 127.829333v130.688C938.666667 881.365333 881.92 938.666667 811.733333 938.666667h-129.621333a127.402667 127.402667 0 0 1-126.976-127.829334v-130.688c0-70.570667 56.746667-127.829333 126.976-127.829333zM341.888 611.84H212.309333a67.84 67.84 0 0 0-67.413333 68.309333v130.688c0 37.802667 30.208 68.266667 67.413333 68.266667h129.578667a67.84 67.84 0 0 0 67.413333-68.266667v-130.688a67.84 67.84 0 0 0-67.413333-68.266666z m469.845333 0h-129.621333a67.84 67.84 0 0 0-67.413333 68.309333v130.688c0 37.802667 30.208 68.266667 67.413333 68.266667h129.621333c37.12 0 67.413333-30.464 67.413334-68.266667v-130.688a67.84 67.84 0 0 0-67.413334-68.266666z m0-526.506667C881.92 85.333333 938.666667 142.634667 938.666667 213.162667V343.893333c0 70.485333-56.789333 127.786667-126.933334 127.786667h-129.621333c-70.186667 0-126.976-57.301333-126.976-127.786667V213.162667A127.402667 127.402667 0 0 1 682.112 85.333333zM341.888 85.333333a127.402667 127.402667 0 0 1 126.976 127.829334V343.893333c0 70.485333-56.789333 127.786667-126.976 127.786667H212.309333C142.08 471.68 85.333333 414.378667 85.333333 343.893333V213.162667A127.402667 127.402667 0 0 1 212.309333 85.333333z m469.845333 59.52h-129.621333a67.84 67.84 0 0 0-67.413333 68.266667V343.893333c0 37.717333 30.208 68.266667 67.413333 68.266667h129.621333c37.12 0 67.413333-30.549333 67.413334-68.266667V213.162667c0-37.802667-30.293333-68.266667-67.413334-68.266667z m-469.845333 0H212.309333a67.84 67.84 0 0 0-67.413333 68.266667V343.893333c0 37.717333 30.250667 68.266667 67.413333 68.266667h129.578667c37.205333 0 67.413333-30.549333 67.413333-68.266667V213.162667c0-37.802667-30.208-68.266667-67.413333-68.266667z" :fill="viewType ? '#fff' : '#333'" p-id="20512" data-spm-anchor-id="a313x.manage_type_myprojects.0.i1.6bbe3a812EOZqS" class="selected"></path></svg>
                            </template>
                        </el-radio-button>
                    </el-radio-group>
                    <el-select v-model="searchForm.status" style="width: 120px" >
                        <el-option label="最新" :value="1" />
                        <el-option label="最旧" :value="2" />
                    </el-select>
                    <el-input v-model="searchForm.keyword" placeholder="搜索图片名称" style="width: 200px" />
                </div>
                <div class="table-wrapper" v-if="!viewType">
                    <el-table :data="tableData" style="width: 100%" height="100%">
                        <el-table-column fixed type="selection" width="55" />
                        <el-table-column label="素材名称" width="240">
                            <template #default="scope">
                                <div class="s-flex ai-ct">
                                    <img :src="scope.row.img" alt="" style="width: 35px; height: 35px; margin-right: 5px;cursor: pointer;" @click="handleClickViewer(scope)" />
                                    <p class="elli-1">{{scope.row.name}}</p>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column property="size" label="尺寸" />
                        <el-table-column label="大小">
                            <template #default="scope">{{ scope.row.width }}*{{ scope.row.height }}px</template>
                        </el-table-column>
                        <el-table-column property="time" label="更新时间" />
                    </el-table>
                </div>
                <div class="grid-wrapper" v-if="viewType">
                    <div class="grid-wrapper-content">
                        <div class="grid-item" v-for="(item,idx) in tableData" :key="idx">
                            <div class="grid-item-mask" :class="{'select': idx == 0}" @click="handleClickImageItem(item)">
                                <el-badge :value="1" :hidden="idx != 0" class="select-index" type="primary" color="var(--main-color)" :offset="[-12,10]">
                                    <el-image style="width: 140px; height: 140px" :src="item.img" fit="scale-down" />
                                </el-badge>
                            </div>
                            <p class="elli-1">{{item.width}}*{{item.height}}px</p>
                        </div>
                    </div>
                </div>
                <div class="pagination-wrapper s-flex ai-ct jc-ct">
                    <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />
                </div>
                <div class="button-wrapper s-flex ai-ct jc-ct">
                    <el-button type="primary" @click="handleClose">确定</el-button>
                    <el-button @click="handleClose">取消</el-button>
                </div>
            </div>
        </div>
        <ImageViewer v-bind="{...viewerData}" @close="() => { viewerData.show = false }" />
    </el-dialog>
</template>

<script setup>
import { ref, reactive , getCurrentInstance, defineEmits, onMounted } from 'vue'
import { Tab, Tabs } from 'vant';
import Page from '@/components/common/Pagination.vue'
import ImageViewer from '@/components/common/ImageViewer.vue'

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
})

const dialogVisible = ref(true)
const treeMenuData = reactive({
    active: '',
    menu: [],
    tree: []
})
const treeData = ref([{
    "id": 0,
    "name": "商品管理",
    "parent_id": -1,
    "dir_type": 1,
    "children": [
        {
            "id": 1,
            "name": "商品图",
            "parent_id": 0,
            "dir_type": 1,
            "children": [
                {
                    "id": 2,
                    "name": "活动图",
                    "parent_id": 1,
                    "dir_type": 1,
                    "children": [
                        {
                            "id": 3,
                            "name": "特卖图",
                            "parent_id": 2,
                            "dir_type": 1
                        },
                        {
                            "id": 4,
                            "name": "秒杀图",
                            "parent_id": 2,
                            "dir_type": 1
                        }
                    ]
                }
            ]
        }
    ]
},
{
    "id": 0,
    "name": "视频管理",
    "parent_id": -1,
    "dir_type": 2,
    "children": [
        {
            "id": 5,
            "name": "搞笑视频",
            "parent_id": 0,
            "dir_type": 2,
            "children": [
                {
                    "id": 6,
                    "name": "欢乐逗比",
                    "parent_id": 5,
                    "dir_type": 2
                },
                {
                    "id": 7,
                    "name": "倒霉熊",
                    "parent_id": 5,
                    "dir_type": 2
                }
            ]
        }
    ]
}])
const viewType = ref(1)
const searchForm = reactive({
    status: 1,
    keyword: ''
})
const pageInfo = reactive({
    page: 1,
    number: 10,
    total: 0,
    currentPage: 1,
    layout: 'total, sizes, prev, pager, next',
})
const tableData = ref([
    {
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },{
        id: 1,
        name: '图片名称',
        img: 'https://cdn.toodudu.com/2024/11/18/VY9GiETCQxSrZTTAOF9169dwvXadL7erBYn7TxZ0.jpg',
        size: '1MB',
        width: 1200,
        height: 1200,
    },
])
const viewerData = reactive({
    show: false,
    index: 0,
    srcList: [],

})
// 单击图片
const handleClickImageItem = () => {

}


// 打开预览图片
const handleClickViewer = (list) => {
    viewerData.index = list.$index
    viewerData.srcList = []
    tableData.value.map((item,idx) => {
        viewerData.srcList.push(item.img)
    })
    viewerData.show = true
}

const handleSizeChange = (val) => {
    //切换一页显示数据条数
    let data = searchForm;
    data.number = val;
    // this.getData(data);
}

const handleCurrentChange = (val) => {
    //切换页码
    let data = searchForm;
    data.number = searchForm.number;
    data.page = val;
    // this.getData(data);
}


const handleClose = () => {
    dialogVisible.value = false
}

onMounted(() => {
    treeData.value.map((item,idx) => {
        treeMenuData.menu.push(item)
        if (item.dir_type == 1) {
            treeMenuData.active = item.dir_type
            treeMenuData.tree = item.children || []
        }
    })
    console.log(treeMenuData)
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
        .pagination-wrapper {
            height: 50px;
            padding: 0 10px;
            border-bottom: 1px solid #d8d8d8;
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
