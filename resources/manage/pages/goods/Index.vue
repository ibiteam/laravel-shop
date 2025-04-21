<template>
    <search-form :model="query">
                <el-form-item label="商品ID" prop="goods_id">
                    <el-input
                        v-model="query.goods_id"
                        placeholder="请输入商品ID"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="商品货号" prop="no">
                    <el-input
                        v-model="query.no"
                        placeholder="请输入商品货号"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="商品名称" prop="goods_name">
                    <el-input
                        v-model="query.goods_name"
                        placeholder="请输入商品名称"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="上架状态" prop="status">
                    <el-select v-model="query.status" placeholder="请选择上架状态">
                        <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="商品分类" prop="category_id">
                    <el-cascader
                        v-model="query.category_id"
                        filterable
                        clearable
                        :options="categoryOptions"
                        :props="{label: 'name',value: 'id',children:'all_children',checkStrictly: true}"
                        placeholder="请选择"
                        @change="selectQueryParamsCategory"
                    ></el-cascader>
                </el-form-item>
                <el-form-item label="创建时间">
                    <el-date-picker
                        v-model="query.created_start_time"
                        type="datetime"
                        placeholder="开始时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                    <span>&nbsp;至&nbsp;</span>
                    <el-date-picker
                        v-model="query.created_end_time"
                        type="datetime"
                        placeholder="结束时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item label="更新时间">
                    <el-date-picker
                        v-model="query.updated_start_time"
                        type="datetime"
                        placeholder="开始时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                    <span>&nbsp;至&nbsp;</span>
                    <el-date-picker
                        v-model="query.updated_end_time"
                        type="datetime"
                        placeholder="结束时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-button  type="primary" @click="handleSearch">搜索</el-button>
                    <el-button  type="danger" @click="openDetail(0)">添加</el-button>
                </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handlePageChange"
    >
            <el-table-column label="商品ID" prop="id"></el-table-column>
            <el-table-column label="分类" prop="category.name"></el-table-column>
            <el-table-column label="商品" width="400px">
                <template #default="{row}">
                    <div style="display:flex;">
                        <el-image style="width: 50px; height: 50px" :src="row.image" :preview-src-list="[row.image]" :z-index="9999"></el-image>
                        <div style="padding-left: 20px;">
                            <div>{{ row.name }}</div>
                            <div>{{ row.no }}</div>
                        </div>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="商品单价" prop="price"></el-table-column>
            <el-table-column label="销量" prop="sales_volume"></el-table-column>
            <el-table-column label="库存" prop="total"></el-table-column>
            <el-table-column label="排序" prop="sort"></el-table-column>
            <el-table-column label="上架" prop="status">
                <template #default="{row}">
                    <el-switch
                        v-model="row.status"
                        :active-value="1"
                        :inactive-value="0"
                        @change="handleStatusChange(row.id)"
                    >
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="更新时间" prop="updated_at"></el-table-column>
            <el-table-column label="操作">
                <template #default="{row}">
                    <el-button link type="primary" @click="openDetail(row.id)">编辑</el-button>
                </template>
            </el-table-column>
    </page-table>
</template>
<script setup lang="ts">
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';
import Http from '@/utils/http';
const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

const tableData = ref([]);
const loading = ref(false);
const defaultPage = {
    page: 1,
    per_page: 10,
}
const pagination = reactive({...defaultPage})
// 是否展示选项
const statusOptions = [
    { value: -1, label: '全部' },
    { value: 1, label: '上架' },
    { value: 0, label: '下架' }
];

const categoryOptions = ref([]);

const defaultQuery = {
    goods_id: '',
    goods_name: '',
    no: '',
    status: -1,
    category_id: '',
    created_start_time: '',
    created_end_time: '',
    updated_start_time: '',
    updated_end_time: '',
}
// 添加查询参数对象，增加搜索条件
const query = reactive({...defaultQuery})
// 搜索方法
const handleSearch = () => {
    getData(1);
};

/* 商品分类选择触发函数 */
const selectQueryParamsCategory = (item) => {
    if (item == undefined) {
        query.category_id = '';
    } else {
        query.category_id = item[parseInt(item.length) - 1]
    }
}
const handlePageChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}
const getData = (page = defaultPage.page) => {
    loading.value = true;
    // 更新当前页码
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    }

    Http.doGet('goods/info', params).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data;
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}

// 修改上架状态
const handleStatusChange = (id) => {
    Http.doPost('goods/info/change/status', { id: id}).then(res => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message)
            getData(pagination.page)
        } else {
            cns.$message.error(res.message)
        }
    })
}
const openDetail = (id) => {
    router.push({name: 'manage.goods.update',params:{id:id}})
}

onMounted( () => {
    Http.doGet('goods/category')().then(res => {
        if (cns.$successCode(res.code)) {
            categoryOptions.value = res.data;
        }
    })
    getData()
});
</script>

<style scoped lang="scss">
:deep(.el-table .el-table__cell){
    z-index: unset;
}

</style>
