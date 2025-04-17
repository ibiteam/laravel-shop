<template>
    退款详情页
</template>

<script setup>
import {
    applyRefundDetail,
    applyRefundAgreeApply,
    applyRefundCloseApply,
    applyRefundExecuteRefund,
    applyRefundRefuseRefund,
    applyRefundConfirmReceipt,
    applyRefundQueryExpress
} from '@/api/order.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const router = useRouter();
const route = useRoute();

const cns = getCurrentInstance().appContext.config.globalProperties;
const detailFormLoading = ref(false);


const getDetail = () => {
    detailFormLoading.value = true;
    applyRefundDetail({ id: route.params.id }).then(res => {
        detailFormLoading.value = false;
        if (cns.$successCode(res.code)) {
            console.log(res.data);

        } else {
            cns.$message.error(res.message);
            router.push({ name: 'manage.apply_refund.index' });
        }
    }).catch(() => {
        detailFormLoading.value = false;
        cns.$message.error('获取数据失败');
    });
};

onMounted(() => {
    getDetail();
});
</script>

<style scoped lang="scss">

</style>
