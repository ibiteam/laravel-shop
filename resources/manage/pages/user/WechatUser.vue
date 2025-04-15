<script setup lang="ts">

import { Search, RefreshLeft } from '@element-plus/icons-vue';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import PublicPageTable from '@/components/common/PublicPageTable.vue';
import { wechatUserIndex } from '@/api/user';
const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

const tableData = ref([]);
const loading = ref(false);


const subscribeOptions = [
    { value: 0, label: '未关注', },
    { value: 1, label: '已关注', },
];

const queryParams = reactive({
    nickname: '',
    user_name: '',
    is_subscribe: null,
    subscribe_start_time: '',
    subscribe_end_time: '',
    page: 1,
    number: 10,
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

// 重置搜索条件
const resetSearch = () => {
    queryParams.nickname = '';
    queryParams.user_name = '';
    queryParams.is_subscribe = '';
    queryParams.subscribe_start_time = '';
    queryParams.subscribe_end_time = '';
    queryParams.page = 1;
    queryParams.number = 10;
    getData(1);
};

// 添加分页相关状态
const pageInfo = reactive({
    per_page: 10,
    total: 0,
    current_page: 1,
})

// 页码改变
const handleCurrentChange = (val) => {
    getData(val);
}

// 每页条数改变
const handleSizeChange = (val) => {
    pageInfo.per_page = val;
    getData(1);
}

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
}

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;
    queryParams.number = pageInfo.per_page;

    wechatUserIndex(queryParams).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
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


onMounted(() => {
    getData();
})
</script>

<template>
    <div class="common-wrap">
        <el-header style="padding: 10px 0;height: auto;">
            <!-- 添加搜索表单 -->
            <el-form :inline="true" :model="queryParams" class="search-form" label-width="100px">
                <el-form-item label="微信昵称" prop="nickname">
                    <el-input
                        v-model="queryParams.nickname"
                        placeholder="请输入微信昵称搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="用户名" prop="user_name">
                    <el-input
                        v-model="queryParams.user_name"
                        placeholder="请输入用户名搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="是否关注" prop="is_subscribe">
                    <el-select v-model="queryParams.is_subscribe" placeholder="请选择是否关注" clearable>
                        <el-option v-for="item in subscribeOptions" :key="item.value" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="关注开始时间" prop="subscribe_start_time">
                    <el-date-picker
                        v-model="queryParams.subscribe_start_time"
                        type="datetime"
                        placeholder="请选择关注开始时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item label="关注结束时间" prop="subscribe_end_time">
                    <el-date-picker
                        v-model="queryParams.subscribe_end_time"
                        type="datetime"
                        placeholder="请选择关注结束时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                    <el-button :icon="RefreshLeft" @click="resetSearch">重置</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <PublicPageTable
            :data="tableData"
            v-loading="loading"
            :pageInfo="pageInfo"
            @sizeChange="handleSizeChange"
            @currentChange="handleCurrentChange"
            style="width: 100%;">
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
        </PublicPageTable>
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
</style>
