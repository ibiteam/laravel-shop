<script setup lang="ts">
import { Plus, RefreshLeft, Search } from '@element-plus/icons-vue';
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { shipCompanyChangeStatus, shipCompanyEdit, shipCompanyIndex, shipCompanyUpdate } from '@/api/set';
import Page from '@/components/common/Pagination.vue'
import _ from 'lodash';

const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

const tableData = ref([]);
const loading = ref(false);

const queryParams = reactive({
    page: 1,
    number: 10,
    name: '',
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

// 重置搜索条件
const resetSearch = () => {
    queryParams.name = '';
    getData(1);
};

// 添加分页相关状态
const pageInfo = reactive({
    number: 10,
    total: 0,
    currentPage: 1,
})

// 页码改变
const handleCurrentChange = (val) => {
    getData(val);
}

// 每页条数改变
const handleSizeChange = (val) => {
    pageInfo.number = val;
    getData(1);
}

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.number = Number(meta.per_page);
    pageInfo.currentPage = meta.current_page;
}

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;
    queryParams.number = pageInfo.number;
    shipCompanyIndex(queryParams).then(res => {
        loading.value = false;
        if (res.code === 200) {
            tableData.value = res.data.list;
            // 更新分页信息
            setPageInfo(res.data.meta);
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}


// 表格修改字段
const handleStatusChange = (itemId) => {
    shipCompanyChangeStatus({ id: itemId }).then(res => {
        if (res.code === 200) {
            cns.$message.success(res.message);
            getData(pageInfo.currentPage)
        } else {
            cns.$message.error(res.message);
        }
    });
}

const detailDialogVisible = ref(false);
const detailDialogTitle = ref('');
const detailFormLoading = ref(false);
const detailFormRef = ref(null);
const detailSubmitLoading = ref(false);
const detailForm = reactive({
    id: 0,
    name: '',
    code: '',
    status: 1,
});
const detailFormRules = ref({
    name: [{ required: true, message: '请输入名称', trigger: 'blur' }, { min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }],
    code: [
        { required: true, message: '请输入别名', trigger: 'blur' },
        { min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' },
        { pattern: /^[A-Za-z]+$/, message: '别名必须是英文字符', trigger: 'blur' }
    ],
    status: [{ required: true, message: '请选择状态', trigger: 'change' }],
})

const openDetailDialog = (itemId) => {
    detailDialogVisible.value = true;
    detailDialogTitle.value = '添加快递公司'
    if (itemId > 0) {
        detailDialogTitle.value = '编辑快递公司'
        detailFormLoading.value = true;
        shipCompanyEdit({ id: itemId }).then(res => {
            detailFormLoading.value = false;
            if (res.code === 200) {
                detailForm.id = res.data.id
                detailForm.name = res.data.name
                detailForm.code = res.data.code
                detailForm.status = res.data.status ? 1 : 0
            } else {
                cns.$message.error(res.message);
                closeDetailDialog();
            }
        }).catch(error => {
            detailFormLoading.value = false;
            cns.$message.error('操作失败');
            closeDetailDialog();
        });
    }
};

const closeDetailDialog = () => {
    detailDialogVisible.value = false;
    detailDialogTitle.value = '';
    detailFormLoading.value = false;
    detailSubmitLoading.value = false

    detailForm.id = 0
    detailForm.name = ''
    detailForm.code = ''
    detailForm.status = 1
}

const submitDetailForm = _.throttle(() => {
    detailFormRef.value.validate((valid) => {
        if (valid) {
            detailSubmitLoading.value = true;
            shipCompanyUpdate(detailForm).then(res => {
                detailSubmitLoading.value = false;
                if (res.code === 200) {
                    closeDetailDialog();
                    getData(pageInfo.currentPage);
                } else {
                    cns.$message.error(res.message);
                }
            });
        }
    });
}, 1000);

onMounted(() => {
    getData()
})

</script>

<template>
    <el-header style="padding: 10px 0;height: auto;">
        <!-- 添加搜索表单 -->
        <el-form :inline="true" :model="queryParams" class="search-form">
            <el-form-item label="名称" prop="name">
                <el-input
                    v-model="queryParams.name"
                    placeholder="请输入快递公司名称"
                    clearable
                    @keyup.enter="handleSearch"
                />
            </el-form-item>
            <el-form-item>
                <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                <el-button :icon="RefreshLeft" @click="resetSearch">重置</el-button>
                <el-button :icon="Plus" type="warning" @click="openDetailDialog(0)">添加</el-button>
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
        <el-table-column label="名称" prop="name"></el-table-column>
        <el-table-column label="别名" prop="code"></el-table-column>
        <el-table-column label="状态" prop="is_enabled">
            <template #default="scope">
                <el-switch
                    v-model="scope.row.status"
                    active-color="#13ce66"
                    inactive-color="#ff4949"
                    :active-value="true"
                    :inactive-value="false"
                    @change="handleStatusChange(scope.row.id)"
                >
                </el-switch>
            </template>
        </el-table-column>
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button link type="primary" size="large" @click="openDetailDialog(scope.row.id)">编辑</el-button>
            </template>
        </el-table-column>
    </el-table>
    <!-- 添加分页组件 -->
    <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />

    <el-dialog
        v-model="detailDialogVisible"
        :title="detailDialogTitle"
        width="700"
        center
        :before-close="closeDetailDialog">
        <div v-loading="detailFormLoading" class="s-flex jc-ct">
            <el-form :model="detailForm" ref="detailFormRef" :rules="detailFormRules" label-width="auto"
                     style="width: 480px" size="default">
                <el-form-item label="名称" prop="name">
                    <el-input v-model="detailForm.name" />
                </el-form-item>
                <el-form-item label="别名" prop="code">
                    <el-input v-model="detailForm.code" />
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-switch v-model="detailForm.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closeDetailDialog()">取消</el-button>
                <el-button type="primary" :loading="detailSubmitLoading" @click="submitDetailForm()">提交</el-button>
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

.payment-method-icon {
    width: 50px;
    height: 50px;
    display: block;
}

:deep(.el-table__row .cell) {
    display: flex;
    align-items: center;
}
.logo-uploader .el-upload {
    border: 1px dashed var(--el-border-color);
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: var(--el-transition-duration-fast);
}

.logo-uploader .el-upload:hover {
    border-color: var(--el-color-primary);
}

.el-icon.logo-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 50px;
    height: 50px;
    text-align: center;
}
</style>
