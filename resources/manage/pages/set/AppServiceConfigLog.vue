<script setup lang="ts">
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
    alias: '',
    name: '',
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

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;
    Http.doGet('set/app_service_log',queryParams).then((res : any) => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data;
            name_list.value = res.data.alias_list;
            alias_list.value = res.data.name_list;
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}
const handleShowClick = (row : any, title : string) => {
    dialogShowVisible.value = true;
    configTitle.value = title
    window_data.value = row
}
const handleCloseSeeConfig = () => {
    dialogShowVisible.value = false;
    configTitle.value = ''
    window_data.value = ''
}

const handlePageChange = (page: number, per_page: number) => {
    pagination.per_page = per_page;
    getData(page);
};

onMounted(() => {
    getData()
});

const window_data = ref('');
const configTitle = ref('');
const dialogShowVisible = ref(false);
const name_list = ref([]);
const alias_list = ref([]);
const tableData = ref([]);
const loading = ref(false);
</script>

<template>
    <search-form :model="queryParams">
        <el-form-item label="用户名" prop="user_name">
            <el-input v-model="queryParams.user_name" clearable placeholder="请输入" @keyup.enter="handleSearch()" />
        </el-form-item>
        <el-form-item label="别名">
            <el-select v-model="queryParams.alias" clearable filterable style="width: 240px" >
                <el-option
                    v-for="(item, key) in alias_list"
                    :key="key"
                    :label="item"
                    :value="item"
                />
            </el-select>
        </el-form-item>
        <el-form-item label="类型">
            <el-select v-model="queryParams.name" clearable style="width: 240px" >
                <el-option
                    v-for="(item, key) in name_list"
                    :key="key"
                    :label="item"
                    :value="item"
                />
            </el-select>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="handleSearch()">搜索</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        stripe
        border
        @change="handlePageChange"
        v-loading="loading">
        <el-table-column label="ID" prop="id"></el-table-column>
        <el-table-column label="用户名" prop="user_name"></el-table-column>
        <el-table-column label="请求参数">
            <template #default="scope">
                <el-button type="primary" size="small" @click="handleShowClick(scope.row.request_param, '请求参数')">查看</el-button>
            </template>
        </el-table-column>
        <el-table-column label="返回数据">
            <template #default="scope">
                <el-button type="primary" size="small" @click="handleShowClick(scope.row.result_data, '返回数据')">查看</el-button>
            </template>
        </el-table-column>
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="类型" prop="name"></el-table-column>
    </page-table>

    <el-dialog
        :title="configTitle"
        v-model="dialogShowVisible"
        width="800px"
        :before-close="handleCloseSeeConfig"
    >
        {{ window_data }}
        <br>
        <div style="text-align: center;margin-top: 10px">
            <el-button type="primary" @click="handleCloseSeeConfig">确认</el-button>
        </div>
    </el-dialog>
</template>
