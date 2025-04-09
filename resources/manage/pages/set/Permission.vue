<template>
    <div class="common-wrap">
        <el-header style="padding-top: 10px;">
            <el-form :inline="true" :model="searchForm" class="search-form">
                <el-form-item label="名称/权限值" prop="keywords">
                    <el-input v-model="searchForm.keywords" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="getData()">搜索</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <!--todo: 权限-->
    </div>
</template>

<script setup>
import { Plus, Search } from '@element-plus/icons-vue';
import { permissionIndex, permissionStore } from '@/api/set.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    keywords: ''
});

const permissionData = ref([]);
const loading = ref(false);
const submitFormRef = ref(null);
const submitLoading = ref(false);
const submitForm = reactive({
    id: 0,
    parent_id: 0,
    name: '',
    display_name: '',
    icon: '',
    sort: 0
});

const submitFormRules = reactive({
    name: [{ required: true, message: '请输入权限值', trigger: 'blur' }],
    display_name: [{ required: true, message: '请输入名称', trigger: 'blur' }]
});

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            permissionStore(submitForm).then(res => {
                submitLoading.value = false;
                if (cns.$successCode(res.code)) {
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

const getData = () => {
    loading.value = true;
    permissionIndex(searchForm).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            permissionData.value = res.data;
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
