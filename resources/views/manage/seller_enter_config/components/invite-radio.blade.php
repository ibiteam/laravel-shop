<style>
    /*文本*/
    .invite-radio .invite-radio-box { display: inline-flex; margin-left: 36px; border-radius: 6px; border: 1px solid #ebebeb; }
    .invite-radio .el-form-item .el-form-item__error { margin-left: 26px; }
</style>

<script type="text/x-template" id="invite-radio">
    <div class="temp-item invite-radio template-banner template-public-width" :class="{ 's-flex ai-ct jc-ct': temp_item.activity_info && !temp_item.activity_info.length, active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <template>
            <el-form :model="temp_item" :rules="temp_item" ref="temp_item" class="" v-load="set_save_loading">
                <div class="invite-form s-flex jc-bt">
                    <em class="public-handle-drag"></em>
                    <div class="flex-1">
                        <el-form-item prop="name" :hide-required-asterisk="!temp_item.is_need" class="s-flex ai-ct input-error-margin" :rules="{ required: true, message: '请输入问题', trigger: 'blur' }">
                            <div class="s-flex ai-ct">
                                <label class="invite-label el-form-item__label"><span class="invite-required" v-if="temp_item.is_need">*</span><template v-if="temp_info.is_show_serial_number">@{{computedTemplateSerialNumber(temp_index)}}</template></label>
                                <el-input v-model="temp_item.name" class="input-none-border input-error-margin" @input="$forceUpdate()" style="width: 90%;" placeholder="请输入问题" ></el-input>
                            </div>
                        </el-form-item>
                        <el-form-item prop="tips" label-width="52px" :hide-required-asterisk="true" :rules="{ required: false, message: '请输入描述', trigger: 'blur' }">
                            <el-input v-model="temp_item.tips" class="input-none-border input-sub" @input="$forceUpdate()" style="width: 90%;" placeholder="可添加描述" ></el-input>
                        </el-form-item>
                        <template v-for="(item, index) in temp_item.select_options">
                            <el-form-item :prop="`select_options.${index}.name`" label-width="52px" :hide-required-asterisk="true" :rules="{ required: true, message: `请输入选项${index * 1 + 1}`, trigger: 'blur' }">
                                <div class="s-flex ai-ct">
                                    {{--<em class="iconfont" style="margin-right: 10px;">&#xe674;</em>--}}
                                    <em class="iconfont" style="margin-right: 10px;">&#xe7c9;</em>
                                    <el-input v-model="item.name" @input="$forceUpdate()" style="width: 300px;" :placeholder="`选项${index * 1 + 1}`" ></el-input>
                                    <em class="invite-remove iconfont cursorp" style="margin-left: 10px;" @click="handleClickDeleteData(index, 'select_options')" v-if="temp_item.select_options.length > 1">&#xe82b;</em>
                                </div>
                            </el-form-item>
                        </template>
                        <div style="display: table;">
                            <div class="invite-add s-flex ai-ct cursorp" @click="handleClickAddData('select_options', 10, '单选数据', { title: '' })">
                                <em class="iconfont">&#xe620;</em>
                                <label>添加</label>
                            </div>
                        </div>
                    </div>
                    <div  class="invite-delete s-flex cursorp" @click="handleClickDeleteTempItem">
                        <em class="iconfont">&#xe7e2;</em>
                        <span>删除</span>
                    </div>
                </div>
                <div class="invite-form-setting s-flex ai-ct jc-bt">
                    <div class="s-flex ai-ct">
                        <label><em class="iconfont" :data-type="temp_item.type" v-html="temp_item.icon"></em>单选框</label>
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
    Vue.component('invite-radio', {
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
                msg_error: null
            }
        },
        computed: {
            computedTemplateSerialNumber () {
                return function (value) {
                    return (value * 1 + 1).toString().padStart(2, '0')
                }
            }
        },
        template: '#invite-radio',
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
            /** 点击添加组件数据 */
            async handleClickAddData (dom, len, warning_text, data) {
                const item = JSON.parse(JSON.stringify(data))
                let newValue = this.getNestedProperty(this.temp_item, dom)
                if (newValue.length >= len) {
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.error(`最多可添加${len}个${warning_text}`)
                    return false
                }
                newValue.push(item)
                this.$forceUpdate()
            },
            /** 点击删除组件数据 */
            async handleClickDeleteData (index, parent) {
                const newValue = await this.getNestedProperty(this.temp_item, parent)
                newValue.splice(index, 1)
                this.$forceUpdate()
                this.msg_error && this.msg_error.close()
                // this.msg_error = this.$message.success('删除成功！')
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
