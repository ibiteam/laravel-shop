<style>
    /*文本*/
    .invite-date .invite-date-box { display: inline-flex; margin-left: 36px; border-radius: 6px; border: 1px solid #ebebeb; }
    .invite-date dl { width: 118px; }
    .invite-date dl dt,
    .invite-date dl dd { height: 34px; line-height: 34px; padding: 0 10px; font-size: 12px; cursor: pointer; }
    .invite-date dl dt { font-weight: 500; }
    .invite-date dl dt:hover { background-color: #ebebeb; }
    .invite-date dl dd { border-top: 1px solid #ebebeb; }
    .invite-date dl:not(:last-child) dt,
    .invite-date dl:not(:last-child) dd { border-right: 1px solid #ebebeb; }
</style>

<script type="text/x-template" id="invite-date">
    <div class="temp-item invite-date template-banner template-public-width">
        <template>
            <el-form :model="temp_item" :rules="temp_item" ref="temp_item" class="" v-load="set_save_loading">
                <div class="invite-form s-flex jc-bt">
                    <em class="public-handle-drag"></em>
                    <div class="flex-1">
                        <el-form-item prop="name" class="s-flex ai-ct input-error-margin" :rules="{ required: true, message: '请输入问题', trigger: 'blur' }">
                            <div class="s-flex ai-ct">
                                <label class="invite-label el-form-item__label"><span class="invite-required" v-if="temp_item.is_need">*</span><template v-if="temp_info.is_show_serial_number">@{{computedTemplateSerialNumber(temp_index)}}</template></label>
                                <el-input v-model="temp_item.name" class="input-none-border" @input="$forceUpdate()" style="width: 90%;" placeholder="请输入问题" ></el-input>
                            </div>
                        </el-form-item>
                        <el-form-item prop="tips" label-width="52px" :hide-required-asterisk="true" :rules="{ required: false, message: '请输入描述', trigger: 'blur' }">
                            <el-input v-model="temp_item.tips" class="input-none-border input-sub" @input="$forceUpdate()" style="width: 90%;" placeholder="可添加描述" ></el-input>
                        </el-form-item>
                        <el-form-item prop="style_type" label-width="52px" v-if="temp_item.type == 'date'" :hide-required-asterisk="true" :rules="{ required: true, message: '请输入描述', trigger: 'blur' }">
                            <el-select v-model="temp_item.style_type" @change="$forceUpdate()" style="width: 300px;" placeholder="请选择">
                                <el-option v-for="(item, index) in temp_item.style_type_map" :key="item.value" :label="item.name" :value="item.value"></el-option>
                            </el-select>
                        </el-form-item>
                    </div>
                    <div  class="invite-delete s-flex cursorp" @click="handleClickDeleteTempItem">
                        <em class="iconfont">&#xe7e2;</em>
                        <span>删除</span>
                    </div>
                </div>
                <div class="invite-form-setting s-flex ai-ct jc-bt">
                    <div class="s-flex ai-ct">
                        <label><em class="iconfont" :data-type="temp_item.type" v-html="temp_item.icon"></em>@{{ temp_item.type == 'date' ? '日期' : '标题' }}</label>
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
    Vue.component('invite-date', {
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
            computedTemplateSerialNumber () {
                return function (value) {
                    return (value * 1 + 1).toString().padStart(2, '0')
                }
            }
        },
        template: '#invite-date',
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
