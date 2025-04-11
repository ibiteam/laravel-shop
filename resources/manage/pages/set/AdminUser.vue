<script setup>
import { Plus, Search } from '@element-plus/icons-vue';
import Page from '@/components/common/Pagination.vue'
import { adminUserIndex, adminUserStore, adminUserChangeStatus, adminUserRoles } from '@/api/set.js';
import { ref, reactive, getCurrentInstance, onMounted, nextTick } from 'vue';
import { useRouter } from 'vue-router';

const cns = getCurrentInstance().appContext.config.globalProperties;
const router = useRouter();

const searchForm = reactive({
    user_name: '',
    role_id: '',
    status: '1',
    page: 1,
    number: 10
});
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});
const tableData = ref([]);
const rolesData = ref([]);
const loading = ref(false);
const checkAll = ref(false);
const isIndeterminate = ref(false);
const storeDialogVisible = ref(false);
const storeDialogTitle = ref('');
const submitFormRef = ref(null);
const submitLoading = ref(false);
const submitForm = reactive({
    id: 0,
    user_name: '',
    password: '',
    confirm_password: '',
    phone: '',
    job_no: '',
    role_ids: [],
    status: 1
});

const validatorPassword = (rule, value, callback) => {
    const fag1 = /^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z0-9\W_!@#$%^&*`~()-+=]+$)(?![a-z0-9]+$)(?![a-z\W_!@#$%^&*`~()-+=]+$)(?![0-9\W_!@#$%^&*`~()-+=]+$)[a-zA-Z0-9\W_!@#$%^&*`~()-+=]/;

    if (!submitForm.id) {
        if (!value) {
            callback(new Error('请输入登录密码'));
            return false;
        }
        if (value.length < 6) {
            callback(new Error('密码不能小于6位'));
            return false;
        }
        if (submitForm.confirm_password && value !== submitForm.confirm_password) {
            callback(new Error('两次密码不同'));
            return false;
        }
        if (!fag1.test(value)) {
            callback(new Error('密码必须包含大写字母，小写字母，数字，特殊字符`@#$%^&*`~()-+=`中的任意三种'));
            return false;
        }
    }
    if (fag1.test(value) && value.length >= 6 && value === submitForm.confirm_password) {
        if (submitFormRef.value) {
            submitFormRef.value.clearValidate(['password', 'confirm_password']);
        }
    }
    callback();
};
const validatorConfirmPassword = (rule, value, callback) => {
    if (!submitForm.id) {
        if (!value) {
            callback(new Error('请输入确认密码'));
            return false;
        }
        if (!submitForm.password) {
            callback(new Error('请先输入登录密码'));
            return false;
        }
        if (value !== submitForm.password) {
            callback(new Error('两次密码不同'));
            return false;
        }
    }
    callback();
};
const validatorPhone = (rule, value, callback) => {
    if (!value) {
        callback(new Error('请输入手机号码'));
        return;
    }
    const isPhone = /^(13|14|15|17|18|16|19)\d{9}$/;
    if (!isPhone.test(value)) {
        callback(new Error('请输入正确的手机号码格式'));
        return;
    }
    callback();
};

const submitFormRules = reactive({
    user_name: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
    phone: [{ required: true, validator: validatorPhone, trigger: 'blur' }],
    password: [{ validator: validatorPassword, trigger: 'blur' }],
    confirm_password: [{ validator: validatorConfirmPassword, trigger: 'blur' }]
});

const openStoreDialog = (row = {}) => {
    storeDialogTitle.value = row.id > 0 ? '编辑' : '添加';
    if (row.id) {
        submitForm.id = row.id;
        submitForm.user_name = row.user_name;
        submitForm.phone = row.phone;
        submitForm.job_no = row.job_no;
        submitForm.role_ids = row.role_ids;
        submitForm.status = row.status;
    } else {
        submitForm.id = 0;
        submitForm.user_name = '';
        submitForm.password = '';
        submitForm.confirm_password = '';
        submitForm.phone = '';
        submitForm.job_no = '';
        submitForm.role_ids = [];
        submitForm.status = 1;
    }

    let checkedCount = submitForm.role_ids.length;
    checkAll.value = checkedCount === rolesData.value.length;
    isIndeterminate.value = checkedCount > 0 && checkedCount < rolesData.value.length;

    storeDialogVisible.value = true;
    nextTick(() => {
        submitFormRef.value.clearValidate();
    });
};
const closeStoreDialog = () => {
    storeDialogTitle.value = '';
    submitForm.id = 0;
    submitForm.user_name = '';
    submitForm.password = '';
    submitForm.confirm_password = '';
    submitForm.phone = '';
    submitForm.job_no = '';
    submitForm.role_ids = [];
    submitForm.status = 1;
    storeDialogVisible.value = false;
};

const handleCheckAllChange = (value) => {
    submitForm.role_ids = value ? Array.from(rolesData.value, role => role.value) : [];
    isIndeterminate.value = false;
};
const handleCheckedRolesChange = (value) => {
    let checkedCount = value.length;
    checkAll.value = checkedCount === rolesData.value.length;
    isIndeterminate.value = checkedCount > 0 && checkedCount < rolesData.value.length;
};

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            adminUserStore(submitForm).then(res => {
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

const changeStatus = (row) => {
    adminUserChangeStatus({
        id: row.id,
        status: row.status
    }).then(res => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
        }
    });
};

// 跳转操作日志
const openAdminOperationLog = (admin_user_name) => {
    router.push({ name: 'manage.admin_operation_log.index', query: { admin_user_name: admin_user_name } });
};

/* 获取角色 */
const getRoles = () => {
    adminUserRoles().then(res => {
        if (cns.$successCode(res.code)) {
            rolesData.value = res.data;
        }
    }).catch(() => {
    });
};

const getData = (page = 1) => {
    loading.value = true;
    searchForm.page = page;
    adminUserIndex(searchForm).then(res => {
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
    getRoles();
});
</script>

<template>
    <div class="common-wrap">
        <el-header style="padding-top: 10px;">
            <el-form :inline="true" :model="searchForm" class="search-form">
                <el-form-item label="用户名" prop="user_name">
                    <el-input v-model="searchForm.user_name" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item label="所属角色">
                    <el-select v-model="searchForm.role_id" clearable filterable placeholder="请选择">
                        <el-option v-for="item in rolesData"
                                   :key="item.value" :label="item.label" :value="item.value">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="是否启用">
                    <el-select v-model="searchForm.status" placeholder="请选择">
                        <el-option label="全部" value="-1"></el-option>
                        <el-option label="启用" value="1"></el-option>
                        <el-option label="禁用" value="0"></el-option>
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
            <el-table-column label="用户名" prop="user_name"></el-table-column>
            <el-table-column label="所属角色" prop="role_name"></el-table-column>
            <el-table-column label="工号" prop="job_no"></el-table-column>
            <el-table-column label="是否启用" prop="status">
                <template #default="scope">
                    <el-switch
                        v-model="scope.row.status"
                        :active-value="1" :inactive-value="0"
                        active-color="#13ce66" inactive-color="#ff4949"
                        @click="changeStatus(scope.row)">
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="最新登录时间" prop="latest_login_time"></el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="操作">
                <template #default="scope">
                    <el-button link type="primary" size="large" @click="openStoreDialog(scope.row)">编辑</el-button>
                    <el-button link type="primary" size="large" @click="openAdminOperationLog(scope.row.user_name)">操作日志
                    </el-button>
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
                    <el-form-item label="用户名" prop="user_name">
                        <el-input v-model="submitForm.user_name" />
                    </el-form-item>
                    <el-form-item label="登录密码" prop="password">
                        <el-input v-model="submitForm.password" show-password></el-input>
                    </el-form-item>
                    <el-form-item label="确认密码" prop="confirm_password">
                        <el-input v-model="submitForm.confirm_password" show-password></el-input>
                    </el-form-item>
                    <el-form-item label="手机号" prop="phone">
                        <el-input v-model="submitForm.phone" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="工号" prop="job_no">
                        <el-input v-model="submitForm.job_no"></el-input>
                    </el-form-item>
                    <el-form-item label="所属角色" prop="role_ids">
                        <div class="role-checkbox-container">
                            <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll"
                                         @change="handleCheckAllChange">全选
                            </el-checkbox>
                            <el-checkbox-group v-model="submitForm.role_ids" @change="handleCheckedRolesChange">
                                <el-checkbox v-for="role in rolesData" :label="role.value" :key="role.label">
                                    {{ role.label }}
                                </el-checkbox>
                            </el-checkbox-group>
                        </div>
                    </el-form-item>
                    <el-form-item label="是否启用" prop="status">
                        <el-switch v-model="submitForm.status" :active-value="1" :inactive-value="0" />
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

.role-checkbox-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;

    .el-checkbox-group {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
    }

    .el-checkbox {
        margin-right: 10px;
        margin-bottom: 5px;
    }
}
</style>
