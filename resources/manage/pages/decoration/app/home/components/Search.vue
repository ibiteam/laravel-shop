<template>
    <section class="bottom-navbar-wrapper">

        <setting-bar></setting-bar>
    </section>
</template>

<script setup>
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
</script>

<style lang='scss' scoped>
.bottom-navbar-wrapper{
    width: 375px;
    height: 50px;
    background-color: #fff;
    margin: 0 auto;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
}
</style>