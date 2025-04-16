<script setup>
import { Plus, Search } from '@element-plus/icons-vue';
import Page from '@/components/common/Pagination.vue'
import { applyRefundIndex } from '@/api/order.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    user_name: '',
    goods_name: '',
    order_sn: '',
    no: '',
    type: null,
    status: null,
    start_time: '',
    end_time: '',
    number: 10,
    page: 1
});
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});
const tableData = ref([]);
const loading = ref(false);


const imageShow = (url) => {
    window.open(url);
};

const getData = (page = 1) => {
    loading.value = true;
    searchForm.page = page;
    applyRefundIndex(searchForm).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data.list;
            setPageInfo(res.data.meta);
        } else {
            cns.$message.error(res.message);
        }
    }).catch(() => {
        loading.value = false;
        cns.$message.error('获取数据失败');
    });
};

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
};
// 页码改变
const handleCurrentChange = (val) => {
    getData(val);
};
// 每页条数改变
const handleSizeChange = (val) => {
    searchForm.number = val;
    pageInfo.per_page = val;
    getData(1);
};

onMounted(() => {
    getData();
});
</script>
<template>
    <div class="common-wrap">
        <el-header style="padding: 10px 0;height: auto;">
            <el-form :inline="true" :model="searchForm" class="search-form" label-width="120px">
                <el-form-item label="用户名称">
                    <el-input v-model="searchForm.user_name" clearable placeholder="请输入"></el-input>
                </el-form-item>
                <el-form-item label="商品名称">
                    <el-input v-model="searchForm.goods_name" clearable placeholder="请输入"></el-input>
                </el-form-item>
                <el-form-item label="订单号">
                    <el-input v-model="searchForm.order_sn" clearable placeholder="请输入"></el-input>
                </el-form-item>
                <el-form-item label="退款单号">
                    <el-input v-model="searchForm.no" clearable placeholder="请输入"></el-input>
                </el-form-item>
                <el-form-item label="类型">
                    <el-select v-model="searchForm.type" clearable placeholder="请选择">
                        <el-option label="退款" value="0"></el-option>
                        <el-option label="退货退款" value="1"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
                    <el-select v-model="searchForm.status" clearable placeholder="请选择">
                        <el-option label="退款待处理" value="0"></el-option>
                        <el-option label="已经拒绝退款" value="1"></el-option>
                        <el-option label="退货审核成功" value="2"></el-option>
                        <el-option label="买家已发货" value="3"></el-option>
                        <el-option label="卖家已收货" value="4"></el-option>
                        <el-option label="退款成功" value="5"></el-option>
                        <el-option label="退款关闭" value="6"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="申请时间">
                    <el-date-picker
                        v-model="searchForm.start_time"
                        type="datetime"
                        placeholder="开始时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                    <span>&nbsp;至&nbsp;</span>
                    <el-date-picker
                        v-model="searchForm.end_time"
                        type="datetime"
                        placeholder="结束时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="getData()">搜索</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <el-table
            :data="tableData"
            stripe border
            v-loading="loading"
            style="width: 100%;">
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="用户名称" prop="user_name"></el-table-column>
            <el-table-column label="商品名称" prop="goods_name"></el-table-column>
            <el-table-column label="订单号" prop="order_sn"></el-table-column>
            <el-table-column label="退款单号" prop="no"></el-table-column>
            <el-table-column label="退款金额" prop="money"></el-table-column>
            <el-table-column label="退款数量" prop="number"></el-table-column>
            <el-table-column label="状态">
                <template #default="scope">
                    <span v-if="scope.row.status == 0">退款待处理</span>
                    <span v-if="scope.row.status == 1">已经拒绝退款</span>
                    <span v-if="scope.row.status == 2">退货审核成功</span>
                    <span v-if="scope.row.status == 3">买家已发货</span>
                    <span v-if="scope.row.status == 4">卖家已收货</span>
                    <span v-if="scope.row.status == 5">退款成功</span>
                    <span v-if="scope.row.status == 6">退款关闭</span>
                </template>
            </el-table-column>
            <el-table-column label="类型">
                <template #default="scope">
                    <span v-if="scope.row.type == 0">退款</span>
                    <span v-if="scope.row.type == 1">退货退款</span>
                </template>
            </el-table-column>
            <el-table-column label="退款原因" prop="reason"></el-table-column>
            <el-table-column label="退款描述" prop="description"></el-table-column>
            <el-table-column label="退款凭证">
                <template #default="scope">
                    <el-image
                        style="width: 30px; height: 30px; margin-right: 10px"
                        v-for="(item, index) in scope.row.certificate" :key="index" :src="item" @click="imageShow(item)"
                        fit="cover" lazy>
                    </el-image>
                </template>
            </el-table-column>
            <el-table-column label="是否撤销">
                <template #default="scope">
                    <span v-if="scope.row.is_revoke == 0">否</span>
                    <span v-if="scope.row.is_revoke == 1">是</span>
                </template>
            </el-table-column>
            <el-table-column label="结果描述" prop="result"></el-table-column>
            <el-table-column label="申请次数" prop="count"></el-table-column>
            <el-table-column label="申请时间" prop="created_at"></el-table-column>
            <el-table-column label="更新时间" prop="updated_at"></el-table-column>
            <!--<el-table-column label="操作">
                <template #default="scope">
                    <el-button link type="primary" size="large" @click="openLogDialog(scope.row)">协商历史</el-button>
                    <el-button link type="primary" size="large" @click="openShipDialog(scope.row)">物流轨迹</el-button>
                </template>
            </el-table-column>-->
        </el-table>
        <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />
    </div>
</template>

<style scoped lang="scss">
.search-form {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 10px;

    :deep(.el-select) {
        width: 200px;
    }

    :deep(.el-input) {
        width: 200px;
    }
}
</style>
