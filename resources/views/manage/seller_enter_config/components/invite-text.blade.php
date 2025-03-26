<style>
    /*文本*/
    .invite-text .invite-text-box { display: inline-flex; margin-left: 36px; border-radius: 6px; border: 1px solid #ebebeb; }
    .invite-text dl { width: 118px; }
    .invite-text dl dt,
    .invite-text dl dd { height: 34px; line-height: 34px; padding: 0 10px; font-size: 12px; cursor: pointer; }
    .invite-text dl dt { font-weight: 500; }
    .invite-text dl dt:hover { background-color: #ebebeb; }
    .invite-text dl dd { border-top: 1px solid #ebebeb; }
    .invite-text dl:not(:last-child) dt,
    .invite-text dl:not(:last-child) dd { border-right: 1px solid #ebebeb; }
</style>

<script type="text/x-template" id="invite-text">
    <div class="temp-item invite-text template-banner template-public-width">
        <template>
            <el-form :model="temp_item" :rules="temp_item" ref="temp_item" class="" v-load="set_save_loading">
                <div class="invite-form s-flex jc-bt">
                    <em class="public-handle-drag"></em>
                    <div class="flex-1">
                        <el-form-item prop="name" class="s-flex ai-ct input-error-margin" :rules="{ required: true, message: temp_item.placeholder ? temp_item.placeholder : '请输入问题', trigger: 'blur' }">
                            <div class="s-flex ai-ct">
                                <label class="invite-label el-form-item__label" ><span class="invite-required" v-if="temp_item.is_need">*</span><template v-if="temp_info.is_show_serial_number">@{{computedTemplateSerialNumber(temp_index)}}</template></label>
                                <el-input v-model="temp_item.name" class="input-none-border" @input="$forceUpdate()" style="width: 90%;" :placeholder="temp_item.placeholder ? temp_item.placeholder : '请输入问题'" ></el-input>
                            </div>
                        </el-form-item>
                        <el-form-item prop="tips" label-width="52px" :hide-required-asterisk="true" :rules="{ required: false, message: '请输入描述', trigger: 'blur' }">
                            <el-input v-model="temp_item.tips" @input="$forceUpdate()" class="input-none-border input-sub" style="width: 90%;" placeholder="可添加描述" ></el-input>
                        </el-form-item>
                        <el-form-item label-width="52px" :hide-required-asterisk="true">
                            <el-input :type="temp_item.type" @input="$forceUpdate()" class="input-disabled" disabled style="width: 90%;" placeholder="待用户输入" ></el-input>
                        </el-form-item>
                    </div>
                    <div  class="invite-delete s-flex cursorp" @click="handleClickDeleteTempItem">
                        <em class="iconfont">&#xe7e2;</em>
                        <span>删除</span>
                    </div>
                </div>
                <div class="invite-form-setting s-flex ai-ct jc-bt">
                    <div class="s-flex ai-ct">
                        <label><em class="iconfont" :data-type="temp_item.type" v-html="temp_item.icon"></em>@{{ temp_item.type == 'textarea' ? '多行文本' : '文本框' }}</label>
                        <el-select v-model="temp_item.limit_type" placeholder="数据类型" clearable @change="$forceUpdate()" style="width: 100px;" v-if="temp_item.type == 'text'">
                            <el-option v-for="item in temp_item.limit_type_map" :key="item.value" :label="item.name" :value="item.value"></el-option>
                        </el-select>
                    </div>
                    <div class="s-flex ai-ct">
                        <div class="invite-setting-item s-flex ai-ct cursorp" @click="temp_item.is_need = !temp_item.is_need, $forceUpdate()" :style="{ color: temp_item.is_need ? '#278ff0' : '' }">
                            <em class="iconfont">@{{ temp_item.is_need ? '&#xe771;' : '&#xe770;' }}</em>
                            <label>必填</label>
                        </div>
                        <div class="invite-setting-item s-flex ai-ct cursorp" @click="handleClickCopyCurrentItem">
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
    Vue.component('invite-text', {
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
        template: '#invite-text',
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
