<template>
    <div class="common-wrap role-wrap">
        <el-header style="padding-top: 10px;">
            <el-form :inline="true" :model="searchForm" class="search-form">
                <el-form-item label="角色名称" prop="display_name">
                    <el-input v-model="searchForm.display_name" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="getData()">查询</el-button>
                    <el-button type="danger" @click="openStoreDialog()">添加</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <el-table
            :data="tableData"
            stripe border
            v-loading="loading"
            style="width: 100%;">
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="角色名称" prop="display_name"></el-table-column>
            <el-table-column label="角色用户数" prop="role_number"></el-table-column>
            <el-table-column label="描述" prop="description"></el-table-column>
            <el-table-column label="是否展示" prop="is_show">
                <template #default="scope">
                    <el-switch
                        v-model="scope.row.is_show"
                        :active-value="1" :inactive-value="0"
                        active-color="#13ce66" inactive-color="#ff4949"
                        @click="changeShow(scope.row)">
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="操作">
                <template #default="scope">
                    <el-button type="primary" size="large" @click="openStoreDialog(scope.row.id)">编辑</el-button>
                    <el-button type="success" size="large" v-if="!scope.row.role_number" @click="handleDestroy(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="pagination-container" v-if="pageInfo.total > 0">
            <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page="pageInfo.current_page"
                :page-sizes="[10, 20, 30, 50, 100]"
                :page-size="pageInfo.per_page"
                layout="total, sizes, prev, pager, next, jumper"
                :total="pageInfo.total">
            </el-pagination>
        </div>

        <el-dialog
            width="700" center :before-close="closeStoreDialog"
            :close-on-click-modal="false"
            v-model="storeDialogVisible" :title="storeDialogTitle">
            <div v-loading="detailFormLoading" class="s-flex jc-ct">
                <el-form :model="submitForm" ref="submitFormRef" :rules="submitFormRules" label-width="auto"
                         style="width: 480px" size="default">
                    <el-form-item label="角色名称" prop="display_name">
                        <el-input v-model="submitForm.display_name" />
                    </el-form-item>
                    <el-form-item label="角色描述" prop="description">
                        <el-input v-model="submitForm.description" />
                    </el-form-item>
                    <!--todo: 角色权限-->
                    <div class="permission-main">
                        <div class="permission-main-title">角色权限（角色拥有的权限）</div>
                        <el-checkbox v-model="checkPermission" class="permission-check" @change="handleCheckAllChange">全选</el-checkbox>
                        <el-tree
                            ref="treeRef"
                            :data="allPermissions"
                            :props="defaultProps"
                            :default-checked-keys="submitForm.self_permissions"
                            node-key="id"
                            show-checkbox
                            @check="handleCheckChange"
                        ></el-tree>
                    </div>
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

<script setup lang="ts">
import { roleIndex, roleInfo, roleStore, roleChangeShow, roleDestroy } from '@/api/set.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    display_name: '',
    page: 1,
    number: 10
});
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});
const tableData = ref([]);
const loading = ref(false);
const detailFormLoading = ref(false);
const storeDialogVisible = ref(false);
const storeDialogTitle = ref('');
const submitFormRef = ref(null);
const submitLoading = ref(false);
const allPermissions = ref([]);
const allPermissionsKey =  ref([])
const submitForm = reactive({
    id: 0,
    display_name: '',
    description: '',
    self_permissions: []
});

const submitFormRules = reactive({
    display_name: [{ required: true, message: '请输入角色名称', trigger: 'blur' }]
});
const checkPermission = ref(false)
const treeRef = ref(null)
const defaultProps = {
    children: 'children',
    label: 'display_name',
}
const openStoreDialog = (id = 0) => {
    storeDialogTitle.value = id > 0 ? '编辑' : '添加';
    detailFormLoading.value = true;
    roleInfo({ id: id }).then(res => {
        detailFormLoading.value = false;
        if (cns.$successCode(res.code)) {
            allPermissions.value = res.data.all_permissions;
            res.data.all_permissions.forEach(item => {
                allPermissionsKey.value.push(item.id)
                if (item.children && item.children.length > 0){
                    item.children.forEach(ite => {
                        allPermissionsKey.value.push(ite.id)
                        if (ite.children && ite.children.length > 0){
                            ite.children.forEach(it => {
                                allPermissionsKey.value.push(it.id)
                                if (it.children && it.children.length > 0){
                                    it.children.forEach(itd => {
                                        allPermissionsKey.value.push(itd.id)
                                    })
                                }
                            })
                        }
                    })
                }
            })

            if (id > 0) {
                submitForm.id = res.data.role_info.id;
                submitForm.display_name = res.data.role_info.display_name;
                submitForm.description = res.data.role_info.description;
                submitForm.self_permissions = res.data.role_info.self_permissions;
                if (submitForm.self_permissions.length == allPermissionsKey.value.length){
                    checkPermission.value = true
                }
            }
        } else {
            detailFormLoading.value = false;
            cns.$message.error(res.message);
            closeStoreDialog();
        }
    }).catch(error => {
        cns.$message.error('获取信息失败');
        closeStoreDialog();
    });

    storeDialogVisible.value = true;
};

const closeStoreDialog = () => {
    storeDialogTitle.value = '';
    detailFormLoading.value = false;
    allPermissions.value = []
    allPermissionsKey.value = []
    checkPermission.value = false
    submitForm.id = 0;
    submitForm.display_name = '';
    submitForm.description = '';
    submitForm.self_permissions = [];
    storeDialogVisible.value = false;
};

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            roleStore(submitForm).then(res => {
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

const changeShow = (row) => {
    roleChangeShow({
        id: row.id,
        is_show: row.is_show
    }).then(res => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
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
        roleDestroy({ id: id }).then(res => {
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
    roleIndex(searchForm).then(res => {
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

const handleCheckAllChange = () =>{
    if (checkPermission.value){
        treeRef.value.setCheckedKeys(allPermissionsKey.value)
        submitForm.self_permissions = allPermissionsKey.value
    }else{
        treeRef.value.setCheckedKeys([])
        submitForm.self_permissions = []
    }
}
const handleCheckChange = () => {
    submitForm.self_permissions = treeRef.value.getCheckedKeys()
    if (submitForm.self_permissions.length == allPermissionsKey.value.length){
        checkPermission.value = true
    }else{
        checkPermission.value = false
    }
}

onMounted(() => {
    getData();
});
</script>

<style scoped lang="scss">
.role-wrap{
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
    .permission-main{
        .permission-main-title{
            color: #3d3d3d;
            font-size: 14px;
            font-weight: 500;
        }
        .permission-check{
            margin-top: 10px;
        }
        .el-tree{
            max-height: 400px;
            overflow-y: auto;
            margin-top: 10px;
        }
    }
}
</style>
