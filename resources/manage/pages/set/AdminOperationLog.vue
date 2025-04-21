<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="操作人" prop="admin_user_name">
            <el-input v-model="query.admin_user_name" clearable placeholder="请输入" @keyup.enter="getData()" />
        </el-form-item>
        <el-form-item label="操作信息" prop="description">
            <el-input v-model="query.description" clearable placeholder="请输入" @keyup.enter="getData()" />
        </el-form-item>
        <el-form-item label="选择角色">
            <el-select v-model="query.role_id" clearable filterable placeholder="请选择">
                <el-option v-for="item in rolesData"
                           :key="item.value" :label="item.label" :value="item.value">
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="IP" prop="ip">
            <el-input v-model="query.ip" clearable placeholder="请输入" @keyup.enter="getData()" />
        </el-form-item>
        <el-form-item label="操作时间">
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
        <el-table-column label="操作时间" prop="created_at"></el-table-column>
        <el-table-column label="操作人" prop="admin_user_name"></el-table-column>
        <el-table-column label="所属角色" prop="role_name"></el-table-column>
        <el-table-column label="操作信息" prop="description"></el-table-column>
        <el-table-column label="IP" prop="ip"></el-table-column>
    </page-table>
</template>

<script setup lang="ts">
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import { useRoute } from 'vue-router';
import { adminOperationLogIndex, adminUserRoles } from '@/api/set.js';

const cns = getCurrentInstance().appContext.config.globalProperties;
const route = useRoute();

const defaultQuery = reactive({
    start_time: '',
    end_time: '',
    admin_user_name: '',
    description: '',
    role_id: '',
    ip: '',
    page: 1,
    number: 10
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

const tableData = ref([]);
const rolesData = ref([]);
const loading = ref(false);

/* 获取角色 */
const getRoles = () => {
    adminUserRoles().then((res: any) => {
        if (cns.$successCode(res.code)) {
            rolesData.value = res.data;
        }
    }).catch(() => {
    });
};

const getData = (page: number = defaultPage.page) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    };
    adminOperationLogIndex(params).then((res: any) => {
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
    query.admin_user_name = String(route.query.admin_user_name || '');
    getData();
    getRoles();
});
</script>

<style scoped lang="scss">

</style>
