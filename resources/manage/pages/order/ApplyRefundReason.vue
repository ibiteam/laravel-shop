<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="类型">
            <el-select v-model="query.type" clearable placeholder="请选择">
                <el-option label="退款" value="0"></el-option>
                <el-option label="退货退款" value="1"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button type="danger" @click="openStoreDialog()">添加</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        :maxHeight="'700px'"
        v-loading="loading"
        @change="handlePageChange"
    >
        <el-table-column label="原因" prop="content"></el-table-column>
        <el-table-column label="类型">
            <template #default="{ row }">
                <span v-if="row.type == 0">退款</span>
                <span v-if="row.type == 1">退货退款</span>
            </template>
        </el-table-column>
        <el-table-column label="排序" prop="sort"></el-table-column>
        <el-table-column label="操作">
            <template #default="{ row }">
                <el-button link type="primary" size="large" @click="openStoreDialog(row)">编辑</el-button>
                <el-button link type="danger" size="large" @click="handleDestroy(row.id)">删除</el-button>
            </template>
        </el-table-column>
    </page-table>

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
</template>

<script setup lang="ts">
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';
import { applyRefundReasonIndex, applyRefundReasonStore, applyRefundReasonDestroy } from '@/api/order.js';
import { ref, reactive, getCurrentInstance, onMounted, nextTick } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;
const tableData = ref({});
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

const defaultQuery = reactive({
    type: null,
    number: 10,
    page: 1
});
const query = reactive({ ...defaultQuery });

const defaultPage = {
    page: 1,
    per_page: 10
};
const pagination = reactive({ ...defaultPage });
const handlePageChange = (page: number, per_page: number) => {
    pagination.per_page = per_page;
    getData(page);
};

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
    submitFormRef.value.validate((valid: any) => {
        if (valid) {
            submitLoading.value = true;
            applyRefundReasonStore(submitForm).then((res: any) => {
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

const handleDestroy = (id: number) => {
    cns.$confirm('此操作将永久删除, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        applyRefundReasonDestroy({ id: id }).then((res: any) => {
            if (cns.$successCode(res.code)) {
                getData();
                cns.$message.success(res.message);
            } else {
                cns.$message.error(res.message);
            }
        }).catch(() => {
            cns.$message.error('操作失败');
        });
    });
};

const getData = (page: number = defaultPage.page) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    };
    applyRefundReasonIndex(params).then((res: any) => {
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
    getData();
});
</script>

<style scoped lang="scss">

</style>
