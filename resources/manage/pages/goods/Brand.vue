<script setup lang="ts">
import { Plus, Search, RefreshLeft } from '@element-plus/icons-vue'
import { brandIndex } from '@/api/goods.js'
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
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
                <el-button :icon="Plus" type="warning">添加</el-button>
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
