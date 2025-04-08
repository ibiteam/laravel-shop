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
import { useCommonStore } from '@/store'
const commonStore = useCommonStore()
onMounted(() => {
    // 取到路由带过来的参数
    let routeValue = commonStore.refreshView;
    console.log(routeValue)
    let view = {
        name: routeValue.name,
        title: "正在刷新",
        path: routeValue.path,
    };
    //返回路由的数据
    commonStore.delVisitedViews(view).then((views) => {
        commonStore.visitedViews.forEach((item, index) => {
            if (item.name === 'manage.refresh.index') {
                commonStore.visitedViews.splice(index, 1)
            }
        })
        if (routeValue.name && routeValue.name != 'manage.refresh.index'){
            router.replace({
                name: routeValue.name,
                query:routeValue.query,
                params:routeValue.params
            });
        }else{
            router.replace({
                name: 'manage.home.index'
            });
        }

    })
});
</script>

<style lang="scss" rel="stylesheet/scss" type="text/scss" scoped>

</style>
