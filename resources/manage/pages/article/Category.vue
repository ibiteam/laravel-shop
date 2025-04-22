<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="分类名称" prop="name">
            <el-input v-model="query.name" clearable placeholder="请输入" @keyup.enter="getData()" />
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
        <el-table-column label="分类名称" min-width="120">
            <template #default="scope">
                <div class="s-flex ai-ct">
                    {{ scope.row.name }}【{{ scope.row.id }}】
                </div>
            </template>
        </el-table-column>
        <el-table-column label="别名" prop="alias"></el-table-column>
        <el-table-column label="类型" prop="type">
            <template #default="scope">
                <template v-if="scope.row.type === 1">普通分类</template>
                <template v-if="scope.row.type === 2">系统分类</template>
            </template>
        </el-table-column>
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
        <el-table-column label="排序(由大到小)" prop="sort"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button link type="primary" size="large" @click="openStoreDialog(scope.row.id)">编辑</el-button>
                <el-button link type="warning" size="large" v-if="scope.row.is_show"
                           @click="openMoveDialog(scope.row.id)">转移文章
                </el-button>
                <el-button link type="danger" size="large" v-if="!scope.row.alias"
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
                <el-form-item label="所属分类" prop="parent_id">
                    <el-cascader v-model="submitForm.parent_id" placeholder="顶级分类" style="width: 400px;"
                                 filterable clearable :options="treeCategories"
                                 :props="{ value: 'id', label: 'name', checkStrictly: true, emitPath:false }">
                    </el-cascader>
                </el-form-item>
                <el-form-item label="名称" prop="name">
                    <el-input v-model="submitForm.name" />
                </el-form-item>
                <el-form-item label="别名" prop="alias">
                    <el-input v-model="submitForm.alias" />
                </el-form-item>
                <el-form-item label="标题" prop="title">
                    <el-input v-model="submitForm.title" />
                </el-form-item>
                <el-form-item label="描述" prop="description">
                    <el-input v-model="submitForm.description" />
                </el-form-item>
                <el-form-item label="关键字" prop="keywords">
                    <el-input v-model="submitForm.keywords" />
                </el-form-item>
                <el-form-item label="类型" prop="type">
                    <el-radio v-model="submitForm.type" label="1">普通分类</el-radio>
                    <el-radio v-model="submitForm.type" label="2">系统分类</el-radio>
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <el-input v-model="submitForm.sort" />
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

    <el-dialog
        width="700" center :before-close="closeMoveDialog"
        v-model="moveDialogVisible" title="转移文章">
        <div style="margin: 20px 0;">
            <p style="font-size: 18px; font-weight: bold">什么是转移文章?</p>
            <p>在添加文章或文章管理中,如果需要对文章的分类进行变更,那么你可以通过此功能正确管理你的文章分类。</p>
        </div>
        <div v-loading="detailFormLoading" class="s-flex jc-ct">
            <el-form :model="moveForm" ref="moveFormRef" label-width="auto" style="width: 480px" size="default">
                <el-form-item label="当前分类" prop="old_category_id">
                    <el-cascader v-model="moveForm.old_category_id" placeholder="请选择" disabled
                                 filterable clearable
                                 :options="treeCategories" :show-all-levels="false"
                                 :props="{ value: 'id', label: 'name', checkStrictly: true, emitPath:false }">
                    </el-cascader>
                    <p>仅限转移当前分类下的文章，不含子类。</p>
                </el-form-item>
                <el-form-item label="目标分类" prop="new_category_id">
                    <el-cascader v-model="moveForm.new_category_id" placeholder="请选择"
                                 filterable clearable
                                 :options="treeCategories" :show-all-levels="false"
                                 :props="{ value: 'id', label: 'name', checkStrictly: true, emitPath:false }">
                    </el-cascader>
                    <p>目标分类:转移后，文章归属于该分类。</p>
                </el-form-item>
            </el-form>
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closeMoveDialog()">取消</el-button>
                <el-button type="primary" :loading="submitLoading" @click="onMoveSubmit()">提交</el-button>
            </div>
        </template>
    </el-dialog>

</template>

<script setup>
import { ref, reactive, getCurrentInstance, onMounted, nextTick } from 'vue';
import SearchForm from '@/components/common/SearchForm.vue';
import Http from '@/utils/http';

const cns = getCurrentInstance().appContext.config.globalProperties;

const defaultQuery = reactive({
    name: ''
});
const query = reactive({ ...defaultQuery });

const tableData = ref([]);
const loading = ref(false);
const detailFormLoading = ref(false);
const storeDialogVisible = ref(false);
const storeDialogTitle = ref('');
const submitFormRef = ref(null);
const submitLoading = ref(false);
const treeCategories = ref([]);
const submitForm = reactive({
    id: 0,
    parent_id: 0,
    name: '',
    alias: '',
    title: '',
    description: '',
    keywords: '',
    type: 0,
    sort: 0,
    is_show: 1
});
const moveDialogVisible = ref(false);
const moveForm = reactive({
    old_category_id: 0,
    new_category_id: 0
});

const submitFormRules = reactive({
    name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
    title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
    description: [{ required: true, message: '请输入描述', trigger: 'blur' }],
    keywords: [{ required: true, message: '请输入关键字', trigger: 'blur' }],
    type: [{ required: true, message: '请选择类型', trigger: 'change' }]
});

const openStoreDialog = (categoryId = 0) => {
    storeDialogTitle.value = categoryId > 0 ? '编辑分类' : '添加分类';
    detailFormLoading.value = true;
    Http.doGet('article/category/info', { id: categoryId }).then(res => {
        detailFormLoading.value = false;
        if (cns.$successCode(res.code)) {
            treeCategories.value = res.data.tree_categories;
            if (categoryId > 0) {
                submitForm.id = res.data.info.id;
                submitForm.parent_id = res.data.info.parent_id;
                submitForm.name = res.data.info.name;
                submitForm.alias = res.data.info.alias;
                submitForm.title = res.data.info.title;
                submitForm.description = res.data.info.description;
                submitForm.keywords = res.data.info.keywords;
                submitForm.type = res.data.info.type;
                submitForm.sort = res.data.info.sort;
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
    submitForm.title = '';
    submitForm.description = '';
    submitForm.keywords = '';
    submitForm.type = 0;
    submitForm.sort = 0;
    submitForm.is_show = 1;
    storeDialogVisible.value = false;
};

// 提交
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            Http.doPost('article/category/update', submitForm).then(res => {
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

const openMoveDialog = (oldCategoryId) => {
    if (oldCategoryId <= 0) {
        cns.$message.error('请选择分类');
        return false;
    }
    detailFormLoading.value = true;
    Http.doGet('article/category/info', { id: 0 }).then(res => {
        detailFormLoading.value = false;
        if (cns.$successCode(res.code)) {
            treeCategories.value = res.data.tree_categories;
            moveForm.old_category_id = oldCategoryId;
            moveForm.new_category_id = 0;
        } else {
            detailFormLoading.value = false;
            cns.$message.error(res.message);
            closeMoveDialog();
        }
    }).catch(error => {
        cns.$message.error('获取分类信息失败');
        closeMoveDialog();
    });

    moveDialogVisible.value = true;
};

const closeMoveDialog = () => {
    moveForm.old_category_id = 0;
    moveForm.new_category_id = 0;
    detailFormLoading.value = false;
    moveDialogVisible.value = false;
};

// 转移文章
const onMoveSubmit = () => {
    submitLoading.value = true;
    Http.doPost('article/category/move', moveForm).then(res => {
        submitLoading.value = false;
        if (cns.$successCode(res.code)) {
            closeMoveDialog();
            getData();
        } else {
            cns.$message.error(res.message);
        }
    });
};

const handleDestroy = (categoryId) => {
    cns.$confirm('此操作将永久删除, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        Http.doPost('article/category/destroy', { id: categoryId }).then(res => {
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
    Http.doPost('article/category/change_show', {
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

const getData = () => {
    loading.value = true;
    Http.doGet('article/category', query).then(res => {
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
