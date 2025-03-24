<template>
    <div class="public-footer">
        <div class="wrap" style="border-top: 0px;">
            <div style=" line-height: 30px;text-align:center;">
                <span>Copyright ©2015 - 现在 北京涂多多电子商务股份有限公司 版权所有</span>
            </div>
        </div>
    </div>
</template>

<script setup>

</script>

<style scoped>
.public-footer {
    width: 1440px;
    margin: 0 auto;
    padding-bottom: 42px;
}

.public-footer .wrap p a {
    font-size: 14px;
    text-decoration: none;
    color: #777;
    line-height: 30px;
    display: inline-block;
}

.public-footer .wrap p span {
    margin: 0 5px 0 2px;
    transform: scale(0.7);
    display: inline-block;
    color: #777;
    line-height: 30px;
}

.public-footer .wrap p:first-of-type span {
    position: relative;
    top: 1px;
}

.public-footer .wrap p a:hover {
    color: var(--main-color);
}

.public-footer .wrap {
    overflow: inherit;
    text-align: center;
    height: auto;
}

.public-footer .wrap > div {
    text-align: center;
    width: 100%;
}

.public-footer .wrap > div span {
    color: #777;
    font-size: 14px;
    display: inline-block;
    height: 20px;
    padding: 0 20px;
}

.public-footer .wrap ul {
    padding-top: 15px;
    list-style: none;
    max-width: 100%;
    display: inline-block;
}

.public-footer .wrap ul li {
    float: left;
}

.public-footer .wrap ul li img {
    width: auto;
    height: 36px;
    margin: 0 12px;
}
</style>
