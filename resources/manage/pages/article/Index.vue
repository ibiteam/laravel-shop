<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="标题" prop="title">
            <el-input v-model="query.title" clearable placeholder="请输入" @keyup.enter="getData()" />
        </el-form-item>
        <el-form-item label="作者" prop="author">
            <el-input v-model="query.author" clearable placeholder="请输入" @keyup.enter="getData()" />
        </el-form-item>
        <el-form-item label="创建人" prop="admin_user_name">
            <el-input v-model="query.admin_user_name" clearable placeholder="请输入"
                      @keyup.enter="getData()" />
        </el-form-item>
        <el-form-item label="所属分类">
            <el-cascader v-model="query.article_category_id" placeholder="请选择" filterable clearable
                         :options="treeCategories" :show-all-levels="false"
                         :props="{ value: 'id', label: 'name', checkStrictly: true, emitPath:false }">
            </el-cascader>
        </el-form-item>
        <el-form-item label="是否置顶">
            <el-select v-model="query.is_top" placeholder="请选择" clearable>
                <el-option label="是" value="1"></el-option>
                <el-option label="否" value="0"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="创建时间">
            <el-date-picker
                v-model="query.start_time"
                type="datetime"
                placeholder="开始时间"
                value-format="YYYY-MM-DD HH:mm:ss">
            </el-date-picker>
            <span>&nbsp;至&nbsp;</span>
            <el-date-picker
                v-model="query.end_time"
                type="datetime"
                placeholder="结束时间"
                value-format="YYYY-MM-DD HH:mm:ss">
            </el-date-picker>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button @click="resetSearch">重置</el-button>
            <el-button type="danger" @click="openArticleForm()">添加</el-button>
        </el-form-item>
    </search-form>

    <page-table
        :data="tableData"
        :maxHeight="'700px'"
        v-loading="loading"
        @change="handlePageChange"
    >
        <el-table-column label="ID" prop="id" width="80px"></el-table-column>
        <el-table-column label="标题" prop="title"></el-table-column>
        <el-table-column label="作者" prop="author"></el-table-column>
        <el-table-column label="所属分类" prop="category_name"></el-table-column>
        <el-table-column label="是否置顶" prop="is_top">
            <template #default="{ row }">
                <el-switch v-model="row.is_top"
                           :active-value="1" :inactive-value="0"
                           active-color="#13ce66" inactive-color="#ff4949"
                           @click="changeField(row.id, row.is_top, 'is_top')">
                </el-switch>
            </template>
        </el-table-column>
        <el-table-column label="是否显示" prop="is_show">
            <template #default="{ row }">
                <el-switch v-model="row.is_show"
                           :active-value="1" :inactive-value="0"
                           active-color="#13ce66" inactive-color="#ff4949"
                           @click="changeField(row.id, row.is_show, 'is_show')">
                </el-switch>
            </template>
        </el-table-column>
        <el-table-column label="是否登录" prop="is_login">
            <template #default="{ row }">
                <el-switch v-model="row.is_login"
                           :active-value="1" :inactive-value="0"
                           active-color="#13ce66" inactive-color="#ff4949"
                           @click="changeField(row.id, row.is_login, 'is_login')">
                </el-switch>
            </template>
        </el-table-column>
        <el-table-column label="排序" prop="sort" width="80px"></el-table-column>
        <el-table-column label="浏览量" prop="click_count" width="80px"></el-table-column>
        <el-table-column label="创建人" prop="admin_user_name"></el-table-column>
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="{ row }">
                <el-button link type="primary" @click="openArticleForm(row.id)">编辑</el-button>
                <el-button link type="warning" @click="handleCopy(row.id)">生成副本</el-button>
                <el-button link type="danger" @click="handleDestroy(row.id)">删除</el-button>
            </template>
        </el-table-column>
    </page-table>
</template>

<script setup lang="ts">
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Http from '@/utils/http';

const router = useRouter();
const cns = getCurrentInstance().appContext.config.globalProperties;
const loading = ref(false);
const tableData = ref({});
const treeCategories = ref([]);

const defaultQuery = reactive({
    title: '',
    admin_user_name: '',
    author: '',
    article_category_id: '',
    is_top: null,
    start_time: '',
    end_time: '',
});
const query = reactive({ ...defaultQuery });

const defaultPage = {
    page: 1,
    per_page: 10
};
const pagination = reactive({ ...defaultPage });
const handlePageChange = (page: number, per_page: number) => {
    pagination.per_page = per_page;
    getData(page);
};

const resetSearch = () => {
    Object.assign(query, defaultQuery);
    Object.assign(pagination, defaultPage);
    getData();
};

const changeField = (id: number, value: any, field: any) => {
    Http.doPost('article/change_field', {
        id: id,
        value: value,
        field: field
    }).then((res: any) => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
        }
    });
};

// 获取分类
const getCategories = () => {
    Http.doGet('article/info').then((res: any) => {
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
const handleCopy = (articleId: number) => {
    cns.$confirm('确定要生成副本吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        Http.doPost('article/copy', { id: articleId }).then((res: any) => {
            if (cns.$successCode(res.code)) {
                getData();
                cns.$message.success(res.message);
            } else {
                cns.$message.error(res.message);
            }
        }).catch(() => {
            cns.$message.error('操作失败');
        });
    });
};

// 删除
const handleDestroy = (articleId: number) => {
    cns.$confirm('此操作将永久删除, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        Http.doPost('article/destroy', { id: articleId }).then((res: any) => {
            if (cns.$successCode(res.code)) {
                getData();
                cns.$message.success(res.message);
            } else {
                cns.$message.error(res.message);
            }
        }).catch(() => {
            cns.$message.error('操作失败');
        });
    });
};

const getData = (page: number = defaultPage.page) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    };
    Http.doGet('article', params).then((res: any) => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data;
        } else {
            cns.$message.error(res.message);
        }
    }).catch(() => {
        loading.value = false;
    });
};

onMounted(() => {
    getData();
    getCategories();
});
</script>

<style scoped lang="scss">

</style>
