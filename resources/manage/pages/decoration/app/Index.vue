<script setup>
import { appDecoration } from '@/api/decoration.js';
import Page from '@/components/common/Pagination.vue'
import { useRouter } from 'vue-router';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    page: 1,
    number: 10,
});

// 添加分页相关状态
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});

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

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
}

const getData = (page = 1) => {
    // 更新当前页码
    queryParams.page = page;
    loading.value = true;
    appDecoration(queryParams).then(res => {
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

onMounted( () => {
    getData()
});

const tableData = ref([]);
const loading = ref(false);

const goDecoration = (row) => {
    router.push({ name: 'manage.app_decoration.home' , query: {id: row.id}})
}

</script>

<template>
    <el-table
        :data="tableData"
        stripe
        border
        v-loading="loading"
        style="width: 100%;">
        <el-table-column label="用户ID" prop="id"></el-table-column>
        <el-table-column label="页面" prop="name"></el-table-column>
        <el-table-column label="页面标题" prop="title"></el-table-column>
        <el-table-column label="访问地址">
            <template #default="scope" >
                <div style="display: flex;align-items: center;">
                    <span style="margin-right: 15px;">预览</span>
                    <el-icon><CopyDocument /></el-icon>
                </div>
            </template>
        </el-table-column>
        <el-table-column label="装修人" prop="admin_user_name"></el-table-column>
        <el-table-column label="添加时间" prop="created_at"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button @click="goDecoration(scope.row)">页面装修</el-button>
            </template>
        </el-table-column>
    </el-table>
    <!-- 添加分页组件 -->
    <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />
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
