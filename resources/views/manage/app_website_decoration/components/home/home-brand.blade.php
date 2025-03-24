<style>
    /* 品牌精选 */
    .template-brand {}
    .template-brand ul { padding: 0 15px 15px 15px; }
    .template-brand ul li { width: 120px; margin: 0 15px; }
    .template-brand ul li .brand-image { width: 120px; height: 40px; }
    .template-brand ul li p { margin: 20px 0; text-align: center; font-size: 20px; }
</style>

<script type="text/x-template" id="home-brand-template">
    <div class="temp-item" :class="{ 's-flex ai-ct jc-ct': temp_item.activity_info && !temp_item.activity_info.length, active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <public-mark-template :title="temp_item.name" :index="temp_index" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>
        <template>
            <template>
                <div class="temp-item-nodata s-flex flex-dir ai-ct jc-ct" v-if="!temp_item.data || computedDataType(temp_item.data) == 'Array' || !temp_item.data.items.length">
                    <p>@{{ temp_item.name }}</p>
                    <p>点击“设置”，配置组件内需要展示的内容吧</p>
                </div>
                <div class="template-brand public-box" v-else>
                    <div class="public-title">
                        <div class="public_text">@{{ temp_item.data.name }}</div>
                        <div class="look_more s-flex ai-ct" v-if="temp_item.data.url">
                            <div class="look_more_view">更多 <em class="iconfont">&#xe772;</em></div>
                        </div>
                    </div>
                    <ul class="s-flex ai-ct flex-wrap">
                        <li v-for="item in temp_item.data.items">
                            <div class="brand-image img-set s-flex ai-ct jc-ct">
                                <img :src="item.image" alt="">
                            </div>
                            <p>@{{ item.title }}</p>
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
                            <el-form-item label="URL链接" label-width="110px" v-if="templateSetForm.url">
                                <div class="s-flex ai-ct">
                                    <el-form-item style="margin-bottom: 22px;">
                                        <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="templateSetForm.url.alias" @change="templateSetForm.url.value = '', templateSetForm.url.default_selection_data = [], clearFormValidate('form_url_value')" @clear="templateSetForm.url.value = ''" placeholder="请选择" style="width: 150px;">
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
                                                   :remote-method="(query) => {
                                                handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: templateSetForm.url.alias }, parent: 'url', target: 'default_selection_data' }),
                                                handleRemoteSearchChose({ parent: `url.default_selection_data`, chose: templateSetForm.url.value })
                                           }"
                                                   @change="(value) => handleRemoteSearchChose({ parent: `url.default_selection_data`, chose: templateSetForm.url.value, ref: 'form_url_value' })"
                                                   @focus="handleRemoteSearchChose({ parent: `url.default_selection_data`, chose: templateSetForm.url.value })"
                                                   :placeholder="computedOptionsPlaceholder(templateSetForm.url.alias)" style="width: 300px">
                                            <el-option v-for="option in templateSetForm.url.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                        </el-select>
                                        <el-input class="radius-left-none__input" v-else v-model="templateSetForm.url.value" :disabled="(!computedOptionsPlaceholder(templateSetForm.url.alias) && templateSetForm.url.alias != '' && templateSetForm.url.alias != null) || (!templateSetForm.url.alias && !templateSetForm.url.value)" :placeholder="computedOptionsPlaceholder(templateSetForm.url.alias) ? computedOptionsPlaceholder(templateSetForm.url.alias) : !computedOptionsPlaceholder(templateSetForm.url.alias) && templateSetForm.url.alias != '' && templateSetForm.url.alias != null ? '系统生成链接' : ''" style="width: 350px;"></el-input>
                                    </el-form-item>
                                </div>
                                <p class="warning-text">控制是否在标题右侧显示更多，配置链接后将跳转至指定页面。</p>
                            </el-form-item>
                        </div>
                    </div>
                    <div class="drawer-item" v-if="templateSetForm.items">
                        <div class="drawer-item-dt">
                            <h1>品牌数据</h1>
                        </div>
                        <div class="drawer-item-dd">
                            <dl>
                                <dt>
                                    <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                        <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                            <span>*</span>
                                            <label>图片</label>
                                            <el-tooltip
                                                    style="margin: 0 20px 0 0;"
                                                    effect="light"
                                                    placement="top"
                                                    width="200"
                                                    trigger="hover">
                                                <div slot="content" class="drawer-upload-warning">
                                                    <p>支持jpg、jpeg、png、gif格式</p>
                                                    <p>建议尺寸：120px*62px</p>
                                                    <p>建议大小：2M内</p>
                                                </div>
                                                <em class="iconfont cursorp">&#xe72d;</em>
                                            </el-tooltip>
                                        </li>
                                        <li class="s-flex ai-ct jc-ct" style="width: 200px;">
                                            <span>*</span>
                                            <label>名称</label>
                                        </li>
                                        <li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">URL链接</li>
                                        <li class="s-flex ai-ct jc-ct" style="width: 90px;">排序</li>
                                        <li class="s-flex ai-ct jc-ct" style="width: 100px; margin: 0;">操作</li>
                                    </ul>
                                </dt>
                                <dd class="s-flex flex-dir ai-ct">
                                    <ul class="s-flex ai-ct" style="height: 90px; padding: 0 40px;" v-for="(item, index) in templateSetForm.items">
                                        <li style="width: 90px;">
                                            <el-form-item :key="index" :prop="'items.' + index + '.image'" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                                <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                    <img v-if="item.image" :src="item.image" alt="">
                                                    <div  class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!item.image" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'image'})">
                                                        <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                            <em class="iconfont">&#xe727;</em>
                                                        </div>
                                                        <p>未上传</p>
                                                    </div>
                                                    <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                        <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'image'})">
                                                            <em class="iconfont">&#xe727;</em>
                                                        </div>
                                                        <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile({ parent: `items.${index}`, target: 'image'})">
                                                            <em class="iconfont">&#xe738;</em>
                                                        </div>
                                                    </div>
                                                </div>
                                            </el-form-item>
                                        </li>
                                        <li style="width: 200px;">
                                            <el-form-item :prop="'items.' + index + '.title'" :rules="
                                                [
                                                    { required: true, message: '请输入名称', trigger: 'blur' },
                                                    { max: 6, message: '名称不能超过6个字符', trigger: 'blur' },
                                                    { validator: (rule, value, callback) => {
                                                        //  校验是否有相同的名称
                                                        const same_name = templateSetForm.items.filter(data => value == data.title)
                                                        if (same_name.length > 1) { callback(new Error('该名称已存在，请修改！')); } else { callback(); }
                                                    }, trigger: 'blur' },
                                                ]
                                            ">
                                                <el-input v-model="item.title" placeholder="请输入名称" ></el-input>
                                            </el-form-item>
                                        </li>
                                        <li class="s-flex ai-ct jc-ct flex-1" style="width: 400px;">
                                            <el-form-item :prop="'items.' + index + '.alias'">
                                                <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="item.alias" @change="item.value = '', item.url.default_selection_data = [], item.alias == 'yi_qi_xiu' && handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: '', alias: item.alias }, parent: `items.${index}.url`, target: 'default_selection_data' }), clearFormValidate(`items_value${index}`)" @clear="item.value = ''" placeholder="请选择" style="width: 200px;">
                                                    <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                                </el-select>
                                            </el-form-item>
                                            <el-form-item :ref="`items_value${index}`" :prop="'items.' + index + '.value'" :rules="{ required: item.alias != '' && computedOptionsPlaceholder(item.alias), message: computedOptionsPlaceholder(item.alias), trigger: 'blur' }">
                                                <el-select v-model="item.value"
                                                   v-if="computedOptionsPlaceholder(item.alias) && computedOptionsCanRemote(item.alias)"
                                                   filterable
                                                   remote
                                                   reserve-keyword
                                                   clearable
                                                   :remote-method="(query) => {
                                                        handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: item.alias }, parent: `items.${index}.url`, target: 'default_selection_data' }),
                                                        handleRemoteSearchChose({ parent: `items.${index}.url.default_selection_data`, chose: item.value })
                                                   }"
                                                   @change="(value) => handleRemoteSearchChose({ parent: `items.${index}.url.default_selection_data`, list: templateSetForm.items, index, chose: item.value, ref: `items_value${index}`, selected: item.url })"
                                                   @focus="handleRemoteSearchChose({ parent: `items.${index}.url.default_selection_data`, chose: item.value })"
                                                   :placeholder="computedOptionsPlaceholder(item.alias)" style="width: 330px">
                                                    <el-option v-for="option in item.url.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                </el-select>
                                                <el-input class="radius-left-none__input" v-else v-model="item.value" :disabled="(!computedOptionsPlaceholder(item.alias) && item.alias != '' && item.alias != null) || (!item.alias && !item.value)" :placeholder="computedOptionsPlaceholder(item.alias) ? computedOptionsPlaceholder(item.alias) : !computedOptionsPlaceholder(item.alias) && item.alias != '' && item.alias != null ? '系统生成链接' : ''" style="width: 330px;"></el-input>
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
                                            <div class="drawer-btn cursorp s-flex ai-ct jc-ct" @click="handleClickDeleteData(index, `items`)">删除</div>
                                        </li>
                                    </ul>
                                    <p class="warning-nodata" v-if="!templateSetForm.items.length">暂无数据！</p>
                                    <div class="drawer-btn primary s-flex ai-ct jc-ct" :class="{ disabled: templateSetForm.items.length >= 8 }" @click="handleClickAddImageData({ target: 'items', max: 8, length: templateSetForm.items.length, item: {'image':'', 'title': '', 'alias':'https', 'value':'', url: { 'alias':'https', 'value':'' }, 'sort':'1'} })">添加（@{{templateSetForm.items.length}}/8）</div>
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
    Vue.component('home-brand-template', {
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
        template: '#home-brand-template',
        methods: {
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting (data) {
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
                this.templateSetForm.items.map(oneitem => {
                    if (oneitem.url) {
                        oneitem.alias = oneitem.url.alias
                        oneitem.value = oneitem.url.value
                    }
                })
                this.templateSetForm.slideIndex = 0
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
                        this.$forceUpdate()
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
                        if (!this.templateSetForm.items) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error('至少添加1个品牌')
                            return false
                        }

                        this.templateSetForm.items.map(oneitem => {
                            if (oneitem.url) {
                                oneitem.url.alias = oneitem.alias
                                oneitem.url.value = oneitem.value
                            }
                        })

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
