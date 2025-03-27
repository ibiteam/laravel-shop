@extends('manage.layout')

@section('content')
    <div class="flex-content flex_1">
        <div class="container">
            <div class="update-box s_flex">
                <div class="good-form">
                    <el-form ref="updateForm" :model="updateForm" :rules="updateFormRules" label-width="120px" size="small">
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
                                <em class="iconfont" slot="reference" style="cursor: pointer;">&#xe72d;</em>
                            </el-popover>
                        </el-form-item>
                        <el-form-item label="商品副标题" prop="goods_brief">
                            <el-input v-model="updateForm.goods_brief" style="width: 95%;" placeholder="请输入商品副标题"></el-input>
                            <el-popover
                                placement="right"
                                title=""
                                width="auto"
                                trigger="hover"
                                content="{{ __('shop/goods.index.goods_desc') }}">
                                <em class="iconfont" slot="reference" style="cursor: pointer;">&#xe72d;</em>
                            </el-popover>
                        </el-form-item>
                        <el-form-item label="自定义属性">
                            <template v-if="updateForm.goods_attr.length">
                                <div class="input-li s_flex ai_ct" v-for="(item,index) in updateForm.goods_attr" :key="index" style="margin-bottom: 10px;">
                                    <div style="width: 95%;" class="s_flex jc_bt ai_ct">
                                        <el-input v-model="item.attr_name" placeholder="请输入属性名" style="width: 48%;" :maxlength="5" show-word-limit></el-input>
                                        <el-input v-model="item.attr_value" placeholder="请输入属性值" style="width: 48%;" :maxlength="20"></el-input>
                                    </div>
                                    <div class="dels" style="margin-left: 5px;" @click="delGoodsAttr(index)">
                                        <span>删除</span>
                                    </div>
                                </div>
                            </template>
                            <template>
                                <el-button class="btn primary" size="small" @click="addGoodsAttr">添加</el-button>
                                <div class="tips">
                                    <span>可设置自定义属性，如内存：8G</span>
                                </div>
                            </template>
                        </el-form-item>
                        <el-form-item label="图片" prop="goods_img">
                            <div class="s_flex">
                                <draggable class="good_picture s_flex" v-model="updateForm.goods_img">
                                    <div class="good_picture_list" v-for="(its,ids) in updateForm.goods_img" :key="ids">
                                        <div class="good_picture_li">
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
                                </draggable>
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
                            <template slot="label">
                                商品规格
                                <el-popover
                                    placement="right"
                                    title=""
                                    width="auto"
                                    trigger="hover"
                                    content="可添加多个规格属性的商品">
                                    <em class="iconfont" slot="reference" style="cursor: pointer;">&#xe72d;</em>
                                </el-popover>
                            </template>
                            <div class="specifications" :class="{ 's_flex' : !goods_specs_template.values.length , 'jc_bt' : !goods_specs_template.values.length , 'fd_rs' : !goods_specs_template.values.length }">
                                <div class="specifications-select s_flex jc_fe">
                                    <el-select placeholder="please_select"
                                               style="width: 160px;" ref="mySelect">
                                        <el-option v-for="(item,index) in specificationsArr"
                                            :key="item.id">
                                            <div class="option-li s_flex jc_bt ai_bs" @click="chooseSpecs(index)">
                                                <span>@{{ item.name }}</span>
                                                <el-tag
                                                    slot="reference"
                                                    v-if="true"
                                                    size="mini"
                                                    effect="dark"
                                                    @click.native.stop="delSelect(index)"
                                                    style="float: right; margin-top: 8px; margin-left: 3px"
                                                >
                                                    <em class="iconfont">&#xe8b6;</em>
                                                </el-tag>
                                            </div>
                                        </el-option>
                                    </el-select>
                                </div>
                                <div class="specifications-box" v-if="goods_specs_template.values.length">
                                    <el-form ref="templateForm" :model="goods_specs_template" :rules="templateRules"
                                             size="small">
                                        <el-card class="specifications-list"
                                                 v-for="(item,index) in goods_specs_template.values" :key="index">
                                            <div class="s_flex jc_fe">
                                                <div style="cursor: pointer;" @click="delGoodsSpecs(index)">
                                                    <em class="iconfont" style="font-size: 14px;">&#xe686;</em>
                                                </div>
                                            </div>
                                            <div class="specifications-content s_flex jc_bt">
                                                <div class="left">
                                                    <div class="label">
                                                        <span>名称</span>
                                                    </div>
                                                    <el-form-item :prop="'values.' + index + '.spec_name'">
                                                        <el-input v-model="item.spec_name" size="mini"
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
                                                                          size="mini" maxlength="10"
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
                                                                <el-link type="primary" slot="reference" :underline="false" icon="el-icon-plus" :disabled="!computedSpecs(index)" @click="addSpecs(index)">{{ __('shop/goods.index.add_spec_item') }}</el-link>
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
                                    <el-button class="btn primary" size="small" @click="addGoodsSpecs()"
                                               v-if="goods_specs_template.values.length < 3">添加</el-button>
                                    <template v-if="goods_specs_template.values.length">
                                        <el-button class="btn" size="small" v-if="!goods_specs_template.id"
                                                   @click="updaterTemplate()">保存模板</el-button>
                                        <el-button class="btn" size="small" @click="updaterTemplate()"
                                                   v-else>更新模板</el-button>
                                    </template>
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item label="销售规格" v-if="goods_specs_template.values.length">
                            <div class="more-input s_flex jc_fe">
                                <div class="more-li">
                                    <label>
                                        <span>价格</span>
                                    </label>
                                    <el-input v-model="moreInput.shop_price" size="mini" style="width: 80px;" placeholder="" @input="moreInput.shop_price = formatInput(moreInput.shop_price)"></el-input>
                                </div>
                                <div class="more-li">
                                    <label>
                                        <span>库存</span>
                                    </label>
                                    <el-input v-model="moreInput.number" size="mini" style="width: 80px;" placeholder=""
                                              @input="moreInput.number = formatInput(moreInput.number)"></el-input>
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
                                            <el-input v-model="scope.row.shop_price" size="mini" placeholder=""
                                                      @input="scope.row.shop_price = formatInput(scope.row.shop_price)"></el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    label="库存">
                                    <template slot-scope="scope">
                                        <el-form-item :prop="'goods_skus.' + scope.$index + '.number'"
                                                      :rules="updateFormRules.number">
                                            <el-input v-model="scope.row.number" size="mini" placeholder=""
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
                            <div class="s_flex ai_ct">
                                <el-form-item prop="shop_price">
                                    <el-input size="mini" style="width: 100px;" v-model="updateForm.shop_price" @input="updateForm.shop_price = formatInput(updateForm.shop_price)" placeholder=""><span slot="suffix">元</span></el-input>
                                </el-form-item>
                            </div>
                        </el-form-item>
                        <el-form-item label="库存" prop="goods_number">
                            <el-input-number v-model="updateForm.goods_number" :disabled="updateForm.goods_skus.length"
                                             :min="1" style="width: 160px;"></el-input-number>
                            <div class="tips" v-if="goods_specs_template.values.length">
                                <span>多规格商品库存为所有SKU的库存总和</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="商品详情" prop="goods_desc">
                            <quill-editor :content.sync="updateForm.goods_desc" placeholder="" height="500px" min-height="500px" />
                        </el-form-item>
                        <el-form-item label="上架" prop="is_on_sale">
                            <el-radio-group v-model="updateForm.is_on_sale">
                                <el-radio :label="1">立即上架</el-radio>
                                <el-radio :label="0">放入仓库</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="限购" prop="limit_number" class="limit_number">
                            <div>
                                <el-radio-group v-model="is_limit_number" @change="changeLimitNumber">
                                    <el-radio :label="false">不限购</el-radio>
                                    <el-radio :label="true">限购</el-radio>
                                </el-radio-group>
                                <div v-if="is_limit_number">
                                    <el-input-number v-model="updateForm.limit_number" :min="1"></el-input-number>
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item>
                            <el-button class="btn primary sumbit" style="width: 120px;height: 40px;" @click="sumbitGood" :loading="loading">提交</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('manage.goods.components.good-detail')
    @include('manage.goods.components.quill-editor')
    <script src="/js/draggable/Sortable.min.js" type="text/javascript"></script>
    <script src="/js/draggable/vuedraggable.umd.min.js" type="text/javascript"></script>
    <script>
        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(this, args);
                }, wait);
            };
        }

        let vm = new Vue({
            el: '#app',
            data() {
                this.validateFile = (rule, value, callback) => {
                    if (!this.updateForm.goods_img.length) {
                        callback(new Error('请上传商品图片'));
                    } else {
                        callback();
                    }
                }
                this.validatePrice = (rule, value, callback, type) => {
                    if (this.updateForm.goods_skus.length) {
                        const checkPrices = (data) => {
                            return data.filter(item => {
                                const shopPrice = parseFloat(item.shop_price) || 0;
                                return shopPrice < 0
                            });
                        };
                        const invalidItems = checkPrices(this.updateForm.goods_skus);
                        if (invalidItems.length) {
                            callback(new Error('价格不能小于0'));
                        } else {
                            callback();
                        }
                    } else {
                        callback();
                    }
                }
                this.validateDesc = (rule, value, callback) => {
                    if (this.updateForm.goods_desc == '' || this.updateForm.goods_desc == '<p style="color: rgb(51, 51, 51); line-height: 2;"><br></p>') {
                        callback(new Error('商品详情不能为空'));
                    } else {
                        callback();
                    }
                }
                this.specValue = (rule, value, callback, index, id) => {
                    let arr = this.goods_specs_template.values[index].spec_value
                    const hasDuplicates = arr.reduce((acc, current) => {
                        // 如果spec_value_name已经在acc中，标记为true
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
                return {
                    category:[],
                    updateForm: {
                        id: 0,
                        goods_cate_id: null, //商品分类id
                        member_id: null, //用户id
                        goods_name: '', //商品名称
                        tag: '', //商品标签
                        goods_brief: '', //商品副标题
                        goods_attr: [],
                        goods_img: [], //图片
                        unit: '', //单位
                        shop_price: 0, //价格
                        goods_number: 10, //库存
                        goods_desc: '', // 商品详情
                        is_on_sale: 1, //是否上架
                        limit_number: 0, // 不限购
                        goods_skus: [], // 规格table
                        goods_specs: [],
                    },
                    updateFormRules: {
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
                            {validator: this.validateFile, trigger: 'change'}
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
                            {validator: this.validateDesc, trigger: 'blur'}
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
                                validator: (rule, value, callback) => this.validatePrice(rule, value, callback, 'shop_price'),
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
                    },
                    shop_price_show: false, //价格显示
                    is_limit_number: false,// 限购选择
                    specificationsArr: [],
                    goods_specs_template: {
                        name: '',
                        template_id: null,
                        values: [],
                    }, // 商品规格
                    moreInput: {
                        integral_money: '',
                        shop_price: '',
                        number: ''
                    },
                    loading: false
                };
            },
            computed: {
                templateRules() {
                    const rules = {};
                    this.goods_specs_template.values.forEach((field, index) => {
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
                                    validator: (rule, value, callback) => this.specValue(rule, value, callback, index, id),
                                    trigger: 'blur'
                                }
                            ];
                        })
                    });
                    return rules;
                }
            },
            watch: {
                'goods_specs_template.values': {
                    handler: debounce((newVal, oldVal) => {
                        if (newVal.length) {
                            vm.toTableArray(newVal)
                        } else {
                            vm.$set(vm.updateForm, 'goods_skus', [])
                            vm.$set(vm.updateForm, 'goods_specs', [])
                        }
                    }, 500),
                    deep: true,
                    immediate: false
                },
                'updateForm.shop_price': {
                    handler(val) {
                        if (val && val > 0) {
                            this.shop_price_show = true
                        } else {
                            this.shop_price_show = false
                        }
                    }
                },
                'updateForm.goods_skus': {
                    handler(val) {
                        if (val.length) {
                            let numberArr = Array.from(val, a => a.number)
                            let sum = numberArr.reduce((accumulator, currentValue) => Number(accumulator) + Number(currentValue), 0);
                            this.$set(this.updateForm, 'goods_number', sum)
                        } else {

                        }
                    },
                    deep: true,
                    immediate: false
                }
            },
            methods: {
                more_integralPrice(index) {
                    let skus = this.updateForm.goods_skus[index]
                    const prop2 = `goods_skus.${index}.shop_price`;
                    return [
                        {
                            validator: (rule, value, callback) => {
                                if (Number(skus.shop_price) < 0) {
                                    callback(new Error(`价格必须大于0`));
                                } else {
                                    this.$refs.updateForm.clearValidate(prop2);
                                    callback();
                                }
                            },
                            trigger: 'blur',
                        },
                    ]
                },
                getTemplate() {
                    this.doGet('{{route('front.sku_template.index')}}').then(res => {
                        if (res.errcode === 0) {
                            this.specificationsArr = [...res.data]
                        }
                    })
                },
                changeLimitNumber(val) {
                    if (val) {
                        this.$set(this.updateForm, 'limit_number', 1)
                    } else {
                        this.$set(this.updateForm, 'limit_number', 0)
                    }
                },
                addGoodsAttr() { // 增加自定义属性值
                    this.updateForm.goods_attr.push({
                        attr_name: '',
                        attr_value: ''
                    })
                },
                delGoodsAttr(index) { // 删除自定义属性值
                    this.updateForm.goods_attr.splice(index, 1)
                },
                delGoodsSpecs(index) { // 删除商品规格
                    this.goods_specs_template.values.splice(index, 1)
                },
                addGoodsSpecs() { // 增加商品规格
                    this.goods_specs_template.values.push({
                        spec_name: '',
                        spec_id: '',
                        spec_value: [
                            {
                                spec_value_name: '',
                                spec_value_id: ''
                            }
                        ]
                    })
                },
                computedSpecs(index) { // 计算是否可新增
                    return this.goods_specs_template.values[index].spec_value.every(a => a.spec_value_name)
                },
                addSpecs(index) { // 增加规格值
                    if (this.computedSpecs(index) && this.goods_specs_template.values[index].spec_value.length < 6) {
                        this.goods_specs_template.values[index].spec_value.push({
                            spec_value_name: '',
                            spec_value_id: ''
                        })
                    }
                },
                delSpecs(index, index2) { // 删除规格值
                    if (this.goods_specs_template.values[index].spec_value.length === 1) {
                        this.$set(this.goods_specs_template.values[index].spec_value[index2], 'spec_value_name', '')
                    } else {
                        this.goods_specs_template.values[index].spec_value.splice(index2, 1)
                    }
                },
                objectSpanMethod({row, column, rowIndex, columnIndex}) {
                    const field = column.property;
                    if (field && field.startsWith('template')) {
                        let spanCount = 1;
                        for (let i = rowIndex + 1; i < this.updateForm.goods_skus.length; i++) {
                            if (this.updateForm.goods_skus[i][field] === row[field]) {
                                spanCount++;
                            } else {
                                break;
                            }
                        }
                        if (rowIndex === 0 || this.updateForm.goods_skus[rowIndex - 1][field] !== row[field]) {
                            return {rowspan: spanCount, colspan: 1};
                        } else {
                            return {rowspan: 0, colspan: 0};
                        }
                    }
                    return {rowspan: 1, colspan: 1};
                },
                toTableArray(specs) { // 数组转换
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
                        thumb: '', // 缩略图
                        shop_price: '', // 价格
                        integral_money: '', // 积分价格
                        number: '', // 库存
                        is_show: 1, // 是否显示
                    }, 0);
                    this.updateForm.goods_skus = [...result]
                    this.updateForm.goods_specs = [...specs]
                },
                formatInput(value) {
                    let match = value.match(/^-?\d*\.?\d{0,9}/);
                    return match ? match[0] : '';
                },
                handleTableRemove(index) { // 删除图片
                    this.$set(this.updateForm.goods_skus[index], 'thumb', '')
                },
                filling() { // 批量填充
                    let newArray = this.updateForm.goods_skus.map(item => {
                        return {
                            ...item,
                            integral_money: this.moreInput.integral_money === '' ? item.integral_money : this.moreInput.integral_money,
                            shop_price: this.moreInput.shop_price === '' ? item.shop_price : this.moreInput.shop_price,
                            number: this.moreInput.number === '' ? item.number : this.moreInput.number
                        }
                    })
                    this.$set(this.updateForm, 'goods_skus', [...newArray])
                    // 取消填入的校验
                    Object.keys(this.moreInput).map(its => {
                        if(this.moreInput[its]){
                            for(let i = 0; i < this.updateForm.goods_skus.length; i++){
                                this.$refs.updateForm.clearValidate('goods_skus.' + i + '.' + its);
                            }
                        }
                    })
                    this.$set(this, 'moreInput', {
                        integral_money: '',
                        shop_price: '',
                        number: ''
                    })
                },
                setMain(index) { // 设置主图
                    if (index) {
                        let picture = this.updateForm.goods_img.splice(index, 1)
                        this.updateForm.goods_img.unshift(picture[0])
                    }
                },
                chooseSpecs(index) {
                    let obj = this.specificationsArr.find((_a, b) => b === index)
                    this.$set(this, 'goods_specs_template', obj)
                },
                delSelect(index) { // 删除规格
                    vm.$refs.mySelect.visible = true
                    this.$confirm('是否删除模板', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        this.doPost('{{ route('front.sku_template.destroy') }}', {template_id: this.specificationsArr[index].id}).then(res => {
                            if (res.errcode === 0) {
                                this.specificationsArr.splice(index, 1)
                                this.$message.success(res.errmsg)
                            }
                        })
                    })
                },
                handleExceed(files, fileList) {
                    this.$message.warning("最多上传6个文件");
                },
                beforeUpload(file) {
                    var type = false;
                    if (file.type != "image/jpg" && file.type != "image/jpeg" && file.type != "image/png") {
                        if (file.type) {
                            type = true
                        }
                    }
                    const isLt2M = file.size / 1024 / 1024 <= 5;
                    if (type || !isLt2M) {
                        this.$message.error("支持 .png .jpg .jpeg格式，单个附件不得超过5M!");
                        return false;
                    }
                },
                handleSuccess(res, type, name) {
                    if (type === 'thumb') {
                        let goods_skus = JSON.parse(JSON.stringify(this.updateForm.goods_skus))
                        goods_skus.map(a => {
                            if (a.template_1 === name) {
                                a.thumb = res.data.file
                            }
                        })
                        this.$set(this.updateForm, 'goods_skus', [
                            ...goods_skus
                        ])
                    } else {
                        this.updateForm.goods_img.push(res.data.file)
                    }
                },
                setCheck(val, type) {
                    this.$set(this.updateForm, type, val ? 1 : 0)
                },
                updaterTemplate() { // 更新模板
                    this.$refs['templateForm'].validate((valid) => {
                        if (valid) {
                            if (this.goods_specs_template.name) {
                                this.doPost('{{ route('front.sku_template.update') }}', this.goods_specs_template).then(res => {
                                    if (res.errcode === 0) {
                                        this.$message.success('保存模板成功')
                                    } else {
                                        this.$message.error(res.message)
                                    }
                                    this.goods_specs_template.name = ''
                                    this.getTemplate()
                                })
                            } else {
                                this.$prompt('请输入模板名称', '提示', {
                                    confirmButtonText: '确定',
                                    cancelButtonText: '取消',
                                    inputPattern: /\S+/,
                                    inputErrorMessage: '请输入模板名称'
                                }).then(({value}) => {
                                    this.goods_specs_template.name = value
                                    this.updaterTemplate()
                                })
                            }
                        } else {
                            console.log('error submit!!');
                            this.$message.error("请完善销售规格");
                            return false
                        }
                    })
                },
                sumbitGood() { // 提交
                    this.loading = true
                    this.$refs['updateForm'].validate((valid) => {
                        if (valid) {
                            this.doPost('{{ route('front.goods.update') }}', this.updateForm).then(res => {
                                if (res.errcode === 0) {
                                    this.$message.success(res.message)
                                    setTimeout(() => {
                                        window.location.href = '' // 去列表页
                                    }, 500)
                                } else {
                                    this.$message.error(res.message)
                                }
                                this.loading = false
                            }).catch(error => {
                                this.loading = false
                            })
                        } else {
                            this.$message.error("请完善商品信息");
                            this.loading = false
                            return false
                        }
                    })
                },
            },
            mounted() {
                let goodsInfo = null;
                if (goodsInfo) {
                    this.$nextTick(() => {
                        this.$set(this, 'updateForm', {...goodsInfo})
                        this.is_limit_number = !!goodsInfo.limit_number
                    });
                }
                this.getTemplate()
            },
        })

    </script>
@endsection

@section('css')
    <style>
        .flex-content .container {
            overflow: hidden;
        }

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
        }
        .update-box::-webkit-scrollbar {
            display: none; /* 针对 Chrome, Safari 和 Opera */
        }

        .update-box .good-form {
            height: 100%;
            padding: 0 10px;
        }
        .update-box .good-form .el-form{
            padding-bottom: 20px;
        }
        .good-detail {
            position: sticky;
            top: 0;
            right: 0;
        }
        .good-detail .views{
            text-align: center;
            margin-bottom: 20px;
        }
        .good-detail .views span{
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }

        /*    */
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

        .btn {
            border-radius: 5px;
        }

        .btn.primary {
            background: #EB1515;
        }

        .btn span {
            font-size: 12px;
        }

        .btn.primary span {
            color: #FFFFFF;
        }

        .el-button.btn.primary:focus, .el-button.btn.primary:hover {
            background: #EB1515;
        }

        .btn.sumbit {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn.sumbit .el-icon-loading {
            font-size: 16px;
        }

        .btn.sumbit span {
            font-size: 14px;
        }

        .tips span {
            font-size: 12px;
            font-weight: normal;
            line-height: 24px;
            color: #9E9E9E;
        }

        .limit_number .el-form-item__content {
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

        /**/
        .specifications {

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
        .good_picture .good_picture_list{
            margin-right: 10px;
        }

        .good_picture .good_picture_li {
            width: 84px;
            height: 84px;
            position: relative;
        }

        .good_picture .good_picture_li img {
            width: 100%;
            height: 100%;
        }

        .good_picture .good_picture_li .masking {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            background: rgba(0, 0, 0, 0.5);
            cursor: move;
            display: none;
        }

        .good_picture .good_picture_li:hover .masking {
            display: block;
        }

        .good_picture .good_picture_li .masking .el-icon-delete {
            font-size: 18px;
            color: #fff;
            cursor: pointer;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .good_picture .main {
            border-radius: 15px;
            height: 25px;
            line-height: 23px;
            text-align: center;
            cursor: pointer;
            margin: 10px auto 0;
        }

        .good_picture .main.main-img {
            border: solid 1px red;
        }

        .good_picture .main span {
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
@endsection
