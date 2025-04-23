<script setup>
import Http from '@/utils/http'
import PageTable from '@/components/common/PageTable.vue'
import { useRouter } from 'vue-router';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()
import { DocumentCopy } from '@element-plus/icons-vue';

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    page: 1,
    per_page: 10,
});

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
    // 更新当前页码
    queryParams.page = page;
    loading.value = true;
    Http.doGet('app_decoration', queryParams).then(res => {
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

const copyText = (url) => {
    const tempInput = document.createElement('input')
    tempInput.value = url
    document.body.appendChild(tempInput)
    tempInput.select()
    document.execCommand('copy')
    document.body.removeChild(tempInput)
    cns.$message.success('复制成功')
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
    <page-table
        :data="tableData"
        stripe
        border
        @change="handlePageChange"
        v-loading="loading">
        <el-table-column label="用户ID" prop="id"></el-table-column>
        <el-table-column label="页面" prop="name"></el-table-column>
        <el-table-column label="页面标题" prop="title"></el-table-column>
        <el-table-column label="访问地址">
            <template #default="scope" >
                <div style="display: flex;align-items: center;">
                    <a target="_blank" :href="scope.row.path"><span style="margin-right: 15px;">预览</span></a>
                    <el-icon style="cursor: pointer" @click="copyText(scope.row.path)"><DocumentCopy /></el-icon>
                </div>
            </template>
        </el-table-column>
        <el-table-column label="装修人" prop="admin_user.user_name"></el-table-column>
        <el-table-column label="添加时间" prop="created_at"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at"></el-table-column>
        <el-table-column label="操作">
            <template #default="scope">
                <el-button @click="goDecoration(scope.row)">页面装修</el-button>
            </template>
        </el-table-column>
    </page-table>
</template>

<style scoped lang="scss">
</style>
