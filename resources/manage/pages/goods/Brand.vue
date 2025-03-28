<script setup lang="ts">
import { Plus, Search, RefreshLeft } from '@element-plus/icons-vue'
import { brandIndex, brandEdit, brandUpdate, brandDestroy } from '@/api/goods.js';
import { fileUpload } from '@/api/common.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import _ from 'lodash';
const cns = getCurrentInstance().appContext.config.globalProperties

const tableData = ref([]);
const loading = ref(false);

// 是否展示选项
const showOptions = [
    { value: -1, label: '全部' },
    { value: 1, label: '是' },
    { value: 0, label: '否' }
];

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    page: 1,
    number: 10,
    name: '',      // 品牌名称搜索
    is_show: -1    // 是否展示，-1表示全部
});

// 添加分页相关状态
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

// 重置搜索条件
const resetSearch = () => {
    queryParams.name = '';
    queryParams.is_show = -1;
    getData(1);
};

// 页码改变
const handleCurrentChange = (val) => {
    getData(val);
}

// 每页条数改变
const handleSizeChange = (val) => {
    queryParams.number = val;
    pageInfo.per_page = val;
    getData(1);
}

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;

    brandIndex(queryParams).then(res => {
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

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
}

const detailDialogVisible = ref(false);
const detailDialogTitle = ref('');
const detailFormLoading = ref(false);
const detailSubmitLoading = ref(false);
const detailFormRef = ref(null)

const detailForm = reactive({
    id: 0,
    name: '',
    logo: '',
    sort: 0,
    is_show: 1,
});
const detailFormRules = reactive({
    name: [
        { required: true, message: '请输入品牌名称', trigger: 'blur' },
        { min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }
    ],
    logo: [
        { required: true, message: '请输入品牌LOGO', trigger: 'blur' },
    ],
    sort: [
        { required: true, message: '请输入排序', trigger: 'blur' },
    ],
    is_show: [
        { required: true, message: '请选择是否展示', trigger: 'blur' },
    ]
})

const openDetailDialog = (goodsBrandId = 0) => {
    detailDialogVisible.value = true
    detailDialogTitle.value = '添加品牌';
    if (goodsBrandId > 0) {
        detailDialogTitle.value = '编辑品牌'
        detailFormLoading.value = true
        brandEdit({ id:goodsBrandId }).then(res => {
            detailFormLoading.value = false
            if (res.code === 200) {
                detailForm.id = res.data.id
                detailForm.name = res.data.name
                detailForm.logo = res.data.logo
                detailForm.sort = res.data.sort
                detailForm.is_show = res.data.is_show
            } else {
                cns.$message.error(res.message)
                closeDetailDialog()
            }
        }).catch(error => {
            detailFormLoading.value = false
            cns.$message.error('操作失败')
            closeDetailDialog()
        })
    }
}

const closeDetailDialog = () => {
    detailDialogVisible.value = false;
    detailDialogTitle.value = '';
    detailFormLoading.value = false;
    detailSubmitLoading.value = false;

    detailForm.id = 0
    detailForm.name = ''
    detailForm.logo = ''
    detailForm.sort = 0
    detailForm.is_show = 1
}

// 文件变更处理函数 - 直接上传文件
const handleLogoChange = (uploadFile) => {
    // 如果有文件，直接上传
    if (uploadFile && uploadFile.raw) {
        const formData = new FormData();
        formData.append('file', uploadFile.raw);

        detailFormLoading.value = true;
        fileUpload(formData).then(res => {
            detailFormLoading.value = false;
            if (res.code === 200) {
                detailForm.logo = res.data.url;
                cns.$message.success('上传成功');
            } else {
                cns.$message.error(res.message || '上传失败');
            }

            // 清除上传队列，确保下次可以重新选择同一个文件
            if (uploadRef.value) {
                uploadRef.value.clearFiles();
            }
        }).catch(error => {
            detailFormLoading.value = false;
            cns.$message.error('上传失败');

            // 出错时也需要清除文件列表
            if (uploadRef.value) {
                uploadRef.value.clearFiles();
            }
        });
    }
}

const submitDetailForm = _.throttle(() => {
    detailFormRef.value.validate((valid) => {
        if (valid) {
            detailSubmitLoading.value = true;
            brandUpdate(detailForm).then(res => {
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
const handleDestroy = (goodsBrandId) => {
    cns.$confirm('此操作将永久删除该品牌, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        brandDestroy({id:goodsBrandId}).then(res => {
            if (res.code === 200) {
                getData()
                cns.$message.success(res.message)
            } else {
                cns.$message.error(res.message)
            }
        }).catch(error => {
            cns.$message.error('操作失败')
        })
    })
}

onMounted( () => {
    getData()
});
</script>

<template>
    <el-header style="padding: 10px 0;">
        <!-- 添加搜索表单 -->
        <el-form :inline="true" :model="queryParams" class="search-form">
            <el-form-item label="品牌名称" prop="name">
                <el-input
                    v-model="queryParams.name"
                    placeholder="请输入品牌名称"
                    clearable
                    @keyup.enter="handleSearch"
                />
            </el-form-item>
            <el-form-item label="是否展示" prop="is_show">
                <el-select v-model="queryParams.is_show" placeholder="请选择">
                    <el-option v-for="item in showOptions" :key="item.value" :label="item.label" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                <el-button :icon="RefreshLeft" @click="resetSearch">重置</el-button>
                <el-button :icon="Plus" type="warning" @click="openDetailDialog()">添加</el-button>
            </el-form-item>
        </el-form>
    </el-header>
    <el-table
        :data="tableData"
        stripe
        border
        v-loading="loading"
        style="width: 100%;">
        <el-table-column label="品牌ID" prop="id"></el-table-column>
        <el-table-column label="品牌名称" prop="name"></el-table-column>
        <el-table-column label="品牌LOGO" prop="logo">
            <template #default="scope">
                <img :src="scope.row.logo" alt="" style="width: 50px;">
            </template>
        </el-table-column>
        <el-table-column label="排序" prop="sort"></el-table-column>
        <el-table-column label="是否展示" prop="is_show">
            <template #default="scope">
                <el-switch disabled v-model="scope.row.is_show" :active-value="1" :inactive-value="0" />
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

    <!-- 添加分页组件 -->
    <div class="pagination-container" v-if="pageInfo.total > 0">
        <el-pagination
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="pageInfo.current_page"
            :page-sizes="[10, 15, 30, 50, 100]"
            :page-size="pageInfo.per_page"
            layout="total, sizes, prev, pager, next, jumper"
            :total="pageInfo.total">
        </el-pagination>
    </div>

    <!-- 添加|编辑弹窗 -->
    <el-dialog
        v-model="detailDialogVisible"
        :title="detailDialogTitle"
        width="700"
        center
        :before-close="closeDetailDialog">
        <div v-loading="detailFormLoading" class="s-flex jc-ct">
            <el-form :model="detailForm" ref="detailFormRef" :rules="detailFormRules" label-width="auto" style="width: 480px" size="default">
                <el-form-item label="品牌名称" prop="name">
                    <el-input v-model="detailForm.name" />
                </el-form-item>
                <el-form-item label="分类排序" prop="sort">
                    <el-input v-model="detailForm.sort" />
                </el-form-item>
                <el-form-item label="是否显示" prop="is_show">
                    <el-switch v-model="detailForm.is_show" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="品牌图片" prop="logo">
                    <el-upload
                        ref="uploadRef"
                        v-model="detailForm.logo"
                        class="logo-uploader"
                        action="#"
                        :auto-upload="false"
                        :on-change="handleLogoChange"
                        accept="image/jpeg,image/png,image/gif"
                        :limit="1"
                        :show-file-list="false">
                        <img v-if="detailForm.logo" :src="detailForm.logo" class="logo" />
                        <el-icon v-else class="logo-uploader-icon"><Plus /></el-icon>
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

.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 15px;
}
</style>
