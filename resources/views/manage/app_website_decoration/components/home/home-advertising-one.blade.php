<style>
    /*广告位1*/
    .home-advertising-one .swiper-slide,
    .home-advertising-one .swiper-slide img { width: 100%; height: 100% }
    .home-advertising-one .swiper-slide { border-radius: 20px; overflow: hidden; position: relative;}
    .home-advertising-one .swiper-slide .ad-tips {width: 120px; height: 40px; background: linear-gradient( 180deg, rgba(51,51,51,0.6) 0%, rgba(0,0,0,0.6) 100%); border-radius: 20px 0 0 0; text-align: center; line-height: 40px; color: #fff; font-size: 24px; position: absolute; bottom: 0; right: 0;}

    .home-advertising-one .carousel-indicators li { width: 10px; height: 10px; border-radius: 20px; background-color: rgba(255, 255, 255, 0.5); }
    .home-advertising-one .carousel-indicators li.active { width: 26px; background-color: #ffffff; }

    .template-one-banner-swiper { width: 710px; height: var(--swiper-height); overflow: hidden; }
    .template-one-banner-swiper .swiper-slide { height: var(--swiper-height); border-radius: 20px; overflow: hidden; }
    .template-one-banner-swiper .swiper-pagination-bullet { width: 10px; height: 10px; border-radius: 20px; background: rgba(255, 255, 255, 0.5); }
    .template-one-banner-swiper .swiper-pagination-bullet.swiper-pagination-bullet-active { width: 26px; background: rgba(255, 255, 255, 1); }
</style>

<script type="text/x-template" id="home-advertising-one">
    <div class="temp-item home-advertising-one template-banner template-public-width"  style="min-height: 200px;" :class="{ 's-flex ai-ct jc-ct': temp_item.activity_info && !temp_item.activity_info.length, active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <public-mark-template :title="temp_item.name" :index="temp_index" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>
        <template>
            <div class=" s-flex ai-ct" v-if="!temp_item.data || computedDataType(temp_item.data) == 'Array' || !temp_item.data.items.length">
                <div class="template-data-null border-line backg-color-white s-flex ai-ct jc-ct" style="height: 200px;">未上传图片</div>
            </div>
            <div class="s-flex ai-ct" v-else>
                {{--<carousel-swiper
                        :width="temp_item.data.width"
                        :height="temp_item.data.height"
                        :list="temp_item.data.items"
                        :key="swiperKey">
                    <template slot="default" slot-scope="scope">
                        <img :src="scope.row.icon" alt="">
                    </template>
                </carousel-swiper>--}}
                <div :style="{ '--swiper-height': temp_item.data.height + 'px', 'height': temp_item.data.height + 'px' }">
                    <div class="swiper template-one-banner-swiper s-flex ai-ct flex-wrap jc-bt" :data-height="temp_item.data.height" :style="{ height: temp_item.data.height + 'px' }" :class="`template-one-banner-swiper${temp_index}`">
                        <div class="swiper-wrapper" :style="{ height: temp_item.data.height + 'px' }">
                            <div class="swiper-slide img-set-ad img-set-full" v-for="(item, index) in temp_item.data.items" :data-height="temp_item.data.height" :style="{ width: temp_item.data.width + 'px', height: temp_item.data.height + 'px' }">
                                <img :src="item.icon" alt="" />
                                <div class="ad-tips" v-if="item.is_ad && is_ad">广告</div>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
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
                    <!--设置板块：Banner轮播图-->
                    <template v-if="templateSetForm.data">
                        <div class="drawer-item">
                            <div class="drawer-item-dt">
                                <h1>广告位尺寸</h1>
                            </div>
                            <div class="drawer-item-dd" style="padding: 0 20px;">
                                <p class="warning-text" style="margin: 30px 0;">若广告位尺寸被修改，则广告位数据需要按照修改后的尺寸作图，否则在手机中会变形显示</p>
                                <el-form-item label="图片宽高" label-width="80px" :prop="'height'" :rules="
                                    [
                                        { required: true, message: '请输入图片高度', trigger: 'blur' },
                                        { validator: (rule, value, callback) => {
                                            if (value < 200 || value > 350) {
                                                callback(new Error('图片高度范围是200px~350px'));
                                            } else if (isNaN(value)) {
                                                callback(new Error('请输入数字'));
                                            } else if (!Number.isInteger(value * 1)) {
                                                callback(new Error('请输入整数'));
                                            } else { callback(); }
                                        }, trigger: 'blur' },
                                    ]
                                    ">
                                    <div class="s-flex ai-ct">
                                        <el-input v-model="templateSetForm.width" disabled style="width: 200px;"></el-input>&nbsp;&nbsp;px
                                        <p style="margin: 0 20px;">~</p>
                                        <el-input v-model="templateSetForm.height" style="width: 200px;"></el-input>&nbsp;&nbsp;px
                                    </div>
                                    <p class="warning-text" style="line-height: 2; margin: 10px 0;">宽度不支持修改</p>
                                    <p class="warning-text" style="line-height: 2; margin: 10px 0;">高度范围：200px~350px，只支持设置为整数</p>
                                </el-form-item>
                            </div>
                        </div>
                        <div class="drawer-item">
                            <div class="drawer-item-dt">
                                <h1>广告位数据</h1>
                            </div>
                            <div class="drawer-item-dd">
                                <div class="s-flex ai-ct jc-bt" style="margin: 20px 0;">
                                    <div class="s-flex ai-ct" style="margin: 20px 0;">
                                        <div class="drawer-upload-warning" style="margin: 0;">
                                            <p>支持jpg、jpeg、png、gif格式</p>
                                            <p>建议大小：2M内</p>
                                        </div>
                                    </div>
                                    <div class="drawer-btn s-flex ai-ct jc-ct cursorp" :class="{ disabled: templateSetForm.data.length >= 20 }" @click="handleClickAddImageData({ target: 'data', max: 20, length: templateSetForm.data.length, item: { 'image': '', time_arr: [], date: { type: 1, value: {} }, 'alias': 'https', 'value': '', 'url': { 'alias': 'https', 'value': '', }, 'sort': 1, 'is_show': 1, 'is_ad': 0 } })">
                                        <em class="iconfont">&#xe620;</em>
                                        <label>添加（@{{ templateSetForm.data.length }}/20）</label>
                                    </div>
                                </div>
                                <dl>
                                    <dt>
                                        <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                                <span>*</span>
                                                <label>图片</label>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct flex-1" style="width: 100%;">URL链接</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 210px;">
                                                <span>*</span>
                                                <label>显示时间</label>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 70px;">排序</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 70px;" v-if="is_ad">
                                                <label>广告</label>
                                                <el-tooltip
                                                        effect="light"
                                                        placement="right"
                                                        width="200"
                                                        trigger="hover">
                                                    <div slot="content" class="drawer-upload-warning" style="width: 300px;">
                                                        <p>《互联网广告管理暂行办法》中第七条规定：互联网广告应当具有可识别性，显著标明“广告”，使消费者能够辨明其为广告。</p>
                                                        <p>1、商业广告定义：以推销商品或服务为目的的，含有链接的文字、图片或者视频等形式的广告、电子邮件广告、付费搜索广告、商业性展示中的广告以及其他通过互联网媒介商业广告等；</p>
                                                        <p>2、付费搜索广告应当与自然搜索结果明显区分；</p>
                                                        <p>3、对于违反规定，工商部门可对广告发布者处十万元以下罚款。</p>
                                                    </div>
                                                    <em class="iconfont cursorp">&#xe72d;</em>
                                                </el-tooltip>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px; text-align: center;">是否显示</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 100px; margin: 0;">操作</li>
                                        </ul>
                                    </dt>
                                    <dd class="s-flex flex-dir ai-ct">
                                        <ul class="s-flex ai-ct" style="height: 90px; padding: 0 40px;" v-for="(item, index) in templateSetForm.data">
                                            <li style="width: 90px;">
                                                <el-form-item :key="index" :prop="'data.' + index + '.image'" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                                    <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                        <img v-if="item.image" :src="item.image" alt="">
                                                        <div  class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!item.image" @click="handleClickUploadFile({ parent: `data.${index}`, validate: 2, target: 'image' })">
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <p>未上传</p>
                                                        </div>
                                                        <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `data.${index}`, validate: 2, target: 'image' })">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile({ parent: `data.${index}`, target: 'image' })">
                                                                <em class="iconfont">&#xe738;</em>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </el-form-item>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct flex-1" style="width: 100%;">
                                                <el-form-item>
                                                    <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="item.alias" @change="item.value = '', item.default_selection_data = [], item.alias == 'yi_qi_xiu' && handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: '', alias: item.alias }, parent: `data.${index}.url`, target: 'default_selection_data' }), clearFormValidate(`data_url_value${index}`)" @clear="item.value = ''" placeholder="请选择" style="width: 170px;">
                                                        <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                                    </el-select>
                                                </el-form-item>
                                                <el-form-item :ref="`data_url_value${index}`" :prop="'data.' + index + '.value'" :rules="{ required: item.alias != '' && computedOptionsPlaceholder(item.alias), message: computedOptionsPlaceholder(item.alias), trigger: 'blur' }">
                                                    <el-select v-model="item.value"
                                                       v-if="computedOptionsPlaceholder(item.alias) && computedOptionsCanRemote(item.alias)"
                                                       filterable
                                                       remote
                                                       reserve-keyword
                                                       clearable
                                                       :remote-method="(query) => {
                                                            handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: item.alias }, parent: `data.${index}.url`, target: 'default_selection_data' }),
                                                            handleRemoteSearchChose({ parent: `data.${index}.url.default_selection_data`, chose: item.value })
                                                       }"
                                                       @change="(value) => handleRemoteSearchChose({ parent: `data.${index}.url.default_selection_data`, list: templateSetForm.data, index, chose: item.value, ref: `data_url_value${index}`, selected: item.url })"
                                                       @focus="handleRemoteSearchChose({ parent: `data.${index}.url.default_selection_data`, chose: item.value })"
                                                       :placeholder="computedOptionsPlaceholder(item.alias)" style="width: 150px">
                                                        <el-option v-for="option in item.url.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                    </el-select>
                                                    <el-input class="radius-left-none__input" v-else v-model="item.value" :disabled="(!computedOptionsPlaceholder(item.alias) && item.alias != '' && item.alias != null) || (!item.alias && !item.value)" :placeholder="computedOptionsPlaceholder(item.alias) ? computedOptionsPlaceholder(item.alias) : !computedOptionsPlaceholder(item.alias) && item.alias != '' && item.alias != null ? '系统生成链接' : ''" style="width: 150px;"></el-input>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 210px;">
                                                <el-form-item>
                                                    <div class="s-flex ai-ct" style="min-height: 40px; padding: 0 15px; border: 1px solid #DCDFE6; border-radius: 4px;">
                                                        <em class="iconfont">&#xe678;</em>
                                                        <el-date-picker
                                                                class="flex-dir"
                                                                :class="{ 'range-separator-hide': item.time_arr && item.time_arr.length }"
                                                                prefix-icon="el-hide"
                                                                value-format="yyyy-MM-dd HH:mm:ss"
                                                                v-model="item.time_arr"
                                                                type="datetimerange"
                                                                :default-time="['00:00:00', '23:59:59']"
                                                                :picker-options="pickerOptions"
                                                                @change="$forceUpdate()"
                                                                @blur="$forceUpdate()"
                                                                @focus="$forceUpdate()"
                                                                range-separator="长期">
                                                        </el-date-picker>
                                                        <em class="el-icon-circle-close cursorp" v-if="item.time_arr && item.time_arr.length" style="color: #C0C4CC;" @click="item.time_arr = [], $forceUpdate()"></em>
                                                    </div>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 70px;">
                                                <el-form-item :prop="'data.' + index + '.sort'" :rules="
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
                                            <li style="width: 70px; text-align: center;" v-if="is_ad">
                                                <el-switch v-model="item.is_ad" :active-value="1" :inactive-value="0" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                            </li>
                                            <li style="width: 90px; text-align: center;">
                                                <el-switch v-model="item.is_show" :active-value="1" :inactive-value="0" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                            </li>
                                            <li style="width: 100px; margin: 0;">
                                                <div class="drawer-btn cursorp s-flex ai-ct jc-ct" @click="handleClickDeleteData(index, `data`)">删除</div>
                                            </li>
                                        </ul>
                                        <p class="warning-nodata" v-if="!templateSetForm.data.length">暂无数据！</p>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </template>
                </div>
                <el-form-item style="margin: 0; box-shadow: 0 -0.2rem 0.2rem -0.22rem rgba(0, 0, 0, 0.2);">
                    <div class="s-flex ai-ct jc-ct" style="margin: 20px 0;">
                        <div style="width: 120px; margin: 0 15px;" class="drawer-btn primary s-flex ai-ct jc-ct" @click="handleClickSubmitSetForm('templateSetForm')">保存</div>
                        <div style="width: 120px; margin: 0 15px;" class="drawer-btn s-flex ai-ct jc-ct" @click.stop="handleClickCancelSetForm('templateSetForm')">取消</div>
                    </div>
                </el-form-item>
            </el-form>
            {{--<el-dialog
                title="提示"
                :visible.sync="is_show_imageview"
                width="900px">

        </el-dialog>--}}
        </el-drawer>
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

<!--模块遮罩组件-->
@include('manage.app_website_decoration.components.template-mark-setting')
<!--广告轮播组件-->
@include('manage.app_website_decoration.components.carousel-swiper')
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
    Vue.component('home-advertising-one', {
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
            is_ad: {
                type: Boolean,
                default: false
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
                swiperKey: Math.round(new Date() / 1000),
                pickerOptions: {    //  日期选择范围限制
                    disabledDate: (time) => {
                        return time.getTime() < Date.now() - (24*60*60*1000)
                    }
                },
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
        template: '#home-advertising-one',
        methods: {
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting (data) {
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
                this.templateSetForm.data.map(oneitem => {
                    if (oneitem.date.value.start_time && oneitem.date.value.end_time) {
                        oneitem.time_arr = [oneitem.date.value.start_time, oneitem.date.value.end_time]
                    } else {
                        oneitem.time_arr = []
                    }
                    if (oneitem.url) {
                        oneitem.alias = oneitem.url.alias
                        oneitem.value = oneitem.url.value
                    }
                    oneitem.date.type = oneitem.time_arr.length ? 0 : 1
                })
                this.templateSetFormType = item.component_name
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
                    this.templateSetForm[this.uploadTarget] = this.templateSetForm[this.uploadTarget].concat(result)
                    this.$set(this.templateSetForm, this.uploadTarget, this.templateSetForm[this.uploadTarget])
                }

                this.clearFormValidate(`templateSetForm`)
            },
            /** 删除图片操作 **/
            handleClickDeleteFile (data) {
                const { parent, target } = data
                if (parent) {
                    let newValue = this.getNestedProperty(this.templateSetForm, parent)
                    this.$set(newValue, target, '');
                } else {
                    this.$set(this.templateSetForm, target, '')
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
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.error(res.message)
                    }
                })
            },
            /** 下拉选中禁用 */
            handleRemoteSearchChose (data) {
                setTimeout(() => {
                    const { parent, list, index, chose, value = 'value', child_value, ref, selected } = data
                    const choseType = Object.prototype.toString.call(chose).slice(8, -1)
                    const newList = this.getNestedProperty(this.templateSetForm, parent)
                    if (newList && newList.length) {
                        if (choseType == 'Array') {
                            newList.map(item => chose.some(child => item.is_disabled = item[value] == child[child_value]))
                        } else {
                            newList.map(item => item.is_disabled = item[value] == chose)
                        }
                    }
                    //  针对易企秀下拉选择更换广告图片
                    /*if (selected && selected.alias == 'yi_qi_xiu') {
                        const find_data = selected.default_selection_data.find(option => option.value == selected.value)
                        if (find_data) list[index].image = find_data.image
                    }*/
                    ref && this.clearFormValidate(ref)
                    this.$forceUpdate()
                }, 200)
                // (newList && newList.length) && (newList.map(item => item.is_disabled = item[value] == chose))
            },
            /** 点击添加组件数据 */
            async handleClickAddData (dom, len, warning_text, data) {
                const item = JSON.parse(JSON.stringify(data))
                let newValue = this.getNestedProperty(this.templateSetForm, dom)
                /*const is_null = newValue.filter(item => item.image == '' || (item.url.alias != '' && item.url.value == ''))
                if (is_null.length) {
                    this.$message.error('请先完善数据再进行添加')
                    return false
                }*/
                if (newValue.length >= len) {
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.error(`最多可添加${len}个${warning_text}`)
                    return false
                }
                newValue.push(item)
            },
            /** 点击批量添加图片组件数据 */
            handleClickAddImageData (data) {
                let { type, target, image_name = 'image', max, length, parent, validate = 2, item } = data
                if (max > -1 && length >= max) {
                    return false
                }
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
            async handleClickDeleteData (index, parent) {
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
                newValue.splice(index, 1)
                this.msg_error && this.msg_error.close()
                this.msg_error = this.$message.success('删除成功！')
            },
            /** 点击提交设置弹窗数据 */
            handleClickSubmitSetForm (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        if (!this.templateSetForm.data.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error('至少添加1个广告图')
                            return false
                        }

                        this.templateSetForm.data.map(item => {
                            item.date.type = item.time_arr && item.time_arr.length ? 0 : 1
                            if (item.time_arr.length) {
                                item.date.value.start_time = item.time_arr[0]
                                item.date.value.end_time = item.time_arr[1]
                                item.date.type = '0'
                            } else {
                                item.date.value.start_time = ''
                                item.date.value.end_time = ''
                                item.date.type = '1'
                                item.time_arr = []
                            }
                            item.url.alias = item.alias
                            item.url.value = item.value
                        })

                        const newData = JSON.parse(JSON.stringify(this.temp_item))
                        newData.content = this.templateSetForm
                        if (newData.data) delete newData.data
                        if (isNaN(newData.id)) newData.id = ''
                        this.set_save_loading = true
                        const info = { component_name: this.templateSetFormType, component_data: newData, name: this.templateSetForm.name }
                        this.doPost('{!! route('manage.app_web_decoration.get_content_data_by_alias') !!}', info).then(res => {
                            if (res.code == 200) {
                                this.is_show_drawer = false
                                this.is_setting_save = true
                                this.set_save_loading = false
                                this.$set(this.temp_item, 'data', res.data)
                                this.$set(this.temp_item, 'content', this.templateSetForm)
                                this.swiperKey = Math.round(new Date() / 1000),
                                this.$emit('save', { content: this.templateSetForm, data: res.data, index: this.temp_index })

                                setTimeout(() => {
                                    //  如果轮播实例化存在，则销毁重新注册
                                    if (this[`template_one_banner_swiper${this.temp_index || 0}`]) {
                                        this[`template_one_banner_swiper${this.temp_index || 0}`].destroy()
                                    }
                                    this[`template_one_banner_swiper${this.temp_index || 0}`] = new Swiper(`.template-one-banner-swiper${this.temp_index || 0}`, {
                                        initialSlide: 0,
                                        slidesPerView: 1,
                                        autoplay: {
                                            disableOnInteraction: false,
                                            delay: 3000 //3秒切换一次
                                        },
                                        observer: true,
                                        observeParents: true,
                                        loop: this.temp_item.data.items.length > 1,
                                        pagination: {
                                            el: ".swiper-pagination",
                                            clickable: true,
                                        },
                                    });
                                }, 200)
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
        },
        mounted () {
            if (this.temp_item.data && this.temp_item.data.items && this.temp_item.data.items.length) {
                this[`template_one_banner_swiper${this.temp_index}`] = new Swiper(`.template-one-banner-swiper${this.temp_index}`, {
                    initialSlide: 0,
                    slidesPerView: 1,
                    autoplay: {
                        disableOnInteraction: false,
                        delay: 3000 //3秒切换一次
                    },
                    observer: true,
                    observeParents: true,
                    loop: this.temp_item.data.items.length > 1,
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                });
            }
        }
    })
</script>
