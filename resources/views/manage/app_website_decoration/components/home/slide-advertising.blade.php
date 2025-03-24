<style>
    /*广告位1*/
    .slide-advertising { width: 100px; height: 100px; background-color: #ffffff; position: fixed; right: 0; top: 10%; }
</style>

<script type="text/x-template" id="slide-advertising">
    <div class="temp-item template-public-width" :class="{ active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <div class="slide-advertising" :style="{ top }">
            <public-mark-template title="" :index="temp_index" height="100%" :is_show_delete="false" alias="home" :show_alone="true" :is_show_switch="false" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>
            <div class="s-flex ai-ct" v-if="!temp_item.data || !temp_item.data.icon">
                <div class="photo-null-temp_item border-line backg-color-white s-flex ai-ct jc-ct" style="width: 100px; height: 100px; line-height: 1.5; padding: 0 10px; text-align: center;">@{{ title }}</div>
            </div>
            <div class="img-set s-flex ai-ct jc-ct" v-else>
                <img :src="temp_item.data.icon" alt="">
            </div>
        </div>
        <!--设置弹窗-->
        <el-drawer  :with-header="false" :modal-append-to-body="false" :close-on-press-escape="false" :show-close="false" :wrapper-closable="false" size="1200px" :visible.sync="is_show_drawer" :direction="direction">
            <input type="file" @change="handleChangeUploadFile" value="" style="display: none;" ref="upload_file" />
            <el-form :model="templateSetForm" :rules="templateSetRule" ref="templateSetForm" class="drawer-content" v-load="set_save_loading">
                <div class="drawer-name s-flex ai-ct">
                    <label>设置板块：</label>
                    <span>@{{ temp_item.name }}</span>
                </div>
                <div style="height: calc(100vh - 160px); overflow-y: auto;" ref="drawerBox">
                    <!--设置板块：Banner轮播图-->
                    <template>
                        <div class="drawer-item">
                            <div class="s-flex ai-ct" style="margin: 20px 0;">
                                <div class="drawer-upload-warning" style="margin: 0;">
                                    <p>支持jpg、jpeg、png、gif格式</p>
                                    <p>建议尺寸：@{{ temp_index == 'large_screen' || temp_index == 'red_envelope' ? '750px*750px' : '200px*200px' }}</p>
                                    <p>建议大小：2M内</p>
                                </div>
                            </div>
                            <div class="drawer-item-dd">
                                <dl>
                                    <dt>
                                        <ul class="s-flex ai-ct" style="padding: 0 20px;">
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                                <span>*</span>
                                                <label>图片</label>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 500px;">URL链接</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 400px;"><span>*</span>显示时间</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 70px; text-align: center;">是否显示</li>
                                        </ul>
                                    </dt>
                                    <dd class="s-flex flex-dir ai-ct">
                                        <ul class="s-flex ai-ct" style="height: 90px; padding: 0 20px;">
                                            <li style="width: 90px;">
                                                <el-form-item :prop="'image'" :rules="{ required: templateSetForm.is_show, message: '请上传图片', trigger: 'blur' }">
                                                    <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                        <img v-if="templateSetForm.image" :src="templateSetForm.image" alt="">
                                                        <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!templateSetForm.image" @click="handleClickUploadFile({ target: 'image', validate: 2, fixed_size_width: temp_index == 'large_screen' || temp_index == 'red_envelope' ? 750 : 200, fixed_size_height: temp_index == 'large_screen' || temp_index == 'red_envelope' ? 750 : 200 })">
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <p>未上传</p>
                                                        </div>
                                                        <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ target: 'image', validate: 2, fixed_size_width: temp_index == 'large_screen' || temp_index == 'red_envelope' ? 750 : 200, fixed_size_height: temp_index == 'large_screen' || temp_index == 'red_envelope' ? 750 : 200 })">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile({ target: 'image' })">
                                                                <em class="iconfont">&#xe738;</em>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </el-form-item>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct flex-1" style="width: 500px;" v-if="templateSetForm.url">
                                                <el-form-item>
                                                    <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="templateSetForm.url.alias" @change="templateSetForm.url.value = '', templateSetForm.url.default_selection_data = [], templateSetForm.url.alias == 'yi_qi_xiu' && handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: '', alias: templateSetForm.url.alias }, parent: 'url', target: 'default_selection_data' }), clearFormValidate(`form_url_value`)" @clear="templateSetForm.url.value = ''" placeholder="请选择" style="width: 150px;">
                                                        <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                                    </el-select>
                                                </el-form-item>
                                                <el-form-item :ref="`form_url_value`" :prop="'url.value'" :rules="{ required: templateSetForm.url.alias != '' && computedOptionsPlaceholder(templateSetForm.url.alias), message: computedOptionsPlaceholder(templateSetForm.url.alias), trigger: 'blur' }">
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
                                                       @change="(value) => handleRemoteSearchChose({ parent: `url.default_selection_data`, chose: templateSetForm.url.value, ref: 'form_url_value', selected: templateSetForm.url })"
                                                       @focus="handleRemoteSearchChose({ parent: `url.default_selection_data`, chose: templateSetForm.url.value })"
                                                       :placeholder="computedOptionsPlaceholder(templateSetForm.url.alias)" style="width: 300px">
                                                        <el-option v-for="option in templateSetForm.url.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                    </el-select>
                                                    <el-input class="radius-left-none__input" v-else v-model="templateSetForm.url.value" :disabled="(!computedOptionsPlaceholder(templateSetForm.url.alias) && templateSetForm.url.alias != '' && templateSetForm.url.alias != null) || (!templateSetForm.url.alias && !templateSetForm.url.value)" :placeholder="computedOptionsPlaceholder(templateSetForm.url.alias) ? computedOptionsPlaceholder(templateSetForm.url.alias) : !computedOptionsPlaceholder(templateSetForm.url.alias) && templateSetForm.url.alias != '' && templateSetForm.url.alias != null ? '系统生成链接' : ''" style="width: 300px;"></el-input>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 400px;">
                                                <el-form-item>
                                                    <div class="s-flex ai-ct" style="min-height: 40px; padding: 0 15px; border: 1px solid #DCDFE6; border-radius: 4px;">
                                                        <em class="iconfont">&#xe678;</em>
                                                        <el-date-picker
                                                                class="flex-dir"
                                                                :class="{ 'range-separator-hide': templateSetForm.time_arr && templateSetForm.time_arr.length }"
                                                                prefix-icon="el-hide"
                                                                value-format="yyyy-MM-dd HH:mm:ss"
                                                                v-model="templateSetForm.time_arr"
                                                                type="datetimerange"
                                                                :default-time="['00:00:00', '23:59:59']"
                                                                :picker-options="pickerOptions"
                                                                @change="$forceUpdate()"
                                                                @blur="$forceUpdate()"
                                                                @focus="$forceUpdate()"
                                                                range-separator="长期">
                                                        </el-date-picker>
                                                        <em class="el-icon-circle-close cursorp" v-if="templateSetForm.time_arr && templateSetForm.time_arr.length" style="color: #C0C4CC;" @click="templateSetForm.time_arr = [], $forceUpdate()"></em>
                                                    </div>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 70px; text-align: center;">
                                                <el-switch v-model="templateSetForm.is_show" :active-value="1" :inactive-value="0" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                            </li>
                                        </ul>
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
                :fixed_size_width="material_fixed_size_width"
                :fixed_size_height="material_fixed_size_height"
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
    Vue.component('slide-advertising', {
        props: {
            temp_index: [Number, String],
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
            top: {
                type: String,
                default: '10%'
            },
            title: {
                type: String,
                default: '10%'
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
                uploadValidateType: 2,
                material_fixed_size_width: 0,
                material_fixed_size_height: 0
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
        template: '#slide-advertising',
        methods: {
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting (data) {
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
                if (this.templateSetForm.date.value.start_time && this.templateSetForm.date.value.end_time) {
                    this.templateSetForm.time_arr = [this.templateSetForm.date.value.start_time, this.templateSetForm.date.value.end_time]
                } else {
                    this.templateSetForm.time_arr = []
                }
                this.templateSetForm.type = this.templateSetForm.time_arr.length ? 0 : 1
                this.templateSetFormType = item.component_name
                this.is_show_drawer = true
            },
            /** 点击记录图片上传类型 **/
            handleClickUploadFile (data) {
                const { parent, validate, target, index, size, fixed_size_width, fixed_size_height } = data
                this.uploadParent = parent || ''
                this.uploadValidateType = validate
                this.uploadTarget = target || null
                this.uploadIndex = !isNaN(index) ? index : 'null'
                // this.$refs['upload_file'].click()
                this.is_show_material_dialog = true
                this.material_dialog_model = 'single'
                this.material_dialog_max_count = 1
                this.material_dialog_selected_count = 0
                this.material_fixed_size_width = fixed_size_width || 0
                this.material_fixed_size_height = fixed_size_height || 0
            },
            /** 校验图片格式 **/
            beforeAvatarUpload(file) {
                return new Promise(resolve => {
                    let resovlt = true
                    if (file && file.type) {
                        const image = new Image();
                        let _URL = window.URL || window.webkitURL;
                        image.src = _URL.createObjectURL(file);
                        image.onload = () => {
                            if (this.temp_index == 'large_screen' || this.temp_index == 'red_envelope') {
                                if ((image.width / image.height) !== 1 || image.width !== 750) {
                                    this.msg_error && this.msg_error.close()
                                    this.msg_error = this.$message.error('大屏广告仅支持上传尺寸为: 750*750');
                                    resolve(false)
                                    return false
                                }
                            }
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
                            resolve(resovlt)
                        }
                    } else {
                        resovlt = false
                        resolve(resovlt)
                    }
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
                        if (find_data) this.templateSetForm.image = find_data.image
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
                        this.templateSetForm.date.type = this.templateSetForm.time_arr && this.templateSetForm.time_arr.length ? 0 : 1
                        if (this.templateSetForm.time_arr.length) {
                            this.templateSetForm.date.value.start_time = this.templateSetForm.time_arr[0]
                            this.templateSetForm.date.value.end_time = this.templateSetForm.time_arr[1]
                            this.templateSetForm.date.type = '0'
                        } else {
                            this.templateSetForm.date.value.start_time = ''
                            this.templateSetForm.date.value.end_time = ''
                            this.templateSetForm.date.type = '1'
                            this.templateSetForm.time_arr = []
                        }

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
        },
        mounted () {}
    })
</script>
