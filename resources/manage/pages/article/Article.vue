<script setup>
import { Plus, Search } from '@element-plus/icons-vue';
import Page from '@/components/common/Pagination.vue';
import {
    articleIndex,
    articleInfo,
    articleChangeField,
    articleCopy,
    articleDestroy
} from '@/api/article.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    title: '',
    admin_user_name: '',
    author: '',
    article_category_id: '',
    is_top: null,
    start_time: '',
    end_time: '',
    number: 10,
    page: 1
});
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});
const tableData = ref([]);
const treeCategories = ref([]);
const loading = ref(false);

const changeField = (id, value, field) => {
    articleChangeField({
        id: id,
        value: value,
        field: field
    }).then(res => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
        }
    });
};

/* 获取分类 */
const getCategories = () => {
    articleInfo().then(res => {
        if (cns.$successCode(res.code)) {
            treeCategories.value = res.data.tree_categories;
        }
    }).catch(() => {
    });
};

// 新增编辑文章
const openArticleForm = (articleId = 0) => {
    router.push({ name: 'manage.article.form', params: { id: articleId } });
};

// 生成副本
const handleCopy = (articleId) => {
    cns.$confirm('确定要生成副本吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        articleCopy({ id: articleId }).then(res => {
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

// 删除
const handleDestroy = (articleId) => {
    cns.$confirm('此操作将永久删除, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        articleDestroy({ id: articleId }).then(res => {
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
    articleIndex(searchForm).then(res => {
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
    getCategories();
});
</script>
<template>
    <div class="common-wrap">
        <el-header style="padding: 10px 0;height: auto;">
            <el-form :inline="true" :model="searchForm" class="search-form" label-width="100px">
                <el-form-item label="标题" prop="title">
                    <el-input v-model="searchForm.title" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item label="作者" prop="author">
                    <el-input v-model="searchForm.author" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item label="创建人" prop="admin_user_name">
                    <el-input v-model="searchForm.admin_user_name" clearable placeholder="请输入"
                              @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item label="所属分类">
                    <el-cascader v-model="searchForm.article_category_id" placeholder="请选择" filterable clearable
                                 :options="treeCategories" :show-all-levels="false"
                                 :props="{ value: 'id', label: 'name', checkStrictly: true, emitPath:false }">
                    </el-cascader>
                </el-form-item>
                <el-form-item label="是否置顶">
                    <el-select v-model="searchForm.is_top" placeholder="请选择" clearable>
                        <el-option label="是" value="1"></el-option>
                        <el-option label="否" value="0"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="创建时间">
                    <el-date-picker
                        v-model="searchForm.start_time"
                        type="datetime"
                        placeholder="开始时间"
                        value-format="YYYY-MM-DD HH:mm:ss">
                    </el-date-picker>
                    <span>&nbsp;至&nbsp;</span>
                    <el-date-picker
                        v-model="searchForm.end_time"
                        type="datetime"
                        placeholder="结束时间"
                        value-format="YYYY-MM-DD HH:mm:ss">
                    </el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="getData()">搜索</el-button>
                    <el-button :icon="Plus" type="warning" @click="openArticleForm()">添加</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <el-table
            :data="tableData"
            stripe border
            v-loading="loading"
            style="width: 100%;">
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="标题" prop="title"></el-table-column>
            <el-table-column label="作者" prop="author"></el-table-column>
            <el-table-column label="所属分类" prop="category_name"></el-table-column>
            <el-table-column label="是否置顶" prop="is_top">
                <template #default="scope">
                    <el-switch v-model="scope.row.is_top"
                               :active-value="1" :inactive-value="0"
                               active-color="#13ce66" inactive-color="#ff4949"
                               @click="changeField(scope.row.id, scope.row.is_top, 'is_top')">
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="是否显示" prop="is_show">
                <template #default="scope">
                    <el-switch v-model="scope.row.is_show"
                               :active-value="1" :inactive-value="0"
                               active-color="#13ce66" inactive-color="#ff4949"
                               @click="changeField(scope.row.id, scope.row.is_show, 'is_show')">
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="是否登录" prop="is_login">
                <template #default="scope">
                    <el-switch v-model="scope.row.is_login"
                               :active-value="1" :inactive-value="0"
                               active-color="#13ce66" inactive-color="#ff4949"
                               @click="changeField(scope.row.id, scope.row.is_login, 'is_login')">
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="排序" prop="sort"></el-table-column>
            <el-table-column label="浏览量" prop="view_count"></el-table-column>
            <el-table-column label="浏览量" prop="click_count"></el-table-column>
            <el-table-column label="浏览人数" prop="article_view_count"></el-table-column>
            <el-table-column label="创建人" prop="admin_user_name"></el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="操作">
                <template #default="scope">
                    <!--<el-button link type="success" size="large">查看</el-button>-->
                    <el-button link type="primary" size="large" @click="openArticleForm(scope.row.id)">编辑</el-button>
                    <el-button link type="danger" size="large" @click="handleDestroy(scope.row.id)">删除</el-button>
                    <el-button link type="warning" size="large" @click="handleCopy(scope.row.id)">生成副本</el-button>
                </template>
            </el-table-column>
        </el-table>
        <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />
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
</style>
