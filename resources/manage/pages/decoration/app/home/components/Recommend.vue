<template>
    <section>
        <drag-wrapper v-bind="{component: form, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="goods-recommend-you-wrapper" style="min-height: 100px;" @click="handleChooseDragItem">
                    <div class="recommend-wrapper">
                        <div class="recommend-title-wrapper">
                            {{form.content.title ? form.content.title : '为您推荐'}}
                        </div>
                        <div class="goods-wrapper2 s-flex ai-ct jc-bt flex-wrap" v-if="form.content.goods?.goods_data">
                            <div class="goods-item" v-for="item in form.content.goods.goods_data" :key="item.no">
                                <image-wrapper v-bind="{ src: item.image, width: '100%', height: '100%', radius: '10px 10px 0 0' }"/>
                                <div class="goods-info s-flex jc-bt flex-dir">
                                    <div class="goods-name elli-2 s-flex ai-ct fs13">
                                        <Tag color="linear-gradient(90deg, #5436D5 4%, #735CFF 99%)" v-if="item.label">{{item.label}}</Tag>
                                        {{item.name}}
                                    </div>
                                    <div class="s-flex ai-ct jc-bt">
                                        <GoodsPrice v-bind="{price: item.price, priceColor: '#f71111'}"></GoodsPrice>
                                        <span class="fs10 co-999" v-if="item.sales_volume">已售{{item.sales_volume}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </drag-wrapper>
        <teleport to="#decorationAppMain">
            <setting-bar v-bind="{name: form.name}" v-if="temp_index == form.id">
                <template #content>
                    <el-form lable-width="auto" :model="form.content" ref="templateSetForm">
                        <div class="setting-bar-item">
                            <div class="item-title">标题设置</div>
                            <el-form-item label="标题" label-position="top" :prop="'title'" required>
                                <el-input v-model="form.content.title" style="width: 70%;"></el-input>
                            </el-form-item>
                        </div>
                    </el-form>
                </template>
            </setting-bar>
        </teleport>
    </section>
</template>
<script setup>
import DragWrapper from '@/pages/decoration/components/app/DragWrapper.vue'
import ImageWrapper from '@/pages/decoration/components/app/ImageWrapper.vue'
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import GoodsPrice from '@/pages/decoration/components/GoodsPrice.vue'
import { Tag } from 'vant';
import { ref, reactive, watch, getCurrentInstance, onMounted, nextTick } from 'vue'
import { TempField, TempContentField } from '@/pages/decoration/app/home/dataField/Recommend.js'
import { decorationRecommendData } from '@/api/decoration.js'

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    component: {
        type: Object,
        default: () => {
            return {}
        }
    },
    temp_index: {
        type: [Number, String],
        default: ''
    },
    parent: {
        type: Array,
        default: []
    },
    parent_index: {
        type: Number,
        default: 0
    }
})

const form = reactive(TempField())
const templateSetForm = ref(null)
const handleChooseDragItem = () => {
    cns.$bus.emit('chooseDragItem', {temp_index: form.id})
}

const defaultRecommendData = ref([])

const updateRecommendComponentData = (goods_data) => {
    form.content['goods'] = {
        goods_data
    }
}

// 保存
const handleTempFormSubmit = () => {
    templateSetForm.value.validate((valid) => {
        if (valid) {

        }
    })
}

defineExpose({
    getComponentData() {
        return form
    },
    updateRecommendComponentData,
})

watch([() => props.component], (newValue) => {
    if (newValue[0]) {
        let temp = JSON.parse(JSON.stringify(newValue[0]))
        Object.keys(temp).forEach(key => {
            if (key == 'content') {
                form['content'] = {
                    title: temp[key].title ? temp[key].title : '为您推荐',
                    goods: {
                        goods_data: defaultRecommendData.value
                    }
                }
            } else {
                form[key] = temp[key]
            }

        })
    }
}, {
    immediate: true,
    deep: true
})

onMounted(() => {
    decorationRecommendData().then(res => {
        if (cns.$successCode(res.code)) {
            defaultRecommendData.value = res.data.list
            updateRecommendComponentData(res.data.list)
        }
    })
})

</script>

<style lang='scss' scoped>
.goods-recommend-you-wrapper{
    padding: 0 10px 5px;
    .recommend-wrapper {
        border-radius: 10px;
        // background-color: #fff;
        box-sizing: border-box;
    }
    .recommend-title-wrapper{
        width: fit-content;
        max-width: 100%;
        padding: 13px 33px;;
        margin: 0 auto;
        text-align: center;
        position: relative;
        &::before{
            content: '';
            display: block;
            width: 18px;
            height: 15px;
            background: url('@/assets/images/decoration/recommend-left.png') 100% no-repeat;
            background-size: cover;
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            margin: auto 0;
        }
        &::after{
            content: '';
            display: block;
            width: 18px;
            height: 15px;
            background: url('@/assets/images/decoration/recommend-right.png') 100% no-repeat;
            background-size: cover;
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto 0;
        }
    }
    .goods-wrapper2 {
        .goods-item {
            flex: 0 0 calc(50% - 5px);
            margin-bottom: 5px;
            border-radius: 10px;
            background-color: #fff;
            overflow: hidden;
            .goods-info {
                width: 100%;
                padding: 10px;
                box-sizing: border-box;
                .goods-name {
                    margin-bottom: 8px;
                }
            }
        }
    }
    .fs10 {
        font-size: 10px;
    }
}
.goods-form-wrapper {
    width: 100%;
    padding: 8px 0 8px 10px;
    background: #D8D8D8;
    border-radius: 5px;
    box-sizing: border-box;
    overflow: hidden;
    display: flex;
    gap: 10px;
    .goods-thumb-wrapper {
        flex: 0 0 calc(25% - 10px);
        aspect-ratio: 1/1;
        overflow: hidden;
        border-radius: 5px;
        position: relative;
        .goods-thumb-mask{
            width: 100%;
            height: 100%;
            text-align: center;
            color: #fff;
            background: rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }
    }
}
</style>