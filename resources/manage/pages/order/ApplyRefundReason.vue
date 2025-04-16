<script setup>
import { Plus, Search } from '@element-plus/icons-vue';
import { applyRefundReasonIndex, applyRefundReasonStore, applyRefundReasonDestroy } from '@/api/order.js';
import Page from '@/components/common/Pagination.vue'
import { ref, reactive, getCurrentInstance, onMounted, nextTick } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    type: null,
    number: 10,
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
    type: null,
    content: '',
    sort: 0
});
const submitFormRules = reactive({
    content: [{ required: true, message: '请输入原因', trigger: 'blur' }],
    type: [{ required: true, message: '请选择类型', trigger: 'change' }]
});

const openStoreDialog = (row = {}) => {
    storeDialogTitle.value = row.id > 0 ? '编辑' : '添加';
    if (row.id) {
        submitForm.id = row.id;
        submitForm.type = row.type;
        submitForm.content = row.content;
        submitForm.sort = row.sort;
    } else {
        submitForm.id = 0;
        submitForm.type = null;
        submitForm.content = '';
        submitForm.sort = 0;
    }
    storeDialogVisible.value = true;
    nextTick(() => {
        submitFormRef.value.clearValidate();
    });
};
const closeStoreDialog = () => {
    storeDialogTitle.value = '';
    submitForm.id = 0;
    submitForm.type = null;
    submitForm.content = '';
    submitForm.sort = 0;
    storeDialogVisible.value = false;
};

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            applyRefundReasonStore(submitForm).then(res => {
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

const handleDestroy = (id) => {
    cns.$confirm('此操作将永久删除, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        applyRefundReasonDestroy({ id: id }).then(res => {
            if (cns.$successCode(res.code)) {
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

const getData = (page = 1) => {
    loading.value = true;
    searchForm.page = page;
    applyRefundReasonIndex(searchForm).then(res => {
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
});
</script>
<template>
    <div>
        <el-header style="padding-top: 10px;">
            <el-form :inline="true" :model="searchForm" class="search-form">
                <el-form-item label="类型">
                    <el-select v-model="searchForm.type" clearable placeholder="请选择">
                        <el-option label="退款" value="0"></el-option>
                        <el-option label="退货退款" value="1"></el-option>
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
            <el-table-column label="原因" prop="content"></el-table-column>
            <el-table-column label="类型">
                <template #default="scope">
                    <span v-if="scope.row.type == 0">退款</span>
                    <span v-if="scope.row.type == 1">退货退款</span>
                </template>
            </el-table-column>
            <el-table-column label="排序" prop="sort"></el-table-column>
            <el-table-column label="操作">
                <template #default="scope">
                    <el-button link type="primary" size="large" @click="openStoreDialog(scope.row)">编辑</el-button>
                    <el-button link type="danger" size="large" @click="handleDestroy(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />

        <el-dialog
            width="700" center :before-close="closeStoreDialog"
            v-model="storeDialogVisible" :title="storeDialogTitle">
            <div class="s-flex jc-ct">
                <el-form :model="submitForm" ref="submitFormRef" :rules="submitFormRules" label-width="auto"
                         style="width: 480px" size="default">
                    <el-form-item label="类型" prop="type">
                        <el-select v-model="submitForm.type" clearable filterable placeholder="请选择">
                            <el-option label="退款" value="0"></el-option>
                            <el-option label="退货退款" value="1"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="原因" prop="content">
                        <el-input v-model="submitForm.content" />
                    </el-form-item>
                    <el-form-item label="排序" prop="sort">
                        <el-input v-model="submitForm.sort" />
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
