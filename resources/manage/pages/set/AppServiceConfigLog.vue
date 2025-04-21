<script setup>
import { Search } from '@element-plus/icons-vue';
import Page from '@/components/common/Pagination.vue'
import { getAppServiceLog } from '@/api/app_service.js'
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    page: 1,
    number: 10,
    user_name: '',
    alias: '',
    name: '',
});

// 添加分页相关状态
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

// 页码改变
const handleCurrentChange = (val) => {
    getData(val);
}

// 每页条数改变
const handleSizeChange = (val) => {
    queryParams.number = val;
    pageInfo.per_page = val;
    getData(1);
}

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;
    getAppServiceLog(queryParams).then(res => {
        loading.value = false;
        if (res.code === 200) {
            tableData.value = res.data.list;
            name_list.value = res.data.alias_list;
            alias_list.value = res.data.name_list;
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

// 设置分页数据
const handleShowClick = (row, title) => {
    dialogShowVisible.value = true;
    configTitle.value = title
    window_data.value = row
}
// 设置分页数据
const handleCloseSeeConfig = () => {
    dialogShowVisible.value = false;
    configTitle.value = ''
    window_data.value = ''
}

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
    <div>
        <el-header style="padding: 10px 0;">
            <!-- 添加搜索表单 -->
            <el-form :inline="true" :model="queryParams" class="search-form">
                <el-form-item label="用户名" prop="user_name">
                    <el-input
                        v-model="queryParams.user_name"
                        placeholder="请输入用户名"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="别名">
                    <el-select
                        v-model="queryParams.alias"
                        clearable
                        style="width: 240px"
                    >
                        <el-option
                            v-for="(item, key) in alias_list"
                            :key="key"
                            :label="item"
                            :value="item"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="类型">
                    <el-select
                        v-model="queryParams.name"
                        clearable
                        style="width: 240px"
                    >
                        <el-option
                            v-for="(item, key) in name_list"
                            :key="key"
                            :label="item"
                            :value="item"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <el-table
            :data="tableData"
            stripe
            border
            v-loading="loading"
            style="width: 100%;">
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="用户名" prop="user_name"></el-table-column>
            <el-table-column label="请求参数">
                <template #default="scope">
                    <el-button type="primary" @click="handleShowClick(scope.row.request_param, '请求参数')">查看</el-button>
                </template>
            </el-table-column>
            <el-table-column label="返回数据">
                <template #default="scope">
                    <el-button type="primary" @click="handleShowClick(scope.row.result_data, '返回数据')">查看</el-button>
                </template>
            </el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="类型" prop="name"></el-table-column>
        </el-table>
        <!-- 添加分页组件 -->
        <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />
    </div>

    <el-dialog
        :title="configTitle"
        v-model="dialogShowVisible"
        width="800px"
        :before-close="handleCloseSeeConfig"
    >
        <div v-html="window_data"></div>
        <br>
        <div style="text-align: center">
            <el-button type="primary" @click="handleCloseSeeConfig">确认</el-button>
        </div>
    </el-dialog>
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
    display: flex;
    align-items: center;
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
