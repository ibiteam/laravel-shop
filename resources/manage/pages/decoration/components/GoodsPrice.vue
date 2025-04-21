<template>
    <span>
        <span v-if="showPriceSymbol" :style="{ fontSize: unitFontSize + 'px', color: priceColor ? priceColor : 'var(--main-color)' }">{{ priceSymbol }}</span>
        <span :style="{ fontSize: priceFontSize + 'px', color: priceColor ? priceColor : 'var(--main-color)' }">{{ priceIntegerPart }}</span>
        <span :style="{ fontSize: decimalFontSize + 'px', color: priceColor ? priceColor : 'var(--main-color)'}">{{ decimalPart }}</span>
        <span v-if="showUnit" :style="{ fontSize: unitFontSize + 'px', color: unitColor ? unitColor : '#333'}">/{{ unit }}</span>
    </span>
</template>
  
<script setup>
import { computed } from 'vue';

const props = defineProps({
    // 价格
    price: {
        type: [String, Number],
        default: '0',
    },
    // 价格符号
    priceSymbol: {
        type: String,
        default: '￥',
    },
    // 价格单位
    unit: String,
    // 整数字体大小
    priceFontSize: {
        type: String,
        default: '15',
    },
    // 小数点字体大小
    decimalFontSize: {
        type: String,
        default: '11',
    },
    // 单位字体大小
    unitFontSize: {
        type: String,
        default: '11',
    },
    // 单位颜色
    unitColor: {
        type: String,
        default: '#333',
    },
    // 价格颜色
    priceColor: String,
});

const decimalPart = computed(() => {
    const decimalIndex = props.price.toString().indexOf('.');
    return decimalIndex !== -1 ? '.'+props.price.toString().substring(decimalIndex + 1) : '.00';
});

const priceIntegerPart = computed(() => {
    const intergerIndex = props.price.toString().indexOf('.');
    return intergerIndex !== -1 ? props.price.toString().substring(0, props.price.toString().indexOf('.')) : props.price.toString();
});

const showPriceSymbol = computed(() => props.priceSymbol && props.priceSymbol !== '');

const showUnit = computed(() => props.unit && props.unit !== '');
</script>
  
<style scoped>
</style>