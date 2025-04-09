<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();

</script>

<template>
    <div class="order-detail bg-fff manage-public-wrap pd20">
        <p class="tip-title"><a @click="router.push({name:'manage.order.index'})">订单详情 </a><span>></span><span> c20240680001</span></p>
        <div class="detail-item">
            <div class="detail-title">商品信息</div>
            <div>表格</div>
        </div>
        <div class="detail-item">
            <div class="detail-title">订单信息</div>
            <div>表格</div>
        </div>
        <div class="detail-item">
            <div class="detail-title">订单时间节点</div>
            <div>表格</div>
        </div>
        <div class="detail-item">
            <div class="detail-title">费用信息</div>
            <div>表格</div>
        </div>
        <div class="detail-item">
            <div class="detail-title">支付信息</div>
            <div>表格</div>
        </div>
        <div style="display: grid;gap: 30px;grid-template-columns: repeat(2, 1fr);">
            <div class="detail-item">
                <div class="detail-title">购买者信息</div>
                <div class="detail-cont"></div>
            </div>
            <div class="detail-item">
                <div class="detail-title">收货地址</div>
                <div class="detail-cont"></div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.order-detail{
    min-height: 100%;
    .tip-title{
        a{
            color: #333;
            cursor: pointer;
            &:hover{
                color: #3D9CFF;
            }
        }
    }
    .detail-item{
        padding: 15px 0;
        .detail-cont{
            border: 1px solid #E5E5E5;
            padding: 15px;
            min-height: 100px;
            border-radius: 10px;
        }
    }
    .detail-title{
        font-size: 18px;
        font-weight: bold;
        line-height: 30px;
        color: #333;
        position: relative;
        padding-left: 10px;
        margin-bottom: 15px;
        &:before{
            content: '';
            position: absolute;
            width: 4px;
            height: 16px;
            background: #3D9CFF;
            left: 0;
            top: 7px;
            border-radius: 2px;
            z-index: 1;
        }

    }
}

</style>
