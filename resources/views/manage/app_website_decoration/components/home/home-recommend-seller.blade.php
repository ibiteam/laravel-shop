<style>
    /* 推荐商家 */
    .template-recommend-seller {  }
    .template-recommend-seller .recommend-seller-tabs { padding-left: 30px; }
    .template-recommend-seller .recommend-seller-tabs li { padding: 8px 20px; margin-right: 30px; background-color: #F7F7F7; border-radius: 30px; }
    .template-recommend-seller .recommend-seller-tabs li.active { background-color: #FFF1F1; color: #F71111; }
    .template-recommend-seller .recommend-seller-list { padding: 0 0 0 20px; margin: 20px 0 30px 0; overflow-x: auto; overflow-y: hidden; }
    .template-recommend-seller .recommend-seller-list li { width: 200px; height: 260px; margin: 0 10px; padding-bottom: 12px; border-radius: 10px; background-color: #FAFAFA; flex-shrink: 0; }
    .template-recommend-seller .recommend-seller-list li:last-child { margin: 0 20px 0 10px; }
    .template-recommend-seller .recommend-seller-list .goods-image { width: 100%; height: 130px; border-radius: 10px 10px 0 0; overflow: hidden; }
    .template-recommend-seller .recommend-seller-list .shop-logo { width: 92px; height: 92px; margin: -46px auto 0 auto; border-radius: 100%; border: 1px solid #E5E5E5; overflow: hidden; position: relative; }
    /*.template-recommend-seller .recommend-seller-list .shop-logo img { border: 1px solid #E5E5E5; }*/
    .template-recommend-seller .recommend-seller-list h1 { padding: 0 10px; margin: 6px 0 12px 0; text-align: center; font-size: 28px; }
    .template-recommend-seller .recommend-seller-list p { line-height: 30px; padding: 0 10px; text-align: center; font-size: 18px; color: #999999; }
    .template-recommend-seller .recommend-seller-list p.goods-tag { max-width: 160px; padding: 0 6px; margin: 0 auto; border: 1px solid #f71111; border-radius: 4px; color: #f71111; }
</style>

<script type="text/x-template" id="home-recommend-seller">
    <div class="temp-item"  :class="{ 's-flex ai-ct jc-ct': temp_item.activity_info && !temp_item.activity_info.length, active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <public-mark-template :title="temp_item.name" box_height="50px" :index="temp_index" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>
        <template>
            <template>
                <div class="temp-item-nodata s-flex flex-dir ai-ct jc-ct" v-if="!temp_item.data || computedDataType(temp_item.data) == 'Array' || !temp_item.data.data.cat.length">
                    <p>@{{ temp_item.name }}</p>
                    <p>点击“设置”，配置组件内需要展示的内容吧</p>
                </div>
                <div class="template-recommend-seller public-box" v-else>
                    <div class="public-title">
                        <div class="public_text">@{{ temp_item.data.data.name }}</div>
                        <div class="look_more s-flex ai-ct" v-if="temp_item.data.data.value">
                            <div class="look_more_view">更多 <em class="iconfont">&#xe772;</em></div>
                        </div>
                    </div>
                    <ul class="recommend-seller-tabs s-flex ai-ct">
                        <li class="cursorp" :class="{ active: tabIndex == index }" @click="tabIndex = index" v-for="(item, index) in temp_item.data.data.cat_list">@{{ item.cat_name }}</li>
                    </ul>
                    <ul class="recommend-seller-list s-flex ai-ct">
                        <li v-for="(item, index) in temp_item.data.data.cat[tabIndex].shop">
                            <div class="goods-image img-set-full">
                                {{--<img src="https://cdn.toodudu.com/uploads/2023/09/22/publicity2.png" alt="">--}}
                                <img :src="item.street_spjpg" alt="">
                            </div>
                            <div class="shop-logo img-set-full">
                                <img :src="item.shop_logo" alt="">
                            </div>
                            <h1 class="ellipsis-1">@{{ item.shop_name }}</h1>
                            <p class="ellipsis-1" :class="{ 'goods-tag': item.sign_type != 0 }">@{{ item.sign }}</p>
                        </li>
                    </ul>
                </div>
            </template>
        </template>
        <!--设置弹窗-->
        <el-drawer  :with-header="false" :modal-append-to-body="false" :close-on-press-escape="false" :show-close="false" :wrapper-closable="false" size="1200px" :visible.sync="is_show_drawer" :direction="direction">
            <input type="file" @change="handleChangeUploadFile" value="" style="display: none;" ref="upload_file" />
            <el-form :model="templateSetForm" :rules="templateSetRule" ref="templateSetForm" class="drawer-content" v-load="set_save_loading">
                <div class="drawer-name s-flex ai-ct">
                    <label>设置板块：</label>
                    <span>@{{ templateSetFormName }}</span>
                </div>
                <div style="height: calc(100vh - 160px); overflow-y: auto;" ref="drawerBox">
                    <div class="drawer-item">
                        <div class="drawer-item-dt">
                            <h1>基础设置</h1>
                        </div>
                        <div class="drawer-item-dd">
                            <el-form-item label="板块名称" label-width="110px" :prop="'name'" :rules="
                                [
                                    { required: true, message: '请输入板块名称', trigger: 'blur' },
                                    { max: 10, message: '板块名称不能超过10个字符', trigger: 'blur' },
                                    { validator: (rule, value, callback) => {
                                        //  校验是否有相同的名称
                                        const same_name = temp_list.filter(data => data.content.name && data.content.name == templateSetForm.name && data.id != temp_item.id)
                                        if (same_name.length) { callback(new Error('该板块名称已存在，请修改！')); } else { callback(); }
                                    }, trigger: 'blur' },
                                ]
                            ">
                                <el-input v-model="templateSetForm.name" style="width: 500px;"></el-input>
                                <p class="warning-text">用于改变页面中展示的板块名称</p>
                            </el-form-item>
                            <el-form-item label="URL链接" label-width="110px">
                                <div class="s-flex ai-ct">
                                    <el-form-item style="margin-bottom: 22px;">
                                        <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="templateSetForm.alias" @change="templateSetForm.value = '', templateSetForm.default_selection_data = [], clearFormValidate(`form_url_value`)" @clear="templateSetForm.value = ''" placeholder="请选择" clearable style="width: 150px;">
                                            <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item :ref="`form_url_value`" style="margin-bottom: 22px;" :prop="'value'" :rules="{ required: templateSetForm.alias != '' && computedOptionsPlaceholder(templateSetForm.alias), message: computedOptionsPlaceholder(templateSetForm.alias), trigger: 'blur' }">
                                        <el-select v-model="templateSetForm.value"
                                           v-if="computedOptionsPlaceholder(templateSetForm.alias) && computedOptionsCanRemote(templateSetForm.alias)"
                                           filterable
                                           remote
                                           reserve-keyword
                                           clearable
                                           :remote-method="(query) => {
                                                handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: templateSetForm.alias }, target: 'default_selection_data' }),
                                                handleRemoteSearchChose({ parent: `default_selection_data`, chose: templateSetForm.value })
                                           }"
                                           @change="(value) => handleRemoteSearchChose({ parent: `default_selection_data`, chose: templateSetForm.value, ref: 'form_url_value' })"
                                           @focus="handleRemoteSearchChose({ parent: `default_selection_data`, chose: templateSetForm.value })"
                                           :placeholder="computedOptionsPlaceholder(templateSetForm.alias)" style="width: 350px">
                                            <el-option v-for="option in templateSetForm.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                        </el-select>
                                        <el-input class="radius-left-none__input" v-else v-model="templateSetForm.value" :disabled="(!computedOptionsPlaceholder(templateSetForm.alias) && templateSetForm.alias != '' && templateSetForm.alias != null) || (!templateSetForm.alias && !templateSetForm.value)" :placeholder="computedOptionsPlaceholder(templateSetForm.alias) ? computedOptionsPlaceholder(templateSetForm.alias) : !computedOptionsPlaceholder(templateSetForm.alias) && templateSetForm.alias != '' && templateSetForm.alias != null ? '系统生成链接' : ''" style="width: 350px;"></el-input>
                                    </el-form-item>
                                </div>
                                <p class="warning-text">控制是否在标题右侧显示更多，配置链接后将跳转至指定页面。</p>
                            </el-form-item>
                        </div>
                    </div>
                    <div class="drawer-item" v-if="templateSetForm.cat">
                        <div class="drawer-item-dt">
                            <h1>推荐店铺</h1>
                            <div class="s-flex ai-ct jc-bt">
                                <div>
                                    <p>推荐分类1~10个，推荐店铺3~10个</p>
                                    <p>页面按照分类显示店铺，若设置的分类＜2，则页面不显示分类栏，只显示店铺。</p>
                                </div>
                                <div class="drawer-btn s-flex ai-ct jc-ct cursorp" :class="{ disabled: templateSetForm.cat.length >= 10 }" @click="handleClickAddData('cat', 10, '店铺', publicListItem.cat)">
                                    <em class="iconfont">&#xe620;</em>
                                    <label>添加（@{{ templateSetForm.cat.length }}/10）</label>
                                </div>
                            </div>
                        </div>
                        <div class="drawer-item-dd">
                            <drawer-slides :width="1085" :slide_active.sync="slideIndex" target="cat_name" default_text="推荐分类" :list="templateSetForm.cat"></drawer-slides>
                            <div class="drawer-item-cate" v-for="(item, index) in templateSetForm.cat" :key="slideIndex" v-if="slideIndex == index">
                                <div class="cate-title s-flex ai-ct jc-bt" style="height: 40px;">
                                    <h1>推荐分类</h1>
                                    <div class="drawer-btn s-flex ai-ct jc-ct cursorp" v-if="templateSetForm.cat.length > 1" @click="handleClickDeleteData({ index: slideIndex, parent: 'cat', confirm: '确定要删除分类吗？', min: 1, min_text: '最少填写一个推荐分类数据' })">
                                        <label>删除</label>
                                    </div>
                                </div>
                                <el-form-item label="分类名称" label-width="100px" :prop="'cat.' + index + '.cat_name'" :rules="
                                    [
                                        { required: true, message: '请输入分类名称', trigger: 'blur' },
                                        { max: 5, message: '分类名称不能超过5个字符', trigger: 'blur' },
                                        { validator: (rule, value, callback) => {
                                            //  校验是否有相同的名称
                                            const same_name = templateSetForm.cat.filter(data => data.cat_name == value)
                                            if (same_name.length > 1) { callback(new Error('该分类名称已存在，请修改！')); } else { callback(); }
                                        }, trigger: 'blur' },
                                    ]
                                ">
                                    <el-input v-model="item.cat_name" style="width: 500px;"></el-input>
                                </el-form-item>
                                <el-form-item label="分类排序" label-width="100px" :prop="'cat.' + index + '.sort'" :rules="
                                    [
                                        { validator: (rule, value, callback) => {
                                            if (value) {
                                                if (isNaN(value)) {
                                                    callback(new Error('请输入数字'));
                                                } else if (!Number.isInteger(value * 1)) {
                                                    callback(new Error('请输入整数'));
                                                } else if (value < 1) {
                                                    callback(new Error('排序最小值是1'));
                                                } else if (value > 100) {
                                                    callback(new Error('排序最大值是100'));
                                                } else { callback(); }
                                            } else { callback(); }
                                        }, trigger: 'blur' },
                                    ]
                                ">
                                    <el-input v-model="item.sort" style="width: 500px;"></el-input>
                                    <p class="warning-text">分类从左到右依次显示，数字越大越靠左</p>
                                </el-form-item>
                                <el-form-item label="关联店铺" label-width="100px" required></el-form-item>
                                <dl>
                                    <dt>
                                        <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                            <li class="s-flex ai-ct jc-ct flex-1" style="width: 90px;">
                                                <span>*</span>
                                                <label>选择店铺</label>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 100px;">
                                                <label>排序</label>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 80px;">操作</li>
                                        </ul>
                                    </dt>
                                    <dd class="s-flex flex-dir ai-ct" v-if="item.shop">
                                        <ul class="s-flex ai-ct" style="height: 60px; padding: 0 40px;" v-for="(child, childIndex) in item.shop">
                                            <li class="s-flex ai-ct jc-ct flex-1">
                                                <el-form-item :prop="'cat.' + index + '.shop.' + childIndex + '.seller_id'" :rules="{ required: true, message: '请选择店铺', trigger: 'blur' }" style="width: 100%;">
                                                    <el-select class="radius-right-none__input border-right-none__input"
                                                       v-model="child.seller_id"
                                                       filterable
                                                       remote
                                                       reserve-keyword
                                                       clearable
                                                       :remote-method="(query) => handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: query, page_type: 2 }, parent: `cat.${index}.shop.${childIndex}`, target: 'default_selection_data', index, childIndex })"
                                                       @change="(value) => {
                                                            child.shop_name = child.default_selection_data.filter(label => label.value == value).length ? child.default_selection_data.filter(label => label.value == value)[0].name : '',
                                                            handleRemoteSearchChoseShop({ parent: `cat.${index}.shop.${childIndex}.default_selection_data`, chose: templateSetForm.cat, child_value: 'shop' })
                                                       }"
                                                       @focus="handleRemoteSearchChoseShop({ parent: `cat.${index}.shop.${childIndex}.default_selection_data`, chose: templateSetForm.cat, child_value: 'shop' })"
                                                       placeholder="请输入店铺ID/店铺名称"
                                                       style="width: 100%;">
                                                        <el-option v-for="option in child.default_selection_data" :disabled="option.is_disabled" :key="option.value" :label="option.label" :value="option.value"></el-option>
                                                    </el-select>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 100px;">
                                                <el-form-item :prop="'cat.' + index + '.shop.' + childIndex + '.sort'" :rules="
                                                    [
                                                        { validator: (rule, value, callback) => {
                                                            if (value) {
                                                                if (isNaN(value)) {
                                                                    callback(new Error('请输入数字'));
                                                                } else if (!Number.isInteger(value * 1)) {
                                                                    callback(new Error('请输入整数'));
                                                                } else if (value < 1) {
                                                                    callback(new Error('排序最小值是1'));
                                                                } else if (value > 100) {
                                                                    callback(new Error('排序最大值是100'));
                                                                } else { callback(); }
                                                            } else { callback(); }
                                                        }, trigger: 'blur' },
                                                    ]
                                                ">
                                                    <el-input v-model="child.sort" placeholder="1~100"></el-input>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 80px;">
                                                <div class="drawer-btn cursorp s-flex ai-ct jc-ct" @click="handleClickDeleteData({ index: childIndex, parent: `cat.${index}.shop` })">删除</div>
                                            </li>
                                        </ul>
                                        <p class="warning-nodata" v-if="!item.shop.length">还没添加推荐店铺哦</p>
                                        <div class="drawer-btn primary s-flex ai-ct jc-ct" :class="{ disabled: item.shop.length >= 10 }" @click="handleClickAddData(`cat.${index}.shop`, 10, '关联店铺', { seller_id: '', shop_name: '', sort: '1' })">添加（@{{item.shop.length}}/10）</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <el-form-item style="margin: 0; box-shadow: 0 -0.2rem 0.2rem -0.22rem rgba(0, 0, 0, 0.2);">
                    <div class="s-flex ai-ct jc-ct" style="margin: 20px 0;">
                        <div style="width: 120px; margin: 0 15px;" class="drawer-btn primary s-flex ai-ct jc-ct" @click="handleClickSubmitSetForm('templateSetForm')">保存</div>
                        <div style="width: 120px; margin: 0 15px;" class="drawer-btn s-flex ai-ct jc-ct" @click.stop="handleClickCancelSetForm('templateSetForm')">取消</div>
                    </div>
                </el-form-item>
            </el-form>
        </el-drawer>
    </div>
</script>

<!--模块遮罩插件-->
@include('manage.app_website_decoration.components.template-mark-setting')
<!--广告轮播插件-->
@include('manage.app_website_decoration.components.carousel-swiper')
<!--slide tab按钮插件-->
@include('manage.app_website_decoration.components.drawer-slides')
<script>
    /**
     * name 装修模版--按钮切换组件
     * list Tab数据
     * width 父级盒子宽度
     * child_width 滑动元素宽度
     * offset Tab元素间隔：margin-right 值
     * custom_class 自定义类名
     * icon_top 图标上下偏移量
     * initial_index 指定选中项
     * **/
    Vue.component('home-recommend-seller', {
        props: {
            temp_index: {
                type: Number,
                default: 0
            },
            temp_item: {
                type: Object,
                default: () => { return {} }
            },
            temp_list: {
                type: Array,
                default: () => { return [] }
            },
            active_index: {
                type: Number,
                default: 0
            },
            options: {
                type: Array,
                default: () => { return [] }
            },
            mode: {
                type: String,
                default: '1'
            },
            upload_size_validate: {
                type: Number,
                default: 2
            },    //  上传大小限制，以兆为单位，默认2M
        },
        data () {
            return {
                direction: 'rtl',   //  打开方向
                set_save_loading: false,    //  保存加载动画
                is_show_drawer: false,
                templateSetFormName: '',    //  设置弹窗标题
                templateSetFormType: '',    //  设置组件别名
                templateSetFormIndex: '',    //  设置组件索引值
                templateSetForm: {},    //  设置表单数据
                templateSetRule: {},
                publicListItem: {
                    cat: {
                        cat_name: '',
                        sort: '1',
                        shop: [
                            {
                                seller_id: '',
                                shop_name: '',
                                sort: '1',
                            }
                        ]
                    }
                },
                tabIndex: 0,
                slideIndex: 0,
                msg_error: null
            }
        },
        computed: {
            /** 获取公共下拉数据返回输入框占位 */
            computedOptionsPlaceholder () {
                return function(value) {
                    const filter = this.options.filter(item => item.alias == value)
                    return filter.length ? filter[0].desc : ''
                }
            },
            /** 获取公共URL链接配置是否可以进行下拉搜索 */
            computedOptionsCanRemote () {
                return function(value) {
                    const filter = this.options.filter(item => item.alias == value)
                    return filter.length ? filter[0].need_remote_search : false
                }
            },
            /** 获取字段类型 */
            computedDataType () {
                return function (value) {
                    const type = Object.prototype.toString.call(value).slice(8, -1)
                    return type
                }
            },
        },
        template: '#home-recommend-seller',
        methods: {
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting (data) {
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
                this.templateSetForm.slideIndex = 0
                this.templateSetFormType = item.component_name
                this.is_show_drawer = true
            },
            /** 点击记录图片上传类型 **/
            handleClickUploadFile (data) {
                const { parent, validate, target, index, size } = data
                setTimeout(() => {
                    this.uploadType = parent || ''
                    this.uploadValidateType = validate
                    this.uploadTarget = target || null
                    this.uploadIndex = !isNaN(index) ? index : 'null'
                    this.$refs['upload_file'].click()
                }, 300)
            },
            /** 校验图片格式 **/
            beforeAvatarUpload(file) {
                return new Promise(resolve => {
                    let resovlt = true
                    if (file && file.type) {
                        const isPNG = file.type === 'image/png'
                        const isJPEG = file.type === 'image/jpeg'
                        const isJPG = file.type === 'image/jpg'
                        const isLt2M = file.size / 1024 / 1024 < this.upload_size_validate;
                        const isGIF = file.type === 'image/gif'
                        if (this.uploadValidateType >= 2) {
                            if (!isPNG && !isJPEG && !isJPG && !isGIF) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('仅限上传jpg、jpeg、png、gif格式的图片');
                                resolve(false)
                                return false
                            }
                        } else {
                            if (!isPNG && !isJPEG && !isJPG) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('仅限上传jpg、jpeg、png格式的图片');
                                resolve(false)
                                return false
                            }
                        }
                        if (!isLt2M) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`图片大小不能超过${this.upload_size_validate}M`);
                            resovlt = false
                        }
                    } else {
                        resovlt = false
                    }
                    resolve(resovlt)
                })
            },
            /** 执行上传图片操作 **/
            async handleChangeUploadFile (event) {
                if (this.uploadType) {
                    const [file] = event.srcElement.files
                    const is_file = await this.beforeAvatarUpload(file)
                    if (is_file) {
                        let blob
                        let reader = new FileReader()
                        //转化为binary类型
                        reader.readAsArrayBuffer(file)
                        reader.onload = (e) => {
                            if (typeof e.target.result === 'object') {
                                blob = new Blob([e.target.result])
                            } else {
                                blob = e.target.result
                            }
                            const fromdata = new FormData()
                            fromdata.append('file', blob)
                            this.doPost('{!! route('manage.common.upload') !!}', fromdata).then(res => {
                                if (res.code == 200) {
                                    let newValue = this.getNestedProperty(this.templateSetForm, this.uploadType)
                                    if (this.uploadTarget) {
                                        this.$set(newValue, this.uploadTarget, res.data.file)
                                    } else {
                                        this.$set(this.templateSetForm, this.uploadType, res.data.file)
                                    }
                                    this.clearFormValidate(`templateSetForm`)
                                } else {
                                    this.msg_error && this.msg_error.close()
                                    this.msg_error = this.$message.error('图片上传失败');
                                }
                            })
                        }
                    }
                    this.$refs['upload_file'].value = ''
                }
            },
            /** 深度对象调用 */
            getNestedProperty (obj, propertyPath) {
                if (typeof propertyPath === 'string') {
                    propertyPath = propertyPath.split('.');
                }
                if (propertyPath.length === 1) {
                    return obj[propertyPath[0]];
                } else {
                    let nextObj = obj[propertyPath[0]];
                    let nextPath = propertyPath.slice(1);
                    return this.getNestedProperty(nextObj, nextPath);
                }
            },
            /** 初始化对象字段值 */
            clearFormObjectFields (item) {
                return new Promise(resolve => {
                    Object.keys(item).forEach(key => {
                        if (isNaN(item[key]) && key != 'type') {
                            //  检验数据类型
                            const getType = (value) => {
                                return Object.prototype.toString.call(value).slice(8, -1)
                            }
                            switch (getType(item[key])) {
                                case 'String':
                                    item[key] = ''
                                    break
                                case 'Array':
                                    item[key] = []
                                    break
                                case 'Object':
                                    item[key] = {}
                                    break
                            }
                        }
                    })
                    resolve(item)
                })
            },
            /** 远程搜索下拉数据 */
            handleRemoteSearchChange (data) {
                const { path, method, params, parent, target, index, childIndex } = data
                const url = path
                const post_method = method == 'post' ? 'doGet' : 'doPost'
                let newList = null
                parent && (newList = this.getNestedProperty(this.templateSetForm, parent))
                let info = JSON.parse(JSON.stringify(params))
                this[post_method](url, info).then(res => {
                    if (res.code == 200) {
                        const list = res.data
                        if (newList) {
                            this.$set(newList, target, list)
                        } else {
                            this.$set(this.templateSetForm, target, list)
                        }
                    } else {
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.error(res.message)
                    }
                    this.handleRemoteSearchChoseShop({ parent: `cat.${index}.shop.${childIndex}.default_selection_data`, chose: this.templateSetForm.cat, child_value: 'shop' })
                    this.$forceUpdate()
                })
            },
            /** 下拉选中禁用 */
            handleRemoteSearchChose (data) {
                setTimeout(() => {
                    const { parent, chose, value = 'value', child_value, ref } = data
                    const choseType = Object.prototype.toString.call(chose).slice(8, -1)
                    const newList = this.getNestedProperty(this.templateSetForm, parent)
                    if (newList && newList.length) {
                        if (choseType == 'Array') {
                            newList.map(item => chose.some(child => item.is_disabled = item[value] == child[child_value]))
                        } else {
                            newList.map(item => item.is_disabled = item[value] == chose)
                        }
                    }
                    ref && this.clearFormValidate(ref)
                    this.$forceUpdate()
                }, 200)
                // (newList && newList.length) && (newList.map(item => item.is_disabled = item[value] == chose))
            },
            /** 下拉选中禁用 */
            handleRemoteSearchChoseShop (data) {
                setTimeout(() => {
                    const { parent, chose, value = 'value', child_value, ref } = data
                    const choseType = Object.prototype.toString.call(chose).slice(8, -1)
                    const newList = this.getNestedProperty(this.templateSetForm, parent)
                    if (newList && newList.length) {
                        if (choseType == 'Array') {
                            newList.map(item => chose.some(child => item.is_disabled = child[child_value].some(shop => item[value] == shop['seller_id'])))
                        } else {
                            newList.map(item => item.is_disabled = item[value] == chose)
                        }
                    }
                    ref && this.clearFormValidate(ref)
                    this.$forceUpdate()
                }, 200)
                // (newList && newList.length) && (newList.map(item => item.is_disabled = item[value] == chose))
            },
            /** 点击添加组件数据 */
            async handleClickAddData (dom, len, warning_text, data) {
                const item = JSON.parse(JSON.stringify(data))
                let newValue = this.getNestedProperty(this.templateSetForm, dom)
                if (newValue.length >= len) {
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.error(`最多可添加${len}个${warning_text}`)
                    return false
                }
                newValue.push(item)
            },
            /** 点击删除组件数据 */
            async handleClickDeleteData (data) {
                const { index, parent, confirm, min = -1, min_text } = data
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
                if (confirm) {
                    this.$confirm(confirm, '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning',
                        center: true
                    }).then(() => {
                        newValue.splice(index, 1)
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.success('删除成功！')
                    }).catch(() => {});
                } else {
                    if (min > -1 && newValue.length <= min) {
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.success(min_text)
                        return false
                    }
                    newValue.splice(index, 1)
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.success('删除成功！')
                }
                setTimeout(() => this.slideIndex = 0, 200)
            },
            /** 点击提交设置弹窗数据 */
            handleClickSubmitSetForm (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        const cat_name_null = this.templateSetForm.cat.filter(item => item.cat_name == '')
                        const data_null = this.templateSetForm.cat.findIndex(item => !item.shop.length || item.shop.length < 3)
                        const sort_null = this.templateSetForm.cat.filter(item => item.shop.some(child => child.seller_id == ''))
                        if (cat_name_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请输入分类名称`)
                            return false
                        }
                        if (data_null > -1) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`推荐店铺第【${data_null}】项至少添加3个店铺`)
                            return false
                        }
                        if (sort_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请选择店铺`)
                            return false
                        }
                        const newData = JSON.parse(JSON.stringify(this.temp_item))
                        newData.content = this.templateSetForm
                        if (newData.data) delete newData.data
                        if (isNaN(newData.id)) newData.id = ''

                        this.set_save_loading = true
                        const info = { component_name: this.templateSetFormType, component_data: newData, name: this.templateSetFormName }
                        this.doPost('{!! route('manage.app_web_decoration.get_content_data_by_alias') !!}', info).then(res => {
                            if (res.code == 200) {
                                this.is_show_drawer = false
                                this.is_setting_save = true
                                this.set_save_loading = false
                                this.$set(this.temp_item, 'data', res.data)
                                this.$set(this.temp_item, 'content', this.templateSetForm)
                                this.$emit('save', { content: this.templateSetForm, data: res.data, index: this.temp_index })
                            } else {
                                this.is_setting_save = true
                                this.set_save_loading = false
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error(res.message)
                            }
                        })
                    } else {
                        return false;
                    }
                });
            },
            /** 点击取消提交设置弹窗数据 */
            handleClickCancelSetForm (formName) {
                this.$refs[formName].clearValidate();
                if (this.$refs.drawerBox) {
                    this.$refs.drawerBox.scrollTop = 0;
                }
                this.is_show_drawer = false
                // this.$emit('cancel')
            },
            /** 点击查看图片 */
            handleClickOpenPreviewImage (list, index, name) {
                this.imagePreviewList = list
                this.preview_index = index || 0
                this.preview_name = name || 'image'
                setTimeout(() => this.is_show_dialog = true, 400)
            },
        },
        mounted () {}
    })
</script>
