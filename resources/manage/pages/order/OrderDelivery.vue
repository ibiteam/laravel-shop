<script setup lang="ts">
import { Search, RefreshLeft } from '@element-plus/icons-vue'
import { orderDeliveryIndex, orderQueryExpress } from '@/api/order';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import PublicPageTable from '@/components/common/PublicPageTable.vue';
const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

const tableData = ref([]);
const loading = ref(false);

const queryParams = reactive({
    delivery_no: '',
    order_no: '',
    created_start_time: '',
    created_end_time: '',
    page: 1,
    number: 10,
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

// 重置搜索条件
const resetSearch = () => {
    queryParams.delivery_no = '';
    queryParams.order_no = '';
    queryParams.created_start_time = '';
    queryParams.created_end_time = '';
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

    orderDeliveryIndex(queryParams).then(res => {
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

/* 物流轨迹开始 */
const logisticsVisible = ref(false)
const logisticsInitLoading = ref(false);
const logisticsData = ref([])
const openLogisticsDialog = (orderId) => {
    logisticsInitLoading.value = true
    orderQueryExpress({id: orderId}).then(res => {
        logisticsInitLoading.value = false
        if (cns.$successCode(res.code)) {
            logisticsData.value = res.data
            logisticsVisible.value = true
        } else {
            cns.$message.error(res.message)
        }
    })
}
const closeLogisticsDialog = () => {
    logisticsVisible.value = false;
    logisticsData.value = [];
    logisticsInitLoading.value = false;
}
/* 物流轨迹结束 */

onMounted(() => {
    getData();
})
</script>

<template>
    <div class="common-wrap">
        <el-header style="padding: 10px 0;height: auto;">
            <!-- 添加搜索表单 -->
            <el-form :inline="true" :model="queryParams" class="search-form">
                <el-form-item label="运单号" prop="delivery_no">
                    <el-input
                        v-model="queryParams.delivery_no"
                        placeholder="请输入运单号搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="订单编号" prop="order_no">
                    <el-input
                        v-model="queryParams.order_no"
                        placeholder="请输入订单编号搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="发货开始时间" prop="created_start_time">
                    <el-date-picker
                        v-model="queryParams.created_start_time"
                        type="datetime"
                        placeholder="请选择发货开始时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item label="发货结束时间" prop="created_end_time">
                    <el-date-picker
                        v-model="queryParams.created_end_time"
                        type="datetime"
                        placeholder="请选择发货结束时间"
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
            <el-table-column label="ID" prop="id" width="80px"></el-table-column>
            <el-table-column label="运单号" prop="delivery_no" width="180px"></el-table-column>
            <el-table-column label="订单编号" prop="order.no" width="180px"></el-table-column>
            <el-table-column label="快递公司" prop="ship_company.name"></el-table-column>
            <el-table-column label="快递单号" width="240px">
                <template #default="scope">
                    <span>{{ scope.row.ship_no }}</span>
                    <el-button v-if="scope.row.ship_no" class="s-flex ai-ct" :loading="logisticsInitLoading" link type="primary" @click="openLogisticsDialog(scope.row.order_id)">查看物流</el-button>
                </template>
            </el-table-column>
            <el-table-column label="状态">
                <template #default="scope">
                    <span v-if="scope.row.status === 0">待收货</span>
                    <span v-else-if="scope.row.status === 1">已收货</span>
                    <span v-else>--</span>
                </template>
            </el-table-column>
            <el-table-column label="操作人" prop="admin_user.nickname"></el-table-column>
            <el-table-column label="备注">
                <template #default="scope">
                    <span v-if="scope.row.remark">{{ scope.row.remark }}</span>
                    <span v-else>无</span>
                </template>
            </el-table-column>
            <el-table-column label="发货时间" prop="shipped_at" width="160px"></el-table-column>
            <el-table-column label="发货时间" prop="received_at" width="160px"></el-table-column>
        </PublicPageTable>
        <!-- 查看物流 -->
        <el-dialog v-model="logisticsVisible" title="查看物流轨迹" width="600"  center :before-close="closeLogisticsDialog">
            <div style="height: 60vh;overflow: auto">
                <el-timeline :reverse="false">
                    <el-timeline-item v-for="(shipItem, index) in logisticsData" :key="index" :timestamp="shipItem.time">
                        {{ shipItem.context }}
                    </el-timeline-item>
                </el-timeline>
            </div>
            <template #footer>
                <div class="dialog-footer">
                    <el-button type="info" @click="closeLogisticsDialog()">关闭</el-button>
                </div>
            </template>
        </el-dialog>
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
