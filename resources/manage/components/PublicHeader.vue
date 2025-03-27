<template>
    <div class="public-header">
        <div class="public-inner s-flex ai-ct jc-bt">
            <div class="s-flex ai-ct">
                <div class="logo">
                    <a href="/" title="laravel-shop">
                        <img src="@/assets/images/logo.png" alt="laravel-shop">
                    </a>
                </div>
                <span class="title" v-if="title">{{ title }}</span>
            </div>
            <div>
                <slot name="right"></slot>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, getCurrentInstance } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

defineProps({
    title: {
        type: String,
        default: ''
    }
});
</script>

<style scoped lang="scss">
.public-header{
    height: 88px;
    box-shadow: 0 5px 10px rgba(0,0,0,.03);
    .public-inner{
        height: 100%;
    }
    .logo{
        height: 50px;
        img{
            height: 100%;
            width: auto;
        }
    }
    .title{
        margin-left: 30px;
        padding-left: 30px;
        border-left: 1px solid #999;
        line-height: 1;
        font-size: 16px;
    }
}
</style>
