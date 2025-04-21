<script setup>
import { Search} from '@element-plus/icons-vue';
import Page from '@/components/common/Pagination.vue'
import { getGoodsViews } from '@/api/views.js'
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    page: 1,
    number: 10,
    keywords: '',
    user_name: '',
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
    getGoodsViews(queryParams).then(res => {
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

const tableData = ref([]);
const loading = ref(false);
</script>

<template>
    <div>
        <el-header style="padding: 10px 0;">
            <!-- 添加搜索表单 -->
            <el-form :inline="true" :model="queryParams" class="search-form">
                <el-form-item label="商品信息" prop="keywords">
                    <el-input
                        v-model="queryParams.keywords"
                        placeholder="商品id/商品名称"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="用户名" prop="user_name">
                    <el-input
                        v-model="queryParams.user_name"
                        placeholder="请输入用户名"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <el-table
            :data="tableData"
            stripe
            border
            v-loading="loading"
            style="width: 100%;">
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="商品ID" prop="goods_id"></el-table-column>
            <el-table-column label="商品名称" prop="goods_name"></el-table-column>
            <el-table-column label="来源" prop="referer"></el-table-column>
            <el-table-column label="用户名" prop="user_name"></el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="更新时间" prop="updated_at"></el-table-column>
        </el-table>
        <!-- 添加分页组件 -->
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

.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 15px;
}

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
