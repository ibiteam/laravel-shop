<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="流水号" prop="transaction_no">
            <el-input
                v-model="query.transaction_no"
                placeholder="请输入流水号搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="用户名" prop="user_name">
            <el-input
                v-model="query.user_name"
                placeholder="请输入用户名搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="类型" prop="transaction_type">
            <el-select v-model="query.transaction_type" placeholder="请选择流水类型" clearable>
                <el-option v-for="item in transactionTypeOptions" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
        </el-form-item>
        <el-form-item label="状态" prop="status">
            <el-select v-model="query.status" placeholder="请选择流水状态" clearable>
                <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
        </el-form-item>
        <el-form-item label="业务类型" prop="type">
            <el-select v-model="query.type" placeholder="请选择业务类型" clearable>
                <el-option v-for="item in typeOptions" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
        </el-form-item>
        <el-form-item label="订单编号" prop="order_sn">
            <el-input
                v-model="query.order_sn"
                placeholder="请输入订单编号搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="支付开始时间" prop="paid_start_time">
            <el-date-picker
                v-model="query.paid_start_time"
                type="datetime"
                placeholder="请选择支付开始时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
        </el-form-item>
        <el-form-item label="支付结束时间" prop="paid_end_time">
            <el-date-picker
                v-model="query.paid_end_time"
                type="datetime"
                placeholder="请选择支付结束时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button @click="resetSearch">重置</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handleChange"
    >
        <el-table-column label="ID" prop="id" width="80px"></el-table-column>
        <el-table-column label="业务单号" prop="type_no" width="180px"></el-table-column>
        <el-table-column label="业务类型" width="90px">
            <template #default="scope">
                <span v-if="scope.row.type == 'order'">订单</span>
                <span v-else>--</span>
            </template>
        </el-table-column>
        <el-table-column label="父级流水号" prop="parent_transaction_no" width="210px"></el-table-column>
        <el-table-column label="流水号" prop="transaction_no" width="270px"></el-table-column>
        <el-table-column label="类型" width="80px">
            <template #default="scope">
                <el-tag v-if="scope.row.transaction_type == 'pay'" type="success">支付</el-tag>
                <el-tag v-if="scope.row.transaction_type == 'refund'" type="danger">退款</el-tag>
            </template>
        </el-table-column>
        <el-table-column label="用户名" prop="user_name" width="220px"></el-table-column>
        <el-table-column label="支付方式" prop="payment_name" width="100px"></el-table-column>
        <el-table-column label="状态" width="90px">
            <template #default="scope">
                <el-tag v-if="scope.row.status == 1" type="success">成功</el-tag>
                <el-tag v-else type="danger">待处理</el-tag>
            </template>
        </el-table-column>
        <el-table-column label="金额" prop="amount"></el-table-column>
        <el-table-column label="成功时间" prop="paid_at" width="160px"></el-table-column>
        <el-table-column label="创建时间" prop="created_at" width="160px"></el-table-column>
        <el-table-column label="备注" prop="remark" width="160px" show-overflow-tooltip></el-table-column>
        <el-table-column label="操作" width="160px" fixed="right">
            <template #default="scope">
                <el-button link type="primary" v-if="scope.row.can_refund && scope.row.transaction_type === 'pay' && scope.row.status === 1" @click="openRefundDialog(scope.row.id)">退款</el-button>
            </template>
        </el-table-column>
    </page-table>
    <!-- 申请退款 -->
    <el-dialog v-model="refundVisible" title="退款" width="600" center :before-close="closeRefundDialog">
        <el-form :model="refundForm" ref="refundFormRef" :rules="refundFormRules" label-width="auto" style="width: 100%;">
            <el-form-item label="退款原因" prop="reason">
                <el-input v-model="refundForm.reason" placeholder="请输入退款原因" show-word-limit maxlength="80"/>
                <small>若传了退款原因，该原因将在下发给用户的退款消息中显示</small>
                <small>注意：</small>
                <small>1、该退款原因参数的长度不得超过80个字节；</small>
                <small>2、当订单退款金额小于等于1元且为部分退款时，退款原因将不会在消息中体现；</small>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closeRefundDialog()">取消</el-button>
                <el-button type="primary" :loading="submitRefundFormLoading" @click="submitRefundForm()">确定</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script setup lang="ts">
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { transactionIndex, transactionRefund } from '@/api/set.js';
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';

const cns = getCurrentInstance().appContext.config.globalProperties

/* 定义搜索下拉数据 */
const typeOptions = [
    { value: 'order', label: '订单', },
];
const statusOptions = [
    { value: 0, label: '待处理', },
    { value: 1, label: '成功', },
];
const transactionTypeOptions = [
    { value: 'pay', label: '支付', },
    { value: 'refund', label: '退款', },
];
/* 定义表格数据 */
const tableData = ref([]);
const loading = ref(false);
/* 定义搜索参数 */
const defaultQuery = reactive({
    transaction_no: '',
    order_sn: '',
    type: '',
    user_name: '',
    transaction_type: '',
    status: null,
    paid_start_time: '',
    paid_end_time: '',
});
const query = reactive({...defaultQuery})
/* 定义默认分页参数 */
const defaultPage = {
    page: 1,
    per_page: 10,
}
const pagination = reactive({...defaultPage})
/* 重置搜索条件 */
const resetSearch = () => {
    Object.assign(query, defaultQuery)
    Object.assign(pagination, defaultPage)
    getData()
}
/* 获取分页数据 */
const getData = (page:number = defaultPage.page) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    }
    transactionIndex(params).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}
/* 点击分页触发方法 */
const handleChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}

/* 申请退款开始 */
const refundVisible = ref(false)
const refundFormRef = ref(null)
const submitRefundFormLoading = ref(false)
const refundForm = reactive({
    id: 0,
    reason: '',
})
const refundFormRules = {
    reason: [
        { required: false, message: '请输入退款原因', trigger: 'blur' },
    ],
}
const openRefundDialog = (transactionId) => {
    refundVisible.value = true;
    refundForm.id = transactionId
    refundForm.reason = '';
    submitRefundFormLoading.value = false;
}
const submitRefundForm = () => {
    refundFormRef.value.validate((valid) => {
        if (valid) {
            submitRefundFormLoading.value = true;
            transactionRefund(refundForm).then(res => {
                submitRefundFormLoading.value = false;
                if (cns.$successCode(res.code)) {
                    getData(pagination.page)
                    closeRefundDialog()
                    cns.$message.success(res.message)
                } else {
                    cns.$message.error(res.message)
                }
            });
        }
    });
};

const closeRefundDialog = () => {
    refundVisible.value = false;
    refundForm.id = 0;
    refundForm.reason = '';
    submitRefundFormLoading.value = false;
}
/* 申请退款结束 */


onMounted(() => {
    getData();
})
</script>
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
