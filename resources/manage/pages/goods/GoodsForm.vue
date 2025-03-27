<template>
    <div class="flex-content flex-1">
        <div class="container">
            <div class="update-box s-flex">
                <div class="good-form">
                    <el-form ref="updateFormRef" :model="updateForm" :rules="updateFormRules" label-width="120px">
                        <el-form-item label="分类" prop="goods_cate_id">
                            <el-select v-model="updateForm.goods_cate_id" placeholder="请选择分类">
                                <el-option :label="its.label" :value="its.value" v-for="its in category"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="商品名称" prop="goods_name">
                            <el-input v-model="updateForm.goods_name" placeholder="请输入商品名称" style="width: 95%;"></el-input>
                        </el-form-item>
                        <el-form-item label="商品标签" prop="tag">
                            <el-input v-model="updateForm.tag" :maxlength="5" show-word-limit placeholder="可填写热卖，推荐等" style="width: 95%;"></el-input>
                            <el-popover
                                placement="right"
                                title=""
                                width="auto"
                                trigger="hover"
                                content="用于在商品名称前加一个标记">
                                <template #reference>
                                    <em class="iconfont" style="cursor: pointer;">&#xe72d;</em>
                                </template>
                            </el-popover>
                        </el-form-item>
                        <el-form-item label="商品副标题" prop="goods_brief">
                            <el-input v-model="updateForm.goods_brief" style="width: 95%;" placeholder="请输入商品副标题"></el-input>
                            <el-popover
                                placement="right"
                                title=""
                                width="auto"
                                trigger="hover"
                                content="商品详情">
                                <template #reference>
                                    <em class="iconfont" style="cursor: pointer;">&#xe72d;</em>
                                </template>
                            </el-popover>
                        </el-form-item>
                        <el-form-item label="自定义属性">
                            <template v-if="updateForm.goods_attr.length">
                                <div class="input-li s-flex ai-ct" v-for="(item,index) in updateForm.goods_attr" :key="index" style="margin-bottom: 10px;">
                                    <div style="width: 95%;" class="s-flex jc-bt ai-ct">
                                        <el-input v-model="item.attr_name" placeholder="请输入属性名" style="width: 48%;" :maxlength="5" show-word-limit></el-input>
                                        <el-input v-model="item.attr_value" placeholder="请输入属性值" style="width: 48%;" :maxlength="20"></el-input>
                                    </div>
                                    <div class="dels" style="margin-left: 5px;" @click="delGoodsAttr(index)">
                                        <span>删除</span>
                                    </div>
                                </div>
                            </template>
                            <div style="width: 100%;">
                                <el-button type="primary" size="small" @click="addGoodsAttr">添加</el-button>
                                <div class="tips">
                                    <span>可设置自定义属性，如内存：8G</span>
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item label="图片" prop="goods_img">
                            <div class="s-flex">
                                <VueDraggable class="good-picture s-flex" v-model="updateForm.goods_img">
                                    <div class="good-picture-list" v-for="(its,ids) in updateForm.goods_img" :key="ids">
                                        <div class="good-picture-li">
                                            <img :src="its" alt="its">
                                            <div class="masking">
                                                <i class="el-icon-delete" @click="updateForm.goods_img.splice(ids,1)"></i>
                                            </div>
                                        </div>
                                        <div class="main" :class="{'main-img' : !ids}" @click="setMain(ids)">
                                            <span v-if="!ids">主图</span>
                                            <span v-else>设为主图</span>
                                        </div>
                                    </div>
                                </VueDraggable>
                                <el-upload
                                    v-if="updateForm.goods_img.length < 6"
                                    class="avatar-uploader"
                                    action="{{ route('manage.common.upload') }}"
                                    :show-file-list="false"
                                    multiple
                                    :on-success="(res) => handleSuccess(res,'goods_img')"
                                    :on-exceed="handleExceed"
                                    :before-upload="beforeUpload">
                                    <i class="el-icon-plus avatar-uploader-icon"></i>
                                </el-upload>
                            </div>
                            <div class="tips">
                                <span>建议尺寸500*500px，最多6张</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="单位" prop="unit">
                            <el-input v-model="updateForm.unit" placeholder="请输入商品单位" style="width: 160px;"></el-input>
                            <div class="tips">
                                <span>选填，可输入件，公斤等，多单位可在下面商品规格名中标注</span>
                            </div>
                        </el-form-item>
                        <el-form-item>
                            <template #label>
                                商品规格
                                <el-popover
                                    placement="right"
                                    title=""
                                    width="auto"
                                    trigger="hover"
                                    content="可添加多个规格属性的商品">
                                    <template #reference>
                                        <em class="iconfont" style="cursor: pointer;">&#xe72d;</em>
                                    </template>
                                </el-popover>
                            </template>
                            <div class="specifications" :class="{ 's-flex' : !goods_specs_template.values.length , 'jc-bt' : !goods_specs_template.values.length , 'fd-rs' : !goods_specs_template.values.length }">
                                <div class="specifications-select s-flex jc-fe">
                                    <el-select placeholder="请选择" style="width: 160px;" ref="mySelectRef">
                                        <el-option v-for="(item,index) in specificationsArr" :key="item.id">
                                            <template #default>
                                                <div class="option-li s-flex jc-bt ai-bs" @click="chooseSpecs(index)">
                                                    <span>@{{ item.name }}</span>
                                                    <el-tag effect="dark" @click.native.stop="delSelect(index)" style="float: right; margin-top: 8px; margin-left: 3px">
                                                        <em class="iconfont">&#xe8b6;</em>
                                                    </el-tag>
                                                </div>
                                            </template>
                                        </el-option>
                                    </el-select>
                                </div>
                                <div class="specifications-box" v-if="goods_specs_template.values.length">
                                    <el-form ref="templateFormRef" :model="goods_specs_template" :rules="templateRules">
                                        <el-card class="specifications-list"
                                                 v-for="(item,index) in goods_specs_template.values" :key="index">
                                            <div class="s-flex jc-fe">
                                                <div style="cursor: pointer;" @click="delGoodsSpecs(index)">
                                                    <em class="iconfont" style="font-size: 14px;">&#xe686;</em>
                                                </div>
                                            </div>
                                            <div class="specifications-content s-flex jc-bt">
                                                <div class="left">
                                                    <div class="label">
                                                        <span>名称</span>
                                                    </div>
                                                    <el-form-item :prop="'values.' + index + '.spec_name'">
                                                        <el-input v-model="item.spec_name"
                                                                  placeholder="请输入内容"></el-input>
                                                    </el-form-item>
                                                    <div class="tips">
                                                        <span>请输入规格名称</span>
                                                    </div>
                                                </div>
                                                <div class="right">
                                                    <div class="label">
                                                        <span>名称</span>
                                                    </div>
                                                    <div class="specifications-input">
                                                        <template v-for="(its,ids) in item.spec_value">
                                                            <el-form-item
                                                                :prop="'values.' + index + '.spec_value.' + ids + '.spec_value_name'">
                                                                <el-input v-model="its.spec_value_name"
                                                                          placeholder="请输入规格项"
                                                                          maxlength="10"
                                                                          style="width: 120px;margin-right: 10px;"
                                                                          :key="ids"><i slot="suffix"
                                                                                        style="font-size: 12px;cursor: pointer;"
                                                                                        class="iconfont"
                                                                                        @click="delSpecs(index,ids)">&#xe686;</i>
                                                                </el-input>
                                                            </el-form-item>
                                                        </template>
                                                        <template v-if="goods_specs_template.values[index].spec_value.length < 6">
                                                            <el-popover
                                                                placement="right"
                                                                title=""
                                                                width="auto"
                                                                trigger="hover"
                                                                :disabled="computedSpecs(index)"
                                                                content="请填写完当前规格项">
                                                                <template #reference>
                                                                    <el-link type="primary" :underline="false" icon="el-icon-plus" :disabled="!computedSpecs(index)" @click="addSpecs(index)">新增规格项</el-link>
                                                                </template>

                                                            </el-popover>
                                                        </template>
                                                    </div>
                                                    <div class="tips">
                                                        <span>规格项最长为10个字，最多可添加6个规格项。</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </el-card>
                                    </el-form>
                                </div>
                                <div class="specifications-btn">
                                    <el-button type="primary" size="small" @click="addGoodsSpecs()"
                                               v-if="goods_specs_template.values.length < 3">添加</el-button>
                                    <template v-if="goods_specs_template.values.length">
                                        <el-button type="primary" size="small" v-if="!goods_specs_template.id"
                                                   @click="updaterTemplate()">保存模板</el-button>
                                        <el-button type="primary" size="small" @click="updaterTemplate()"
                                                   v-else>更新模板</el-button>
                                    </template>
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item label="销售规格" v-if="goods_specs_template.values.length">
                            <div class="more-input s-flex jc-fe">
                                <div class="more-li">
                                    <label>
                                        <span>价格</span>
                                    </label>
                                    <el-input v-model="moreInput.shop_price" style="width: 80px;" placeholder="" @input="moreInput.shop_price = formatInput(moreInput.shop_price)"></el-input>
                                </div>
                                <div class="more-li">
                                    <label>
                                        <span>库存</span>
                                    </label>
                                    <el-input v-model="moreInput.number" style="width: 80px;" placeholder="" @input="moreInput.number = formatInput(moreInput.number)"></el-input>
                                </div>
                                <el-button @click="filling()">批量填充</el-button>
                            </div>
                            <el-table
                                :data="updateForm.goods_skus"
                                :span-method="objectSpanMethod"
                                border
                                style="width: 100%; margin-top: 20px">
                                <el-table-column
                                    v-for="(its,ids) in goods_specs_template.values"
                                    :prop="`template_${ids + 1}`"
                                    :label="its.spec_name?its.spec_name:'--'"
                                    :width="80">
                                    <template slot-scope="scope">
                                        <span>@{{ scope.row[`template_${ids + 1}`]?scope.row[`template_${ids + 1}`]:'--' }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="thumb"
                                    label="颜色图片"
                                    :width="120">
                                    <template slot-scope="scope">
                                        <el-upload
                                            action="{{ route('manage.common.upload') }}"
                                            class="table-upload"
                                            :show-file-list="false"
                                            v-if="!scope.row.thumb"
                                            :on-success="(res) => handleSuccess(res,'thumb',scope.row.template_1)"
                                            :before-upload="beforeUpload">
                                            <i slot="default" class="el-icon-plus avatar-uploader-icon"></i>
                                        </el-upload>
                                        <div v-else class="thumb">
                                            <img :src="scope.row.thumb" alt="">
                                            <span class="el-upload-list__item-actions">
                                                <i class="el-icon-delete" @click="handleTableRemove(scope.$index)"></i>
                                            </span>
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    label="价格">
                                    <template slot-scope="scope">
                                        <el-form-item :prop="'goods_skus.' + scope.$index + '.shop_price'"
                                                      :rules="more_integralPrice(scope.$index)">
                                            <el-input v-model="scope.row.shop_price" placeholder=""
                                                      @input="scope.row.shop_price = formatInput(scope.row.shop_price)"></el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    label="库存">
                                    <template slot-scope="scope">
                                        <el-form-item :prop="'goods_skus.' + scope.$index + '.number'"
                                                      :rules="updateFormRules.number">
                                            <el-input v-model="scope.row.number" placeholder=""
                                                      @input="scope.row.number = formatInput(scope.row.number)"></el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    label="是否显示"
                                    :width="80">
                                    <template slot-scope="scope">
                                        <el-switch
                                            v-model="scope.row.is_show"
                                            :active-value="1"
                                            :inactive-value="0"
                                            active-color="#13ce66"
                                            inactive-color="#ff4949">
                                        </el-switch>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <div class="tips" v-if="goods_specs_template.values.length">
                                <span>给第一组规格设置图片，用户选择不同规格会看到对应规格图片，建议尺寸：80 x 80 px</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="价格" v-else>
                            <div class="s-flex ai-ct">
                                <el-form-item prop="shop_price">
                                    <el-input style="width: 100px;" v-model="updateForm.shop_price" @input="updateForm.shop_price = formatInput(updateForm.shop_price)" placeholder=""><span slot="suffix">元</span></el-input>
                                </el-form-item>
                            </div>
                        </el-form-item>
                        <el-form-item label="库存" prop="goods_number">
                            <el-input-number v-model="updateForm.goods_number" :disabled="!!updateForm.goods_skus.length"
                                             :min="1" style="width: 160px;"></el-input-number>
                            <div class="tips" v-if="goods_specs_template.values.length">
                                <span>多规格商品库存为所有SKU的库存总和</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="商品详情" prop="goods_desc">
                            <Editor v-model="updateForm.goods_desc" @change="handleChangeGoodsDetail" height="500px" min-height="500px" />
                        </el-form-item>
                        <el-form-item label="上架" prop="is_on_sale">
                            <el-radio-group v-model="updateForm.is_on_sale">
                                <el-radio :value="1">立即上架</el-radio>
                                <el-radio :value="0">放入仓库</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="限购" prop="limit_number" class="limit-number">
                            <div>
                                <el-radio-group v-model="is_limit_number" @change="changeLimitNumber">
                                    <el-radio :value="false">不限购</el-radio>
                                    <el-radio :value="true">限购</el-radio>
                                </el-radio-group>
                                <div v-if="is_limit_number">
                                    <el-input-number v-model="updateForm.limit_number" :min="1"></el-input-number>
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" style="width: 120px;height: 40px;" @click="sumbitGood" :loading="loading">提交</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Editor from '@/components/good/Editor.vue'
import { ref, getCurrentInstance, onMounted, computed, watch } from 'vue'
import { VueDraggable } from 'vue-draggable-plus'
import _ from 'lodash'
const cns = getCurrentInstance().appContext.config.globalProperties
const validateFile = (rule, value, callback) => {
    if (!updateForm.value.goods_img.length) {
        callback(new Error('请上传商品图片'));
    } else {
        callback();
    }
}

const validatePrice = (rule, value, callback, type) => {
    if (updateForm.value.goods_skus.length) {
        const checkPrices = (data) => {
            return data.filter(item => {
                const shopPrice = parseFloat(item.shop_price) || 0;
                return shopPrice < 0
            });
        };
        const invalidItems = checkPrices(updateForm.value.goods_skus);
        if (invalidItems.length) {
            callback(new Error('价格不能小于0'));
        } else {
            callback();
        }
    } else {
        callback();
    }
}

const validateDesc = (rule, value, callback) => {
    if (updateForm.value.goods_desc == '' || updateForm.value.goods_desc == '<p style="color: rgb(51, 51, 51); line-height: 2;"><br></p>') {
        callback(new Error('商品详情不能为空'));
    } else {
        callback();
    }
}

const specValue = (rule, value, callback, index, id) => {
    let arr = goods_specs_template.value.values[index].spec_value
    const hasDuplicates = arr.reduce((acc, current) => {
        if (acc.names[current.spec_value_name]) {
            acc.hasDuplicates = true;
        } else {
            acc.names[current.spec_value_name] = true;
        }
        return acc;
    }, {names: {}, hasDuplicates: false}).hasDuplicates;
    if (hasDuplicates) {
        callback(new Error('规格项重复'));
    } else {
        callback();
    }
}

const category = ref([]);
const updateForm = ref({
    id: 0,
    goods_cate_id: null,
    member_id: null,
    goods_name: '',
    tag: '',
    goods_brief: '',
    goods_attr: [],
    goods_img: [],
    unit: '',
    shop_price: 0,
    goods_number: 10,
    goods_desc: '',
    is_on_sale: 1,
    limit_number: 0,
    goods_skus: [],
    goods_specs: [],
});
const updateFormRef = ref(null);
const templateFormRef = ref(null);
const mySelectRef = ref(null);
const updateFormRules = ref({
    goods_cate_id: [
        {
            required: true,
            message: '请选择商品分类',
            trigger: 'change'
        },
    ],
    goods_name: [
        {required: true, message: '请输入商品名称', trigger: 'blur'},
    ],
    goods_img: [
        {required: true, message: '请上传商品图片', trigger: 'change'},
        {validator: validateFile, trigger: 'change'}
    ],
    goods_number: [
        {
            required: true,
            message: '请输入商品库存',
            trigger: 'blur'
        },
    ],
    goods_desc: [
        {
            required: true,
            message: '请输入商品详情',
            trigger: 'blur'
        },
        {validator: validateDesc, trigger: 'blur'}
    ],
    is_on_sale: [
        {
            required: true,
            message: '请选择是否上架',
            trigger: 'change'
        },
    ],
    limit_number: [
        {
            required: true,
            message: '请输入限购数量',
            trigger: 'blur'
        },
    ],
    shop_price: [
        {
            required: false,
            message: '请输入价格',
            trigger: 'blur'
        },
        {
            validator: (rule, value, callback) => validatePrice(rule, value, callback, 'shop_price'),
            trigger: 'blur'
        }
    ],
    number: [
        {
            required: true,
            message: '请输入商品库存',
            trigger: 'blur'
        },
    ],
});
const shop_price_show = ref(false);
const is_limit_number = ref(false);
const specificationsArr = ref([]);
const goods_specs_template = ref({
    name: '',
    template_id: null,
    values: [],
});
const moreInput = ref({
    shop_price: '',
    number: ''
});
const loading = ref(false);

const templateRules = computed(() => {
    const rules = {};
    goods_specs_template.value.values.forEach((field, index) => {
        rules[`values.${index}.spec_name`] = [
            {
                required: true,
                message: '请输入规格名称',
                trigger: 'blur'
            }
        ];
        field.spec_value.forEach((fd, id) => {
            rules[`values.${index}.spec_value.${id}.spec_value_name`] = [
                {
                    required: true,
                    message: '请输入规格项',
                    trigger: 'blur'
                },
                {
                    validator: (rule, value, callback) => specValue(rule, value, callback, index, id),
                    trigger: 'blur'
                }
            ];
        })
    });
    return rules;
});

watch(() => goods_specs_template.value.values, _.debounce((newVal, oldVal) => {
    if (newVal.length) {
        toTableArray(newVal)
    } else {
        updateForm.value.goods_skus = []
        updateForm.value.goods_specs = []
    }
}, 500), { deep: true, immediate: false });

watch(() => updateForm.value.shop_price, (val) => {
    if (val && val > 0) {
        shop_price_show.value = true
    } else {
        shop_price_show.value = false
    }
});

watch(() => updateForm.value.goods_skus, (val) => {
    if (val.length) {
        let numberArr = Array.from(val, a => a.number)
        let sum = numberArr.reduce((accumulator, currentValue) => Number(accumulator) + Number(currentValue), 0);
        updateForm.value.goods_number = sum
    }
}, { deep: true, immediate: false });

const more_integralPrice = (index) => {
    let skus = updateForm.value.goods_skus[index]
    const prop2 = `goods_skus.${index}.shop_price`;
    return [
        {
            validator: (rule, value, callback) => {
                if (Number(skus.shop_price) < 0) {
                    callback(new Error(`价格必须大于0`));
                } else {
                    updateFormRef.value.clearValidate(prop2);
                    callback();
                }
            },
            trigger: 'blur',
        },
    ]
}

const getTemplate = () => {
    cns.$http.doGet('front.sku_template.index').then(res => {
        if (res.code === 200) {
            specificationsArr.value = [...res.data]
        }
    })
}
const handleChangeGoodsDetail = () => {
    updateFormRef.value.validateField('goods_desc')
}

const changeLimitNumber = (val) => {
    if (val) {
        updateForm.value.limit_number = 1
    } else {
        updateForm.value.limit_number = 0
    }
}

const addGoodsAttr = () => {
    updateForm.value.goods_attr.push({
        attr_name: '',
        attr_value: ''
    })
}

const delGoodsAttr = (index) => {
    updateForm.value.goods_attr.splice(index, 1)
}

const delGoodsSpecs = (index) => {
    goods_specs_template.value.values.splice(index, 1)
}

const addGoodsSpecs = () => {
    goods_specs_template.value.values.push({
        spec_name: '',
        spec_id: '',
        spec_value: [
            {
                spec_value_name: '',
                spec_value_id: ''
            }
        ]
    })
}

const computedSpecs = (index) => {
    return goods_specs_template.value.values[index].spec_value.every(a => a.spec_value_name)
}

const addSpecs = (index) => {
    if (computedSpecs(index) && goods_specs_template.value.values[index].spec_value.length < 6) {
        goods_specs_template.value.values[index].spec_value.push({
            spec_value_name: '',
            spec_value_id: ''
        })
    }
}

const delSpecs = (index, index2) => {
    if (goods_specs_template.value.values[index].spec_value.length === 1) {
        goods_specs_template.value.values[index].spec_value[index2].spec_value_name = ''
    } else {
        goods_specs_template.value.values[index].spec_value.splice(index2, 1)
    }
}

const objectSpanMethod = ({row, column, rowIndex, columnIndex}) => {
    const field = column.property;
    if (field && field.startsWith('template')) {
        let spanCount = 1;
        for (let i = rowIndex + 1; i < updateForm.value.goods_skus.length; i++) {
            if (updateForm.value.goods_skus[i][field] === row[field]) {
                spanCount++;
            } else {
                break;
            }
        }
        if (rowIndex === 0 || updateForm.value.goods_skus[rowIndex - 1][field] !== row[field]) {
            return {rowspan: spanCount, colspan: 1};
        } else {
            return {rowspan: 0, colspan: 0};
        }
    }
    return {rowspan: 1, colspan: 1};
}

const toTableArray = (specs) => {
    const result = [];

    function helper(current, index) {
        if (index === specs.length) {
            result.push({...current});
            return;
        }

        const spec = specs[index];
        const templateName = `template_${index + 1}`;
        spec.spec_value.forEach(value => {
            const next = {...current};
            next[templateName] = value.spec_value_name;
            helper(next, index + 1);
        });
    }

    helper({
        goods_id: null,
        goods_sku_id: null,
        thumb: '',
        shop_price: '',
        number: '',
        is_show: 1,
    }, 0);
    updateForm.value.goods_skus = [...result]
    updateForm.value.goods_specs = [...specs]
}

const formatInput = (value) => {
    let match = value.match(/^-?\d*\.?\d{0,9}/);
    return match ? match[0] : '';
}

const handleTableRemove = (index) => {
    updateForm.value.goods_skus[index].thumb = ''
}

const filling = () => {
    let newArray = updateForm.value.goods_skus.map(item => {
        return {
            ...item,
            shop_price: moreInput.value.shop_price === '' ? item.shop_price : moreInput.value.shop_price,
            number: moreInput.value.number === '' ? item.number : moreInput.value.number
        }
    })
    updateForm.value.goods_skus = [...newArray]
    Object.keys(moreInput.value).map(its => {
        if(moreInput.value[its]){
            for(let i = 0; i < updateForm.value.goods_skus.length; i++){
                updateFormRef.value.clearValidate('goods_skus.' + i + '.' + its);
            }
        }
    })
    moreInput.value = {
        shop_price: '',
        number: ''
    }
}

const setMain = (index) => {
    if (index) {
        let picture = updateForm.value.goods_img.splice(index, 1)
        updateForm.value.goods_img.unshift(picture[0])
    }
}

const chooseSpecs = (index) => {
    let obj = specificationsArr.value.find((_a, b) => b === index)
    goods_specs_template.value = obj
}

const delSelect = (index) => {
    mySelectRef.value.visible = true
    cns.$dialog.confirm('是否删除模板', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
    }).then(() => {
        cns.$http.doPost('front.sku_template.destroy', {template_id: specificationsArr.value[index].id}).then(res => {
            if (res.code === 200) {
                specificationsArr.value.splice(index, 1)
                cns.$message.success(res.message)
            }
        })
    })
}

const handleExceed = (files, fileList) => {
    cns.$message.warning("最多上传6个文件");
}

const beforeUpload = (file) => {
    var type = false;
    if (file.type != "image/jpg" && file.type != "image/jpeg" && file.type != "image/png") {
               if (file.type) {
            type = true
        }
    }
    const isLt2M = file.size / 1024 / 1024 <= 5;
    if (type || !isLt2M) {
        cns.$message.error("支持 .png .jpg .jpeg格式，单个附件不得超过5M!");
        return false;
    }
}

const handleSuccess = (res, type, name) => {
    if (type === 'thumb') {
        let goods_skus = JSON.parse(JSON.stringify(updateForm.value.goods_skus))
        goods_skus.map(a => {
            if (a.template_1 === name) {
                a.thumb = res.data.file
            }
        })
        updateForm.value.goods_skus = [
            ...goods_skus
        ]
    } else {
        updateForm.value.goods_img.push(res.data.file)
    }
}

const setCheck = (val, type) => {
    updateForm.value[type] = val ? 1 : 0
}

const updaterTemplate = () => {
    templateFormRef.value.validate((valid) => {
        if (valid) {
            if (goods_specs_template.value.name) {
                cns.$http.doPost('front.sku_template.update', goods_specs_template.value).then(res => {
                    if (res.code === 200) {
                        cns.$message.success('保存模板成功')
                    } else {
                        cns.$message.error(res.message)
                    }
                    goods_specs_template.value.name = ''
                    getTemplate()
                })
            } else {
                cns.$dialog.prompt('请输入模板名称', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /\S+/,
                    inputErrorMessage: '请输入模板名称'
                }).then(({value}) => {
                    goods_specs_template.value.name = value
                    updaterTemplate()
                })
            }
        } else {
            cns.$message.error("请完善销售规格");
            return false
        }
    })
}

const sumbitGood = () => {
    loading.value = true
    updateFormRef.value.validate((valid) => {
        if (valid) {
            cns.$http.doPost('front.goods.update', updateForm.value).then(res => {
                if (res.code === 200) {
                    cns.$message.success(res.message)
                    setTimeout(() => {
                        window.location.href = ''
                    }, 500)
                } else {
                    cns.$message.error(res.message)
                }
                loading.value = false
            }).catch(error => {
                loading.value = false
            })
        } else {
            cns.$message.error("请完善商品信息");
            loading.value = false
            return false
        }
    })
}

onMounted(() => {
    let goodsInfo = null;
    if (goodsInfo) {
        nextTick(() => {
            updateForm.value = {...goodsInfo}
            is_limit_number.value = !!goodsInfo.limit_number
        });
    }
    getTemplate()
})

</script>

<style scoped lang="scss">

.update-box {
    width: 100%;
    height: 100%;
    background: #fff;
    padding: 30px;
    box-sizing: border-box;
    overflow-y: auto;
    -ms-overflow-style: none; /* 不显示滚动条 */
    scrollbar-width: none; /* 不显示滚动条 */
    position: relative;
    .good-form{
        height: 100%;
        padding: 0 10px;
        .btn {
            border-radius: 5px;
        }

        .btn.sumbit {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn.sumbit .el-icon-loading {
            font-size: 16px;
        }

        :deep(.el-form){
            padding-bottom: 20px;
            .avatar-uploader .el-upload {
                border: 1px dashed #d9d9d9;
                border-radius: 6px;
                cursor: pointer;
                position: relative;
                overflow: hidden;
            }

            .avatar-uploader .el-upload:hover {
                border-color: #409EFF;
            }

            .avatar-uploader-icon {
                font-size: 28px;
                color: #8c939d;
                width: 84px;
                height: 84px;
                line-height: 84px;
                text-align: center;
            }

            .el-upload__tip {
                line-height: 1;
                font-size: 14px;
                font-weight: normal;
                line-height: 22px;
                color: #9E9E9E;
            }

            .tips span {
                font-size: 12px;
                font-weight: normal;
                line-height: 24px;
                color: #9E9E9E;
            }

            .limit-number .el-form-item__content {
                display: flex;
                align-items: baseline;
            }

            .dels span {
                font-size: 12px;
                font-weight: normal;
                line-height: 22px;
                color: #3298F6;
                cursor: pointer;
            }

        }
    }
}
.update-box::-webkit-scrollbar {
    display: none; /* 针对 Chrome, Safari 和 Opera */
}


.specifications .specifications-box {
    margin-top: 30px;
    margin-bottom: 30px;
}

.specifications-box .specifications-list {
    width: 100%;
    background: #FFFFFF;
    margin-bottom: 20px;
}

.specifications-box .specifications-list .specifications-content .left {
    width: 35%;
    padding-right: 15px;
}

.specifications-box .specifications-list .specifications-content .right {
    width: 65%;
    padding-left: 15px;
    position: relative;
}

.specifications-box .specifications-list .specifications-content .right::before {
    position: absolute;
    content: '';
    height: 100%;
    width: 1px;
    left: 0;
    border-left: solid 1px #ccc;
}

.specifications-box .specifications-list .specifications-content .label span {
    font-size: 12px;
    font-weight: normal;
    line-height: 24px;
    color: #3D3D3D;
}

.specifications-input {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
}

.table-upload .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.table-upload .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 48px;
    height: 48px;
    line-height: 48px;
    text-align: center;
}

.thumb {
    width: 48px;
    height: 48px;
    position: relative;
    border: 1px dashed #d9d9d9;
}

.thumb img {
    width: 100%;
    height: 100%;
}

.thumb .el-upload-list__item-actions {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
}

.thumb:hover .el-upload-list__item-actions {
    display: block;
}

.thumb .el-icon-delete {
    line-height: 48px;
    text-align: center;
    width: 48px;
    color: #fff;
    cursor: pointer;
}

.more-input .more-li {
    width: 140px;
    display: flex;
}

.more-input .more-li label {
    font-size: 12px;
    color: #ccc;
    margin-right: 5px;
}
.good-picture .good-picture-list{
    margin-right: 10px;
}

.good-picture .good-picture-li {
    width: 84px;
    height: 84px;
    position: relative;
}

.good-picture .good-picture-li img {
    width: 100%;
    height: 100%;
}

.good-picture .good-picture-li .masking {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    background: rgba(0, 0, 0, 0.5);
    cursor: move;
    display: none;
}

.good-picture .good-picture-li:hover .masking {
    display: block;
}

.good-picture .good-picture-li .masking .el-icon-delete {
    font-size: 18px;
    color: #fff;
    cursor: pointer;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

.good-picture .main {
    border-radius: 15px;
    height: 25px;
    line-height: 23px;
    text-align: center;
    cursor: pointer;
    margin: 10px auto 0;
}

.good-picture .main.main-img {
    border: solid 1px red;
}

.good-picture .main span {
    color: red;
    font-size: 12px;
}


@media only screen and (max-width: 1920px) {

    @media screen and (max-width: 1450px) and (min-width: 1024px) and (max-height: 1000px) {
        .update-box {
            padding: 20px 10px;
        }
    }
}
</style>
