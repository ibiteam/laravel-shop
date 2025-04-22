<template>
    <search-form :model="query">
        <el-form-item label="商品信息" prop="keywords">
            <el-input
                v-model="query.keywords"
                placeholder="商品id/商品名称"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="用户名" prop="user_name">
            <el-input
                v-model="query.user_name"
                placeholder="请输入用户名"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handlePageChange"
    >
        <el-table-column label="ID" prop="id"></el-table-column>
        <el-table-column label="商品ID" prop="goods_id"></el-table-column>
        <el-table-column label="商品名称" prop="goods_name"></el-table-column>
        <el-table-column label="来源" prop="referer"></el-table-column>
        <el-table-column label="用户名" prop="user_name"></el-table-column>
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at"></el-table-column>
    </page-table>
</template>
<script setup lang="ts">
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import Http from '@/utils/http.js';
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';

const cns = getCurrentInstance().appContext.config.globalProperties

const defaultQuery = {
    keywords: '',
    user_name: ''
}
// 添加查询参数对象，增加搜索条件
const query = reactive({...defaultQuery});
const defaultPage = {
    page: 1,
    per_page: 10,
}
const pagination = reactive({...defaultPage})

const getData = (page = defaultPage.page) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    }
    Http.doGet('goods/views',  params).then(res => {
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
const handlePageChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}
onMounted( () => {
    getData()
});

const tableData = ref([]);
const loading = ref(false);
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

.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 15px;
}

.header-picture {
    width: 36px;
    height: 36px;
    overflow: hidden;
    border-radius: 50%;
    background: #fff;
    padding: 1px;
    box-sizing: border-box;
}
.imgs {
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    overflow: hidden;
    border-radius: 50%;
}
.header-picture .imgs img {
    width: 100%;
    height: 100%;
}
.flex-user-information {
    display: flex;align-items: center;
}
.flex-user-information .header-user-names {
    margin: 0 5px;
    width: 90px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.flex-user-information .header-user-names span {
    font-size: 14px;
    font-weight: 400;
    color: #3D3D3D;
}
</style>
