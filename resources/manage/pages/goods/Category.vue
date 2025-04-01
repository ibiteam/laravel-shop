<script setup>
import { Plus, Delete } from '@element-plus/icons-vue';
import { categoryIndex, categoryUpdate, categoryEdit, categoryDestroy, categoryChangeShow } from '@/api/goods.js';
import { fileUpload } from '@/api/common.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import _ from 'lodash';

const cns = getCurrentInstance().appContext.config.globalProperties;

const tableData = ref([]);
const loading = ref(false);

const detailDialogVisible = ref(false);
const detailDialogTitle = ref('');
const detailFormLoading = ref(false);
const topCategories = ref([]);
const detailForm = reactive({
    id: 0,
    name: '',
    title: '',
    keywords: '',
    description: '',
    logo: '',
    parent_id: 0,
    sort: 0,
    is_show: 1
});
const detailFormRules = reactive({
    name: [{ required: true, message: '请输入分类名称', trigger: 'blur' }],
    title: [{ required: true, message: '请输入分类标题', trigger: 'blur' }],
    keywords: [{ required: true, message: '请输入关键词', trigger: 'blur' }],
    description: [{ required: true, message: '请输入描述', trigger: 'blur' }],
    logo: [{ required: true, message: '请上传图片', trigger: 'change' }],
    parent_id: [{ required: true, message: '请选择父级分类', trigger: 'change' }],
    sort: [{ required: true, message: '请输入排序', trigger: 'blur' }],
    is_show: [{ required: true, message: '请设置是否显示', trigger: 'change' }]
});
const detailFormRef = ref(null);
const detailSubmitLoading = ref(false);
const uploadRef = ref(null);

const openDetailDialog = (goodsCategoryId = 0) => {
    detailDialogVisible.value = true;
    detailDialogTitle.value = goodsCategoryId > 0 ? '添加分类' : '编辑分类';
    detailFormLoading.value = true;
    categoryEdit({ id: goodsCategoryId }).then(res => {
        detailFormLoading.value = false;
        if (res.code === 200) {
            topCategories.value = res.data.top_categories;
            if (goodsCategoryId > 0) {
                detailForm.id = res.data.info.id;
                detailForm.title = res.data.info.title;
                detailForm.name = res.data.info.name;
                detailForm.keywords = res.data.info.keywords;
                detailForm.description = res.data.info.description;
                detailForm.logo = res.data.info.logo;
                detailForm.parent_id = res.data.info.parent_id;
                detailForm.sort = res.data.info.sort;
                detailForm.is_show = res.data.info.is_show;
            }
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
    detailDialogTitle.value = '';
    detailFormLoading.value = false;

    detailForm.id = 0;
    detailForm.name = '';
    detailForm.title = '';
    detailForm.keywords = '';
    detailForm.description = '';
    detailForm.logo = '';
    detailForm.parent_id = 0;
    detailForm.sort = 0;
    detailForm.is_show = 1;
};
const uploadFile = async (request) => {
    try {
        const res = await fileUpload({ file: request.file });
        if (res.code === 200) {
            detailForm.logo = res.data.url;
        } else {
            cns.$message.error(res.message);
        }
    } catch (error) {
        console.error('Failed:', error);
    }
};
/* 提交信息弹窗 */
const submitDetailForm = _.throttle(() => {
    detailFormRef.value.validate((valid) => {
        if (valid) {
            detailSubmitLoading.value = true;
            categoryUpdate(detailForm).then(res => {
                detailSubmitLoading.value = false;
                if (res.code === 200) {
                    closeDetailDialog();
                    getData();
                } else {
                    cns.$message.error(res.message);
                }
            });
        }
    });
}, 1000);

const handleDestroy = (goodsCategoryId) => {
    cns.$confirm('此操作将永久删除该分类, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        categoryDestroy({ id: goodsCategoryId }).then(res => {
            if (res.code === 200) {
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
    categoryChangeShow({
        id: row.id,
        is_show: row.is_show
    }).then(res => {
        if (res.code === 200) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
        }
    });
};

const getData = () => {
    categoryIndex().then(res => {
        if (res.code === 200) {
            tableData.value = res.data;
        } else {
            cns.$message.error(res.message);
        }
    });
};

onMounted(() => {
    getData();
});
</script>
<template>
    <el-header style="padding-top: 10px;">
        <el-button type="primary" @click="openDetailDialog()">添加分类</el-button>
    </el-header>
    <el-table
        :data="tableData"
        stripe
        border
        v-loading="loading"
        style="width: 100%;"
        row-key="id"
        :tree-props="{ children: 'all_children' }">
        <el-table-column label="分类名称" min-width="200">
            <template #default="scope">
                <div class="s-flex ai-ct">
                    <el-image class="goods-category-logo" v-if="scope.row.logo" :src="scope.row.logo"
                              style="margin-right: 10px;width: 40px;height: 40px;" :fit="cover"></el-image>
                    {{ scope.row.name }}【{{ scope.row.id }}】
                </div>
            </template>
        </el-table-column>
        <el-table-column label="分类标题" prop="title"></el-table-column>
        <el-table-column label="关键词" prop="keywords"></el-table-column>
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
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button link type="primary" size="large" @click="openDetailDialog(scope.row.id)">编辑</el-button>
                <el-button link type="danger" size="large" @click="handleDestroy(scope.row.id)">删除</el-button>
            </template>
        </el-table-column>
    </el-table>
    <el-dialog
        v-model="detailDialogVisible"
        :title="detailDialogTitle"
        width="700"
        center
        :before-close="closeDetailDialog">
        <div v-loading="detailFormLoading" class="s-flex jc-ct">
            <el-form :model="detailForm" ref="detailFormRef" :rules="detailFormRules" label-width="auto"
                     style="width: 480px" size="default">
                <el-form-item label="上级分类" prop="parent_id">
                    <el-select v-model="detailForm.parent_id" placeholder="请选择上级分类">
                        <el-option v-for="item in topCategories" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="商品分类" prop="name">
                    <el-input v-model="detailForm.name" />
                </el-form-item>
                <el-form-item label="分类标题" prop="title">
                    <el-input v-model="detailForm.title" />
                </el-form-item>
                <el-form-item label="分类关键词" prop="keywords">
                    <el-input v-model="detailForm.keywords" />
                </el-form-item>
                <el-form-item label="分类描述" prop="description">
                    <el-input v-model="detailForm.description" />
                </el-form-item>
                <el-form-item label="分类排序" prop="sort">
                    <el-input v-model="detailForm.sort" />
                </el-form-item>
                <el-form-item label="是否显示" prop="is_show">
                    <el-switch v-model="detailForm.is_show" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="分类图标" prop="logo">
                    <el-upload
                        class="logo-uploader"
                        accept="image/jpeg,image/jpg,image/png"
                        action=""
                        :show-file-list="false"
                        :http-request="(request) => uploadFile(request)"
                        :with-credentials="true"
                    >
                        <img v-if="detailForm.logo" :src="detailForm.logo" class="logo" />
                        <el-icon v-else class="logo-uploader-icon">
                            <Plus />
                        </el-icon>
                    </el-upload>
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

<style scoped lang="scss">
.goods-category-logo {
    width: 30px;
    height: 30px;
    margin-right: 10px;
}

.logo-uploader .logo {
    width: 80px;
    height: 80px;
    display: block;
}

:deep(.el-table__row .cell) {
    display: flex;
    align-items: center;
}
</style>

<style>
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
    width: 80px;
    height: 80px;
    text-align: center;
}

</style>
