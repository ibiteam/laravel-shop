<template>
    <search-form :model="query">
        <el-form-item label="日期">
            <el-date-picker
                v-model="query.start_time"
                type="date"
                placeholder="开始日期"
                value-format="YYYY-MM-DD"
            >
            </el-date-picker>
            <span>&nbsp;至&nbsp;</span>
            <el-date-picker
                v-model="query.end_time"
                type="date"
                placeholder="结束日期"
                value-format="YYYY-MM-DD"
            >
            </el-date-picker>
        </el-form-item>
        <el-form-item label="来源">
            <el-select v-model="query.referer" clearable placeholder="请选择">
                <el-option
                    v-for="item in refererOptions"
                    :key="item.value" :label="item.label" :value="item.value">
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handlePageChange"
    >
        <el-table-column label="日期" prop="statistic_date"></el-table-column>
        <el-table-column label="来源" prop="referer"></el-table-column>
        <el-table-column label="PV数" prop="pv_number"></el-table-column>
        <el-table-column label="UV数" prop="uv_number"></el-table-column>
        <el-table-column label="IP数" prop="ip_number"></el-table-column>
    </page-table>
</template>

<script setup lang="ts">
import PageTable from '@/components/common/PageTable.vue';
import SearchForm from '@/components/common/SearchForm.vue';
import Http from '@/utils/http';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const refererOptions = [
    { label: '全部', value: '' },
    { label: 'H5端', value: 'h5' },
    { label: 'APP端', value: 'app' },
    { label: 'PC端', value: 'pc' },
    { label: '微信小程序', value: 'wechat_mini' }
];

const query = reactive({
    start_time: '',
    end_time: '',
    referer: '',
});
const defaultPage = {
    page: 1,
    per_page: 10
};
const pagination = reactive({ ...defaultPage });
const tableData = ref([]);
const loading = ref(false);

const getData = (page = defaultPage.page) => {
    loading.value = true;
    const params = {
        ...query,
        page: page,
        per_page: pagination.per_page
    };
    Http.doGet('access_statistic', params).then((res: any) => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data;
        } else {
            cns.$message.error(res.message);
        }
    }).catch(() => {
        loading.value = false;
        cns.$message.error('获取数据失败');
    });
};

// 页码改变
const handlePageChange = (page: number, per_page: number) => {
    pagination.per_page = per_page;
    getData(page);
};

onMounted(() => {
    getData();
});
</script>

<style scoped lang="scss">

</style>
