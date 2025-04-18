<template>
    <search-form :model="query" :label-width="120">
        <el-form-item label="订单编号" prop="order_sn">
            <el-input
                v-model="query.order_sn"
                placeholder="请输入订单编号搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="用户ID/用户名" prop="user_keywords">
            <el-input
                v-model="query.user_keywords"
                placeholder="请输入用户ID/用户名搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="商品ID" prop="goods_id">
            <el-input
                v-model="query.goods_id"
                placeholder="请输入商品ID搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="历史商品名称" prop="goods_name">
            <el-input
                v-model="query.goods_name"
                placeholder="请输入历史商品名称搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="订单状态" prop="order_status">
            <el-select v-model="query.order_status" placeholder="请选择订单状态" clearable>
                <el-option v-for="item in orderStatusOptions" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
        </el-form-item>
        <el-form-item label="支付状态" prop="pay_status">
            <el-select v-model="query.pay_status" placeholder="请选择支付状态" clearable>
                <el-option v-for="item in payStatusOptions" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
        </el-form-item>
        <el-form-item label="发货状态" prop="shipping_status">
            <el-select v-model="query.shipping_status" placeholder="请选择发货状态" clearable>
                <el-option v-for="item in shipStatusOptions" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
        </el-form-item>
        <el-form-item label="订单来源" prop="source">
            <el-select v-model="query.source" placeholder="请选择订单来源" clearable>
                <el-option v-for="item in sourceOptions" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
        </el-form-item>
        <el-form-item label="下单时间">
            <el-date-picker
                v-model="query.done_start_time"
                type="datetime"
                placeholder="开始时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
            <span>&nbsp;至&nbsp;</span>
            <el-date-picker
                v-model="query.done_end_time"
                type="datetime"
                placeholder="下单结束时间"
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
        @change="handlePageChange"
    >
        <el-table-column label="订单ID" prop="id" width="80px"></el-table-column>
        <el-table-column label="订单编号" width="180px">
            <template #default="{ row }">
                <el-button link type="primary" @click="copyOrderSn(row.order_sn)">
                    {{ row.order_sn }}<el-icon><DocumentCopy /></el-icon>
                </el-button>
            </template>
        </el-table-column>
        <el-table-column label="下单时间" width="300px">
            <template #default="{ row }">
                <div>{{ row.payer.user_name }}</div>
                <div>{{ row.payer.done_time }}</div>
            </template>
        </el-table-column>
        <el-table-column label="收货人" prop="consignee"></el-table-column>
        <el-table-column label="总金额">
            <template #default="{ row }">
                <span v-if="row.order_amount > 0">{{ row.order_amount }}元</span>
                <span v-if="row.order_amount > 0 && row.integral > 0"> + </span>
                <span v-if="row.integral > 0">{{ row.integral }}{{ row.integral_name }}</span>
            </template>
        </el-table-column>
        <el-table-column label="已付款金额">
            <template #default="{ row }">
                <span v-if="row.money_paid > 0">{{ row.money_paid }}元</span>
                <span v-if="row.money_paid > 0 && row.integral > 0"> + </span>
                <span v-if="row.integral > 0">{{ row.integral }}{{ row.integral_name }}</span>
            </template>
        </el-table-column>
        <el-table-column label="来源" prop="source"></el-table-column>
        <el-table-column label="订单状态" prop="status" width="160px"></el-table-column>
        <el-table-column label="操作">
            <template #default="{ row }">
                <el-button link type="primary" size="large" @click="openDetail(row)">查看</el-button>
            </template>
        </el-table-column>
    </page-table>
</template>
<script setup lang="ts">
import { orderIndex } from '@/api/order.js'
import { ref, reactive, getCurrentInstance, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PageTable from '@/components/common/PageTable.vue'
import SearchForm from '@/components/common/SearchForm.vue'
import { OrderStatus,PayStatus,ShipStatus } from '@/enums/model'
import { DocumentCopy } from '@element-plus/icons-vue';
const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

/* 定义搜索下拉数据 */
const orderStatusOptions = [
    {label: '未确认', value: OrderStatus.Unconfirmed},
    {label: '已确认', value: OrderStatus.Confirmed},
    {label: '已取消', value: OrderStatus.Canceled},
]
const payStatusOptions = [
    {label: '待支付', value: PayStatus.Unpaid},
    {label: '已支付', value: PayStatus.Paid},
]
const shipStatusOptions = [
    {label: '未发货', value: ShipStatus.Unshipped},
    {label: '已发货', value: ShipStatus.Shipped},
    {label: '已收货', value: ShipStatus.Received},
]
const sourceOptions = [
    {label: 'PC端', value: 'pc'},
    {label: 'H5端', value: 'h5'},
    {label: 'APP端', value: 'app'},
    {label: '微信小程序', value: 'wechat_mini'},
]
/* 定义表格数据 */
const tableData = ref({})
const loading = ref(false);
/* 定义搜索参数 */
const defaultQuery = {
    order_sn: '',
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
}
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
    loading.value = true
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    }
    orderIndex(params).then((res:any) => {
        loading.value = false
        if (cns.$successCode(res.code)) {
            tableData.value = res.data
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false
    })
}
/* 点击分页触发方法 */
const handlePageChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}

const openDetail = (row:any) => {
    router.push({name:'manage.order.detail', query: {id: row.id},params:{order_sn: row.order_sn}})
}

// 复制订单编号
const copyOrderSn = (orderSn: string) => {
    const tempInput = document.createElement('input')
    tempInput.value = orderSn
    document.body.appendChild(tempInput)
    tempInput.select()
    document.execCommand('copy')
    document.body.removeChild(tempInput)
    cns.$message.success('订单编号已复制')
}

onMounted( () => {
    getData()
})
</script>
<style scoped lang="scss">

</style>
