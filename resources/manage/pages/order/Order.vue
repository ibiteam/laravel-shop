<script setup lang="ts">
import { Search, RefreshLeft } from '@element-plus/icons-vue'
import { orderIndex } from '@/api/order.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import PublicPageTable from '@/components/common/PublicPageTable.vue';
const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

const tableData = ref([]);
const loading = ref(false);

const orderStatusOptions = [
    {label: '未确认', value: 0},
    {label: '已确认', value: 1},
    {label: '已取消', value: 2},
];
const payStatusOptions = [
    {label: '待支付', value: 0},
    {label: '已支付', value: 1},
];
const shipStatusOptions = [
    {label: '未发货', value: 0},
    {label: '已发货', value: 1},
    {label: '已收货', value: 2},
];

const sourceOptions = [
    {label: 'PC端', value: 'pc'},
    {label: 'H5端', value: 'h5'},
    {label: 'APP端', value: 'app'},
    {label: '微信小程序', value: 'wechat_mini'},
];

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    no: '',
    user_keywords: '',
    goods_id: '',
    goods_name: '',
    consignee_keywords: '',
    order_status: null,
    pay_status: null,
    shipping_status: null,
    done_start_time: null,
    done_end_time: null,
    source: null,
    page: 1,
    number: 10,
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

// 重置搜索条件
const resetSearch = () => {
    queryParams.no = '';
    queryParams.user_keywords = '';
    queryParams.goods_id = '';
    queryParams.goods_name = '';
    queryParams.consignee_keywords = '';
    queryParams.order_status = null;
    queryParams.pay_status = null;
    queryParams.shipping_status = null;
    queryParams.done_start_time = null;
    queryParams.done_end_time = null;
    queryParams.source = null;
    queryParams.page = 1;
    queryParams.number = 10;
    getData(1);
};

// 添加分页相关状态
const pageInfo = reactive({
    number: 10,
    total: 0,
    current_page: 1,
})

// 页码改变
const handleCurrentChange = (val) => {
    getData(val);
}

// 每页条数改变
const handleSizeChange = (val) => {
    pageInfo.per_page = val;
    getData(1);
}

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
}

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;
    queryParams.number = pageInfo.per_page;

    orderIndex(queryParams).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data.list;
            // 更新分页信息
            setPageInfo(res.data.meta);
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}

const openDetailTab = (order) => {
  router.push({name:'manage.order.detail', query: {id: order.id},params:{no: order.no}})
}

onMounted( () => {
    getData()
});
</script>

<template>
    <div class="common-wrap">
        <el-header style="padding: 10px 0;height: auto;">
            <!-- 添加搜索表单 -->
            <el-form :inline="true" :model="queryParams" class="search-form" label-width="120px">
                <el-form-item label="订单编号" prop="no">
                    <el-input
                        v-model="queryParams.no"
                        placeholder="请输入订单编号搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="用户ID/用户名" prop="user_keywords">
                    <el-input
                        v-model="queryParams.user_keywords"
                        placeholder="请输入用户ID/用户名搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="商品ID" prop="goods_id">
                    <el-input
                        v-model="queryParams.goods_id"
                        placeholder="请输入商品ID搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="历史商品名称" prop="goods_name">
                    <el-input
                        v-model="queryParams.goods_name"
                        placeholder="请输入历史商品名称搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="订单状态" prop="order_status">
                    <el-select v-model="queryParams.order_status" placeholder="请选择订单状态" clearable>
                        <el-option v-for="item in orderStatusOptions" :key="item.value" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="支付状态" prop="pay_status">
                    <el-select v-model="queryParams.pay_status" placeholder="请选择支付状态" clearable>
                        <el-option v-for="item in payStatusOptions" :key="item.value" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="发货状态" prop="shipping_status">
                    <el-select v-model="queryParams.shipping_status" placeholder="请选择发货状态" clearable>
                        <el-option v-for="item in shipStatusOptions" :key="item.value" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="订单来源" prop="source">
                    <el-select v-model="queryParams.source" placeholder="请选择订单来源" clearable>
                        <el-option v-for="item in sourceOptions" :key="item.value" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="下单开始时间" prop="done_start_time">
                    <el-date-picker
                        v-model="queryParams.done_start_time"
                        type="datetime"
                        placeholder="请选择下单开始时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item label="下单结束时间" prop="done_end_time">
                    <el-date-picker
                        v-model="queryParams.done_end_time"
                        type="datetime"
                        placeholder="请选择下单结束时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                    <el-button :icon="RefreshLeft" @click="resetSearch">重置</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <PublicPageTable
            :data="tableData"
            v-loading="loading"
            :pageInfo="pageInfo"
            @sizeChange="handleSizeChange"
            @currentChange="handleCurrentChange"
            style="width: 100%;">
            <el-table-column label="订单ID" prop="id"></el-table-column>
            <el-table-column label="订单编号" prop="no"></el-table-column>
            <el-table-column label="下单时间" width="400px">
                <template #default="scope">
                    <div>{{ scope.row.payer.user_name }}</div>
                    <div>{{ scope.row.payer.done_time }}</div>
                </template>
            </el-table-column>
            <el-table-column label="收货人" prop="consignee"></el-table-column>
            <el-table-column label="总金额">
                <template #default="scope">
                    <span v-if="scope.row.order_amount > 0">{{ scope.row.order_amount }}元</span>
                    <span v-if="scope.row.order_amount > 0 && scope.row.integral > 0"> + </span>
                    <span v-if="scope.row.integral > 0">{{ scope.row.integral }}{{ scope.row.integral_name }}</span>
                </template>
            </el-table-column>
            <el-table-column label="已付款金额">
                <template #default="scope">
                    <span v-if="scope.row.money_paid > 0">{{ scope.row.money_paid }}元</span>
                    <span v-if="scope.row.money_paid > 0 && scope.row.integral > 0"> + </span>
                    <span v-if="scope.row.integral > 0">{{ scope.row.integral }}{{ scope.row.integral_name }}</span>
                </template>
            </el-table-column>
            <el-table-column label="来源" prop="source"></el-table-column>
            <el-table-column label="订单状态" prop="status"></el-table-column>
            <el-table-column label="操作">
                <template #default="scope">
                    <el-button link type="primary" size="large" @click="openDetailTab(scope.row)">编辑</el-button>
                </template>
            </el-table-column>
        </PublicPageTable>
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
