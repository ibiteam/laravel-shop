<script setup lang="ts">
import { Plus, Search, RefreshLeft } from '@element-plus/icons-vue'
import { categoryIndex, goodsIndex, goodsChangeStatus } from '@/api/goods.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';
const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

const tableData = ref([]);
const loading = ref(false);

// 是否展示选项
const statusOptions = [
    { value: -1, label: '全部' },
    { value: 1, label: '上架' },
    { value: 0, label: '下架' }
];

const categoryOptions = ref([]);

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    page: 1,
    number: 10,
    goods_id: '',
    name: '',      // 商品名称搜索
    no: '',
    status: -1,
    category_id: '',
    created_start_time: '',
    created_end_time: '',
    updated_start_time: '',
    updated_end_time: '',
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

/* 商品分类选择触发函数 */
const selectQueryParamsCategory = (item) => {
    if (item == undefined) {
        queryParams.category_id = '';
    } else {
        queryParams.category_id = item[parseInt(item.length) - 1]
    }
}

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;

    goodsIndex(queryParams).then(res => {
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

// 修改上架状态
const handleStatusChange = (goodsId) => {
  goodsChangeStatus({ id: goodsId}).then(res => {
      if (res.code === 200) {
          cns.$message.success(res.message)
          getData(pageInfo.current_page)
      } else {
          cns.$message.error(res.message)
      }
    })
}
const openDetailView = (goodsId) => {
    router.push({name:'goodsForm',query:{id:goodsId}})
}

onMounted( () => {
    categoryIndex().then(res => {
        if (res.code === 200) {
            categoryOptions.value = res.data;
        }
    }),
    getData()
});
</script>

<template>
    <el-header style="padding: 10px 0;height: auto;">
        <!-- 添加搜索表单 -->
        <el-form :inline="true" :model="queryParams" class="search-form" label-width="100px">
            <el-form-item label="商品ID" prop="goods_id">
                <el-input
                    v-model="queryParams.goods_id"
                    placeholder="请输入商品ID"
                    clearable
                    @keyup.enter="handleSearch"
                />
            </el-form-item>
            <el-form-item label="商品货号" prop="no">
                <el-input
                    v-model="queryParams.no"
                    placeholder="请输入商品货号"
                    clearable
                    @keyup.enter="handleSearch"
                />
            </el-form-item>
            <el-form-item label="商品名称" prop="name">
                <el-input
                    v-model="queryParams.name"
                    placeholder="请输入商品名称"
                    clearable
                    @keyup.enter="handleSearch"
                />
            </el-form-item>
            <el-form-item label="上架状态" prop="status">
                <el-select v-model="queryParams.status" placeholder="请选择上架状态">
                    <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item label="商品分类" prop="category_id">
                <el-tree-select
                    v-model="queryParams.category_id"
                    :data="categoryOptions"
                    :props="{label: 'name',value: 'id',children:'all_children'}"
                    :check-strictly="true"
                    filterable
                    style="width: 240px"
                >
                </el-tree-select>
            </el-form-item>
            <el-form-item label="创建时间">
                    <el-date-picker
                        v-model="queryParams.created_start_time"
                        type="datetime"
                        placeholder="开始时间"
                        value-format="YYYY-MM-DD hh:mm:ss"
                    >
                    </el-date-picker>
                    <span>&nbsp;至&nbsp;</span>
                    <el-date-picker
                        v-model="queryParams.created_end_time"
                        type="datetime"
                        placeholder="结束时间"
                        value-format="YYYY-MM-DD hh:mm:ss"
                    >
                    </el-date-picker>
            </el-form-item>
            <el-form-item label="更新时间">
                    <el-date-picker
                        v-model="queryParams.updated_start_time"
                        type="datetime"
                        placeholder="开始时间"
                        value-format="YYYY-MM-DD hh:mm:ss"
                    >
                    </el-date-picker>
                    <span>&nbsp;至&nbsp;</span>
                    <el-date-picker
                        v-model="queryParams.updated_end_time"
                        type="datetime"
                        placeholder="结束时间"
                        value-format="YYYY-MM-DD hh:mm:ss"
                    >
                    </el-date-picker>
            </el-form-item>
            <el-form-item>
                <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                <el-button :icon="RefreshLeft" @click="resetSearch">重置</el-button>
                <el-button :icon="Plus" type="warning" @click="openDetailView()">添加</el-button>
            </el-form-item>
        </el-form>
    </el-header>
    <el-table
        :data="tableData"
        stripe
        border
        v-loading="loading"
        style="width: 100%;">
        <el-table-column label="商品ID" prop="id"></el-table-column>
        <el-table-column label="分类" prop="category.name"></el-table-column>
        <el-table-column label="商品" width="400px">
            <template #default="scope">
                <div style="display:flex;">
                    <el-image style="width: 50px; height: 50px" :src="scope.row.image" :preview-src-list="[scope.row.image]"></el-image>
                    <div style="padding-left: 20px;">
                        <div>{{ scope.row.name }}</div>
                        <div>{{ scope.row.no }}</div>
                    </div>
                </div>
            </template>
        </el-table-column>
        <el-table-column label="商品单价" prop="price"></el-table-column>
        <el-table-column label="销量" prop="sales_volume"></el-table-column>
        <el-table-column label="库存" prop="total"></el-table-column>
        <el-table-column label="排序" prop="sort"></el-table-column>
        <el-table-column label="上架" prop="status">
            <template #default="scope">
                <el-switch
                    v-model="scope.row.status"
                    active-color="#13ce66"
                    inactive-color="#ff4949"
                    :active-value="1"
                    :inactive-value="0"
                    @change="handleStatusChange(scope.row.id)"
                >
                </el-switch>
            </template>
        </el-table-column>
        <el-table-column label="创建时间" prop="created_at"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button link type="primary" size="large" @click="openDetailView(scope.row.id)">编辑</el-button>
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
