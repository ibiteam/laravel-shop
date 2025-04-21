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
    per_page: 10,
    name: '',
    type: -1,
});

// 搜索方法
const handleSearch = () => {
    getData(1);
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
    Http.doGet('marketing/bonus', queryParams).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data;
            // 更新分页信息
            setPageInfo(res.data.meta);
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}
// 查看
const examine = (row) => {
    router.push({ name: 'manage.user_bonus.index' , query: {bonus_id: row.id}})
};

onMounted( () => {
    getData()
});

const types = ref([
    {
        value: -1,
        label: '全部'
    },{
        value: 0,
        label: '不限制'
    },
    {
        value: 1,
        label: '限商品'
    },
    {
        value: 2,
        label: '限分类'
    }
]);
const tableData = ref([]);
const loading = ref(false);
</script>

<template>
    <search-form :model="queryParams">
        <el-form-item label="红包名称" prop="name">
            <el-input
                v-model="queryParams.name"
                placeholder="商品id/商品名称"
                clearable
                @keyup.enter="handleSearch"
            />
        </el-form-item>
        <el-form-item label="红包类型" prop="type">
            <el-select
                v-model="queryParams.type"
                style="width: 240px"
            >
                <el-option
                    v-for="item in types"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"
                />
            </el-select>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="handleSearch">搜索</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        stripe
        border
        @change="handlePageChange"
        v-loading="loading">
        <el-table-column label="ID" prop="id"></el-table-column>
        <el-table-column label="红包名称" prop="name"></el-table-column>
        <el-table-column label="红包金额" prop="money"></el-table-column>
        <el-table-column label="最小使用金额" prop="min_amount"></el-table-column>
        <el-table-column label="红包类型" prop="type">
            <template #default="scope">
                <template v-if="scope.row.type == 0">不限制</template>
                <template v-else-if="scope.row.type == 1">限商品</template>
                <template v-else-if="scope.row.type == 2">限分类</template>
            </template>
        </el-table-column>
        <el-table-column label="是否可叠加" prop="is_add"></el-table-column>
        <el-table-column label="联合优惠券" prop="can_use_coupon"></el-table-column>
        <el-table-column label="限领数量" prop="limit"></el-table-column>
        <el-table-column label="发放开始时间" prop="send_start_time"></el-table-column>
        <el-table-column label="使用开始时间" prop="use_start_time"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button type="primary" size="small" @click="examine(scope.row)">查看</el-button>
            </template>
        </el-table-column>
    </page-table>
</template>

<style scoped lang="scss">

</style>
