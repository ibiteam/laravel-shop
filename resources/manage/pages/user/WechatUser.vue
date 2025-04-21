<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="微信昵称" prop="nickname">
            <el-input
                v-model="query.nickname"
                placeholder="请输入微信昵称搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="用户名" prop="user_name">
            <el-input
                v-model="query.user_name"
                placeholder="请输入用户名搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="是否关注" prop="is_subscribe">
            <el-select v-model="query.is_subscribe" placeholder="请选择是否关注" clearable>
                <el-option v-for="item in subscribeOptions" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
        </el-form-item>
        <el-form-item label="关注开始时间" prop="subscribe_start_time">
            <el-date-picker
                v-model="query.subscribe_start_time"
                type="datetime"
                placeholder="请选择关注开始时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
        </el-form-item>
        <el-form-item label="关注结束时间" prop="subscribe_end_time">
            <el-date-picker
                v-model="query.subscribe_end_time"
                type="datetime"
                placeholder="请选择关注结束时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button @click="resetSearch">重置</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handleChange"
    >
        <el-table-column label="ID" prop="id" width="80px"></el-table-column>
        <el-table-column label="微信昵称" prop="nickname"></el-table-column>
        <el-table-column label="微信头像" min-width="100px">
            <template #default="scope">
                <img :src="scope.row.avatar" alt="" style="width: 40px;height: 40px;">
            </template>
        </el-table-column>
        <el-table-column label="用户名" prop="user.user_name" width="220px"></el-table-column>
        <el-table-column label="OPEN_ID" prop="openid" show-overflow-tooltip min-width="200px"></el-table-column>
        <el-table-column label="UNION_ID" prop="unionid" show-overflow-tooltip min-width="200px"></el-table-column>
        <el-table-column label="语言" prop="language"></el-table-column>
        <el-table-column label="是否关注">
            <template #default="scope">
                <el-tag v-if="scope.row.is_subscribe" type="success">已关注</el-tag>
                <el-tag v-else type="danger">未关注</el-tag>
            </template>
        </el-table-column>
        <el-table-column label="关注时间" prop="subscribe_time" width="160px"></el-table-column>
        <el-table-column label="备注" prop="remark" width="160px" show-overflow-tooltip></el-table-column>
    </page-table>
</template>
<script setup lang="ts">
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';
import Http from '@/utils/http';

const cns = getCurrentInstance().appContext.config.globalProperties

/* 定义搜索下拉数据 */
const subscribeOptions = [
    { value: 0, label: '未关注', },
    { value: 1, label: '已关注', },
];
/* 定义表格数据 */
const tableData = ref([]);
const loading = ref(false);
/* 定义搜索参数 */
const defaultQuery = reactive({
    nickname: '',
    user_name: '',
    is_subscribe: null,
    subscribe_start_time: '',
    subscribe_end_time: '',
});
const query = reactive({...defaultQuery})
/* 定义默认分页参数 */
const defaultPage = {
    page: 1,
    per_page: 10,
}
const pagination = reactive({...defaultPage})
/* 重置搜索条件 */
const resetSearch = () => {
    Object.assign(query, defaultQuery)
    Object.assign(pagination, defaultPage)
    getData()
}
/* 获取分页数据 */
const getData = (page:number = defaultPage.page) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    }
    Http.doGet('user/wechat', query).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}
/* 点击分页触发方法 */
const handleChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}

onMounted(() => {
    getData();
})
</script>
<style scoped lang="scss">

</style>
