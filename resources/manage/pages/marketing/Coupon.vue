<script setup>
import { Search} from '@element-plus/icons-vue';
import Page from '@/components/common/Pagination.vue'
import { getUserCoupon } from '@/api/coupon.js'
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter()

const cns = getCurrentInstance().appContext.config.globalProperties

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    page: 1,
    number: 10,
    name: '',
    type: -1,
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
// 查看
const examine = (row) => {
    router.push({ name: '' , query: {id: row.id}})
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
    getUserCoupon(queryParams).then(res => {
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
                <el-form-item label="优惠券名称" prop="name">
                    <el-input
                        v-model="queryParams.name"
                        placeholder="请输入优惠券名称"
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
            <el-table-column label="优惠券名称" prop="name"></el-table-column>
            <el-table-column label="金额" prop="money"></el-table-column>
            <el-table-column label="数量" prop="number"></el-table-column>
            <el-table-column label="限制领取数量" prop="limit"></el-table-column>
            <el-table-column label="优惠券类型" prop="style">
                <template #default="scope">
                    {{ scope.row.style === 1 ? '满减券' : '' }}
                </template>
            </el-table-column>
            <el-table-column label="是否可叠加" prop="is_add"></el-table-column>
            <el-table-column label="发放时间">
                <template #default="scope">
                    {{ scope.row.send_start_time }} <br /> {{ scope.row.send_end_time }}
                </template>
            </el-table-column>
            <el-table-column label="使用时间">
                <template #default="scope">
                    {{ scope.row.start_time }} <br /> {{ scope.row.end_time }}
                </template>
            </el-table-column>
            <el-table-column label="最小使用金额" prop="min_amount"></el-table-column>
            <el-table-column label="优惠券限制类型" prop="type">
                <template #default="scope">
                    <span v-if="scope.row.type == 0">不限制</span>
                    <span v-else-if="scope.row.type == 1">限商品</span>
                    <span v-else-if="scope.row.type == 2">限分类</span>
                </template>
            </el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="操作">
                <template #default="scope">
                    <el-button type="primary" size="small" @click="examine(scope.row)">查看</el-button>
                </template>
            </el-table-column>
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
