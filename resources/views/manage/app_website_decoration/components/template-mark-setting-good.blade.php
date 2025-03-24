<style>
    /*公共模块蒙层*/
    .public-mark {  width: 100%; height: 100%; position: absolute; left: 0; top: 0; z-index: 777; }
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
    .public-mark-btns { width: 100px; padding: 10px; background-color: #ffffff; border: 1px solid #e5e5e6; position: absolute; right: -110px; top: 0; box-sizing: border-box; }
    .public-mark-btns .el-switch { margin-bottom: 10px; }
    .public-mark-btns .btn-item { width: 30px; height: 30px; margin: 5px 0; border: 1px solid #e5e5e6; border-radius: 100%; }
    .public-mark-btns .btn-item.disabled { border-color: #cccccc; color: #cccccc; cursor: not-allowed; }
</style>
<!--模块遮罩组件-->
<script type="text/x-template" id="public-mark-template">
    <div class="public-mark" :class="{'public-mark-out':is_out}">
        <div class="public-mark-box" :class="{ vertical: is_vertical }" :style="{ width, height }"  @click.stop.prevent="handleClickOpenTemplateSetting">
            <div class="public-width s-flex jc-bt" style="box-sizing: border-box;">
                <h1 v-if="is_show_title">@{{ title }}</h1>
                <div class="s-flex ai-ct" v-if="!is_no_setting" style="height: 30px;" @click.stop>
                    <el-tooltip class="item" effect="dark" :content="data[key_name]=='1' ? '显示板块' : '隐藏板块'" placement="right" v-if="data&&Object.keys(data).length>0">
                        <el-switch v-model="data[key_name]" active-value="1" inactive-value="0" active-color="#409EFF" inactive-color="#CCCCCC" @change="(value) => handleSettingCheckTemp({ type: 'switch', value})" v-if="show_alone"></el-switch>
                    </el-tooltip>
                    <div class="public-btn primary" @click="handleClickOpenTemplateSetting" v-if="is_show_set">设置</div>
                </div>
            </div>
            <div class="public-mark-btns s-flex flex-dir ai-ct" v-if="is_show_sort&&!show_alone" @click.stop>
                <el-tooltip class="item" effect="dark" :content="list[index].is_show ? '显示板块' : '隐藏板块'" placement="right" v-if="is_show_set_show&&list.length">
                    <el-switch v-model="list[index].is_show" active-value="1" inactive-value="0" active-color="#409EFF" inactive-color="#CCCCCC" @change="(value) => handleSettingCheckTemp({ type: 'switch', value})"></el-switch>
                </el-tooltip>
                <div class="s-flex ai-ct jc-ct" style="width: 100%;">
                    <el-tooltip class="item" effect="dark" content="向上" placement="right">
                        <div class="btn-item s-flex ai-ct jc-ct cursorp" :class="{ disabled: index == 0 }" @click="handleSettingCheckTemp({ type: 'top', is_top: index == 0 })"><em class="iconfont">&#xe66d;</em></div>
                    </el-tooltip>
                </div>
                <div class="s-flex ai-ct jc-ct" style="width: 100%;">
                    <el-tooltip class="item" effect="dark" content="向下" placement="right">
                        <div class="btn-item s-flex ai-ct jc-ct cursorp" :class="{ disabled: index == (list.length - 1) }" @click="handleSettingCheckTemp({ type: 'bottom', is_bottom: index == (list.length - 1) })"><em class="iconfont">&#xe6f4;</em></div>
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
     * is_show_sort    是否展示排序组件名称，默认 true
     * is_no_setting    是否展示设置按钮，默认 false
     * show_alone   是否展示单独的显示隐藏，默认 false
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
            is_show_set: {
                type: Boolean,
                default: true
            },
            show_alone: {
                type: Boolean,
                default: false
            },
            is_show_set_show:{
                type: Boolean,
                default: true
            },
            is_show_sort: {
                type: Boolean,
                default: true
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
            data: {
                type: Object,
                default: () => {}
            },
            key_name: {
                type: String,
                default: ''
            }
        },
        data () {
            return{
            }
        },
        template: '#public-mark-template',
        methods: {
            /** 点击开启模块设置弹窗 */
            handleClickOpenTemplateSetting () { this.$emit('open') },
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
                                this.list.splice(this.index, 1)
                            }).catch(() => {});
                            break
                    }
                    this.$emit('sort', this.list)
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
        mounted (){}
    })
</script>
