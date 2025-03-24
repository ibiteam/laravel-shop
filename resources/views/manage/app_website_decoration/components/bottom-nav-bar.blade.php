@include('manage.app_website_decoration.style.dialog-setting')
<style>
    /* 标签栏 */
    .temp-item.template-footer { padding: 0; position: relative; }
    .template-footer-con ul { width: 100%; height: 98px; background-color: #ffffff; box-shadow: 0 -0.2rem 0.2rem -0.22rem rgba(0,0,0,.2); }
    .template-footer-con ul li .template-footer-image { width: 44px; height: 44px; }
    .template-footer-con ul li p { padding: 10px 0; font-size: 20px; }
    .el-color-picker {width: 40px; height: 40px;}
</style>

<script type="text/x-template" id="bottom-nav-bar">
    <!-- 标签栏 -->
    <div class="temp-item template-footer public-width" :class="{disabled: is_no_setting}">
        <public-mark-template :title="is_no_setting ? '请前往“首页”进行装修' : templateSetForm.name" height="100%" :index="temp_index" :list="temp_list" :is_show_delete="false" alias="home" :show_alone="true" :is_show_switch="false" :is_no_setting="is_no_setting" @open="() => handleClickOpenTemplateSetting({ name: templateSetForm.name, item: temp_item, index: temp_index })"></public-mark-template>
        <template v-if="templateSetForm.data">
            <div class="template-footer-con">
                <ul class="s-flex ai-ct">
                    <li class="s-flex flex-dir jc-ct ai-ct flex-1" v-for="(menu, menuIndex) in templateSetForm.data.items" :key="`menu${menuIndex}`">
                        <div class="template-footer-image"
                             :style="{
                                width: (menuIndex == 0 && !menu.check_title) || !menu.title ? '88px' : '44px',
                                height: (menuIndex == 0 && !menu.check_title) || !menu.title ? '88px' : '44px',
                            }"
                        >
                            <img :src="menuIndex == 0 ? menu.selection_image : menu.default_image" :width="(menuIndex == 0 && !menu.check_title) || !menu.title ? 88 : 44" :height="(menuIndex == 0 && !menu.check_title) || !menu.title ? 88 : 44" alt="">
                        </div>
                        <p :style="{'color': menuIndex == 0 ? templateSetForm.data.font_selection_color : templateSetForm.data.font_default_color}"
                            v-if="(menuIndex == 0 && menu.check_title) || (menuIndex != 0 && menu.title)">@{{ menuIndex == 0 ? menu.check_title : menu.title }}</p>
                    </li>
                </ul>
            </div>
        </template>
        <!--设置弹窗-->
        <el-drawer :with-header="false" :close-on-press-escape="false" :show-close="false" :wrapper-closable="false" size="1200px" :visible.sync="show_banner_drawer" :direction="direction">
            <input type="file" @change="handleChangeUploadFile" value="" style="display: none;" ref="upload_file" />
            <el-form :model="labelForm" ref="templateSetForm" class="drawer-content" v-loading="set_save_loading">
                <div class="drawer-name s-flex ai-ct">
                    <label>设置板块：</label>
                    <span>@{{ templateSetForm.name }}</span>
                </div>
                <div style="height: calc(100vh - 152px); overflow-y: auto;" ref="drawerBox">
                    <template v-if="templateSetForm.content">
                        <div class="drawer-item">
                            <el-tabs v-model="formActiveName" @tab-click="handleClickFormActive">
                                <el-tab-pane label="默认标签栏" name="default"></el-tab-pane>
                                <el-tab-pane label="活动标签栏" name="activity"></el-tab-pane>
                            </el-tabs>
                            <div class="drawer-item-dd" style="padding: 0 20px;">
                                <el-form-item label="是否开启" v-if="formActiveName == 'activity'" label-width="120px" :rules="
                                    [
                                        { required: true, message: '请设置是否开启', trigger: 'change' },
                                    ]
                                    ">
                                    <el-switch v-model="labelForm.is_show" :active-value="1" active-text="开启" :inactive-value="0" inactive-text="关闭" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                    <p class="warning-text" style="line-height: 2; margin: 10px 0;">开启后，在设置的时间范围内，显示独立配置的活动标签栏；在设置的时间范围外，显示默认标签栏</p>
                                </el-form-item>
                                <el-form-item label="促销活动时间" v-if="formActiveName == 'activity' && labelForm.is_show == 1" label-width="120px" prop="timeRange" :rules="
                                    [
                                        { required: true, message: '请设置促销活动时间', trigger: 'change' },
                                    ]
                                    ">
                                    <el-date-picker
                                        v-model="labelForm.timeRange"
                                        type="datetimerange"
                                        range-separator="至"
                                        start-placeholder="开始日期"
                                        end-placeholder="结束日期"
                                        :default-time="['00:00:00', '23:59:59']">
                                    </el-date-picker>
                                </el-form-item>
                                <template v-if="formActiveName == 'default' || (formActiveName == 'activity' && labelForm.is_show == 1)">
                                    <div>
                                        <el-form-item label="文字默认颜色" label-width="120px" prop="font_default_color" :rules="
                                            [
                                                { required: true, message: '请设置文字默认颜色', trigger: 'change' },
                                            ]
                                            ">
                                            <div class="s-flex ai-ct">
                                                <el-color-picker v-model="labelForm.font_default_color"></el-color-picker>
                                            </div>
                                            <p class="warning-text" style="line-height: 2; margin: 10px 0;">用于改变标签栏中文字的默认颜色</p>
                                        </el-form-item>
                                        <el-form-item label="文字选中颜色" label-width="120px" prop="font_selection_color" :rules="
                                            [
                                                { required: true, message: '请设置文字选中颜色', trigger: 'change' },
                                            ]
                                            ">
                                            <div class="s-flex ai-ct">
                                                <el-color-picker v-model="labelForm.font_selection_color"></el-color-picker>
                                            </div>
                                            <p class="warning-text" style="line-height: 2; margin: 10px 0;">用于改变标签栏中文字的选中颜色</p>
                                        </el-form-item>
                                    </div>
                                </template>
                            </div>
                        <div class="drawer-item" v-if="formActiveName == 'default' || (formActiveName == 'activity' && labelForm.is_show == 1)">
                            <p class="warning-text">最多添加3~5个标签导航。</p>
                            <p class="warning-text">【关联页面】获取的是“移动端装修→买家版”中页面类型是“底部菜单”的数据。</p>
                            <p class="warning-text">特殊页面处理：消息页面仅支持APP端显示，H5和小程序隐藏。</p>
                            <div class="drawer-item-dd" style="padding-left: 0;">
                                <dl>
                                    <dt>
                                        <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px;">编号</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                                <span>*</span>
                                                <label>默认图</label>
                                                <el-tooltip placement="top" effect="light">
                                                    <div slot="content" class="drawer-upload-warning">
                                                        <p>支持jpg、jpeg、png、gif格式</p>
                                                        <p>建议尺寸：74px*74px</p>
                                                        <p>建议大小：2M内</p>
                                                    </div>
                                                    <em class="iconfont cursorp">&#xe72d;</em>
                                                </el-tooltip>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 140px;">
                                                <label>默认名称</label>
                                                <el-tooltip placement="top" effect="light">
                                                    <div slot="content" class="drawer-upload-warning">
                                                        <p>此字段是否填写将控制前端以哪种样式展示。</p>
                                                        <p>①未填写，则标签栏按照“图片”样式展示；</p>
                                                        <p>②已填写，则标签栏按照“图片+文字”样式展示。</p>
                                                    </div>
                                                    <em class="iconfont cursorp">&#xe72d;</em>
                                                </el-tooltip>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                                <span>*</span>
                                                <label>选中图</label>
                                                <el-tooltip placement="top" effect="light">
                                                    <div slot="content" class="drawer-upload-warning">
                                                        <p>支持jpg、jpeg、png、gif格式</p>
                                                        <p>建议尺寸：74px*74px</p>
                                                        <p>建议大小：2M内</p>
                                                    </div>
                                                    <em class="iconfont cursorp">&#xe72d;</em>
                                                </el-tooltip>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 140px;">
                                                <label>选中名称</label>
                                                <el-tooltip placement="top" effect="light">
                                                    <div slot="content" class="drawer-upload-warning">
                                                    <p>此字段是否填写将控制前端以哪种样式展示。</p>
                                                        <p>①未填写，则标签栏按照“图片”样式展示；</p>
                                                        <p>②已填写，则标签栏按照“图片+文字”样式展示。</p>
                                                    </div>
                                                    <em class="iconfont cursorp">&#xe72d;</em>
                                                </el-tooltip>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">
                                                <span>*</span>
                                                <label>关联页面</label>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px; text-align: center;">是否显示</li>
                                        </ul>
                                    </dt>
                                    <dd class="s-flex flex-dir ai-ct">
                                        <ul class="s-flex ai-ct" style="height: 90px; padding: 0 40px;" v-for="(item, index) in labelForm.items">
                                            <li style="width: 90px; text-align:center;">@{{index+1}}</li>
                                            <li style="width: 90px;">
                                                <el-form-item :prop="'items.' + index + '.default_image'" :rules="[
                                                        { validator: (rule, value, callback) => {
                                                            if (labelForm.items[index].is_show == 1 && !value) {
                                                                callback(new Error('请上传图片'))
                                                            } else {
                                                                callback()
                                                            }
                                                        }, trigger: 'change' },
                                                    ]">
                                                    <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                        <img v-if="item.default_image" :src="item.default_image" alt="">
                                                        <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!item.default_image" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'default_image' })">
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <p>未上传</p>
                                                        </div>
                                                        <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'default_image' })">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile(index, 'default_image')">
                                                                <em class="iconfont">&#xe738;</em>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </el-form-item>
                                            </li>
                                            <li style="width:140px;">
                                                <el-form-item :prop="'items.' + index + '.title'" :rules="[
                                                    { max: 4, message: '标签名称不能超过4个字符', trigger: 'blur' },
                                                ]">
                                                    <el-input v-model="item.title" placeholder="请输入名称"></el-input>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 90px;">
                                                <el-form-item :prop="'items.' + index + '.selection_image'" :rules="[
                                                        { validator: (rule, value, callback) => {
                                                            if (labelForm.items[index].is_show == 1 && !value) {
                                                                callback(new Error('请上传图片'))
                                                            } else {
                                                                callback()
                                                            }
                                                        }, trigger: 'change' },
                                                    ]">
                                                    <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                        <img v-if="item.selection_image" :src="item.selection_image" alt="">
                                                        <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!item.selection_image" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'selection_image' })">
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <p>未上传</p>
                                                        </div>
                                                        <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'selection_image' })">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile(index, 'selection_image')">
                                                                <em class="iconfont">&#xe738;</em>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </el-form-item>
                                            </li>
                                            <li style="width:140px;">
                                                <el-form-item :prop="'items.' + index + '.check_title'" :rules="[
                                                    { max: 4, message: '标签名称不能超过4个字符', trigger: 'blur' },
                                                ]">
                                                    <el-input v-model="item.check_title" placeholder="请输入名称"></el-input>
                                                </el-form-item>
                                            </li>
                                            <li style="width:300px;flex:1;">
                                                <el-form-item :prop="'items.' + index + '.value'" :rules="[
                                                        { validator: (rule, value, callback) => {
                                                            if (labelForm.items[index].is_show == 1 && !value) {
                                                                callback(new Error('请关联页面'))
                                                            } else {
                                                                callback()
                                                            }
                                                        }, trigger: 'change' },
                                                    ]">
                                                    <el-select v-model="item.name" v-if="index!=0 && index != 4" style="width: 100%;" clearable filterable placeholder="请输入页面ID/名称" @change="(res) => { return handleSelectChange(res,index)}">
                                                        <el-option
                                                            v-for="page in common_page"
                                                            :key="page.value"
                                                            :label="page.label"
                                                            :value="page"
                                                            :disabled="filterOptionDisabled(page.value)">
                                                        </el-option>
                                                    </el-select>
                                                    <el-input v-else :value="item.default_selection_data[0].label" placeholder="请输入页面ID/名称" disabled></el-input>
                                                </el-form-item>
                                            </li>
                                            <li style="width:90px;text-align:center;">
                                                <el-switch v-model="item.is_show" :active-value="1" :inactive-value="0" active-color="#409EFF" inactive-color="#CBCBCB" :disabled="item.is_fixed == 1"></el-switch>
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

<script>
    Vue.component('bottom-nav-bar', {
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
            is_no_setting: {
                type: Boolean,
                default: true,
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
        watch: {
            temp_item: function(newValue) {
                if (newValue) {
                    this.$set(this, 'templateSetForm', newValue)
                    if (this.templateSetForm && this.templateSetForm.content && this.templateSetForm.content.default_data) {
                        let labelForm = JSON.parse(JSON.stringify(this.templateSetForm.content.default_data))
                        for (let i in labelForm.items) {
                            if (labelForm.items[i].default_selection_data[0] && labelForm.items[i].default_selection_data[0].value) {
                                labelForm.items[i]['name'] = labelForm.items[i].default_selection_data[0].label
                            }
                        }
                        this.$set(this, 'labelForm', labelForm)
                        this.$set(this, 'templateSetFormType', newValue.component_name)
                        this.$set(this, 'templateSetFormName', newValue.name)
                    }
                }
            }
        },
        data () {
            return {
                direction: 'rtl',   //  打开方向
                set_save_loading: false,    //  保存加载动画
                show_banner_drawer: false,
                templateSetFormName: '',    //  设置弹窗标题
                templateSetFormType: '',    //  设置组件别名
                templateSetFormIndex: '',    //  设置组件索引值
                templateSetForm: {},    //  设置表单数据
                templateSetRule: {},
                formActiveName: 'default',
                labelForm: {},
                common_page: [],
                is_setting_save: false,
                msg_error: null,
                is_show_material_dialog: false,
                material_dialog_model: 'single',
                material_dialog_max_count: 1,
                material_dialog_selected_count: 0,
                uploadValidateType: 2
            }
        },
        computed: {
            computedOptionsPlaceholder () {
                return function(value) {
                    const filter = this.options.filter(item => item.alias == value)
                    return filter.length ? filter[0].desc : ''
                }
            }
        },
        template: '#bottom-nav-bar',
        methods: {
            /** 下拉选项置灰判断 */
            filterOptionDisabled(value) {
                let is_have = this.labelForm.items.filter((item) => {return item.value == value})
                return is_have.length > 0
            },
            /** 下拉值修改 */
            handleSelectChange(res, index) {
                let arr = JSON.parse(JSON.stringify(this.labelForm.items))
                arr[index].value = res.value
                arr[index].name = res.label
                this.$set(this.labelForm, 'items', arr)
            },
            /** 获取公共页面下拉数据 */
            getCommonPage() {
                this.doPost('{!! route('manage.app_web_decoration.get_options') !!}', { page_type: 7, keywords: '' }).then(res => {
                    if (res.data && res.data.length > 0) {
                        this.$set(this, 'common_page', res.data)
                    } else {
                        this.$set(this, 'common_page', [])
                    }
                })
            },
            /** 点击打开组件设置弹窗 */
            async handleClickOpenTemplateSetting (data) {
                if (this.show_banner_drawer) return false
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                // this.templateSetForm = item ? item.form : {}
                await this.getCommonPage()
                this.show_banner_drawer = true
            },
            /** 删除图片 */
            handleClickDeleteFile(index, uploadTarget) {
                this.$set(this.labelForm.items[index], uploadTarget, '')
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
                        this.msg_error && this.msg_error.close()
                        if (this.uploadValidateType >= 2) {
                            if (!isPNG && !isJPEG && !isJPG && !isGIF) {
                                this.msg_error = this.$message.error('仅限上传jpg、jpeg、png、gif格式的图片');
                                resolve(false)
                                return false
                            }
                        } else {
                            if (!isPNG && !isJPEG && !isJPG) {
                                this.msg_error = this.$message.error('仅限上传jpg、jpeg、png格式的图片');
                                resolve(false)
                                return false
                            }
                        }
                        if (!isLt2M) {
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
                    let newValue = this.getNestedProperty(this.labelForm, this.uploadParent)
                    if (this.material_dialog_model === 'single') {
                        this.$set(newValue, this.uploadTarget, result)
                    } else {
                        newValue[this.uploadTarget] = newValue[this.uploadTarget].concat(result)
                        this.$set(newValue, this.uploadTarget, newValue[this.uploadTarget])
                    }
                } else {
                    if (this.material_dialog_model === 'single') {
                        this.$set(this.labelForm, this.uploadTarget, result)
                    } else {
                        this.labelForm[this.uploadTarget] = this.labelForm[this.uploadTarget].concat(result)
                        this.$set(this.labelForm, this.uploadTarget, this.labelForm[this.uploadTarget])
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
            /** 顶部表单切换 */
            handleClickFormActive(tabs) {
                if (this.formActiveName == 'default') {
                    if (this.labelForm.timeRange.length == 2) {
                        this.labelForm.start_time = this.labelForm.timeRange[0]
                        this.labelForm.end_time = this.labelForm.timeRange[1]
                    }
                    this.$set(this.templateSetForm.content, 'activity_data', this.labelForm)
                    this.$set(this, 'labelForm', this.templateSetForm.content.default_data)
                } else {
                    this.$set(this.templateSetForm.content, 'default_data', this.labelForm)
                    let activity_data = JSON.parse(JSON.stringify(this.templateSetForm.content.activity_data))
                    activity_data.timeRange = []
                    if (this.templateSetForm.content.activity_data.start_time && this.templateSetForm.content.activity_data.end_time) {
                        activity_data.timeRange = [this.templateSetForm.content.activity_data.start_time, this.templateSetForm.content.activity_data.end_time]
                    }
                    this.$set(this, 'labelForm', activity_data)
                }
            },
            /** 点击提交设置弹窗数据 */
            handleClickSubmitSetForm (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        if (this.formActiveName == 'default') {
                            this.$set(this.templateSetForm.content, 'default_data', this.labelForm)
                        } else {
                            if (this.labelForm.timeRange.length == 2) {
                                this.labelForm.start_time = this.labelForm.timeRange[0]
                                this.labelForm.end_time = this.labelForm.timeRange[1]
                            }
                            this.$set(this.templateSetForm.content, 'activity_data', this.labelForm)
                        }
                        this.set_save_loading = true
                        const info = { component_name: this.templateSetFormType, component_data: this.templateSetForm, name: this.templateSetFormName }
                        this.doPost('{!! route('manage.app_web_decoration.get_content_data_by_alias') !!}', info).then(res => {
                            if (res.code == 200) {
                                this.show_banner_drawer = false
                                this.set_save_loading = false
                                this.$set(this.templateSetForm, 'data', res.data)
                                this.$emit('save', { content: this.templateSetForm.content, data: res.data, index: this.temp_index })
                            } else {
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
                this.show_banner_drawer = false
                let labelForm = JSON.parse(JSON.stringify(this.templateSetForm.content.default_data))
                for (let i in labelForm.items) {
                    if (labelForm.items[i].default_selection_data[0] && labelForm.items[i].default_selection_data[0].value) {
                        labelForm.items[i]['name'] = labelForm.items[i].default_selection_data[0].label
                    }
                }
                this.$set(this, 'labelForm', labelForm)
                // this.$emit('cancel')
            },
        },
        mounted() {
        }
    })
</script>
