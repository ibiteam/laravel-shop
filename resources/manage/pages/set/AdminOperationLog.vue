<template>
    <div>
        <el-header style="padding-top: 10px;">
            <el-form :inline="true" :model="searchForm" class="search-form">
                <el-form-item label="操作时间">
                    <el-date-picker
                        v-model="searchForm.start_time"
                        type="datetime"
                        placeholder="开始时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                    <span>&nbsp;至&nbsp;</span>
                    <el-date-picker
                        v-model="searchForm.end_time"
                        type="datetime"
                        placeholder="结束时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item label="操作人" prop="admin_user_name">
                    <el-input v-model="searchForm.admin_user_name" clearable placeholder="请输入"
                              @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item label="操作信息" prop="description">
                    <el-input v-model="searchForm.description" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item label="选择角色">
                    <el-select v-model="searchForm.role_id" clearable filterable placeholder="请选择">
                        <el-option v-for="item in rolesData"
                                   :key="item.value" :label="item.label" :value="item.value">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="IP" prop="ip">
                    <el-input v-model="searchForm.ip" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="getData()">搜索</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <el-table
            :data="tableData"
            stripe border
            v-loading="loading"
            style="width: 100%;">
            <el-table-column label="操作时间" prop="created_at"></el-table-column>
            <el-table-column label="操作人" prop="admin_user_name"></el-table-column>
            <el-table-column label="所属角色" prop="role_name"></el-table-column>
            <el-table-column label="操作信息" prop="description"></el-table-column>
            <el-table-column label="IP" prop="ip"></el-table-column>
        </el-table>
        <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />
    </div>
</template>

<script setup>
import { Search } from '@element-plus/icons-vue';
import Page from '@/components/common/Pagination.vue'
import { adminOperationLogIndex, adminUserRoles } from '@/api/set.js';
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const cns = getCurrentInstance().appContext.config.globalProperties;
const router = useRouter();
const route = useRoute();

const searchForm = reactive({
    start_time: '',
    end_time: '',
    admin_user_name: '',
    description: '',
    role_id: '',
    ip: '',
    page: 1,
    number: 10
});
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});
const tableData = ref([]);
const rolesData = ref([]);
const loading = ref(false);

/* 获取角色 */
const getRoles = () => {
    adminUserRoles().then(res => {
        if (cns.$successCode(res.code)) {
            rolesData.value = res.data;
        }
    }).catch(() => {
    });
};

const getData = (page = 1) => {
    loading.value = true;
    searchForm.page = page;
    adminOperationLogIndex(searchForm).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data.list;
            setPageInfo(res.data.meta);
        } else {
            cns.$message.error(res.message);
        }
    }).catch(() => {
        loading.value = false;
        cns.$message.error('获取数据失败');
    });
};

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
};
// 页码改变
const handleCurrentChange = (val) => {
    getData(val);
};
// 每页条数改变
const handleSizeChange = (val) => {
    searchForm.number = val;
    pageInfo.per_page = val;
    getData(1);
};

onMounted(() => {
    searchForm.admin_user_name = route.query.admin_user_name || '';
    getData();
    getRoles();
});
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
