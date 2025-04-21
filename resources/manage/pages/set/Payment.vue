<template>
        <search-form :model="query" :label-width="40">
            <el-form-item label="名称" prop="name">
                <el-input
                    v-model="query.name"
                    placeholder="请输入支付方式名称"
                    clearable
                    @keyup.enter="getData()"
                />
            </el-form-item>
            <el-form-item label="状态" prop="is_enabled">
                <el-select v-model="query.is_enabled" placeholder="请选择支付方式状态" clearable>
                    <el-option v-for="item in enabledOptions" :key="item.value" :label="item.label" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="getData()">搜索</el-button>
            </el-form-item>
        </search-form>
        <page-table
            :data="tableData"
            v-loading="loading"
            @change="handleChange"
        >
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="名称" prop="name">
                <template #default="{row}">
                    <el-image class="payment-method-icon" :src="row.icon" style="width: 20px;height: 20px;margin-right: 4px"></el-image>
                    <span>{{ row.name}}</span>
                </template>
            </el-table-column>
            <el-table-column label="支付码" prop="alias"></el-table-column>
            <el-table-column label="描述" prop="description" width="350px" show-overflow-tooltip></el-table-column>
            <el-table-column label="排序" prop="sort"></el-table-column>
            <el-table-column label="状态" prop="is_enabled">
                <template #default="{row}">
                    <el-switch
                        v-model="row.is_enabled"
                        :active-value="true"
                        :inactive-value="false"
                        @change="handleFieldChange(row.id, 'is_enabled','状态')"
                    >
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="支付限制" prop="limit">
                <template #default="{row}">
                    <el-tag type="success" v-if="row.limit < 0">不限制</el-tag>
                    <el-tag type="danger" v-else>{{row.limit}} 元</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="是否推荐" prop="is_recommend">
                <template #default="{row}">
                    <el-switch
                        v-model="row.is_recommend"
                        active-color="#13ce66"
                        inactive-color="#ff4949"
                        :active-value="true"
                        :inactive-value="false"
                        @change="handleFieldChange(row.id, 'is_recommend','是否推荐')"
                    >
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="更新时间" prop="updated_at"></el-table-column>
            <el-table-column label="操作">
                <template #default="{row}">
                    <el-button link type="primary" size="large" @click="openDetailDialog(row)">编辑</el-button>
                </template>
            </el-table-column>
        </page-table>

        <el-dialog
            v-model="detailDialogVisible"
            title="编辑支付方式"
            width="60%"
            center
            :before-close="closeDetailDialog">
            <div class="s-flex jc-ct">
                <el-form :model="detailForm" ref="detailFormRef" :rules="detailFormRules" label-width="auto" style="width: 100%;" size="default">
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
                        <el-form-item label="微信支付应用程序(App)APP_ID" prop="config.app_wechat_pay_app_id">
                            <div class="s-flex ai-ct" style="width: 100%;">
                                <el-input v-model="detailForm.config.app_wechat_pay_app_id" />
                                <el-popover
                                    class="box-item"
                                    content="APPID是商户移动应用唯一标识，在开放平台(移动应用)申请。此处需填写与 商户号 完成绑定的appid"
                                    placement="top-end">
                                    <template #reference>
                                        <el-icon><QuestionFilled /></el-icon>
                                    </template>
                                </el-popover>
                            </div>
                        </el-form-item>
                        <el-form-item label="商户号" prop="config.mch_id" :rules="[{required: true, message: '商户号不能为空', trigger: 'blur'}]">
                            <el-input v-model="detailForm.config.mch_id" />
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
<script setup lang="ts">
import { Plus, QuestionFilled } from '@element-plus/icons-vue';
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import { paymentMethodChangeField, paymentMethodIndex, paymentMethodUpdate } from '@/api/set';
import PageTable from '@/components/common/PageTable.vue'
import SearchForm from '@/components/common/SearchForm.vue'
import Http from '@/utils/http';

const cns = getCurrentInstance().appContext.config.globalProperties

/* 定义搜索下拉数据 */
const enabledOptions = [
    { value: 1, label: '启用' },
    { value: 0, label: '禁用' }
];
/* 定义表格数据 */
const tableData = ref([]);
const loading = ref(false);
/* 定义搜索参数 */
const defaultQuery = reactive({
    name: '',      // 商品名称搜索
    is_enabled: null
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
/* 获取分页数据 */
const getData = (page:number = defaultPage.page) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    }
    paymentMethodIndex(params).then(res => {
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
/* 表格修改字段 */
const handleFieldChange = (id:number,field:any,name:string) => {
    paymentMethodChangeField({ id: id, field: field,name:name }).then((res:any) => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
            getData(pagination.page)
        } else {
            cns.$message.error(res.message);
        }
    });
}
/* 详情弹窗 */
const detailDialogVisible = ref(false);
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

const openDetailDialog = (row:any) => {
    detailDialogVisible.value = true;
    Object.assign(detailForm, row)
};

const closeDetailDialog = () => {
    detailDialogVisible.value = false;
    detailSubmitLoading.value = false

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
        const res = await Http.doUpload('upload',{ file: request.file });
        if (cns.$successCode(res.code)) {
            detailForm.icon = res.data.url;
        } else {
            cns.$message.error(res.message);
        }
    } catch (error) {
        console.error('Failed:', error);
    }
};
const submitDetailForm = () => {
    detailFormRef.value.validate((valid:any) => {
        if (valid) {
            detailSubmitLoading.value = true;
            paymentMethodUpdate(detailForm).then((res:any) => {
                detailSubmitLoading.value = false;
                if (cns.$successCode(res.code)) {
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
