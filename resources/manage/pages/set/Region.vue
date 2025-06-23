<template>
    <el-header>
        <el-button type="danger" @click="clearCache()">清除缓存</el-button>
    </el-header>
    <el-main>
        <el-cascader-panel height="100%" v-model="regionChecked" disabled="true" :options="regionData"
                           :props="{ value: 'id',label: 'name',children: 'all_children'}"></el-cascader-panel>
    </el-main>
</template>

<script setup lang="ts">
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import Http from '@/utils/http';

const cns = getCurrentInstance().appContext.config.globalProperties;

const regionData = ref([]);
const regionChecked = ref([]);
const loading = ref(false);

const clearCache = () => {
    Http.doPost('region/clear_cache', {}).then((res: any) => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
            getData();
        } else {
            cns.$message.error(res.message);
        }
    }).catch(() => {
        cns.$message.error('获取数据失败');
    });
};

const getData = () => {
    loading.value = true;
    Http.doGet('region').then((res: any) => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            regionData.value = res.data.region_list;
            regionChecked.value = res.data.region_checked;
        } else {
            cns.$message.error(res.message);
        }
    }).catch(() => {
        loading.value = false;
        cns.$message.error('获取数据失败');
    });
};

onMounted(() => {
    getData();
});
</script>

<style scoped lang="scss">
:deep(.el-scrollbar.el-cascader-menu) {
    height: 800px;
}

:deep(.el-cascader-menu__wrap.el-scrollbar__wrap) {
    height: 820px;
}

:deep(.el-cascader-menu__list) {
    padding: 6px 0 20px 0;
}
</style>
