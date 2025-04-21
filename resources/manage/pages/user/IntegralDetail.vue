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
    type: 0,
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
    Http.doGet('integral/detail', queryParams).then(res => {
        loading.value = false;
        if (res.code === 200) {
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

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
}

onMounted( () => {
    getData()
});

const tableData = ref([]);
const types = ref([
    {
        value: 0,
        label: '全部'
    },
    {
        value: 1,
        label: '积分获取'
    },
    {
        value: 2,
        label: '积分支出'
    }
]);
const loading = ref(false);
</script>

<template>
    <search-form :model="queryParams">
        <el-form-item label="用户名" prop="user_name">
            <el-input
                v-model="queryParams.user_name"
                placeholder="请输入用户名"
                clearable
                @keyup.enter="handleSearch"
            />
        </el-form-item>
        <el-form-item label="积分类型" prop="type">
            <el-select
                v-model="queryParams.type"
                style="width: 240px"
            >
                <el-option
                    v-for="item in types"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"
                />
            </el-select>
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
            <el-table-column label="用户ID" prop="user_id"></el-table-column>
            <el-table-column label="用户名" prop="user_name"></el-table-column>
            <el-table-column label="积分类型" prop="type">
                <template #default="scope">
                    <el-tag v-if="scope.row.type === 1" type="success">积分获取</el-tag>
                    <el-tag v-else type="danger">积分支出</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="积分" prop="number"></el-table-column>
            <el-table-column label="积分描述" prop="desc"></el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="更新时间" prop="updated_at"></el-table-column>
    </page-table>>
</template>

<style scoped lang="scss">

</style>
