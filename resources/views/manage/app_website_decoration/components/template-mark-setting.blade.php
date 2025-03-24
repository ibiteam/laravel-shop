<style>
    /*公共模块蒙层*/
    .public-mark {  width: 100%; height: 100%; user-select: none; position: absolute; left: 0; top: 0; z-index: 777; }
    .public-mark-out {  width: 100%; height: 100%; position: absolute; left: 0; top: 0; z-index: 999; }
    .public-mark::before { content: ''; width: 12px; height: 100%; position: absolute; right: -12px; top: 0; }
    .public-mark-box { display: none; width: 100%; height: 50px; padding: 10px 20px; background-color: rgba(0, 0, 0, 0.7); box-sizing: border-box; }
    .public-mark:hover .public-mark-box { display: flex; position: relative; }
    .public-mark-box h1 { line-height: 30px; font-size: 14px; font-weight: bold; color: #ffffff; }
    .public-mark-box p { padding: 10px 0; font-size: 12px; color: #ffffff; }
    .public-btn { width: 60px; height: 28px; padding: 0; margin-right: 10px; line-height: 26px; text-align: center; background-color: #ffffff; box-shadow: none; color: #333333; border: 1px solid #CCCCCC; border-radius: 4px; cursor: pointer; }
    .public-btn:hover { color: #409EFF; border-color: #c6e2ff; background-color: #ecf5ff; }
    .public-btn.primary { margin-left: 24px; background-color: #409EFF; border-color: #409EFF; color: #ffffff; }
    .public-default-text { height: 100%; text-align: center; font-size: 28px; color: #D5DEF0; }
    .public-mark-box.vertical { padding: 20px 10px; box-sizing: border-box; }
    .public-mark-box.vertical .public-btn.primary { margin-top: 20px; margin-left: 0; }
    .public-mark-btns { width: 100px; /*padding: 10px;*/ background-color: #ffffff; border: 1px solid #e5e5e6; position: absolute; right: -110px; top: 0; box-sizing: border-box; }
    .public-mark-btns > div:not(.el-switch) { padding: 0 10px; box-sizing: border-box; }
    .public-mark-btns .el-switch { display: flex; width: 100%; height: 73px; align-items: center; justify-content: center; border-bottom: 1px solid #F2F4F9; }
    .public-mark-btns .el-switch .el-switch__core { height: 30px; border-radius: 15px; }
    .public-mark-btns .el-switch .el-switch__core::after { width: 26px; height: 26px; }
    .public-mark-btns .el-switch.is-checked .el-switch__core::after { margin-left: -27px; }
    .public-mark-btns .btn-item { /*width: 30px; height: 30px; margin: 5px 0; border: 1px solid #e5e5e6; border-radius: 100%;*/ }
    .public-mark-btns .btn-item { margin: 20px 0 0 0; }
    .public-mark-btns .btn-item em { font-size: 30px; }
    .public-mark-btns .btn-item.disabled { border-color: #cccccc; color: #cccccc; cursor: not-allowed; }
    .public-mark-btns .no_direct_btn{margin-top: 0!important;border-top: none!important;}
</style>
<!--模块遮罩组件-->
<script type="text/x-template" id="public-mark-template">
    <div class="public-mark" :style="{ height: box_height }" :class="{'public-mark-out':is_out}" @click.stop.prevent="handleClickOpenTemplateSetting('parent')">
        <div class="public-mark-box" :class="{ vertical: is_vertical }" :style="{ width, height }">
            <div class="public-width s-flex jc-bt" style="box-sizing: border-box;">
                <h1 v-if="is_show_title">@{{ title }}</h1>
                {{--<p v-if="warning_text" :style="warning_text_style">@{{ warning_text }}</p>--}}
                <div class="s-flex ai-ct" v-if="!is_no_setting" style="height: 30px;">
                    <el-switch v-model="is_show" class="s-flex ai-ct jc-ct" :active-value="1" :inactive-value="0" active-color="#409EFF" inactive-color="#CCCCCC" @change="(value) => handleSettingCheckTemp({ type: 'switch', value})" v-if="show_alone && is_show_switch"></el-switch>
                    <div class="public-btn primary" v-if="is_show_set" @click.stop.prevent="handleClickOpenTemplateSetting('self')">设置</div>
                    {{--<div class="public-btn" v-if="is_show_delete">删除</div>--}}
                </div>
            </div>
            <div class="public-mark-btns s-flex flex-dir ai-ct" v-if="!alias&&!show_alone" @click.stop>
                <el-tooltip class="item" effect="dark" :content="is_show ? '显示板块' : '隐藏板块'" placement="right">
                    <el-switch v-model="is_show" :active-value="1" :width="50" :inactive-value="0" active-color="#409EFF" inactive-color="#CCCCCC" @change="(value) => handleSettingCheckTemp({ type: 'switch', value})"></el-switch>
                </el-tooltip>
                <div class="s-flex ai-ct jc-bt" style="width: 100%;" v-if="show_top">
                    <el-tooltip class="item" effect="dark" content="置顶" placement="right">
                        <div class="btn-item s-flex ai-ct jc-ct cursorp" :class="{ disabled: index == 0 }" @click="handleSettingCheckTemp({ type: 'ontop', is_top: index == 0 })"><em class="iconfont">&#xe72f;</em></div>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="向上" placement="right">
                        <div class="btn-item s-flex ai-ct jc-ct cursorp" :class="{ disabled: index == 0 }" @click="handleSettingCheckTemp({ type: 'top', is_top: index == 0 })"><em class="iconfont">&#xe730;</em></div>
                    </el-tooltip>
                </div>
                <div class="s-flex ai-ct jc-bt" style="width: 100%;" v-if="show_bottom">
                    <el-tooltip class="item" effect="dark" content="置底" placement="right">
                        <div class="btn-item s-flex ai-ct jc-ct cursorp" :class="{ disabled: index == (list.length - 1) }" @click="handleSettingCheckTemp({ type: 'onbottom', is_bottom: index == (list.length - 1) })"><em class="iconfont">&#xe72c;</em></div>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="向下" placement="right">
                        <div class="btn-item s-flex ai-ct jc-ct cursorp" :class="{ disabled: index == (list.length - 1) }" @click="handleSettingCheckTemp({ type: 'bottom', is_bottom: index == (list.length - 1) })"><em class="iconfont">&#xe72e;</em></div>
                    </el-tooltip>
                </div>
                <div class="s-flex ai-ct jc-ct" style="width: 100%; height: 66px; margin-top: 15px; border-top: 1px solid #F2F4F9;" :class="!show_top&&!show_bottom?'no_direct_btn':''" v-if="is_show_delete">
                    <el-tooltip class="item" effect="dark" content="删除" placement="right">
                        <div class="btn-item s-flex ai-ct jc-ct cursorp" style="margin: 0;" @click="handleSettingCheckTemp({ type: 'delete' })"><em class="iconfont">&#xe7c8;</em></div>
                    </el-tooltip>
                </div>
            </div>
        </div>
    </div>
</script>
<script>
    /**
     * name 组件设置蒙层
     * title    模块名称
     * warning_text    提示文字
     * alias    如果页面属于固定组件，则需要传递组件别名
     * width    组件指定宽度，竖向布局使用，横向布局默认 100%
     * height    组件指定高度，默认 50
     * is_show_title    是否展示组件名称，默认 true
     * is_no_setting    是否展示设置按钮，默认 false
     * is_show_delete   是否展示删除按钮，默认 true
     * is_show_switch 是否展示开关，默认 true
     * show_alone   是否展示单独的显示隐藏，默认 false
     * show_top   是否展示向上及置顶，默认 false
     * show_bottom   是否展示向下及置底，默认 false
     * is_show_set   是否展示设置按钮，默认 true
     * is_vertical   是否进行纵向布局展示，默认 false
     * **/
    Vue.component('public-mark-template', {
        props: {
            title: String,
            warning_text: String,
            alias: String,
            width: {
                type: String,
                default: '100%'
            },
            box_height: {
                type: String,
                default: '100%'
            },
            height: {
                type: String,
                default: '50px'
            },
            is_show_title: {
                type: Boolean,
                default: true
            },
            is_no_setting: {
                type: Boolean,
                default: false
            },
            is_show_delete: {
                type: Boolean,
                default: true
            },
            is_show_switch: {
                type: Boolean,
                default: true
            },
            is_show_set: {
                type: Boolean,
                default: true
            },
            show_alone: {
                type: Boolean,
                default: false
            },
            is_out: {
                type: Boolean,
                default: false
            },
            is_vertical: Boolean,
            index: {
                type: Number,
                default: 0
            },
            list: {
                type: Array,
                default: () => []
            },
            show_top: {
                type: Boolean,
                default: true
            },
            show_bottom: {
                type: Boolean,
                default: true
            },
        },
        data () {
            return{
                is_show: 1
            }
        },
        template: '#public-mark-template',
        methods: {
            /** 点击开启模块设置弹窗 */
            handleClickOpenTemplateSetting (type) {
                if (type == 'parent' && !isNaN(this.index)) {
                    if (!this.is_no_setting && this.is_show_set && this.list[this.index].component_name != 'recommend_seller' && this.list[this.index].component_name != 'recommend_theme') {
                        this.$emit('open')
                    }
                } else {
                    this.$emit('open')
                }
            },
            /** 监听组件设置操作 */
            handleSettingCheckTemp (info) {
                const { type, value, is_top, is_bottom } = info
                const item = this.list[this.index]
                if (type != 'switch') {
                    switch (type) {
                        case 'ontop':
                            this.handleClickMovetoTop(item, this.index, is_top)
                            break
                        case 'top':
                            this.handleClickMovetoUp(item, this.index, is_top)
                            break
                        case 'bottom':
                            this.handleClickMovetoDown(item, this.index)
                            break
                        case 'onbottom':
                            this.handleClickMovetoBottom(item, this.index, is_bottom)
                            break
                        case 'delete':
                            this.$confirm('确定要删除当前组件吗?', '提示', {
                                confirmButtonText: '确定',
                                cancelButtonText: '取消',
                                type: 'warning',
                                center: true
                            }).then(() => {
                                // 删除组件不请求接口。直接页面写删除。等大保存时才删除
                                this.list.splice(this.index, 1)
                            }).catch(() => {});
                            break
                    }
                    this.$emit('sort', this.list)
                } else {
                    this.list[this.index].is_show = value
                }
            },
            /** 点击设置当前模板置顶 **/
            handleClickMovetoTop (item, index, is_top) {
                if (!is_top) {
                    this.list.splice(index, 1)
                    this.list.unshift(item)
                } else {
                    this.$message.error('当前模板已在最顶部');
                }
            },
            /** 点击设置当前模板向上移动 **/
            handleClickMovetoUp (item, index, is_top) {
                if (this.list[index - 1]) {
                    const preItem = this.list[index - 1]
                    this.list.splice(index, 1, preItem)
                    this.list.splice(index - 1, 1, item)
                } else {
                    this.$message.error('当前模板已在最顶部');
                }
            },
            /** 点击设置当前模板向下移动 **/
            handleClickMovetoDown (item, index) {
                if (this.list[index + 1]) {
                    const nextItem = this.list[index + 1]
                    this.list.splice(index, 1, nextItem)
                    this.list.splice(index + 1, 1, item)
                } else {
                    this.$message.error('当前模板已在最底部');
                }
            },
            /** 点击设置当前模板置底 **/
            handleClickMovetoBottom (item, index, is_bottom) {
                if (!is_bottom) {
                    this.list.splice(index, 1)
                    this.list.push(item)
                } else {
                    this.$message.error('当前模板已在最底部');
                }
            },
        },
        mounted (){
            this.$nextTick(() => {
                if (this.is_no_setting) return
                if (this.list && this.list.length > 0 && !isNaN(this.index)) {
                    this.is_show = this.list[this.index].is_show
                }
            })
        }
    })
</script>
