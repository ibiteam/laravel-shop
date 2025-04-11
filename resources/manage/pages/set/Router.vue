<script setup>
import { Plus, Search } from '@element-plus/icons-vue';
import Page from '@/components/common/Pagination.vue'
import { routerIndex, routerStore, routerChangeShow, routerCategories } from '@/api/set.js';
import { ref, reactive, getCurrentInstance, onMounted, nextTick } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    name: '',
    alias: '',
    router_category_id: '',
    is_show: '-1',
    number: 10,
    page: 1
});
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});
const tableData = ref([]);
const categoriesData = ref([]);
const loading = ref(false);
const storeDialogVisible = ref(false);
const storeDialogTitle = ref('');
const submitFormRef = ref(null);
const submitLoading = ref(false);
const submitForm = reactive({
    id: 0,
    router_category_id: '',
    name: '',
    alias: '',
    h5_url: '',
    params: '',
    sort: 0,
    is_show: 1,
});
const submitFormRules = reactive({
    router_category_id: [{ required: true, message: '请选择所属分类', trigger: 'blur' }],
    name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
    alias: [{ required: true, message: '请输入别名', trigger: 'blur' }],
    h5_url: [{ required: true, message: '请输入H5地址', trigger: 'blur' }],
});


const openStoreDialog = (row = {}) => {
    storeDialogTitle.value = row.id > 0 ? '编辑' : '添加';
    if (row.id) {
        submitForm.id = row.id;
        submitForm.router_category_id = row.router_category_id;
        submitForm.name = row.name;
        submitForm.alias = row.alias;
        submitForm.h5_url = row.h5_url;
        submitForm.params = row.params;
        submitForm.sort = row.sort;
        submitForm.is_show = row.is_show;
    } else {
        submitForm.id = 0;
        submitForm.router_category_id = '';
        submitForm.name = '';
        submitForm.alias = '';
        submitForm.h5_url = '';
        submitForm.params = '';
        submitForm.sort = 0;
        submitForm.is_show = 1;
    }
    storeDialogVisible.value = true;
    nextTick(() => {
        submitFormRef.value.clearValidate();
    });
};

const closeStoreDialog = () => {
    storeDialogTitle.value = '';
    submitForm.id = 0;
    submitForm.router_category_id = '';
    submitForm.name = '';
    submitForm.alias = '';
    submitForm.h5_url = '';
    submitForm.params = '';
    submitForm.sort = 0;
    submitForm.is_show = 1;
    storeDialogVisible.value = false;
};

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            routerStore(submitForm).then(res => {
                submitLoading.value = false;
                if (cns.$successCode(res.code)) {
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
    routerChangeShow({
        id: row.id,
        is_show: row.is_show
    }).then(res => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
        }
    });
};

/* 获取分类 */
const getCategories = () => {
    routerCategories().then(res => {
        if (cns.$successCode(res.code)) {
            categoriesData.value = res.data;
        }
    }).catch(() => {
    });
};

const getData = (page = 1) => {
    loading.value = true;
    searchForm.page = page;
    routerIndex(searchForm).then(res => {
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
    getData();
    getCategories();
});
</script>
<template>
    <div class="common-wrap">
        <el-header style="padding-top: 10px;">
            <el-form :inline="true" :model="searchForm" class="search-form">
                <el-form-item label="名称" prop="name">
                    <el-input v-model="searchForm.name" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item label="别名" prop="alias">
                    <el-input v-model="searchForm.alias" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item label="所属分类">
                    <el-select v-model="searchForm.router_category_id" clearable filterable placeholder="请选择">
                        <el-option v-for="item in categoriesData"
                                   :key="item.value" :label="item.label" :value="item.value">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="是否显示">
                    <el-select v-model="searchForm.is_show" placeholder="请选择">
                        <el-option label="全部" value="-1"></el-option>
                        <el-option label="显示" value="1"></el-option>
                        <el-option label="隐藏" value="0"></el-option>
                    </el-select>
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
            <el-table-column label="所属分类" prop="category_name"></el-table-column>
            <el-table-column label="别名" prop="alias"></el-table-column>
            <el-table-column label="H5地址" prop="h5_url"></el-table-column>
            <el-table-column label="排序" prop="sort"></el-table-column>
            <el-table-column label="额外参数" prop="params"></el-table-column>
            <el-table-column label="是否显示" prop="is_show">
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
            <el-table-column label="操作">
                <template #default="scope">
                    <el-button link type="primary" size="large" @click="openStoreDialog(scope.row)">编辑</el-button>
                </template>
            </el-table-column>
        </el-table>
        <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />
        <el-dialog
            width="700" center :before-close="closeStoreDialog"
            v-model="storeDialogVisible" :title="storeDialogTitle">
            <div class="s-flex jc-ct">
                <el-form :model="submitForm" ref="submitFormRef" :rules="submitFormRules" label-width="auto" style="width: 480px" size="default">
                    <el-form-item label="所属分类" prop="router_category_id">
                        <el-select v-model="submitForm.router_category_id" clearable filterable placeholder="请选择">
                            <el-option v-for="item in categoriesData"
                                       :key="item.value" :label="item.label" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="名称" prop="name">
                        <el-input v-model="submitForm.name" />
                    </el-form-item>
                    <el-form-item label="别名" prop="alias">
                        <el-input v-model="submitForm.alias" :disabled = 'submitForm.id > 0'/>
                    </el-form-item>
                    <el-form-item label="H5地址" prop="h5_url">
                        <el-input v-model="submitForm.h5_url" />
                    </el-form-item>
                    <el-form-item label="额外参数" prop="params">
                        <el-input v-model="submitForm.params" placeholder="json格式" />
                    </el-form-item>
                    <el-form-item label="排序" prop="sort">
                        <el-input v-model="submitForm.sort" />
                    </el-form-item>
                    <el-form-item label="是否显示" prop="is_show">
                        <el-switch v-model="submitForm.is_show" :active-value="1" :inactive-value="0" />
                    </el-form-item>
                </el-form>
            </div>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="closeStoreDialog()">取消</el-button>
                    <el-button type="primary" :loading="submitLoading" @click="onSubmit()">提交</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
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
