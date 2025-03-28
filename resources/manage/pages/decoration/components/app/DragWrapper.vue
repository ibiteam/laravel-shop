<template>
    <div class="drag-wrapper">
        <div class="drag-content drag-item" :class="{ 'drag-select': select, 'drag-fixed': !show_select }">
            <div class="drag-hidden s-flex ai-ct jc-ct" v-if="!component.is_show" @click.stop="handleClickHiddenModel">已隐藏</div>
            <slot name="content"></slot>
            <div class="drag-tooltip" :class="{ 'select': select }" v-if="show_tooltip">{{ component.name }}</div>
            <div class="drag-tools s-flex ai-ct jc-bt flex-dir" v-if="select && show_select">
                <icon :name="component.is_show ? 'eye-o' : 'closed-eye'" class="icon" size="20px" @click.stop="handleClickTools({type: 'show'})"/>
                <icon name="delete-o" class="icon" size="20px" @click.stop="handleClickTools({type: 'remove'})"/>
                <icon name="arrow-up" class="icon" size="20px" v-if="parent_index > 0" @click.stop="handleClickTools({type: 'sort_up'})"/>
                <icon name="arrow-down" class="icon" size="20px" v-if="parent_index < parent.length - 1" @click.stop="handleClickTools({type: 'sort_down'})"/>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, getCurrentInstance } from 'vue'
import { Icon } from 'vant';

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    component: {
        type: Object,
        default: () => {
            return {}
        }
    },
    // 是否选中
    select: {
        type: Boolean,
        default: false
    },
    // 是否显示选中工具
    // 默认不显示
    show_select: {
        type: Boolean,
        default: false
    },
    // 是否显示提示
    // 默认显示
    show_tooltip: {
        type: Boolean,
        default: true
    },
    // 装修组件集合
    parent: {
        type: Array,
        default: []
    },
    // 装修组件当前索引
    parent_index: {
        type: Number,
        default: 0
    }
})

const emit = defineEmits(['hiddenModel'])

// 点击隐藏
// 触发父组件隐藏事件
const handleClickHiddenModel = () => {
    emit('hiddenModel')
}
// 点击工具按钮
const handleClickTools = (params) => {
    const { type } = params
    cns.$bus.emit('updateComponentData', {type, component: props.component})
}

</script>

<style lang='scss'>
.drag-wrapper {
    .drag-content {
        width: 375px;
        margin: 0 auto;
        box-sizing: content-box;
        border: 2px solid transparent;
        user-select: none;
        position: relative;
        &.drag-select {
            border-color: var(--main-color);
        }
        &:hover {
            box-shadow: 0 0 5px 0 var(--main-color-30);
            transform: scale(1.005);
            transition: all .2s;
        }
        &.drag-fixed {
            &:hover {
                box-shadow: none;
                transform: scale(1);
            }
        }
        .drag-hidden {
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,.5);
            color: #fff;
            position: absolute;
            top: 0;
            right: 0;
            z-index: 99;
        }
        .drag-tools {
            width: 44px;
            padding-bottom: 10px;
            border-radius: 4px;
            color: #fff;
            background-color: var(--main-color);
            cursor: pointer;
            position: absolute;
            top: 0;
            right: -50px;
            .icon{
                margin-top: 10px;
            }
        }
        .drag-tooltip {
            position: absolute;
            top: 0;
            background: #fff;
            left: -100px;
            width: 86px;
            height: 32px;
            text-align: center;
            line-height: 32px;
            font-size: 13px;
            color: #666;
            border-radius: 3px;
            &::before {
                content: "";
                position: absolute;
                width: 10px;
                height: 10px;
                background: #fff;
                -webkit-transform: rotate(45deg);
                transform: rotate(45deg);
                top: 50%;
                right: -5px;
                margin-top: -5px;
            }
            &.select {
                color: #fff;
                background: var(--main-color);
                &::before {
                    background: var(--main-color);
                }
            }
        }
    }
}
</style>