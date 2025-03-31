<script setup>
import { Plus, Search } from '@element-plus/icons-vue';
import { routerCategoryIndex, routerCategoryStore, routerCategoryChangeShow } from '@/api/set.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    name: '',
    page: 1
});
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});
const tableData = ref([]);
const loading = ref(false);
const storeDialogVisible = ref(false);
const storeDialogTitle = ref('');
const submitFormRef = ref(null);
const submitLoading = ref(false);
const submitForm = reactive({
    id: 0,
    name: '',
    sort: 0,
    is_show: 1
});
const submitFormRules = reactive({
    name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
});


const openStoreDialog = (row = {}) => {
    storeDialogTitle.value = row.id > 0 ? '添加' : '编辑';
    if (row.id) {
        submitForm.id = row.id;
        submitForm.name = row.name;
        submitForm.sort = row.sort;
        submitForm.is_show = row.is_show;
    } else {
        submitForm.id = 0;
        submitForm.name = '';
        submitForm.sort = 0;
        submitForm.is_show = 1;
    }
    storeDialogVisible.value = true;
};
const closeStoreDialog = () => {
    storeDialogTitle.value = '';
    submitForm.id = 0;
    submitForm.name = '';
    submitForm.sort = 0;
    submitForm.is_show = 1;
    storeDialogVisible.value = false;
};

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            routerCategoryStore(submitForm).then(res => {
                submitLoading.value = false;
                if (res.code === 200) {
                    closeStoreDialog();
                    getData();
                } else {
                    cns.$message.error(res.message);
                }
            });
        } else {
            cns.$message.error('表单验证失败');
            return false;
        }
    });
};

const changeShow = (row) => {
    routerCategoryChangeShow({
        id: row.id,
        is_show: row.is_show
    }).then(res => {
        if (res.code === 200) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
        }
    });
};

const getData = (page = 1) => {
    loading.value = true;
    searchForm.page = page;
    routerCategoryIndex(searchForm).then(res => {
        loading.value = false;
        if (res.code === 200) {
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
    getData();
});
</script>
<template>
    <el-header style="padding-top: 10px;">
        <el-form :inline="true" :model="searchForm" class="search-form">
            <el-form-item label="名称" prop="name">
                <el-input v-model="searchForm.name" clearable placeholder="请输入" @keyup.enter="getData()" />
            </el-form-item>
            <el-form-item>
                <el-button :icon="Search" type="primary" @click="getData()">搜索</el-button>
                <el-button :icon="Plus" type="warning" @click="openStoreDialog()">添加</el-button>
            </el-form-item>
        </el-form>
    </el-header>
    <el-table
        :data="tableData"
        stripe border
        v-loading="loading"
        style="width: 100%;">
        <el-table-column label="ID" prop="id"></el-table-column>
        <el-table-column label="名称" prop="name"></el-table-column>
        <el-table-column label="排序" prop="sort"></el-table-column>
        <el-table-column label="是否展示" prop="is_show">
            <template #default="scope">
                <el-switch
                    v-model="scope.row.is_show"
                    :active-value="1" :inactive-value="0"
                    active-color="#13ce66" inactive-color="#ff4949"
                    @click="changeShow(scope.row)">
                </el-switch>
            </template>
        </el-table-column>
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button link type="primary" size="large" @click="openStoreDialog(scope.row)">编辑</el-button>
            </template>
        </el-table-column>
    </el-table>
    <div class="pagination-container" v-if="pageInfo.total > 0">
        <el-pagination
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="pageInfo.current_page"
            :page-sizes="[10, 20, 30, 50, 100]"
            :page-size="pageInfo.per_page"
            layout="total, sizes, prev, pager, next, jumper"
            :total="pageInfo.total">
        </el-pagination>
    </div>

    <el-dialog
        width="700" center :close-on-click-modal="false" :close-on-press-escape="false"
        v-model="storeDialogVisible"
        :title="storeDialogTitle">
        <el-form :model="submitForm" ref="submitFormRef" :rules="submitFormRules" label-width="auto">
            <el-form-item label="名称" prop="name">
                <el-input v-model="submitForm.name" />
            </el-form-item>
            <el-form-item label="排序" prop="sort">
                <el-input v-model="submitForm.sort" />
            </el-form-item>
            <el-form-item label="是否显示" prop="is_show">
                <el-switch v-model="submitForm.is_show" :active-value="1" :inactive-value="0" />
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closeStoreDialog()">取消</el-button>
                <el-button type="primary" :loading="submitLoading" @click="onSubmit()">提交</el-button>
            </div>
        </template>
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
</style>
