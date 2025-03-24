<style>
    /*文本*/
    .invite-upload .invite-upload-box { display: inline-flex; margin-left: 36px; border-radius: 6px; border: 1px solid #ebebeb; }
    .invite-upload dl { width: 118px; }
    .invite-upload dl dt,
    .invite-upload dl dd { height: 34px; line-height: 34px; padding: 0 10px; font-size: 12px; cursor: pointer; }
    .invite-upload dl dt { font-weight: 500; }
    .invite-upload dl dt:hover { background-color: #ebebeb; }
    .invite-upload dl dd { border-top: 1px solid #ebebeb; }
    .invite-upload dl:not(:last-child) dt,
    .invite-upload dl:not(:last-child) dd { border-right: 1px solid #ebebeb; }
</style>

<script type="text/x-template" id="invite-upload">
    <div class="temp-item invite-upload template-banner template-public-width">
        <template>
            <el-form :model="temp_item" :rules="temp_item" ref="temp_item" class="" v-load="set_save_loading">
                <div class="invite-form s-flex jc-bt">
                    <input type="file" @change="handleChangeUploadFile" value="" style="display: none;" ref="upload_file" />
                    <em class="public-handle-drag"></em>
                    <div class="flex-1">
                        <el-form-item prop="name" class="s-flex ai-ct input-error-margin" :rules="{ required: true, message: '请输入问题', trigger: 'blur' }">
                            <div class="invite-title s-flex ai-ct">
                                <label class="invite-label el-form-item__label"><span class="invite-required" v-if="temp_item.is_need">*</span><template v-if="temp_info.is_show_serial_number">@{{computedTemplateSerialNumber(temp_index)}}</template></label>
                                <span v-if="temp_item.type == 'more_file'" class="invite-comment">[多文件]</span>
                                <el-input v-model="temp_item.name" class="input-none-border flex-1" @input="$forceUpdate()" style="width: 90%;" placeholder="请输入问题" ></el-input>
                            </div>
                        </el-form-item>
                        <el-form-item prop="tips" label-width="52px" :hide-required-asterisk="true" :rules="{ required: false, message: '请输入描述', trigger: 'blur' }">
                            <el-input v-model="temp_item.tips" class="input-none-border input-sub" @input="$forceUpdate()" style="width: 90%;" placeholder="可添加描述" ></el-input>
                        </el-form-item>
                        <el-form-item prop="template_url" label-width="52px">
                            <div class="invite-upload-file cursorp s-flex ai-ct jc-ct">
                                <em class="iconfont">&#xe6aa;</em>
                            </div>
                        </el-form-item>
                        <el-form-item label="模版名称" prop="template_name" label-width="120px">
                            <el-input v-model="temp_item.template_name" @input="$forceUpdate()" style="width: 90%;" placeholder="请输入模版名称" ></el-input>
                        </el-form-item>
                        <el-form-item label="模版文件" prop="template_url" label-width="120px">
                            <div class="invite-upload-file cursorp s-flex ai-ct jc-ct" @click="handleClickUploadFile({ validate: 3, target: 'template_url' })">
                                <em class="iconfont">@{{ temp_item.template_url ? '&#xe839;' : '&#xe6aa;'}}</em>
                            </div>
                        </el-form-item>
                        <!--<el-form-item prop="limit_number" label-width="120px" v-if="temp_item.type == 'more_file'">
                            <el-input-number v-model="temp_item.limit_number" :min="0" :max="20" label="最多限制20个文件,0表示不限制"></el-input-number>
                            <p class="warning-text">最多限制20个文件,0表示不限制</p>
                        </el-form-item>-->
                        <!--<el-form-item prop="template_url" label-width="52px">
                            <div class="s-flex ai-fe">
                                <div class="invite-upload-file cursorp s-flex ai-ct jc-ct">
                                    <em class="iconfont">&#xe6aa;</em>
                                </div>
                                <p class="cursorp">@{{ temp_item.template_name ? temp_item.template_name : '未填写模版名称' }}</p>
                            </div>
                        </el-form-item>-->
                    </div>
                    <div  class="invite-delete s-flex cursorp" @click="handleClickDeleteTempItem">
                        <em class="iconfont">&#xe7e2;</em>
                        <span>删除</span>
                    </div>
                </div>
                <div class="invite-form-setting s-flex ai-ct jc-bt">
                    <div class="s-flex ai-ct">
                        <label><em class="iconfont" :data-type="temp_item.type" v-html="temp_item.icon"></em>@{{ temp_item.type == 'more_file' ? '多文件' : '单文件' }}</label>
                    </div>
                    <div class="s-flex ai-ct">
                        <div class="invite-setting-item s-flex ai-ct cursorp" @click="temp_item.is_need = !temp_item.is_need, $forceUpdate()" :style="{ color: temp_item.is_need ? '#278ff0' : '' }">
                            <em class="iconfont">@{{ temp_item.is_need ? '&#xe771;' : '&#xe770;' }}</em>
                            <label>必填</label>
                        </div>
                        <div   class="invite-setting-item s-flex ai-ct cursorp" @click="handleClickCopyCurrentItem">
                            <em class="iconfont">&#xe8b0;</em>
                            <label>复制</label>
                        </div>
                    </div>
                </div>
            </el-form>
        </template>
    </div>
</script>

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
    Vue.component('invite-upload', {
        props: {
            temp_index: {
                type: Number,
                default: 0
            },
            temp_item: {
                type: Object,
                default: () => { return {} }
            },
            temp_info: {
                type: Object,
                default: () => { return {} }
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
            }
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
            computedTemplateSerialNumber () {
                return function (value) {
                    return (value * 1 + 1).toString().padStart(2, '0')
                }
            }
        },
        template: '#invite-upload',
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
                    oneitem.date.type = oneitem.time_arr.length ? 0 : 1
                })
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
                        const isLt500KB = file.size / 1024  / 1024 < this.upload_size_validate;
                        const isGIF = file.type === 'image/gif'
                        if (this.uploadValidateType >= 2) {
                            if (!isPNG && !isJPEG && !isJPG && !isGIF && this.uploadValidateType != 3) {
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
                        /*if (!isLt500KB) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`图片大小不能超过${this.upload_size_validate}M`);
                            resovlt = false
                        }*/
                    } else {
                        resovlt = false
                    }
                    resolve(resovlt)
                })
            },
            /** 执行上传图片操作 **/
            async handleChangeUploadFile (event) {
                const [file] = event.srcElement.files
                const is_file = await this.beforeAvatarUpload(file)
                if (is_file) {
                    const fromdata = new FormData()
                    fromdata.append('file', file)
                    this.doPost('{!! route('manage.upload') !!}', fromdata).then(res => {
                        if (res.code == 200) {
                            if (this.uploadType) {
                                let newValue = this.getNestedProperty(this.temp_item, this.uploadType)
                                this.$set(newValue, this.uploadTarget, res.data.file)
                            } else {
                                this.$set(this.temp_item, this.uploadTarget, res.data.file)
                            }
                            this.clearFormValidate(`temp_item`)
                        } else {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error('图片上传失败');
                        }
                    })
                }
                this.$refs['upload_file'].value = ''
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
            /** 点击删除组件数据 */
            async handleClickDeleteData (index, parent) {
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
                newValue.splice(index, 1)
                this.msg_error && this.msg_error.close()
                this.msg_error = this.$message.success('删除成功！')
            },
            /** 点击删除当前组件 */
            handleClickDeleteTempItem () {
                this.$confirm('确定要删除当前组件吗?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning',
                    center: true
                }).then(() => {
                    this.$emit('delete', this.temp_index)
                }).catch(() => {});
            },
            /** 点击复制当前组件 */
            handleClickCopyCurrentItem () {
                this.$emit('copy', this.temp_index)
            }
        },
        mounted () {}
    })
</script>
