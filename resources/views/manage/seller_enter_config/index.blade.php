@extends('manage.seller_enter_config.layout.layout')

@section('content')
<!--拖拽模版-->
<div class="invite-drag-parent">
    <div class="invite-drag-child s-flex">
        <div class="invite-form-parent">
            <el-form :model="inviteSetForm" ref="inviteSetForm" class="invite-form-box">
                <div class="invite-page-set">
                    <vuedraggable tag="div" v-model="inviteSetForm.invite_items" group="name" chosenClass="temp-drag-chosen" filter=".temp-nocan-drag"
                                  handle=".public-handle-drag" :forceFallback="false" animation="300"
                                  class="temp-drag-perview invite-drag-perview"
                                  @end="handleTemplatePreviewDratEnd" id="tempDragPerview"
                    >
                        <div class="temp-not-nest" v-for="(parent, value) in inviteSetForm.invite_items"
                             v-if="parent.component_name != 'home_nav' && parent.component_name != 'label'"
                             ref="contentForm" :key="value" :data-type="parent.component_name" :data-index="value"
                             @click="tempActiveId = parent.id">
                            <!--自定义标题-->
                            <invite-title :temp_item="parent" :ref="`childForm${value}`" :temp_index="value" :upload_size_validate="upload_size" v-if="parent.type == 'title' || parent.type == 'date'" :temp_info="inviteSetForm" :active_index="tempActiveId" @delete="handleTemplateEventDeleteCallback" @copy="handleTemplateEventCopyCallback"></invite-title>
                        </div>
                    </vuedraggable>
                    <div class="invite-add-temp s-flex ai-ct jc-ct cursorp" @click="is_show_add = true">
                        <em class="iconfont">&#xe64e;</em>
                        <p>添加问题</p>
                    </div>
                </div>
            </el-form>
            <div class="invite-drag-footer s-flex ai-ct jc-ct cursorp">
                <div class="footer-btn" @click="handleSaveAllTemplate">保存</div>
            </div>
        </div>
    </div>
    <el-dialog title="添加问题" :visible.sync="is_show_add" width="400px">
        <div class="template-group-icon">
            <dl v-if="component_icons.basic_components && component_icons.basic_components.length">
                <dt>
                    <h1>基础组件</h1>
                </dt>
                <dd>
                    <div class="template-group-list s-flex flex-wrap">
                        <div class="template-icon-list" v-for="(item, index) in component_icons.basic_components" :index="index" @click="handleClickAddTempStart(item, 'dialog')">
                            <div class="s-flex ai-ct jc-ct">
                                <em class="iconfont" :data-type="item.type" v-html="item.icon" :style="{ color: item.color }"></em>
                            </div>
                            <p>@{{item.name}}</p>
                        </div>
                    </div>
                </dd>
            </dl>
            <dl v-if="component_icons.advanced_components && component_icons.advanced_components.length">
                <dt>
                    <h1>高级组件</h1>
                </dt>
                <dd>
                    <div class="template-group-list s-flex flex-wrap">
                        <div class="template-icon-list" v-for="(item, index) in component_icons.advanced_components" :index="index" @click="handleClickAddTempStart(item, 'dialog')">
                            <div class="s-flex ai-ct jc-ct">
                                <em class="iconfont" :data-type="item.type" v-html="item.icon"></em>
                            </div>
                            <p>@{{item.name}}</p>
                        </div>
                    </div>
                </dd>
            </dl>
            <dl v-if="component_icons.commonly_used_components && component_icons.commonly_used_components.length">
                <dt>
                    <h1>常用组件</h1>
                </dt>
                <dd>
                    <div class="template-group-list s-flex flex-wrap">
                        <div class="template-icon-list" v-for="(item, index) in component_icons.commonly_used_components" :index="index" @click="handleClickAddTempStart(item, 'dialog')">
                            <p>@{{item.name}}</p>
                        </div>
                    </div>
                </dd>
            </dl>
        </div>
    </el-dialog>
</div>
@endsection
@section('js')
<!--导航栏固定组件-->
@include('manage.seller_enter_config.components.select-date')
<!--广告位1拖拽组件-->
@include('manage.seller_enter_config.components.invite-title')

<!--加载动画-->
@include('manage.seller_enter_config.components.save-load')
<script>
    const vm = new Vue({
        el: '#app',
        data() {
            return {
                tempActiveId: null,  //  模块选中id
                is_start: false,    //  当页面组件为第一个时，为 true
                is_show_warn: false,    //  是否展示右上角页面数据保存提示
                is_show_add: false,    //  添加问题弹窗
                is_load_source: false,    //  左侧数据加载
                is_check_source: true,    //  是否展示素材列表
                controlOnStart: true,   //  是否允许 vuedraggable 开始 put 行为实现嵌套
                options: {animation: 500},    //  拖拽配置
                dragType: '',   //  拖拽组件类型

                /** 提交保存加载动画 **/
                save_loading: false,
                is_can_save: true,  //  节流保存
                is_show_dialog: false,     //  是否展示设置弹窗


                direction: 'rtl',
                inviteSetForm: {
                    id:0,
                    invite_items:@json($content)
                },    //  设置弹窗表单数据,
                component_icons: @json($component_icons),
                component_values: @json($component_values),
                public_options_down: [],
                upload_size: 2,
            }
        },
        components: { vuedraggable },
        directives: {
            load: {
                // bind(){}当绑定指令的时候出发
                bind: function (el, binding, vnode) {
                    const mask = new Mask().$mount(el)
                    el.instance = mask
                    el.mask = mask.$el
                    el.maskStyle = {}
                    binding.value && toggleLoading(el, binding)
                },
                // update(){}当数据更新时候会触发该函数
                update: function (el, binding) {
                    if (binding.oldValue !== binding.value) {
                        toggleLoading(el, binding)
                    }
                },
                // unbind(){}解绑的时候触发该函数
                unbind: function (el, binding) {
                    el.instance && el.instance.$destroy()
                }
            }
        },
        methods: {
            /** 页面初始化设置 */
            initPageSet () {
                document.querySelector('body').classList.add('template-edit-page')
                this.initPageData()
                this.save_loading = false
            },
            /** 页面初始化数据 */
            async initPageData () {
                //  常用组件标识添加
                if (this.component_icons.commonly_used_components && this.component_icons.commonly_used_components.length) {
                    this.component_icons.commonly_used_components.map(item => {
                        item.is_usually = true
                        item.placeholder = `请输入${item.name}`
                    })
                }
                //  监听 Ctrl+S 保存
                // document.addEventListener('keydown', this.listenerKeydowm, false)
                // this.initTemplateData()
            },
            /** 获取页面数据 */
            getPageData () {
                this.doGet('{!! route('manage.seller_enter_config.index') !!}' + '?id=' + this.inviteSetForm.id).then(res => {
                    if (res.code == 200) {
                        this.$set(this, 'inviteSetForm', res.data);
                        this.initPageData()
                    } else {
                        this.$message.error(res.message)
                    }
                })
            },
            /** 监听主题色变化 */
            handleCheckBgColorChange (value) {
                document.querySelector('body').setAttribute('style', '--main-color: ' + value)
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
            /*************************************************************** 拖拽相关 **************************************************************/
            /** 点击切换素材开关 **/
            handleClickCheckSourceSwitch () {
                this.is_check_source = !this.is_check_source
                if (this.is_check_source) {
                    $('.invite-content-source').css({
                        'width': '300px',
                        'height': '100vh'
                    });
                    $('.invite-content-source .template-source-switch').css('top', '50px');
                } else {
                    $('.invite-content-source').css({
                        // 'width': '160px',
                        'width': '12px',
                    })
                    $('.invite-content-source .template-source-switch').css('top', 0)
                }
            },
            /** 克隆组件 **/
            handleTempDragClone (dragitem, field) {
                //  获取左侧拖拽数据，对比右侧数据，从而得到要加入到页面模块的数据
                let item
                this.dragType = field
                //  如果拖拽的是固定组件
                item = dragitem.is_usually ? dragitem.value : this.component_values.find(value => value.type == dragitem.type)
                // item.content.name = item.name
                //  生成唯一id，用来设置选中
                item.id = Math.round(new Date() / 1000) + 'add'
                /*//  获取模块标题，过滤掉素材组件
                item.name = item.name && item.act_type != 'source' ? item.name : this.getTemplateName(item.act_type, item)*/
                //  判断拖拽的模版标题是否一致，如有标题一致情况，则重命名标题(1)，diffname对比名称是否相同，diffnumber获取相同类型组件的个数
                const diffname = this.inviteSetForm.invite_items.filter(temp => temp.name == item.name)
                const diffNextName = this.inviteSetForm.invite_items.filter(temp => temp.name == `${item.name}(${diffname.length + 1})` || temp.name == `${item.name}(${diffname.length - 1})`)
                let diffnumber = this.inviteSetForm.invite_items.filter(temp => temp.type == item.type)
                if (diffNextName.length) {
                    diffnumber = diffnumber.concat(diffNextName)
                }
                //  防止双向绑定
                let copyItem = JSON.parse(JSON.stringify(item))
                /*if (diffname.length) {
                    copyItem.name = diffnumber.length != 0 ? `${item.name}(${diffnumber.length})` : copyItem.name
                }*/
                setTimeout(() => {
                    this.tempActiveId = copyItem.id
                }, 200)
                $('.temp-item').addClass('drag')
                return { ...dragitem, ...copyItem }
            },
            /** 点击添加组件 */
            handleClickAddTempStart (item, type) {
                const find = item.is_usually ? item.value : this.component_values.find(value => value.type == item.type)
                const data = JSON.parse(JSON.stringify(find))
                const copy = JSON.parse(JSON.stringify(item))
                const result = { ...copy, ...data }
                this.inviteSetForm.invite_items.push(result)
                const scroll_main = document.querySelector('.invite-drag-child')
                scroll_main.scrollTo({
                    top: scroll_main.scrollHeight,
                    left: 0,
                    behavior: "smooth"
                })
                if (type && type == 'dialog') {
                    this.is_show_add = false
                }
            },
            /** 监听开始拖拽以实现嵌套 **/
            handleDragTempStart (event) {
                this.is_start = true
                if (event.clone.className.split(" ").length > 1) {
                    this.controlOnStart = false;
                } else {
                    this.$nextTick(() => {
                        this.controlOnStart = true;
                    });
                }
            },
            /** 监听拖拽结束，清楚页面中错误嵌套提示 **/
            handleDragTempEnd (event) {
                this.is_drop_source = true
                //  解决火狐兼容问题
                event.stopPropagation()
                event.preventDefault()
                event.dataTransfer = event.originalEvent.dataTransfer
                $('.temp-item').removeClass('drag')
                const tempErrorElement = document.querySelectorAll('.temp-error')
                tempErrorElement.forEach(item => {
                    item.classList.remove('temp-error')
                })

                setTimeout(() => {
                    this.$forceUpdate()
                    this.handleClearFormValidate()
                }, 50)
            },
            /** 数据展示区域拖拽结束 */
            handleTemplatePreviewDratEnd () {
                //  防止拖拽影响设置表单样式
                const dragElementParent = document.querySelectorAll('.temp-not-nest')
                dragElementParent.forEach(item => item.style.transform = '')
            },
            /** 监听组件移动，判断是否可以嵌套板块 **/
            handleDragTempMove (event, original) {
                this.is_drop_source = false
                //  默认阻止操作设置基础组件坐标位置，所有判断条件通过后打开，若条件未通过将会被拦截
                this.drag_is_basic = false

                return true
            },
            /*************************************************************** 设置相关 **************************************************************/
            /** 删除组件 */
            handleTemplateEventDeleteCallback (index) {
                this.inviteSetForm.invite_items.splice(index, 1)
                this.$forceUpdate()
                setTimeout(() => {
                    this.handleClearFormValidate()
                }, 100)
            },
            /** 复制组件 */
            handleTemplateEventCopyCallback (index) {
                const data = JSON.parse(JSON.stringify(this.inviteSetForm.invite_items[index]))
                this.inviteSetForm.invite_items.splice(index, 0, data)
                this.$forceUpdate()
                setTimeout(() => {
                    this.handleClearFormValidate()
                    // this.$forceUpdate()
                }, 100)
            },
            /** 清楚表单错误校验提示 */
            handleClearFormValidate () {
                this.inviteSetForm.invite_items.forEach((item, index) => {
                    this.$refs[`childForm${index}`][0].$refs.temp_item.clearValidate()
                })
            },
            /** 点击退出装修 **/
            handleExitAllTemplate () {
                this.$confirm('您的这版装修的内容将不被保存。', '确定要退出装修吗？', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning',
                    center: true
                }).then(() => {
                    //  跳转至<网站装修>页面
                    // window.close()
                    parent.closeSelf('网站装修', '{!! route('manage.app_web_decoration.index') !!}');
                }).catch(() => {
                });
            },
            /** 校验页面设置表单是否合法 */
            isFormValidate () {
                return new Promise(resolve => {
                    this.$refs['inviteSetForm'].validate((valid) => {
                        resolve(valid)
                    })
                })
            },
            /** 点击或Ctrl+S 保存 **/
            async handleSaveAllTemplate (type) {
                let validateLists = [], that = this
                //  校验页面设置表单
                const is_valid = await this.isFormValidate()

                //  同步校验自组件表单
                async function asyncForEach (array, callback) {
                    for (let index in array) {
                        that.$refs[`childForm${index}`][0].$refs.temp_item.validate(async (valid) => {
                            validateLists.push(valid)
                            await callback(array[index])
                        })
                    }
                }

                //  同步等待循环操作结束后，执行下一步操作
                asyncForEach(this.inviteSetForm.invite_items, async () => {
                    if (validateLists.length === this.inviteSetForm.invite_items.length) {
                        const filter_data = validateLists.filter(item => !item)
                        if (!filter_data.length && is_valid) {
                            if (this.is_can_save) {
                                this.is_can_save = false
                                if (!this.inviteSetForm.invite_items.length) {
                                    this.$confirm('页面未装修，请装修后再发布！', '', {
                                        confirmButtonText: '确定',
                                        showCancelButton: false,
                                        type: 'warning'
                                    }).then(() => {}).catch(() => {});
                                    this.is_can_save = true
                                    return false
                                }
                                this.inviteSetForm.invite_items.map(item => {
                                    if (isNaN(item.id)) item.id = ''
                                })

                                let info = this.inviteSetForm

                                let is_save = 'ctrl'
                                if (type == 'save') {
                                    is_save = await this.isSureSave()
                                }
                                if (is_save == 'ctrl' || is_save == 'confirm') {
                                    this.save_loading = true
                                    this.doPost('{!! route('manage.seller_enter_config.update') !!}', info).then(res => {
                                        if (res.code == 200) {
                                            this.inviteSetForm.id = res.data.id
                                            this.is_can_save = true
                                            this.$message.success('保存成功！')
                                            // this.getPageData()
                                            parent.closeSelf('邀约模板列表', '{!! route('manage.seller_enter.index') !!}');
                                        } else {
                                            this.$message.error(res.message)
                                            this.is_can_save = true
                                        }
                                        this.save_loading = false
                                    })
                                } else {
                                    this.is_can_save = true
                                    this.save_loading = false
                                }
                            }
                        }
                    }
                })
            },
            /** 询问是否确定保存 **/
            isSureSave () {
                return new Promise(resolve => {
                    this.$confirm('您这次装修的内容将被应用到线上。', '确定要发布吗？', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning',
                        center: true
                    }).then(() => {
                        resolve('confirm')
                    }).catch(() => {
                        this.is_can_save = true
                        resolve('cancel')
                    });
                })
            },
            /** 启动监听键盘事件 **/
            listenerKeydowm (event) {
                if (event.keyCode == 83 && (navigator.platform.match("Mac") ? event.metaKey : event.ctrlKey)) {
                    if (this.setting_show) return false
                    this.handleSaveAllTemplate('ctrl')
                    event.stopPropagation();
                    event.preventDefault();
                }
            },
        },
        mounted () {
            console.log(this.inviteSetForm)
            console.log(this.component_icons)
            console.log(this.component_values)
            this.save_loading = true
            this.initPageSet()
        },
        destroyed () {
            document.querySelector('body').classList.remove('template-edit-page')
        },
        created () {
            //  解决火狐兼容问题
            document.body.ondrop = function (event) {
                event.preventDefault()
                event.stopPropagation()
            }
        },
    })
</script>
@endsection
@section('css')
<style>

    .template-footer-con ul {
        width: 100%;
        height: 98px;
        background-color: #ffffff;
        box-shadow: 0 -0.2rem 0.2rem -0.22rem rgba(0, 0, 0, .2);
    }

    .template-footer-con ul li { /*margin: 0 50px;*/
    }

    .template-footer-con ul li .template-footer-image {
        width: 44px;
        height: 44px;
    }

    .template-footer-con ul li p {
        padding: 10px 0;
        font-size: 20px;
    }


</style>
@endsection
