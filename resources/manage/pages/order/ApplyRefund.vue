<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="用户名称">
            <el-input v-model="query.user_name" clearable placeholder="请输入" @keyup.enter="getData()"></el-input>
        </el-form-item>
        <el-form-item label="商品名称">
            <el-input v-model="query.goods_name" clearable placeholder="请输入" @keyup.enter="getData()"></el-input>
        </el-form-item>
        <el-form-item label="订单号">
            <el-input v-model="query.order_sn" clearable placeholder="请输入" @keyup.enter="getData()"></el-input>
        </el-form-item>
        <el-form-item label="退款单号">
            <el-input v-model="query.no" clearable placeholder="请输入" @keyup.enter="getData()"></el-input>
        </el-form-item>
        <el-form-item label="类型">
            <el-select v-model="query.type" clearable placeholder="请选择">
                <el-option label="退款" value="0"></el-option>
                <el-option label="退货退款" value="1"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="状态">
            <el-select v-model="query.status" clearable placeholder="请选择">
                <el-option
                    v-for="item in refundStatuses"
                    :key="item.value" :label="item.label" :value="item.value">
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="申请时间">
            <el-date-picker
                v-model="query.start_time"
                type="datetime"
                placeholder="开始时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
            <span>&nbsp;至&nbsp;</span>
            <el-date-picker
                v-model="query.end_time"
                type="datetime"
                placeholder="结束时间"
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
        :maxHeight="'700px'"
        v-loading="loading"
        @change="handlePageChange"
    >
        <el-table-column label="ID" prop="id"></el-table-column>
        <el-table-column label="用户名称" prop="user_name"></el-table-column>
        <el-table-column label="商品名称" prop="goods_name"></el-table-column>
        <el-table-column label="订单号" prop="order_sn"></el-table-column>
        <el-table-column label="退款单号" prop="no"></el-table-column>
        <el-table-column label="退款金额" prop="money"></el-table-column>
        <el-table-column label="退款数量" prop="number"></el-table-column>
        <el-table-column label="类型">
            <template #default="{ row }">
                <span v-if="row.type == 0">退款</span>
                <span v-if="row.type == 1">退货退款</span>
            </template>
        </el-table-column>
        <el-table-column label="退款原因" prop="reason"></el-table-column>
        <el-table-column label="退款描述" prop="description"></el-table-column>
        <el-table-column label="退款凭证">
            <template #default="{ row }">
                <el-image
                    style="width: 30px; height: 30px; margin-right: 10px"
                    v-for="(item, index) in row.certificate" :key="index" :src="item" @click="imageShow(item)"
                    fit="cover" lazy>
                </el-image>
            </template>
        </el-table-column>
        <el-table-column label="是否撤销">
            <template #default="{ row }">
                <span v-if="row.is_revoke == 0">否</span>
                <span v-if="row.is_revoke == 1">是</span>
            </template>
        </el-table-column>
        <el-table-column label="结果描述" prop="result"></el-table-column>
        <el-table-column label="申请次数" prop="count"></el-table-column>
        <el-table-column label="申请时间" prop="created_at"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at"></el-table-column>
        <el-table-column label="退款状态">
            <template #default="{ row }">
                <template v-for="item in refundStatuses">
                    <el-button
                        link type="primary" size="large"
                        v-if="row.status == item.value"
                        @click="openDetail(row.id)">
                        <span>{{ item.label }}</span>
                    </el-button>
                </template>
            </template>
        </el-table-column>
    </page-table>
</template>

<script setup lang="ts">
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Http from '@/utils/http';

const router = useRouter();
const cns = getCurrentInstance().appContext.config.globalProperties;
const tableData = ref([]);
const loading = ref(false);

const refundStatuses = [
    { label: '退款待处理', value: 0 },
    { label: '已经拒绝退款', value: 1 },
    { label: '退货审核成功', value: 2 },
    { label: '买家已发货', value: 3 },
    { label: '卖家已收货', value: 4 },
    { label: '退款成功', value: 5 },
    { label: '退款关闭', value: 6 }
];

const defaultQuery = reactive({
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
const query = reactive({ ...defaultQuery });

const resetSearch = () => {
    Object.assign(query, defaultQuery);
    Object.assign(pagination, defaultPage);
    getData();
};

const defaultPage = {
    page: 1,
    per_page: 10
};
const pagination = reactive({ ...defaultPage });
const handlePageChange = (page: number, per_page: number) => {
    pagination.per_page = per_page;
    getData(page);
};

const imageShow = (url: string) => {
    window.open(url);
};

const openDetail = (id: number) => {
    router.push({ name: 'manage.apply_refund.detail', params: { id: id } });
};

const getData = (page: number = defaultPage.page) => {
    loading.value = true;
    Http.doGet('apply_refund', { ...query, page: page, per_page: pagination.per_page }).then((res: any) => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data;
        } else {
            cns.$message.error(res.message);
        }
    }).catch(() => {
        loading.value = false;
        cns.$message.error('获取数据失败');
    });
};

onMounted(() => {
    getData();
});
</script>

<style scoped lang="scss">

</style>
