<template>
    <div>

    </div>
</template>
<script setup>
import { nextTick, onMounted, ref, reactive, onUnmounted, computed,getCurrentInstance } from 'vue';
import { useRoute,useRouter } from 'vue-router';
const cns = getCurrentInstance().appContext.config.globalProperties
const route = useRoute()
const router =  useRouter()
import { useTabsStore } from '@/store'
const tabsStore = useTabsStore()
onMounted(() => {
    // 取到路由带过来的参数
    let routeValue = tabsStore.refreshView;
    let view = {
        name: routeValue.name,
        title: "正在刷新",
        path: routeValue.path,
    };
    //返回路由的数据
    tabsStore.delVisitedViews(view).then((views) => {
        tabsStore.visitedViews.forEach((item, index) => {
            if (item.name === 'manage.refresh.index') {
                tabsStore.visitedViews.splice(index, 1)
            }
        })
        router.replace({
            name: routeValue.name,
            query:routeValue.query,
            params:routeValue.params
        });
    })
});
</script>

<style lang="scss" rel="stylesheet/scss" type="text/scss" scoped>

</style>
