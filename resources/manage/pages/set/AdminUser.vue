<template>
    <search-form :model="query">
        <el-form-item label="用户名" prop="user_name">
            <el-input v-model="query.user_name" clearable placeholder="请输入" @keyup.enter="getData()" />
        </el-form-item>
        <el-form-item label="所属角色">
            <el-select v-model="query.role_id" clearable filterable placeholder="请选择">
                <el-option v-for="item in rolesData"
                           :key="item.value" :label="item.label" :value="item.value">
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="是否启用">
            <el-select v-model="query.status" placeholder="请选择">
                <el-option label="全部" value="-1"></el-option>
                <el-option label="启用" value="1"></el-option>
                <el-option label="禁用" value="0"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button type="danger" @click="openStoreDialog()">添加</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handlePageChange"
    >
        <el-table-column label="ID" prop="id"></el-table-column>
        <el-table-column label="用户名" prop="user_name"></el-table-column>
        <el-table-column label="所属角色" prop="role_name"></el-table-column>
        <el-table-column label="工号" prop="job_no"></el-table-column>
        <switch-column label="是否启用" prop="status" url="admin_user/change_field"></switch-column>
        <el-table-column label="最新登录时间" prop="latest_login_time"></el-table-column>
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="{row}">
                <el-button link type="primary" size="large" @click="openStoreDialog(row)">编辑</el-button>
                <el-button link type="primary" size="large" @click="openAdminOperationLog(row.user_name)">操作日志
                </el-button>
            </template>
        </el-table-column>
    </page-table>
    <form-dialog
        v-model:visible="storeDialogVisible"
        :model-value="submitForm"
        :rules="submitFormRules"
        :title="storeDialogTitle"
        url="admin_user/update"
        @submitSuccess="closeStoreDialog()"
    >
        <el-form-item label="用户名" prop="user_name">
            <el-input v-model="submitForm.user_name" />
        </el-form-item>
        <el-form-item label="登录密码" prop="password">
            <el-input v-model="submitForm.password" show-password></el-input>
        </el-form-item>
        <el-form-item label="确认密码" prop="password_confirmation">
            <el-input v-model="submitForm.password_confirmation" show-password></el-input>
        </el-form-item>
        <el-form-item label="手机号" prop="phone">
            <el-input v-model="submitForm.phone" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="工号" prop="job_no">
            <el-input v-model="submitForm.job_no"></el-input>
        </el-form-item>
        <checkbox-group :options="rolesData"  v-model="submitForm.role_ids" label="所属角色" prop="role_ids" />
        <el-form-item label="是否启用" prop="status">
            <el-switch v-model="submitForm.status" :active-value="1" :inactive-value="0" />
        </el-form-item>
    </form-dialog>
</template>
<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import Http from '@/utils/http';
import { isTelPhone } from '@/utils/public'
import { isSuccess } from '@/utils/constants'
import PageTable from '@/components/common/PageTable.vue';
import SearchForm from '@/components/common/SearchForm.vue';
import SwitchColumn from '@/components/common/SwitchColumn.vue';
import CheckboxGroup from '@/components/common/CheckboxGroup.vue';
import FormDialog from '@/components/common/FormDialog.vue';
const router = useRouter();

const query = reactive({
    user_name: '',
    role_id: '',
    status: '1'
});
const defaultPage = {
    page: 1,
    per_page: 10
};
const pagination = reactive({ ...defaultPage });
const tableData = ref([]);
const rolesData = ref<{ label: string; value: number | string }[]>([]);
const loading = ref(false);
const storeDialogVisible = ref(false);
const storeDialogTitle = ref('');
const submitFormRef = ref(null);
const defaultSubmitForm = {
    id: 0,
    user_name: '',
    password: '',
    password_confirmation: '',
    phone: '',
    job_no: '',
    role_ids: [],
    status: 1
};
const submitForm = reactive({ ...defaultSubmitForm });

const validatorPassword = (rule:any, value:any, callback:any) => {
    const password_rule = /^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z0-9\W_!@#$%^&*`~()-+=]+$)(?![a-z0-9]+$)(?![a-z\W_!@#$%^&*`~()-+=]+$)(?![0-9\W_!@#$%^&*`~()-+=]+$)[a-zA-Z0-9\W_!@#$%^&*`~()-+=]/;

    if (!submitForm.id) {
        if (!value) {
            callback(new Error('请输入登录密码'));
            return false;
        }
        if (value.length < 8 || value.length > 20) {
            callback(new Error('密码长度需8-20位之间'));
            return false;
        }
        if (submitForm.password_confirmation && value !== submitForm.password_confirmation) {
            callback(new Error('两次密码不同'));
            return false;
        }
        if (!password_rule.test(value)) {
            callback(new Error('密码必须包含大写字母，小写字母，数字，特殊字符`@#$%^&*`~()-+=`中的任意三种'));
            return false;
        }
    }
    if (password_rule.test(value) && value.length >= 6 && value === submitForm.password_confirmation) {
        if (submitFormRef.value) {
            submitFormRef.value.clearValidate(['password', 'password_confirmation']);
        }
    }
    callback();
};
const validatorConfirmPassword = (rule:any, value:any, callback:any) => {
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
const validatorPhone = (rule:any, value:any, callback:any) => {
    if (!value) {
        callback(new Error('请输入手机号码'));
        return;
    }
    if (!isTelPhone(value)) {
        callback(new Error('请输入正确的手机号码格式'));
        return;
    }
    callback();
};

const submitFormRules = reactive({
    user_name: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
    phone: [{ required: true, validator: validatorPhone, trigger: 'blur' }],
    password: [{ validator: validatorPassword, trigger: 'blur' }],
    password_confirmation: [{ validator: validatorConfirmPassword, trigger: 'blur' }]
});

const openStoreDialog = (row = {}) => {
    storeDialogTitle.value = row.id > 0 ? '编辑管理员' : '添加管理员';
    if (row.id) {
        submitForm.id = row.id;
        submitForm.user_name = row.user_name;
        submitForm.phone = row.phone;
        submitForm.job_no = row.job_no;
        submitForm.role_ids = row.role_ids;
        submitForm.status = row.status;
    } else {
        Object.assign(submitForm, defaultSubmitForm);
    }
    storeDialogVisible.value = true;
};
const closeStoreDialog = () => {
    storeDialogTitle.value = '';
    Object.assign(submitForm, defaultSubmitForm);
    storeDialogVisible.value = false;
    getData()
};

// 跳转操作日志
const openAdminOperationLog = (admin_user_name:string) => {
    router.push({ name: 'manage.admin_operation_log.index', query: { admin_user_name: admin_user_name } });
};

/* 获取角色 */
const getRoles = () => {
    Http.doGet('admin_user/roles').then((res: any) => {
        if (isSuccess(res.code)) {
            rolesData.value = res.data;
        }
    }).catch(() => {
    });
};

const getData = (page = defaultPage.page) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    };
    Http.doGet('admin_user', params).then((res: any) => {
        loading.value = false;
        if (isSuccess(res.code)) {
            tableData.value = res.data;
        } else {
            ElMessage.error(res.message);
        }
    }).catch(() => {
        loading.value = false;
        ElMessage.error('获取数据失败');
    });
};

// 页码改变
const handlePageChange = (page: number, per_page: number) => {
    pagination.per_page = per_page;
    getData(page);
};

onMounted(() => {
    getData();
    getRoles();
});
</script>

<style scoped lang="scss">

</style>
