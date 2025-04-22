<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="名称" prop="name">
            <el-input v-model="query.name" clearable placeholder="请输入" @keyup.enter="getData()" />
        </el-form-item>
        <el-form-item label="别名" prop="alias">
            <el-input v-model="query.alias" clearable placeholder="请输入" @keyup.enter="getData()" />
        </el-form-item>
        <el-form-item label="是否显示">
            <el-select v-model="query.is_show" placeholder="请选择">
                <el-option label="全部" value="-1"></el-option>
                <el-option label="显示" value="1"></el-option>
                <el-option label="隐藏" value="0"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button type="danger" @click="openStoreDialog()">添加</el-button>
        </el-form-item>
    </search-form>
    <el-table
        :data="tableData"
        stripe border
        v-loading="loading"
        style="width: 100%;"
        row-key="id"
        :tree-props="{ children: 'all_children' }">
        <el-table-column label="分类" min-width="120">
            <template #default="scope">
                <div class="s-flex ai-ct">
                    {{ scope.row.name }}【{{ scope.row.id }}】
                </div>
            </template>
        </el-table-column>
        <el-table-column label="别名" prop="alias"></el-table-column>
        <el-table-column label="类型" prop="type">
            <template #default="scope">
                <template v-if="scope.row.type === 1">链接</template>
                <template v-if="scope.row.type === 2">菜单</template>
            </template>
        </el-table-column>
        <el-table-column label="页面" prop="page_title"></el-table-column>
        <el-table-column label="链接数量" prop="routers_count"></el-table-column>
        <el-table-column label="是否显示" prop="is_show">
            <template #default="scope">
                <el-switch
                    v-model="scope.row.is_show"
                    :active-value="1" :inactive-value="0"
                    active-color="#13ce66" inactive-color="#ff4949"
                    @click="changeShow(scope.row)">
                </el-switch>
            </template>
        </el-table-column>
        <!--<el-table-column label="排序" prop="sort"></el-table-column>-->
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button link type="primary" size="large" @click="openStoreDialog(scope.row.id)">编辑</el-button>
                <el-button link type="danger" size="large" v-if="!scope.row.routers_count"
                           @click="handleDestroy(scope.row.id)">删除
                </el-button>
            </template>
        </el-table-column>
    </el-table>

    <el-dialog
        width="700" center :before-close="closeStoreDialog"
        v-model="storeDialogVisible" :title="storeDialogTitle">
        <div v-loading="detailFormLoading" class="s-flex jc-ct">
            <el-form :model="submitForm" ref="submitFormRef" :rules="submitFormRules" label-width="auto"
                     style="width: 480px" size="default">
                <el-form-item label="分类" prop="parent_id">
                    <el-select v-model="submitForm.parent_id" placeholder="请选择分类"
                               @change="changeCategory(submitForm)">
                        <el-option v-for="item in topCategories" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="名称" prop="name">
                    <el-input v-model="submitForm.name" />
                </el-form-item>
                <el-form-item label="别名" prop="alias">
                    <el-input v-model="submitForm.alias" :disabled='submitForm.id > 0' />
                </el-form-item>
                <el-form-item label="类型" prop="type">
                    <el-radio v-model="submitForm.type" label="1" v-if="submitForm.parent_id === 0">链接</el-radio>
                    <el-radio v-model="submitForm.type" label="2">菜单</el-radio>
                </el-form-item>
                <el-form-item label="页面" prop="page_name"
                              v-if="!(submitForm.parent_id === 0 && submitForm.type === '2')"
                              :rules="{required: true, message: '请选择页面', trigger: 'change'}">
                    <el-select v-model="submitForm.page_name" placeholder="请选择"
                               filterable remote reserve-keyword :remote-method="searchPages" :loading="remoteLoading">
                        <el-option v-for="item in pagePermissions" :label="item.display_name"
                                   :value="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="是否显示" prop="is_show">
                    <el-switch v-model="submitForm.is_show" :active-value="1" :inactive-value="0" />
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

<script setup>
import SearchForm from '@/components/common/SearchForm.vue';
import { ref, reactive, getCurrentInstance, onMounted, nextTick } from 'vue';
import Http from '@/utils/http';

const cns = getCurrentInstance().appContext.config.globalProperties;

const defaultQuery = reactive({
    name: '',
    alias: '',
    is_show: '-1'
});
const query = reactive({ ...defaultQuery });

const tableData = ref([]);
const loading = ref(false);
const detailFormLoading = ref(false);
const remoteLoading = ref(false);
const storeDialogVisible = ref(false);
const storeDialogTitle = ref('');
const submitFormRef = ref(null);
const submitLoading = ref(false);
const topCategories = ref([]);
const pagePermissions = ref([]);
const submitForm = reactive({
    id: 0,
    parent_id: 0,
    name: '',
    alias: '',
    type: 0,
    page_name: '',
    is_show: 1
});

const submitFormRules = reactive({
    parent_id: [{ required: true, message: '请选择分类', trigger: 'change' }],
    name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
    alias: [{ required: true, message: '请输入别名', trigger: 'blur' }],
    type: [{ required: true, message: '请选择类型', trigger: 'change' }]
});

const openStoreDialog = (categoryId = 0) => {
    storeDialogTitle.value = categoryId > 0 ? '编辑分类' : '添加分类';
    detailFormLoading.value = true;
    Http.doGet('router_category/info', { id: categoryId }).then(res => {
        detailFormLoading.value = false;
        if (cns.$successCode(res.code)) {
            topCategories.value = res.data.top_categories;
            pagePermissions.value = res.data.page_permissions;
            if (categoryId > 0) {
                submitForm.id = res.data.info.id;
                submitForm.parent_id = res.data.info.parent_id;
                submitForm.name = res.data.info.name;
                submitForm.alias = res.data.info.alias;
                submitForm.type = res.data.info.type;
                submitForm.page_name = res.data.info.page_name;
                submitForm.is_show = res.data.info.is_show;
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
    nextTick(() => {
        submitFormRef.value.clearValidate();
    });
};

const closeStoreDialog = () => {
    storeDialogTitle.value = '';
    detailFormLoading.value = false;
    submitForm.id = 0;
    submitForm.parent_id = 0;
    submitForm.name = '';
    submitForm.alias = '';
    submitForm.type = 0;
    submitForm.page_name = '';
    submitForm.is_show = 1;
    storeDialogVisible.value = false;
};

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            Http.doPost('router_category/update', submitForm).then(res => {
                submitLoading.value = false;
                if (cns.$successCode(res.code)) {
                    closeStoreDialog();
                    getData();
                } else {
                    cns.$message.error(res.message);
                }
            });
        } else {
            submitLoading.value = false;
            cns.$message.error('表单验证失败');
            return false;
        }
    });
};

const handleDestroy = (categoryId) => {
    cns.$confirm('此操作将永久删除该分类, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        Http.doPost('router_category/destroy', { id: categoryId }).then(res => {
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

const changeShow = (row) => {
    Http.doPost('router_category/change_show', {
        id: row.id,
        is_show: row.is_show
    }).then(res => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
            getData();
        }
    });
};

const searchPages = (query) => {
    if (query !== '') {
        remoteLoading.value = true;
        Http.doGet('router_category/pages', { keywords: query }).then(res => {
            remoteLoading.value = false;
            if (cns.$successCode(res.code)) {
                pagePermissions.value = res.data;
            }
        });
    }
};

const changeCategory = (form) => {
    submitForm.type = 0;
    submitForm.page_name = '';
};

const getData = () => {
    loading.value = true;
    Http.doGet('router_category', query).then(res => {
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
:deep(.el-table__row .cell) {
    display: flex;
    align-items: center;
}
</style>
