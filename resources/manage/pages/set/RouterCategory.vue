<script setup>
import { Plus, Search } from '@element-plus/icons-vue';
import { routerCategoryIndex, routerCategoryInfo, routerCategoryStore, routerCategoryDestroy, routerCategoryChangeShow } from '@/api/set.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    name: ''
});

const tableData = ref([]);
const loading = ref(false);
const storeDialogVisible = ref(false);
const storeDialogTitle = ref('');
const submitFormRef = ref(null);
const submitLoading = ref(false);
const topCategories = ref([]);
const submitForm = reactive({
    id: 0,
    parent_id: 0,
    name: '',
    alias: '',
    type: 0,
    page_name: '',
    sort: 0,
    is_show: 1
});
const submitFormRules = reactive({
    parent_id: [{ required: true, message: '请选择父级分类', trigger: 'change' }],
    name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
    alias: [{ required: true, message: '请输入别名', trigger: 'blur' }],
    type: [{ required: true, message: '请选择类型', trigger: 'change' }]
});


const openStoreDialog = (categoryId = 0) => {
    storeDialogTitle.value = categoryId > 0 ? '添加' : '编辑';

    routerCategoryInfo({ id: categoryId }).then(res => {
        if (res.code === 200) {
            topCategories.value = res.data.top_categories;
            if (categoryId > 0) {
                submitForm.id = res.data.info.id;
                submitForm.parent_id = res.data.info.parent_id;
                submitForm.name = res.data.info.name;
                submitForm.alias = res.data.info.alias;
                submitForm.type = res.data.info.type;
                submitForm.page_name = res.data.info.page_name;
                submitForm.sort = res.data.info.sort;
                submitForm.is_show = res.data.info.is_show;
            }
        } else {
            cns.$message.error(res.message);
            closeStoreDialog();
        }
    }).catch(error => {
        cns.$message.error('获取信息失败');
        closeStoreDialog();
    });

    storeDialogVisible.value = true;
};

const closeStoreDialog = () => {
    storeDialogTitle.value = '';
    submitForm.id = 0;
    submitForm.parent_id = 0;
    submitForm.name = '';
    submitForm.alias = '';
    submitForm.type = 0;
    submitForm.page_name = '';
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

const handleDestroy = (categoryId) => {
    cns.$confirm('此操作将永久删除该分类, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        routerCategoryDestroy({ id: categoryId }).then(res => {
            if (res.code === 200) {
                getData();
                cns.$message.success(res.message);
            } else {
                cns.$message.error(res.message);
            }
        }).catch(error => {
            cns.$message.error('操作失败');
        });
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

const getData = () => {
    loading.value = true;
    routerCategoryIndex(searchForm).then(res => {
        loading.value = false;
        if (res.code === 200) {
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
        style="width: 100%;"
        row-key="id"
        :tree-props="{ children: 'all_children' }">
        <el-table-column label="分类名称" min-width="200">
            <template #default="scope">
                <div class="s-flex ai-ct">
                    {{ scope.row.name }}【{{ scope.row.id }}】
                </div>
            </template>
        </el-table-column>
        <el-table-column label="别名" prop="alias"></el-table-column>
        <el-table-column label="类型" prop="type">
            <template #default="scope">
                <template v-if="scope.row.type === 1">链接</template>
                <template v-if="scope.row.type === 2">页面</template>
            </template>
        </el-table-column>
        <el-table-column label="页面" prop="page_name"></el-table-column>
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
                <el-button link type="primary" size="large" @click="openStoreDialog(scope.row.id)">编辑</el-button>
                <el-button link type="danger" size="large" @click="handleDestroy(scope.row.id)">删除</el-button>
            </template>
        </el-table-column>
    </el-table>

    <el-dialog
        width="700" center :close-on-click-modal="false" :close-on-press-escape="false"
        v-model="storeDialogVisible" :title="storeDialogTitle">
        <el-form :model="submitForm" ref="submitFormRef" :rules="submitFormRules" label-width="auto">
            <el-form-item label="上级分类" prop="parent_id">
                <el-select v-model="submitForm.parent_id" placeholder="请选择上级分类">
                    <el-option v-for="item in topCategories" :label="item.label" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item label="名称" prop="name">
                <el-input v-model="submitForm.name" />
            </el-form-item>
            <el-form-item label="别名" prop="alias">
                <el-input v-model="submitForm.alias" />
            </el-form-item>
            <el-form-item label="类型" prop="type">
                <el-radio v-model="submitForm.type" label="1" v-if="submitForm.parent_id === 0">链接</el-radio>
                <el-radio v-model="submitForm.type" label="2">页面</el-radio>
            </el-form-item>
            <el-form-item label="页面名称" prop="page_name" v-if="submitForm.type === '2' && submitForm.parent_id > 0">
                <el-input v-model="submitForm.page_name" />
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

:deep(.el-table__row .cell) {
    display: flex;
    align-items: center;
}
</style>
