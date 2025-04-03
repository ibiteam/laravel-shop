<script setup lang="ts">
import { Plus, RefreshLeft, Search } from '@element-plus/icons-vue';
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { paymentMethodChangeField, paymentMethodEdit, paymentMethodIndex, paymentMethodUpdate } from '@/api/set';
import { fileUpload } from '@/api/common';
import Page from '@/components/common/Pagination.vue'
import _ from 'lodash';
import { isSuccess } from '@/utils/constants';

const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

const tableData = ref([]);
const loading = ref(false);

const enabledOptions = [
    { value: 1, label: '启用' },
    { value: 0, label: '禁用' }
];

const queryParams = reactive({
    page: 1,
    number: 10,
    name: '',      // 商品名称搜索
    is_enabled: null
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

// 重置搜索条件
const resetSearch = () => {
    queryParams.name = '';
    queryParams.is_enabled = null;
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
    paymentMethodIndex(queryParams).then(res => {
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
const handleFieldChange = (itemId,field) => {
    paymentMethodChangeField({ id: itemId, field: field }).then(res => {
        if (isSuccess(res.code)) {
            cns.$message.success(res.message);
            getData(pageInfo.currentPage)
        } else {
            cns.$message.error(res.message);
        }
    });
}

const detailDialogVisible = ref(false);
const detailFormLoading = ref(false);
const detailFormRef = ref(null);
const detailSubmitLoading = ref(false);
const detailForm = reactive({
    id: '',
    name: '',
    alias: '',
    is_enabled: true,
    icon: '',
    description: '',
    limit: '',
    is_recommend: false,
    sort: 0,
    config: {}
});
const detailFormRules = ref({
    name: [{ required: true, message: '请输入名称', trigger: 'blur' }, { min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }],
    is_enabled: [{ required: true, message: '请选择状态', trigger: 'change' }],
    is_recommend: [{ required: true, message: '请选择是否推荐', trigger: 'change' }],
    icon: [{ required: true, message: '请上传图标', trigger: 'blur' }],
    description: [{ required: true, message: '请输入描述', trigger: 'blur' }, { min: 2, max: 255, message: '长度在 2 到 255 个字符', trigger: 'blur' }],
    limit: [{ required: true, message: '请输入限额', trigger: 'blur' },],
    sort: [{ required: true, message: '请输入排序', trigger: 'blur' },],
})

const openDetailDialog = (itemId) => {
    detailDialogVisible.value = true;
    detailFormLoading.value = true;
    paymentMethodEdit({ id: itemId }).then(res => {
        detailFormLoading.value = false;
        if (isSuccess(res.code)) {
            detailForm.id = res.data.id
            detailForm.name = res.data.name
            detailForm.alias = res.data.alias
            detailForm.is_enabled = res.data.is_enabled
            detailForm.icon = res.data.icon
            detailForm.description = res.data.description
            detailForm.limit = res.data.limit
            detailForm.is_recommend = res.data.is_recommend
            detailForm.sort = res.data.sort
            detailForm.config = res.data.config
        } else {
            cns.$message.error(res.message);
            closeDetailDialog();
        }
    }).catch(error => {
        detailFormLoading.value = false;
        cns.$message.error('操作失败');
        closeDetailDialog();
    });
};

const closeDetailDialog = () => {
    detailDialogVisible.value = false;
    detailFormLoading.value = false;

    detailForm.id = ''
    detailForm.name = ''
    detailForm.alias = ''
    detailForm.is_enabled = true
    detailForm.icon = ''
    detailForm.description = ''
    detailForm.limit = ''
    detailForm.is_recommend = false
    detailForm.sort = 0
    detailForm.config = {}
}

const uploadFile = async (request) => {
    try {
        const res = await fileUpload({ file: request.file });
        if (isSuccess(res.code)) {
            detailForm.icon = res.data.url;
        } else {
            cns.$message.error(res.message);
        }
    } catch (error) {
        console.error('Failed:', error);
    }
};
const submitDetailForm = _.throttle(() => {
    detailFormRef.value.validate((valid) => {
        if (valid) {
            detailForm.is_enabled = detailForm.is_enabled ? 1 : 0
            detailForm.is_recommend = detailForm.is_recommend ? 1 : 0
            detailSubmitLoading.value = true;
            paymentMethodUpdate(detailForm).then(res => {
                detailSubmitLoading.value = false;
                if (isSuccess(res.code)) {
                    closeDetailDialog();
                    getData();
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
                    placeholder="请输入支付方式名称"
                    clearable
                    @keyup.enter="handleSearch"
                />
            </el-form-item>
            <el-form-item label="状态" prop="is_enabled">
                <el-select v-model="queryParams.is_enabled" placeholder="请选择支付方式状态" clearable>
                    <el-option v-for="item in enabledOptions" :key="item.value" :label="item.label" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                <el-button :icon="RefreshLeft" @click="resetSearch">重置</el-button>
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
        <el-table-column label="支付码" prop="alias"></el-table-column>
        <el-table-column label="ICON">
            <template #default="scope">
                <el-image class="payment-method-icon" :src="scope.row.icon"></el-image>
            </template>
        </el-table-column>
        <el-table-column label="描述" prop="description" width="350px" show-overflow-tooltip></el-table-column>
        <el-table-column label="排序" prop="sort"></el-table-column>
        <el-table-column label="状态" prop="is_enabled">
            <template #default="scope">
                <el-switch
                    v-model="scope.row.is_enabled"
                    active-color="#13ce66"
                    inactive-color="#ff4949"
                    :active-value="true"
                    :inactive-value="false"
                    @change="handleFieldChange(scope.row.id, 'is_enabled')"
                >
                </el-switch>
            </template>
        </el-table-column>
        <el-table-column label="支付限制" prop="limit">
            <template #default="scope">
                <el-button disabled type="success" v-if="scope.row.limit < 0">不限制</el-button>
                <el-button disabled type="danger" v-else>{{scope.row.limit}} 元</el-button>
            </template>
        </el-table-column>
        <el-table-column label="是否推荐" prop="is_recommend">
            <template #default="scope">
                <el-switch
                    v-model="scope.row.is_recommend"
                    active-color="#13ce66"
                    inactive-color="#ff4949"
                    :active-value="true"
                    :inactive-value="false"
                    @change="handleFieldChange(scope.row.id, 'is_recommend')"
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
        title="编辑支付方式"
        width="700"
        center
        :before-close="closeDetailDialog">
        <div v-loading="detailFormLoading" class="s-flex jc-ct">
            <el-form :model="detailForm" ref="detailFormRef" :rules="detailFormRules" label-width="auto"
                     style="width: 480px" size="default">
                <el-form-item label="名称" prop="name">
                    <el-input v-model="detailForm.name" />
                </el-form-item>
                <el-form-item label="状态" prop="is_enabled">
                    <el-switch v-model="detailForm.is_enabled" :active-value="true" :inactive-value="false" />
                </el-form-item>
                <el-form-item label="是否推荐" prop="is_recommend">
                    <el-switch v-model="detailForm.is_recommend" :active-value="true" :inactive-value="false" />
                </el-form-item>
                <el-form-item label="描述" prop="description">
                    <el-input v-model="detailForm.description" type="textarea" rows="2" />
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <el-input v-model.number="detailForm.sort" />
                </el-form-item>
                <el-form-item label="支付限制" prop="limit">
                    <el-input v-model.number="detailForm.limit" />
                </el-form-item>
                <el-form-item label="图标" prop="icon">
                    <el-upload
                        class="logo-uploader"
                        accept="image/jpeg,image/jpg,image/png"
                        action=""
                        :show-file-list="false"
                        :http-request="(request) => uploadFile(request)"
                        :with-credentials="true"
                    >
                        <img v-if="detailForm.icon" :src="detailForm.icon" class="payment-method-icon" />
                        <el-icon v-else class="logo-uploader-icon">
                            <Plus />
                        </el-icon>
                    </el-upload>
                </el-form-item>
                <template v-if="detailForm.alias === 'wechat'">
                    <el-form-item label="商户号" prop="config.mic_id" :rules="[{required: true, message: '商户号不能为空', trigger: 'blur'}]">
                        <el-input v-model="detailForm.config.mic_id" />
                    </el-form-item>
                    <el-form-item label="商户密钥" prop="config.secret_key" :rules="[{required: true, message: '商户密钥不能为空', trigger: 'blur'}]">
                        <el-input v-model="detailForm.config.secret_key" />
                    </el-form-item>
                    <el-form-item label="商户密钥（V2）" prop="config.v2_secret_key" :rules="[{required: true, message: '商户密钥（V2）不能为空', trigger: 'blur'}]">
                        <el-input v-model="detailForm.config.v2_secret_key" />
                    </el-form-item>
                    <el-form-item label="商户私钥" prop="config.private_key" :rules="[{required: true, message: '商户私钥不能为空', trigger: 'blur'}]">
                        <el-input v-model="detailForm.config.private_key" type="textarea" rows="6" />
                    </el-form-item>
                    <el-form-item label="商户证书" prop="config.certificate" :rules="[{required: true, message: '商户证书不能为空', trigger: 'blur'}]">
                        <el-input v-model="detailForm.config.certificate" type="textarea" rows="6" />
                    </el-form-item>
                </template>
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
