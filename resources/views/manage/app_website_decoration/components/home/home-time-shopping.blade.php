<style>
    /* 限时抢购 */
    .template-time-shopping { background-position: top; background-repeat: no-repeat; background-size: 710px 123px; }
    .template-time-shopping .list-data { padding: 0 0 30px 20px; }
    .template-time-shopping ul { overflow: hidden; }
    .template-time-shopping ul li { width: 170px; margin: 0 10px; }
    .template-time-shopping ul li:last-child { margin: 0 20px 0 10px; }
    .template-time-shopping ul li .goods-image { width: 170px; height: 170px; }
    .template-time-shopping ul li .goods-image .goods-tag { padding: 4px 8px; background-color: rgba(0, 0, 0, 0.5); border-radius: 4px; font-size: 20px; color: #ffffff; position: absolute; right: 10px; bottom: 2px; }
    .template-time-shopping ul li .goods-price { margin-top: 10px; }
    .template-time-shopping ul li .goods-price em { font-size: 26px; color: #f71111; }
    .template-time-shopping ul li .goods-price p {}
    .template-time-shopping ul li .goods-nprogress { width: 130px; height: 14px; margin: 10px auto; background-color: #FFDFDE; border-radius: 8px; position: relative; }
    .template-time-shopping ul li .goods-nprogress .nprogress-detail { width: 50%; height: 100%; background-color: #F71111; border-radius: 8px; position: absolute; left: 0; top: 0; }
    .template-time-shopping ul li h1 { text-align: center; font-size: 20px; color: #999999; }
    .template-time-shopping .public-title { background-color: transparent; }
</style>

<script type="text/x-template" id="home-time-shopping">
    <div class="temp-item" :class="{ 's-flex ai-ct jc-ct': temp_item.activity_info && !temp_item.activity_info.length, active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <public-mark-template :title="temp_item.name" :index="temp_index" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>

        <template>
            <div class="temp-item-nodata s-flex flex-dir ai-ct jc-ct" v-if="!temp_item.data || computedDataType(temp_item.data) == 'Array' || !temp_item.data.items.length">
                <p>@{{ temp_item.name }}</p>
                <p>点击“设置”，配置组件内需要展示的内容吧</p>
            </div>
            <template v-else>
                <div class="template-time-shopping public-box" :style="{ backgroundImage: `url(${temp_item.data.image})` }">
                    <div class="public-title public-title-bgimage">
                        <div class="public_text"></div>
                        <div class="look_more s-flex ai-ct" v-if="temp_item.data.url">
                            <div class="look_more_view">更多 <em class="iconfont">&#xe772;</em></div>
                        </div>
                    </div>
                    <div class="list-data">
                        <ul class="s-flex ai-ct">
                            <li v-for="item in temp_item.data.items">
                                <div class="goods-image img-set s-flex ai-ct jc-ct">
                                    <img :src="item.goods_thumb" alt="">
                                    <div class="goods-tag ellipsis-1" :style="{ maxWidth: '70%' }" v-if="item.rate == '100%'">卖光了</div>
                                </div>
                                <div class="goods-price s-flex ai-ct">
                                    <em class="iconfont">&#xe7c5;</em>
                                    <form-price :price="item.goods_price" weight="bolder" int_size="26" df_size="20" sign_size="24" color="#F71111"></form-price>
                                </div>
                                <div class="goods-nprogress">
                                    <div class="nprogress-detail" :style="{ width: item.rate }"></div>
                                </div>
                                <h1>已售@{{ item.rate }}</h1>
                            </li>
                        </ul>
                    </div>
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
                            <el-form-item label="板块背景" label-width="110px" :prop="'image'" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                <div class="s-flex">
                                    <div class="drawer-upload s-flex ai-ct jc-ct cursorp">
                                        <img v-if="templateSetForm.image" :src="templateSetForm.image" alt="">
                                        <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!templateSetForm.image" @click="handleClickUploadFile({ target: 'image', validate: 1 })">
                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                <em class="iconfont">&#xe727;</em>
                                            </div>
                                            <p>未上传</p>
                                        </div>
                                        <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-if="templateSetForm.image" @click="handleClickUploadFile({ target: 'image', validate: 1 })">
                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0;">
                                                <em class="iconfont">&#xe727;</em>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="drawer-upload-warning">
                                        <p>支持jpg、jpeg、png格式</p>
                                        <p>建议尺寸：710px*123px</p>
                                        <p>建议大小：2M内</p>
                                        <el-tooltip
                                                style="margin: 0 20px 0 0;"
                                                effect="light"
                                                placement="right"
                                                width="100"
                                                popper-class="drawer-preview-image"
                                                trigger="hover">
                                            <div slot="content" class="drawer-upload-warning">
                                                <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2023/11/01/time_shop.png', is_show_imageview = true">
                                                    <em class="iconfont cursorp">&#xe7c3;</em>
                                                </div>
                                                <el-image style="width: 100px; height: 100px" :src="'https://cdn.toodudu.com/uploads/2023/11/01/time_shop.png'"></el-image>
                                            </div>
                                            <p slot="reference" class="iconfont cursorp" style="display: table; margin-right: 20px; color: #278ff0;">查看示例</p>
                                        </el-tooltip>
                                    </div>
                                </div>
                            </el-form-item>
                            <el-form-item label="URL链接" label-width="110px" v-if="templateSetForm.url">
                                <div class="s-flex ai-ct">
                                    <el-form-item style="margin-bottom: 22px;" :prop="'url.alias'">
                                        <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="templateSetForm.url.alias" @change="templateSetForm.url.value = '', templateSetForm.url.default_selection_data = [], clearFormValidate(`form_url_value`)" @clear="templateSetForm.url.value = ''" placeholder="请选择" style="width: 150px;">
                                            <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item :ref="`form_url_value`" style="margin-bottom: 22px;" :prop="'url.value'" :rules="{ required: templateSetForm.url.alias != '' && computedOptionsPlaceholder(templateSetForm.url.alias), message: computedOptionsPlaceholder(templateSetForm.url.alias), trigger: 'blur' }">
                                        <el-select v-model="templateSetForm.url.value"
                                           v-if="computedOptionsPlaceholder(templateSetForm.url.alias) && computedOptionsCanRemote(templateSetForm.url.alias)"
                                           filterable
                                           remote
                                           reserve-keyword
                                           clearable
                                           :remote-method="async (query) => {
                                                await handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: templateSetForm.url.alias }, parent: 'url', target: 'default_selection_data' }),
                                                handleRemoteSearchChose({ parent: `url.default_selection_data`, chose: templateSetForm.url.value })
                                           }"
                                           @change="(value) => handleRemoteSearchChose({ parent: `url.default_selection_data`, chose: templateSetForm.url.value, ref: 'form_url_value' })"
                                           @focus="handleRemoteSearchChose({ parent: `url.default_selection_data`, chose: templateSetForm.url.value })"
                                           :placeholder="computedOptionsPlaceholder(templateSetForm.url.alias)" style="width: 350px">
                                            <el-option v-for="option in templateSetForm.url.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                        </el-select>
                                        <el-input class="radius-left-none__input" v-else v-model="templateSetForm.url.value" :disabled="(!computedOptionsPlaceholder(templateSetForm.url.alias) && templateSetForm.url.alias != '' && templateSetForm.url.alias != null) || (!templateSetForm.url.alias && !templateSetForm.url.value)" :placeholder="computedOptionsPlaceholder(templateSetForm.url.alias) ? computedOptionsPlaceholder(templateSetForm.url.alias) : !computedOptionsPlaceholder(templateSetForm.url.alias) && templateSetForm.url.alias != '' && templateSetForm.url.alias != null ? '系统生成链接' : ''" style="width: 350px;"></el-input>
                                    </el-form-item>
                                </div>
                                <p class="warning-text">控制是否在标题右侧显示更多，配置链接后将跳转至指定页面。</p>
                            </el-form-item>
                        </div>
                    </div>
                    <div class="drawer-item">
                        <div class="drawer-item-dt">
                            <h1>推荐活动</h1>
                        </div>
                        <div class="drawer-item-dd" style="padding: 0 20px;" v-if="templateSetForm.activity_data">
                            <el-form-item label="选择活动" label-width="110px">
                                <div class="s-flex ai-ct">
                                    <el-select
                                            style="width: 500px;"
                                            v-model="selected_activity_id"
                                            filterable
                                            remote
                                            reserve-keyword
                                            clearable
                                            :remote-method="(query) => handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: query, page_type: 15 }, target: 'default_selection_data' })"
                                            @change="(value) => handleRemoteSearchChose({ parent: `default_selection_data`, chose: templateSetForm.activity_data })"
                                            @focus="handleRemoteSearchChose({ parent: `default_selection_data`, chose: templateSetForm.activity_data })"
                                            placeholder="请输入活动ID或名称">
                                        <el-option v-for="item in templateSetForm.default_selection_data" :key="item.value" :disabled="item.is_disabled" :label="item.label" :value="item.value"></el-option>
                                    </el-select>
                                    <div class="drawer-btn primary s-flex ai-ct jc-ct" style="margin-left: 20px;" :class="{ disabled: templateSetForm.activity_data.length >= 10 }"
                                         @click="() => {
                                            const filter = templateSetForm.default_selection_data.filter(selected => selected.value == selected_activity_id)
                                            filter.length && handleClickAddData({ dom: `activity_data`, len: 10, warning_text: '推荐产品', data: { label: filter[0].label, value: filter[0].value } }),
                                            filter.length && (templateSetForm.activity_ids = templateSetForm.activity_data.map(act => { return act.value })),
                                            filter.length && handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: templateSetForm.activity_ids, page_type: 16 }, parent: `activity_data`, target: 'default_selection_data' }),
                                            selected_activity_id = ''
                                        }"
                                    >添加（@{{ templateSetForm.activity_data.length }}/10）</div>
                                </div>
                                <p class="warning-text">多选。仅支持选择秒杀、特卖，且状态是未开始、进行中的活动</p>
                            </el-form-item>
                            <el-form-item label="已选择活动" label-width="110px" class="cate-choosed" :prop="'activity_data'" :rules="[{ required: true, message: '请选择活动', trigger: 'blur' }]">
                                <ul class="s-flex ai-ct flex-wrap" v-if="templateSetForm.activity_data.length">
                                    <li class="s-flex ai-ct cursorp" v-for="(child, childIndex) in templateSetForm.activity_data" v-if="child.label">
                                        <span>@{{ child.label }}</span>
                                        <em class="iconfont" @click="handleClickDeleteActivityData({ index: childIndex, parent: `activity_data`, list: templateSetForm.items, list_value: 'act_id' }), templateSetForm.activity_ids.splice(childIndex, 1)">&#xe634;</em>
                                    </li>
                                </ul>
                                <p class="warning-text" style="line-height: 40px; font-size: 14px;" v-else>请选择活动</p>
                            </el-form-item>
                            <el-form-item label="选择活动商品" label-width="110px" required></el-form-item>
                            <dl v-if="templateSetForm.items">
                                <dt>
                                    <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                        <li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">
                                            <span>*</span>
                                            <label>商品名称</label>
                                        </li>
                                        <li class="s-flex ai-ct jc-ct" style="width: 90px;">排序</li>
                                        <li class="s-flex ai-ct jc-ct" style="width: 100px; margin: 0;">操作</li>
                                    </ul>
                                </dt>
                                <dd class="s-flex flex-dir ai-ct">
                                    <ul class="s-flex ai-ct" style="height: 60px; padding: 0 40px;" v-for="(item, index) in templateSetForm.items">
                                        <li class="s-flex ai-ct jc-ct flex-1">
                                            <el-form-item style="width: 100%;" :key="index" :prop="'items.' + index + '.seckill_id'" :rules="{ required: true, message: '请选择活动商品', trigger: 'blur' }">
                                                <el-select
                                                        style="width: 100%;"
                                                        v-model="item.seckill_id"
                                                        filterable
                                                        @change="(value) => {
                                                            const filter = templateSetForm.activity_data.default_selection_data.filter(item => value == item.value)
                                                            filter.length && (item.act_id = filter[0].act_id),
                                                            handleRemoteSearchChose({ parent: `activity_data.default_selection_data`, chose: templateSetForm.items, child_value: 'seckill_id' })
                                                        }"
                                                        @focus="handleRemoteSearchChose({ parent: `activity_data.default_selection_data`, chose: templateSetForm.items, child_value: 'seckill_id' })"
                                                        placeholder="请输入商品活动ID或商品名称">
                                                    <el-option v-for="option in templateSetForm.activity_data.default_selection_data" :disabled="option.is_disabled" :key="option.value" :label="option.label" :value="option.value"></el-option>
                                                </el-select>
                                            </el-form-item>
                                        </li>
                                        <li style="width: 90px;">
                                            <el-form-item :prop="'items.' + index + '.sort'" :rules="
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
                                                <el-input v-model="item.sort" placeholder="1~100" ></el-input>
                                            </el-form-item>
                                        </li>
                                        <li style="width: 100px; margin: 0;">
                                            <div class="drawer-btn cursorp s-flex ai-ct jc-ct" @click="handleClickDeleteData({ index, parent: `items` })">删除</div>
                                        </li>
                                    </ul>
                                    <p class="warning-nodata" v-if="!templateSetForm.items.length">暂无数据！</p>
                                    <div class="drawer-btn primary s-flex ai-ct jc-ct" :class="{ disabled: templateSetForm.items.length >= 10 }" @click="handleClickAddData({ dom: 'items', len: 10, warning_text: '商品', data: {'seckill_id':'', act_id: '', sort: '1' } })">添加（@{{templateSetForm.items.length}}/10）</div>
                                </dd>
                            </dl>
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
        <el-dialog
                title="提示"
                :visible.sync="is_show_imageview"
                width="600px">
            <div class="img-set s-flex ai-ct jc-ct">
                <img :src="image_view" alt="">
            </div>
        </el-dialog>
        <material-lib
                :is_show="is_show_material_dialog"
                :model="material_dialog_model"
                :selected_count="material_dialog_selected_count"
                :max_count="material_dialog_max_count"
                :upload_validate="uploadValidateType"
                @close="is_show_material_dialog = false"
                @update="handleChangeUploadFile" />
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
    Vue.component('home-time-shopping', {
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
                selected_activity_id: '',
                is_show_imageview: false,
                image_view: '',
                selected_activity_id: '',
                msg_error: null,
                is_show_material_dialog: false,
                material_dialog_model: 'single',
                material_dialog_max_count: 1,
                material_dialog_selected_count: 0,
                uploadValidateType: 2
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
        template: '#home-time-shopping',
        methods: {
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting (data) {
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
                this.templateSetForm.slideIndex = 0
                this.templateSetFormType = item.component_name
                this.templateSetForm.default_selection_data = this.templateSetForm.default_selection_data ? this.templateSetForm.default_selection_data : []
                this.templateSetForm.activity_data = this.templateSetForm.activity_data ? this.templateSetForm.activity_data : []
                this.handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: this.templateSetForm.activity_ids, page_type: 16 }, parent: `activity_data`, target: 'default_selection_data' })
                this.is_show_drawer = true
            },
            /** 点击记录图片上传类型 **/
            handleClickUploadFile (data) {
                const { parent, validate, target, index, size } = data
                this.uploadParent = parent || ''
                this.uploadValidateType = validate
                this.uploadTarget = target || null
                this.uploadIndex = !isNaN(index) ? index : 'null'
                // this.$refs['upload_file'].click()
                this.is_show_material_dialog = true
                this.material_dialog_model = 'single'
                this.material_dialog_max_count = 1
                this.material_dialog_selected_count = 0
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
            async handleChangeUploadFile (data) {
                let result;
                if (this.material_dialog_model === 'single') {
                    result = data[0].img
                } else {
                    result = []
                    data.forEach(item => {
                        this.uploadDataItem[this.uploadImageName] = item.img
                        result.push({ ...this.uploadDataItem })
                    })
                }
                if (this.uploadParent) {
                    let newValue = this.getNestedProperty(this.templateSetForm, this.uploadParent)
                    if (this.material_dialog_model === 'single') {
                        this.$set(newValue, this.uploadTarget, result)
                    } else {
                        newValue[this.uploadTarget] = newValue[this.uploadTarget].concat(result)
                        this.$set(newValue, this.uploadTarget, newValue[this.uploadTarget])
                    }
                } else {
                    if (this.material_dialog_model === 'single') {
                        this.$set(this.templateSetForm, this.uploadTarget, result)
                    } else {
                        this.templateSetForm[this.uploadTarget] = this.templateSetForm[this.uploadTarget].concat(result)
                        this.$set(this.templateSetForm, this.uploadTarget, this.templateSetForm[this.uploadTarget])
                    }
                }

                this.clearFormValidate(`templateSetForm`)
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
                const { path, method, params, parent, target } = data
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
                        this.$message.error(res.message)
                    }
                    if (params.page_type == 15) {
                        this.handleRemoteSearchChose({ parent: `default_selection_data`, chose: this.templateSetForm.activity_data })
                    } else if (params.page_type == 16) {
                        this.handleRemoteSearchChose({ parent: `${parent}.default_selection_data`, chose: this.templateSetForm.items, child_value: 'seckill_id', options: 'default_selection_data' })
                    }
                    this.$forceUpdate()
                })
            },
            /** 下拉选中禁用 */
            handleRemoteSearchChose (data) {
                setTimeout(() => {
                    const { parent, chose, value = 'value', child_value = 'value', ref, options } = data
                    const choseType = Object.prototype.toString.call(chose).slice(8, -1)
                    const newList = this.getNestedProperty(this.templateSetForm, parent)
                    let optionList = null
                    options && (optionList = this.getNestedProperty(this.templateSetForm, options))
                    if (newList && newList.length) {
                        if (choseType == 'Array') {
                            newList.map(item => chose.some(child => item.is_disabled = item[value] == child[child_value]))
                        } else {
                            newList.map(item => item.is_disabled = item[value] == chose)
                        }
                    } else {
                        (optionList && optionList.length) && optionList.map(item => item.is_disabled = false)
                    }
                    ref && this.clearFormValidate(ref)
                }, 200)
                setTimeout(() => {
                    this.$forceUpdate()
                }, 300)
                // (newList && newList.length) && (newList.map(item => item.is_disabled = item[value] == chose))
            },
            /** 点击添加组件数据 */
            async handleClickAddData (value) {
                const { dom, len, warning_text, data } = value
                let newValue = this.getNestedProperty(this.templateSetForm, dom)
                if (newValue.length >= len) {
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.error(`最多可添加${len}个${warning_text}`)
                    return false
                }
                newValue.push(data)
            },
            /** 点击批量添加图片组件数据 */
            handleClickAddImageData (data) {
                let { type, target, image_name = 'image', max, length, parent, validate = 2, item } = data
                if (max > -1 && length >= max) return false;
                this.uploadType = type
                this.uploadTarget = target
                this.uploadParent = parent
                this.uploadValidateType = validate
                this.uploadDataItem = item
                this.uploadImageName = image_name
                // this.$refs['upload_file'].click()
                this.is_show_material_dialog = true
                this.material_dialog_model = 'multiple'
                this.material_dialog_max_count = max
                this.material_dialog_selected_count = length
            },
            /** 点击删除组件数据 */
            async handleClickDeleteData (data) {
                const { index, parent } = data
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
                newValue.splice(index, 1)
                this.msg_error && this.msg_error.close()
                this.msg_error = this.$message.success('删除成功！')
            },
            /** 点击删除组件数据 */
            async handleClickDeleteActivityData (data) {
                const { index, parent, list, list_value } = data
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
                //  获取删除活动对应的商品数据
                const delete_length = list.filter(item => item[list_value] == newValue[index].value)
                //  再将商品列表中的活动ID与即将删除的商品数据列表中的活动ID进行比较，并删除
                list.map((item, key) => {
                    if (delete_length.some(child => child[list_value] == item[list_value])) {
                        list.splice(key, 1)
                    }
                })
                //  删除已选择的活动，清除禁用的下拉选项
                setTimeout(() => {
                    this.handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: this.templateSetForm.activity_ids, page_type: 16 }, parent: `activity_data`, target: 'default_selection_data' })
                    this.handleRemoteSearchChose({ parent: `default_selection_data`, chose: this.templateSetForm.activity_data })
                    newValue.splice(index, 1)
                    this.$forceUpdate()
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.success('删除成功！')
                }, 200)
            },
            /** 点击提交设置弹窗数据 */
            handleClickSubmitSetForm (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        if (!this.templateSetForm.items.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error('至少添加1个商品')
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
