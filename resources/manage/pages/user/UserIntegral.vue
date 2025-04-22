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
    keywords: '',
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;
    Http.doGet('integral', queryParams).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data;
            // 更新分页信息
            setPageInfo(res.data.meta);
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}

const defaultPage = {
    page: 1,
    per_page: 10
};

const pagination = reactive({ ...defaultPage });

const handlePageChange = (page, per_page) => {
    pagination.per_page = per_page;
    getData(page);
};

onMounted( () => {
    getData()
});

const tableData = ref([]);
const loading = ref(false);
</script>

<template>
    <search-form :model="queryParams">
        <el-form-item label="商品信息" prop="keywords">
            <el-input
                v-model="queryParams.keywords"
                placeholder="商品id/商品名称"
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
        <el-table-column label="用户ID" prop="id"></el-table-column>
        <el-table-column label="用户名" prop="user_name"></el-table-column>
        <el-table-column label="总积分" prop="integral"></el-table-column>
    </page-table>
</template>

<style scoped lang="scss">

</style>
