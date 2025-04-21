<script setup>
import Http from '@/utils/http'
import PageTable from '@/components/common/PageTable.vue'
import SearchForm from '@/components/common/SearchForm.vue'
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    page: 1,
    per_page: 10,
    user_name: '',
    coupon_id: '',
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

const defaultPage = {
    page: 1,
    per_page: 10
};

const pagination = reactive({ ...defaultPage });

const handlePageChange = (page, per_page) => {
    pagination.per_page = per_page;
    getData(page);
};

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;
    Http.doGet('marketing/coupon/user', queryParams).then(res => {
        loading.value = false;
        if (res.code === 200) {
            tableData.value = res.data;
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}

onMounted( () => {
    // 解析URL查询字符串
    const urlParams = new URLSearchParams(window.location.search);
    // 获取user_id参数
    queryParams.coupon_id = urlParams.get('coupon_id') || '';
    getData()
});
const tableData = ref([]);
const loading = ref(false);
</script>

<template>
    <search-form :model="queryParams">
        <el-form-item label="用户名称" prop="user_name">
            <el-input
                v-model="queryParams.user_name"
                placeholder="请输入用户名称"
                clearable
                @keyup.enter="handleSearch"
            />
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="handleSearch">搜索</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        stripe
        border
        @change="handlePageChange"
        v-loading="loading">
        <el-table-column label="ID" prop="id"></el-table-column>
        <el-table-column label="红包名称" prop="name"></el-table-column>
        <el-table-column label="用户名称" prop="user_name"></el-table-column>
        <el-table-column label="使用时间" prop="used_time"></el-table-column>
        <el-table-column label="订单编号" prop="order_sn"></el-table-column>
        <el-table-column label="领取时间" prop="created_at"></el-table-column>
    </page-table>
</template>

<style scoped lang="scss">
</style>
