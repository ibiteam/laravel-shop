<script setup>
import Http from '@/utils/http'
import PageTable from '@/components/common/PageTable.vue'
import SearchForm from '@/components/common/SearchForm.vue'
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

// 搜索方法
const handleSearch = () => {
    getData(1);
};
// 查看
const examine = (row) => {
    router.push({ name: 'manage.user_coupon.index' , query: {coupon_id: row.id}})
};

const defaultPage = {
    page: 1,
    per_page: 10
};

const pagination = reactive({ ...defaultPage });

const handlePageChange = (page, per_page) => {
    pagination.per_page = per_page;
    getData(page);
};

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;
    Http.doGet('coupon', queryParams).then(res => {
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

onMounted( () => {
    getData()
});
const tableData = ref([]);
const loading = ref(false);
</script>

<template>
    <search-form :model="queryParams">
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
    </search-form>
    <page-table
        :data="tableData"
        stripe
        border
        @change="handlePageChange"
        v-loading="loading">
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
    </page-table>
</template>

<style scoped lang="scss">
</style>
