<template>
    <section>
        <drag-wrapper v-bind="{component: form, select: temp_index == form.id, show_select: true, parent, parent_index}" @hiddenModel="handleChooseDragItem">
            <template #content>
                <div class="goods-recommend-wrapper" style="min-height: 100px;" @click="handleChooseDragItem">
                    <div class="recommend-wrapper" v-if="!form.content.goods?.goods_data || !form.content.goods.goods_data?.length">
                        <div class="goods-wrapper1">
                            <div class="goods-item s-flex ai-fs jc-bt">
                                <image-wrapper v-bind="{ src: '', width: '130px', height: '130px', radius: '10px' }"/>
                                <div class="goods-info s-flex jc-bt flex-dir">
                                    <div class="goods-name elli-2 s-flex ai-ct fs13">
                                        <Tag color="linear-gradient(90deg, #5436D5 4%, #735CFF 99%)">商品标签</Tag>
                                        商品名称
                                    </div>
                                    <div class="goods-subname elli-1">
                                        商品副标题
                                    </div>
                                    <GoodsPrice v-bind="{price: '199.00', priceColor: '#f71111'}"></GoodsPrice>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="recommend-wrapper" v-if="form.content.goods.goods_data && form.content.goods.goods_data.length">
                        <div class="recommend-title-wrapper s-flex" :class="form.content.title.align == 'center' ? 'jc-ct' : 'jc-fs'" style="margin-bottom: 10px;">
                            <div class="ad-title s-flex ai-ct jc-ct" >
                                <image-wrapper v-if="form.content.title.image" v-bind="{ src: form.content.title.image, width: '16px', height: '16px', radius: '0' }" style="margin-right: 6px;"/>
                                <span class="fs14 fw-b" :style="{color: form.content.title.color}">{{form.content.title.name}}</span>
                            </div>
                            <div class="recommend-title-link s-flex ai-ct fs12">
                                {{ form.content.title.suffix }}<em class="iconfont icon-gengduo"  v-if="form.content.title.url.value" style="font-size: 12px;"></em>
                            </div>
                        </div>
                        <div class="goods-wrapper1" v-if="form.content.layout == 1 && form.content.goods.goods_data">
                            <div class="goods-item s-flex ai-fs jc-bt" v-for="item in form.content.goods.goods_data" :key="item.no">
                                <image-wrapper v-bind="{ src: item.image, width: '130px', height: '130px', radius: '10px' }"/>
                                <div class="goods-info s-flex jc-bt flex-dir">
                                    <div class="goods-name elli-2 s-flex ai-ct fs13">
                                        <Tag color="linear-gradient(90deg, #5436D5 4%, #735CFF 99%)" v-if="item.label">{{item.label}}</Tag>
                                        {{item.name}}
                                    </div>
                                    <div class="goods-subname elli-1" v-if="item.sub_name">
                                        {{item.sub_name}}
                                    </div>
                                    <GoodsPrice v-bind="{price: item.price, priceColor: '#f71111'}"></GoodsPrice>
                                </div>
                            </div>
                        </div>
                        <div class="goods-wrapper2 s-flex ai-ct jc-bt flex-wrap" v-if="form.content.layout == 2 && form.content.goods.goods_data">
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
                        <div class="goods-wrapper3 s-flex ai-ct jc-fs flex-wrap" v-if="form.content.layout == 3 && form.content.goods.goods_data">
                            <div class="goods-item" v-for="item in form.content.goods.goods_data" :key="item.no">
                                <image-wrapper v-bind="{ src: item.image, width: '100%', height: '100%', radius: '10px 10px 0 0' }"/>
                                <div class="goods-info s-flex jc-bt flex-dir">
                                    <div class="goods-name elli-2 s-flex ai-ct fs13">
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
                            <div class="item-title">显示设置</div>
                            <el-form-item label="商品布局" label-position="top" :prop="'layout'" required>
                                <el-radio-group v-model="form.content.layout" fill="var(--main-color)">
                                    <el-radio v-for="layout in LayoutOption" :value="layout.value" :key="layout.value">{{layout.label}}</el-radio>
                                </el-radio-group>
                            </el-form-item>
                        </div>
                        <div class="setting-bar-item">
                            <div class="item-title">标题设置</div>
                            <el-form-item label="小图标" label-position="top" :prop="['title', 'image']">
                                <div>
                                    <ImageUpload
                                        :src="form.content.title.image"
                                        @material="() => {
                                            handleOpenUpload(['title', 'image'])
                                        }"
                                        @local="(image) => {
                                            form.content.title.image = image
                                        }"
                                        @remove="() => {
                                            form.content.title.image = ''
                                        }"
                                    />
                                    <p class="item-title-info" style="margin-bottom: 0;">建议尺寸：32 * 32</p>
                                </div>
                            </el-form-item>
                            <el-form-item label="标题" label-position="top" :prop="['title', 'name']">
                                <el-input v-model="form.content.title.name" style="width: 70%;"></el-input>
                                <el-color-picker v-model="form.content.title.color" @change="() => {
                                    if (!form.content.title.color) {
                                        form.content.title.color = '#333333'
                                    }
                                }"/>
                            </el-form-item>
                            <el-form-item label="对齐方式" label-position="top" :prop="['title', 'align']">
                                <el-radio-group v-model="form.content.title.align">
                                    <el-tooltip
                                        v-for="align in TitleAlignOption"
                                        :key="align.value"
                                        effect="dark"
                                        :content="align.label"
                                        placement="bottom"
                                    >
                                        <el-radio-button  :value="align.value" size="small">
                                            <template #default>
                                                <em :class="align.icon"></em>
                                            </template>
                                        </el-radio-button>
                                    </el-tooltip>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="右侧文字" label-position="top" :prop="['title', 'suffix']">
                                <el-input v-model="form.content.title.suffix" style="width: 70%;" placeholder="如：更多"></el-input>
                            </el-form-item>
                            <el-form-item label="链接" label-position="top" :prop="['title', 'url', 'value']">
                                <LinkInput
                                    style="width: 70%;"
                                    :name="form.content.title.url.name"
                                    :value="form.content.title.url.value"
                                    @select="handleOpenLink(['title', 'url'])"
                                    @input="(res) => {
                                        form.content.title.url = res
                                    }"
                                    @clear="(res) => {
                                        form.content.title.url = res
                                    }"
                                />
                            </el-form-item>
                        </div>
                        <div class="setting-bar-item">
                            <div class="item-title">商品设置</div>
                            <el-form-item label="推荐规则" label-position="top" :prop="['goods', 'rule']" required @change="getIntelligentRecommendData">
                                <el-radio-group v-model="form.content.goods.rule" fill="var(--main-color)">
                                    <el-radio v-for="rule in RuleOption" :value="rule.value" :key="rule.value">{{rule.label}}</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <template v-if="form.content.goods.rule == 1">
                                <el-form-item label="排序类型" :prop="['goods', 'sort_type']">
                                    <el-select v-model="form.content.goods.sort_type" style="width: 70%;" @change="getIntelligentRecommendData">
                                        <el-option v-for="sort_type in SortTypeOption" :key="sort_type.value" :value="sort_type.value" :label="sort_type.label"></el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item class="not-required" label="数量限制" :prop="['goods', 'number']" :rules="
                                    [
                                        { required: true, message: '请输入数量限制', trigger: 'blur' },
                                        { validator: (rule, value, callback) => {
                                            if (value < RecommendGoodsNumber.min || value > RecommendGoodsNumber.max) {
                                                callback(new Error(`数量限制在${RecommendGoodsNumber.min}~${RecommendGoodsNumber.max}`));
                                            } else if (isNaN(value)) {
                                                callback(new Error('请输入数字'));
                                            } else if (!Number.isInteger(value * 1)) {
                                                callback(new Error('请输入整数'));
                                            } else {
                                                getIntelligentRecommendData()
                                                callback();
                                            }
                                        }, trigger: 'blur' },
                                    ]
                                ">
                                    <el-input v-model="form.content.goods.number" style="width: 70%;"></el-input>
                                </el-form-item>
                            </template>
                            <template v-else>
                                <el-form-item label="" label-position="top" :prop="['goods', 'goods_nos']" :rules="[
                                    { required: true, message: '最少选择1个商品', trigger: 'change' },
                                    {
                                        validator: (rule, value, callback) => {
                                            if (value.length <= 0) {
                                                callback(new Error('最少选择1个商品'));
                                            } else if (value.length > 20) {
                                                callback(new Error('最多选择20个商品'));
                                            } else { callback(); }
                                        }, trigger: 'change'
                                    },
                                ]">
                                    <div class="goods-form-wrapper" v-if="form.content.goods.goods_data && form.content.goods.goods_data.length">
                                        <div class="goods-thumb-wrapper" v-for="(item,index) in form.content.goods.goods_data" :key="item.no">
                                            <image-wrapper v-bind="{ src: item.image, width: '100%', height: '100%' }" style="z-index: 2;"/>
                                            <div class="goods-thumb-mask s-flex ai-ct jc-ct" v-if="index == 3 && form.content.goods.goods_nos.length > 4">+{{ form.content.goods.goods_nos.length - 4 }}</div>
                                        </div>
                                    </div>
                                </el-form-item>
                                <el-button type="primary" style="width: 100%;" :disabled="form.content.goods.goods_data.length >= MaxGoodsNumber" @click="handleAddGoods">添加({{form.content.goods.goods_data.length}}/{{MaxGoodsNumber}})</el-button>
                            </template>
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
import ImageUpload from '@/pages/decoration/components/ImageUpload.vue'
import LinkInput from '@/pages/decoration/components/LinkInput.vue'
import GoodsPrice from '@/pages/decoration/components/GoodsPrice.vue'
import { Tag } from 'vant';
import { ref, reactive, watch, getCurrentInstance } from 'vue'
import { TempField, LayoutOption, TitleAlignOption, MaxGoodsNumber, RuleOption, SortTypeOption, RecommendGoodsNumber } from '@/pages/decoration/app/home/dataField/GoodsRecommend.js'
import { updateNested } from '@/pages/decoration/utils/common.js'
import Http from '@/utils/http'

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
// 通知打开选择图片弹窗
const handleOpenUpload = (keys) => {
    cns.$bus.emit('openUploadDialog', {temp_index: form.id, keys, show: true, dir_type: 1, multiple: false})
}
// 更新上传图片数据
const updateUploadComponentData = (res) => {
    form.content = updateNested(form.content, res.keys, res.file[0].file_path)
}

// 通知打开选择路由弹窗
const handleOpenLink = (keys) => {
    cns.$bus.emit('openLinkDialog', {temp_index: form.id, keys, show: true})
}

// 更新选择路由数据
const updateLinkComponentData = (res) => {
    form.content = updateNested(form.content, res.keys, res.link)
}

// 添加商品数据
const handleAddGoods = () => {
    if (form.content.goods.goods_nos.length >= MaxGoodsNumber) return
    cns.$bus.emit('openGoodsDialog', {temp_index: form.id, show: true, max: MaxGoodsNumber, default_goods: form.content.goods.goods_data, default_nos: form.content.goods.goods_nos})
}

// 更新商品数据
const updateGoodsComponentData = (res) => {
    let nos = []
    new Array(...res.goods).forEach(item => {
        nos.push(item.no)
    })
    form.content.goods.goods_nos = nos
    form.content.goods.goods_data = res.goods
}

const getIntelligentRecommendData = () => {
    if (form.content.goods.rule === '') return
    if (form.content.goods.rule == 1) {
        Http.doPost('app_decoration/goods/intelligent', {sort_type: form.content.goods.sort_type, number: form.content.goods.number}).then(res => {
            if (cns.$successCode(res.code)) {
                form.content.goods['goods_data'] = res.data
            }
        })
    } else {
        form.content.goods.goods_data = []
        form.content.goods['goods_nos'] = []
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
    updateUploadComponentData,
    updateLinkComponentData,
    updateGoodsComponentData,
})

watch([() => props.component], (newValue) => {
    if (newValue[0]) {
        let temp = JSON.parse(JSON.stringify(newValue[0]))
        Object.keys(temp).forEach(key => {
            form[key] = temp[key]
        })
        if (form.content.goods?.sort_type == 1) {
            getIntelligentRecommendData()
        }
    }
}, {
    immediate: true,
    deep: true
})

</script>

<style lang='scss' scoped>
.goods-recommend-wrapper{
    padding: 5px 10px 5px;
    .recommend-wrapper {
        border-radius: 10px;
        padding: 10px 10px 0;
        background-color: #fff;
        box-sizing: border-box;
    }
    .recommend-title-wrapper {
        position: relative;
        .recommend-title-link {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto 0;
        }
    }
    .goods-wrapper1 {
        padding-bottom: 10px;
        .goods-item {
            height: 130px;
            margin-bottom: 15px;
            overflow: hidden;
            &:last-child {
                margin-bottom: 0;
            }
        }
        .goods-info {
            width: calc(100% - 130px);
            height: 100%;
            padding-left: 10px;
        }
        .goods-subname {
            color: #f71111;
            width: 100%;
        }
    }
    .goods-wrapper2 {
        .goods-item {
            flex: 0 0 calc(50% - 5px);
            // width: 175px;
            border-radius: 10px 10px 0 0;
            overflow: hidden;
            .goods-info {
                width: 100%;
                padding: 10px;
                box-sizing: border-box;
                .goods-name {
                    height: 36px;
                    margin-bottom: 8px;
                }
            }
        }
    }
    .goods-wrapper3 {
        .goods-item {
            margin-bottom: 5px;
            flex: 0 0 calc(33.33% - 5px);
            border-radius: 10px 10px 0 0;
            overflow: hidden;
            .goods-info {
                width: 100%;
                padding: 5px 0;
                box-sizing: border-box;
                .goods-name {
                    height: 36px;
                    margin-bottom: 4px;
                }
            }
        }
        .goods-item:nth-child(3n+2) {
            margin-left: 7.5px;
            margin-right: 7.5px;
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
