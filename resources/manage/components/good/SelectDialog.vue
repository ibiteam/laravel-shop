<template>
    <el-dialog
        class="custom-dialog"
        header-class="custom-dialog-header"
        body-class="custom-dialog-body"
        v-model="dialogVisible"
        width="1000"
        :before-close="handleClose">
        <template #header>
            <p class="custom-dialog-title fs16">添加商品</p>
        </template>
        <div class="custom-dialog-content s-flex jc-bt">
            <div class="left-wrapper">
                <el-tabs v-model="tabActive" class="demo-tabs" @tab-click="''">
                    <el-tab-pane label="手动添加" name="1">
                        <el-form :inline="true" :model="queryParams" class="search-form" label-width="auto" label-position="left">
                            <div>
                                <el-form-item label="关键词" prop="keywords" style="width: 100%;">
                                    <el-input
                                        style="width: 100%;"
                                        v-model="queryParams.keywords"
                                        placeholder="请输入商品名称/商品货号"
                                        clearable
                                    />
                                </el-form-item>
                            </div>
                            <div class="s-flex ai-ct jc-bt">
                                <el-form-item label="商品ID" prop="goods_id" style="width: 40%;">
                                    <el-input
                                        v-model="queryParams.goods_id"
                                        placeholder="请输入商品ID"
                                        clearable
                                    />
                                </el-form-item>
                                <el-form-item label="分类" prop="category_id" style="width: 40%; margin-right: 0;">
                                    <el-cascader
                                        v-model="queryParams.category_id"
                                        filterable
                                        clearable
                                        :options="categoryOptions"
                                        :props="{label: 'name',value: 'id',children:'all_children',checkStrictly: true}"
                                        placeholder="请选择"
                                        @change="selectQueryParamsCategory"
                                    ></el-cascader>
                                </el-form-item>
                            </div>
                            <el-button type="primary" :disabled="tableLoading" @click="getGoodsList({page: 1})">查询</el-button>
                        </el-form>
                        <div style="margin-top: 20px;">
                            <el-checkbox v-model="check.all" :indeterminate="check.isIndeterminate" @change="handleCheckAllChange">全选</el-checkbox>
                        </div>
                        <el-checkbox-group v-model="check.data" :max="max" @change="handleCheckedChange">
                            <div class="table-wrapper" v-infinite-scroll="handleLoad" :infinite-scroll-disabled="tableLoading || tableData.length >= pageInfo.total">
                                <div class="goods-item" v-for="item in tableData" :key="item.no">
                                    <el-checkbox class="icon-check" :value="item">
                                        <template #default>
                                            <div class="s-flex ai-ct jc-bt">
                                                <el-image :src="item.image" fit="fill" :style="{ width: '50px', height: '50px', borderRadius: '4px'}">
                                                    <template #placeholder>
                                                        <div class="image-item">
                                                            <img src="@/assets/images/decoration/app-nopic.png" />
                                                        </div>
                                                    </template>
                                                    <template #error>
                                                        <div class="image-item">
                                                            <img src="@/assets/images/decoration/app-nopic.png" />
                                                        </div>
                                                    </template>
                                                </el-image>
                                                <div class="goods-info">
                                                    <p class="goods-name co-333 elli-1">{{item.name}}</p>
                                                    <div class="s-flex ai-ct jc-bt mt-10">
                                                        <div class="goods-price">¥{{item.price}}</div>
                                                        <span class="co-666">库存 {{item.total}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </el-checkbox>
                                </div>
                            </div>
                        </el-checkbox-group>
                    </el-tab-pane>
                    <el-tab-pane label="商品ID导入" name="2">
                        <el-input
                            v-model="export_text"
                            :rows="10"
                            resize="none"
                            type="textarea"
                            placeholder="请输入商品ID,以逗号或者回车形式隔开"
                        />
                        <!-- <el-input-tag
                            v-model="export_data"
                            ref="inputTagRef"
                            placeholder="请输入商品ID,以逗号或者回车形式隔开"
                        /> -->
                        <div class="s-flex jc-fe mt-10">
                            <el-button type="primary" :disabled="exporting" @click.stop="handleImport">导入</el-button>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
            <div class="right-wrapper">
                <div class="group-title s-flex ai-ct jc-bt">
                    <p>已选商品({{check.data.length}}/{{max}})</p>
                    <el-button text class="co-999" @click="handleRemoveAll">全部清除</el-button>
                </div>
                <VueDraggable
                    class="goods-group-dragable"
                    v-model="check.data"
                    :animation="1000"
                    :group="{name: 'goods', pull: true, put: true}"
                    handle=".goods-bars"
                    :forceFallback="false"
                    >
                    <div class="goods-item s-flex ai-ct jc-bt mb-10" v-for="(item, index) in check.data" :key="item.no">
                        <em class="iconfont icon-drag goods-bars" style="font-size:20px"></em>
                        <em class="iconfont icon-guanbi" @click.stop="handleRemove(index)"></em>
                        <el-image :src="item.image" fit="fill" :style="{ width: '50px', height: '50px', borderRadius: '4px'}">
                            <template #placeholder>
                                <div class="image-item">
                                    <img src="@/assets/images/decoration/app-nopic.png" />
                                </div>
                            </template>
                            <template #error>
                                <div class="image-item">
                                    <img src="@/assets/images/decoration/app-nopic.png" />
                                </div>
                            </template>
                        </el-image>
                        <div class="goods-info">
                            <p class="goods-name elli-1">{{item.name}}</p>
                            <div class="s-flex ai-ct jc-bt mt-10">
                                <div class="goods-price">¥{{item.price}}</div>
                                <span class="co-666">库存{{item.total}}</span>
                            </div>
                        </div>
                    </div>
                </VueDraggable>
                <div class="button-wrapper s-flex ai-ct jc-ct">
                    <el-button type="primary" @click="handleConfirm">确定</el-button>
                    <el-button @click="handleClose">取消</el-button>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<script setup>
import { ref, reactive , getCurrentInstance, defineEmits, onMounted, watch } from 'vue'
import { VueDraggable } from 'vue-draggable-plus'
import Http from '@/utils/http.js';

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    max: {
        type: Number,
        default: 20
    },
    default_nos: {
        type: Array,
        default: () => {
            return []
        }
    },
    default_goods: {
        type: Array,
        default: () => {
            return []
        }
    }
})
const emit = defineEmits(['confirm', 'close'])

const dialogVisible = ref(false)
const tabActive = ref('1')
const queryParams = reactive({
    keywords: '',      // 商品名称搜索
    goods_id: '',
    category_id: '',
})
const categoryOptions = ref([]);
// 导入id
const export_text = ref('')
const export_data = ref([])
const exporting = ref(false)
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
// 勾选数据
const check = reactive({
    all: false,
    isIndeterminate: false, // 设置不确定状态，仅负责样式控制
    nos: [],
    data: []
})

/* 商品分类选择触发函数 */
const selectQueryParamsCategory = (item) => {
    if (item == undefined) {
        queryParams.category_id = '';
    } else {
        queryParams.category_id = item[parseInt(item.length) - 1]
    }
}

// 全选
const handleCheckAllChange = (val) => {
    if (val) {
        if (tableData.length > 20) {
            cns.$message({
                message: `最多只能选择${props.max}条数据`,
                type: 'warning'
            })
            return
        }
        check.nos = tableData.value.map(item => {
            return item.no
        })
        check.data = JSON.parse(JSON.stringify(tableData.value))
    } else {
        check.nos = []
        check.data = []
    }
    check.isIndeterminate = false
}
// 单选
const handleCheckedChange = (value) => {
    const checkedCount = value.length
    check.all = checkedCount === tableData.value.length
    check.isIndeterminate = checkedCount > 0 && checkedCount < tableData.value.length
}

// 全部删除
const handleRemoveAll = () => {
    check.data = []
    handleCheckedChange([])
    handleCheckAllChange(false)
}

// 单个删除
const handleRemove = (index) => {
    check.data.splice(index, 1)
    handleCheckedChange(check.data)
}

const handleClose = () => {
    dialogVisible.value = false
    emit('close')
}

const handleConfirm = () => {
    dialogVisible.value = false
    emit('confirm', check.data)
}

const handleImport = () => {
    if (exporting.value) return
    exporting.value = true
    if (export_data.value.length == 0) {
        cns.$message.error('请输入要导入的商品id')
        exporting.value = false
        return
    }
    if (export_data.value.length + check.data.length > props.max) {
        cns.$message.error(`最多只能导入${props.max - check.data.length}条数据，请修改`)
        exporting.value = false
        return
    }
    Http.doPost('app_decoration/goods/import', {goods_ids: export_data.value, goods_nos: check.nos}).then(res => {
        if (cns.$successCode(res.code)) {
            if (res.data.length > 0) {
                export_data.value = []
                export_text.value = ''
                cns.$message.success('导入成功')
                check.data = res.data
                check.nos = check.data.map(item => {
                    return item.no
                })
            }
        } else {
            cns.$message.error(res.message)
        }
        exporting.value = false
    })
}

const handleLoad = () => {
    if (tableLoading.value) return
    getGoodsList({page: pageInfo.current_page++})
}

const getGoodsList = (params = {page: 1}) => {
    if (tableLoading.value) return
    const {page} = params;
    tableLoading.value = true
    Http.doPost('app_decoration/goods/list', {...queryParams, page}).then(res => {
        if (cns.$successCode(res.code)) {
            tableData.value = res.data.list;
            // // 更新分页信息
            pageInfo.total = res.data.meta.total;
            pageInfo.per_page = Number(res.data.meta.per_page);
            pageInfo.current_page = res.data.meta.current_page;
        } else {
            cns.$message.error(res.message)
        }
        tableLoading.value = false
    }).catch(() => {
        tableLoading.value = false
    })
}

const getCategory = () => {
    Http.doGet('goods/category').then(res => {
        if (cns.$successCode(res.code)) {
            categoryOptions.value = res.data;
        }
    })
}


watch([() => props], (newVal) => {
    if (newVal[0]) {
        dialogVisible.value = newVal[0].show
        if (dialogVisible.value) {
            getGoodsList()
            getCategory()
        }
        if (newVal[0].default_goods.length > 0) {
            check.data = JSON.parse(JSON.stringify(newVal[0].default_goods))
            check.nos = check.data.map(item => {
                return item.no
            })
        }
    }
}, {
    immediate: true,
    deep: true
})

watch(() => export_text, (newVal) => {
    if (newVal && newVal.value != '') {
        let inputText = JSON.parse(JSON.stringify(newVal.value))
        // 使用正则表达式分割文本，逗号或换行符作为分隔符
        const regex = /[,，\n]+/g;
        const splitResult = inputText.split(regex).filter(item => item.trim() !== '');
        export_data.value = splitResult;
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
    .goods-item {
        height: 98px;
        background-color: #fff;
        border: 1px solid #d8d8d8;
        border-radius: 4px;
        position: relative;
        .icon-bars {
            font-size: 20px;
            padding: 10px;
            cursor: move;
        }
        .icon-guanbi {
            font-size: 16px;
            position: absolute;
            top: -8px;
            right: -8px;
            cursor: pointer;
        }
        .image-item {
            width: 50px;
            height: 50px;
            text-align: center;
            background-color: #fff;
            overflow: hidden;
            img {
                width: auto;
                height: 100%;
                max-height: 100%;
                margin: 0 auto;
            }
        }
        .goods-info {
            width: calc(100% - 90px);
            padding: 0 10px;
            box-sizing: border-box;
            line-height: normal;
            .goods-price {
                color: var(--red-color);
            }
        }
    }
    .left-wrapper {
        width: calc(100% - 328px);
        height: 100%;
        padding: 0 10px 14px;
        border-right: 1px solid #d8d8d8;
        box-sizing: border-box;
        .table-wrapper {
            width: 100%;
            max-height: 500px;
            padding: 0 10px;
            box-sizing: border-box;
            overflow: hidden auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
            .goods-item {
                flex: 0 0 calc(50% - 30px);
                .icon-check {
                    width: 100%;
                    height: 100%;
                    :deep(.el-checkbox__input) {
                        padding: 10px;
                    }
                }
                .goods-info {
                    width: calc(100% - 60px);
                    .goods-name {
                        width: 170px;
                    }
                }
            }
        }
    }
    .right-wrapper {
        width: 328px;
        height: 100%;
        box-sizing: border-box;
        .group-title {
            height: 50px;
            padding: 0 20px;
        }
        .goods-group-dragable {
            width: 100%;
            height: calc(100% - 100px);
            padding: 0 20px;
            box-sizing: border-box;
            overflow: hidden auto;
            .goods-item {
                &:first-child{
                    margin-top: 10px;
                }
            }
            &::-webkit-scrollbar {
                display: none;
            }
        }
        .button-wrapper {
            height: 50px;
            padding: 0 10px;
        }
    }
}

.mt-10 {
    margin-top: 10px;
}
.mb-10 {
    margin-bottom: 10px;
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
