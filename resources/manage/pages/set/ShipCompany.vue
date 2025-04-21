<template>
    <search-form :model="query" :label-width="80">
        <el-form-item label="名称" prop="name">
            <el-input
                v-model="query.name"
                placeholder="请输入快递公司名称"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button @click="resetSearch">重置</el-button>
            <el-button type="warning" @click="openDetailDialog(0)">添加</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handleChange"
    >
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
    </page-table>
    <!-- 详情弹窗 -->
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
<script setup lang="ts">
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';
import Http from '@/utils/http';

const cns = getCurrentInstance().appContext.config.globalProperties

/* 定义表格数据 */
const tableData = ref([]);
const loading = ref(false);
/* 定义搜索参数 */
const defaultQuery = reactive({
    name: '',
});
const query = reactive({...defaultQuery})
/* 定义默认分页参数 */
const defaultPage = {
    page: 1,
    per_page: 10,
}
const pagination = reactive({...defaultPage})
/* 重置搜索条件 */
const resetSearch = () => {
    Object.assign(query, defaultQuery)
    Object.assign(pagination, defaultPage)
    getData()
}

const getData = (page = 1) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    }
    Http.doGet('set/ship_company', params).then(res => {
        loading.value = false;
        if (res.code === 200) {
            tableData.value = res.data
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}
/* 点击分页触发方法 */
const handleChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}

// 表格修改字段
const handleStatusChange = (itemId) => {
    Http.doPost('set/ship_company/change_status', { id: itemId }).then(res => {
        if (res.code === 200) {
            cns.$message.success(res.message);
            getData(pagination.page)
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
        Http.doGet('set/ship_company/edit', { id: itemId }).then(res => {
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

const submitDetailForm = () => {
    detailFormRef.value.validate((valid) => {
        if (valid) {
            detailSubmitLoading.value = true;
            Http.doPost('set/ship_company/update', detailForm).then(res => {
                detailSubmitLoading.value = false;
                if (res.code === 200) {
                    closeDetailDialog();
                    getData(pagination.page);
                } else {
                    cns.$message.error(res.message);
                }
            });
        }
    });
};

onMounted(() => {
    getData()
})

</script>
<style scoped lang="scss">
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
