<template>
    <search-form :model="query">
        <el-form-item label="用户名" prop="user_name">
            <el-input
                v-model="query.user_name"
                placeholder="请输入用户名"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button type="danger" @click="addUser()" >添加</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        :maxHeight="'700px'"
        v-loading="loading"
        @change="handlePageChange"
    >
        <el-table-column label="用户ID" prop="id"></el-table-column>
        <el-table-column label="用户名" prop="username">
            <template #default="scope" >
                <div class="flex-user-information s_flex ai_ct">
                    <div class="header-picture">
                        <div class="imgs">
                            <img :src="scope.row.avatar" alt="">
                        </div>
                    </div>
                    <div class="header-user-names">
                        <span> {{ scope.row.user_name }}</span>
                    </div>
                </div>
            </template>
        </el-table-column>
        <el-table-column label="昵称" prop="nickname"></el-table-column>
        <el-table-column label="手机号" prop="phone"></el-table-column>
        <el-table-column label="来源" prop="source"></el-table-column>
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button type="primary" @click="modifyUser(scope.row)">编辑</el-button>
                <el-button type="success" @click="userAddress(scope.row)">收货地址</el-button>
            </template>
        </el-table-column>
    </page-table>
    <!--  添加用户  -->
    <el-dialog v-model="dialogFormVisible"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               :title="updateTitle"
               center
               width="500">
        <el-form :model="subForm" :rules="rules" ref="subFormRef">
            <el-form-item label="用户名" prop="user_name" :label-width="formLabelWidth">
                <el-input v-model="subForm.user_name" autocomplete="off" />
            </el-form-item>
            <el-form-item label="手机号" prop="phone" :label-width="formLabelWidth">
                <el-input v-model="subForm.phone" autocomplete="off" />
            </el-form-item>
            <el-form-item label="密码" :prop="subForm.id ? '' : 'password'" :label-width="formLabelWidth">
                <el-input v-model="subForm.password" type="password" autocomplete="off" />
            </el-form-item>
            <el-form-item label="确认密码" :prop="subForm.id ? '' : 'confirm_password'" :label-width="formLabelWidth">
                <el-input v-model="subForm.confirm_password" type="password" autocomplete="off" />
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closePasswordDialog('subForm')">关闭</el-button>
                <el-button type="primary" v-loading="updateLoading" @click="updateForm('subForm')">确认</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script setup lang="ts">
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Http from '@/utils/http.js';
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';

const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

// 添加查询参数对象，增加搜索条件
const defaultQuery = {
    user_name: '',
}
const query = reactive({...defaultQuery});
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
const getData = (page:number = defaultPage.page) => {
    loading.value = true;
    Http.doGet('user/index', { ...query, page: page, per_page: pagination.per_page }).then(res => {
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
const handlePageChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}


const updateForm = () => {
    updateLoading.value = true
    subFormRef.value.validate((valid) => {
        if (valid) {
            Http.doPost('manage/user/update', subForm.value).then(function (res) {
                if (res.code === 200) {
                    dialogFormVisible.value = false;
                    cns.$message.success('保存成功');
                    getData();
                } else {
                    cns.$message.error(res.data.message);
                }
            }).catch(function () {
            });
            updateLoading.value = false;
        } else {
            updateLoading.value = false;
            return false;
        }
    })
};

const userAddress = (row) => {
    router.push({ name: 'manage.user.address' , query: {user_id: row.id}})
};

const modifyUser = (row) => {
    subForm.value = {
        id: row.id,
        user_name: row.user_name,
        phone: row.phone,
        avatar: row.avatar,
        password: "",
        confirm_password: "",
    }
    dialogFormVisible.value = true
    updateTitle.value = '编辑用户'
};

const addUser = () => {
    subForm.value = {
        id: 0,
        user_name: "",
        phone: "",
        password: "",
        confirm_password: "",
        avatar: "",
    }
    dialogFormVisible.value = true
    updateTitle.value = '添加用户'
};

// 关闭弹窗
const closePasswordDialog = (form) => {
    subForm.value = {
        id: 0,
        user_name: "",
        phone: "",
        password: "",
        confirm_password: "",
        avatar: "",
    }
    subFormRef.value.resetFields()
    dialogFormVisible.value = false;
};

onMounted( () => {
    getData()
});

const tableData = ref([]);
const updateTitle = ref('');
const subFormRef = ref(null);
const updateLoading = ref(false);
const loading = ref(false);
const dialogFormVisible = ref(false);
const subForm = ref({
    id: 0,
    password: "",
    confirm_password: "",
});
const validateConfirmPassword = (rule, value, callback) => {
    if (value === '') {
        callback(new Error('请输入确认密码'));
    } else if (value !== subForm.password) {
        callback(new Error('两次输入密码不一致!'));
    } else {
        callback();
    }
};

const rules = reactive({
    user_name: [
        { required: true, message: '请输入用户名', trigger: 'blur' },
    ],
    phone: [
        { required: true, message: '请输入手机号', trigger: 'blur' },
    ],
    password: [
        { required: true, message: '请输入密码', trigger: 'blur' },
        { min: 8, max: 30, message: '长度在 8 到 30 个字符', trigger: 'blur' }
    ],
    confirm_password: [
        { required: true, message: '请输入确认密码', trigger: 'blur' },
        { min: 8, max: 30, message: '长度在 8 到 30 个字符', trigger: 'blur' },
        { validator: validateConfirmPassword, trigger: 'blur'}
    ],
})

</script>

<style scoped lang="scss">
.header-picture {
    width: 36px;
    height: 36px;
    overflow: hidden;
    border-radius: 50%;
    background: #fff;
    padding: 1px;
    box-sizing: border-box;
}
.imgs {
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    overflow: hidden;
    border-radius: 50%;
}
.header-picture .imgs img {
    width: 100%;
    height: 100%;
}
.flex-user-information {
    display: flex;align-items: center;
}
.flex-user-information .header-user-names {
    margin: 0 5px;
    width: 90px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.flex-user-information .header-user-names span {
    font-size: 14px;
    font-weight: 400;
    color: #3D3D3D;
}
</style>
