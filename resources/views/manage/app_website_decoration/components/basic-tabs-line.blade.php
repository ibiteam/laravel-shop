<style>
    .basic-tabs-line { width: 100%; height: 50px; overflow: hidden; -webkit-user-select: none; -moz-user-select: none; user-select: none; vertical-align: middle; }
    .basic-tabs-line .tab-btn { width: 24px; height: 24px; background-color: rgba(0, 0, 0, 0.3); border-radius: 999px; position: absolute; top: 12px; cursor: pointer; }
    .basic-tabs-line .tab-btn.left { left: 0; }
    .basic-tabs-line .tab-btn.right { right: 0; }
    .basic-tabs-line .tab-btn em { color: #ffffff; }
    .basic-tabs-line .tab-item:hover::before, .basic-tabs .tab-item.active::before { bottom: 0; }
    .basic-tabs-scrollbar {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        box-sizing: content-box;
        height: 100%;
        padding-bottom: 15px;
        position: relative;
        -webkit-user-select: none;
        user-select: none;
    }
    .basic-tabs-line .tab-item { line-height: 1; padding: 18px 0; margin-right: 48px; font-size: 26px; color: var(--font-color); flex-shrink: 0; position: relative; cursor: pointer; }
    .basic-tabs-line .tab-item.active { font-weight: bold; }
    /*.basic-tabs-line .tab-item.active::before { content: ''; width: 25px; height: 3px; background-color: var(--main-color); border-radius: 2px; position: absolute; left: 50%; bottom: 0; transform: translate(-50%); }*/

</style>
<!--设置弹窗--按钮切换组件-->
<script type="text/x-template" id="basic-tabs-line">
    <div class="basic-tabs-line" :class="custom_class" :style="style">
        <!--<div class="tab-btn left s-flex ai-ct jc-ct" :style="{ top: icon_top }" @click="handleClickTranslateTabLeft"><em class="iconfont" style="width: 16px; height: 16px;">&#xebb9;</em></div>-->
        {{--<div class="tab-btn right s-flex ai-ct jc-ct" :style="{ top: icon_top }" @click="handleClickTranslateTab"><em class="iconfont" style="width: 14px; height: 16px;">&#xe777;</em></div>--}}
        <div class="basic-tabs-scrollbar s-flex ai-ct" :style="{ width: child_width, height: child_height }" ref="tabsTotal">
            <div class="tab-item" :class="{ active: tab_active == index }" :style="{ 'margin-right': offset + 'px' }" v-for="(item, index) in list" ref="tabsItem" @click="handleClickTabTransItem(index, $event)"><p>@{{ item[target] }}</p></div>
            <div class="basic-tabs-trans" ref="tabsTrans" :style="{ 'left': `${transWidth}px` }">
            </div>
        </div>
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
    Vue.component('basic-tabs-line', {
        props: {
            tab_active: {
                type: Number,
                default: 0
            },
            list: {
                type: Array,
                default: () => { return [] }
            },
            style: {
                type: Object,
                default: () => { return {} }
            },
            child_width: {
                type: String,
                default: '320px'
            },
            child_height: {
                type: String,
                default: '100%'
            },
            offset: {
                type: Number,
                default: 36
            },
            custom_class: {
                type: String,
                default: ''
            },
            icon_top: {
                type: String,
                default: '12px'
            },
            initial_index: {
                type: Number,
                default: 0
            },
            target: {
                type: String,
                default: ''
            }
        },
        data () {
            return{
                transTotalWidth: 0, //  滑动总长度
                transLastWidth: 0, //  最大偏移量
                transIndex: 0,  //  记录滚动个数
                transWidth: 0,  //  滑动距离
                transRightOver: false,   //  是否超出右侧偏移量
                transLeftOver: false,   //  是否超出左侧偏移量
                tabsIndex: 0,
                scrollOption: {
                    top: 0,
                    left: 0,
                    behavior: "smooth"
                },
                trans_type: '', //  滚动边界方向（左/右）
            }
        },
        template: '#basic-tabs-line',
        methods: {
            /** 点击tab切换 */
            async handleClickTranslateTab (index) {
                /*if (this.trans_type == 'left') this.trans_type = ''
                if (this.$refs.tabsItem[this.transIndex + 1]) {
                    //  滑动元素
                    let tabsTrans = this.$refs.tabsTotal
                    const offset = this.scrollOption.left + this.$refs.tabsItem[this.transIndex + 1].clientWidth + this.offset
                    this.trans_type = await this.handleListenerScrollBorder(tabsTrans, offset);
                    if (this.trans_type) return false
                    this.scrollOption.left = offset < 0 ? 0 : offset;
                    this.transIndex ++
                    tabsTrans.scrollTo(this.scrollOption);
                }*/
                //  滑动元素
                let tabsTrans = this.$refs.tabsTotal
                const offset = this.scrollOption.left + this.$refs.tabsItem[this.transIndex + 1].clientWidth + this.offset
                this.scrollOption.left = offset < 0 ? 0 : offset;
                tabsTrans.scrollTo(this.scrollOption);
                // this.handleClickTranslateTab()
            },
            /** 点击tab切换 */
            async handleClickTranslateTabLeft (index) {
                if (this.trans_type == 'right') this.trans_type = ''
                if (this.$refs.tabsItem[this.transIndex - 1]) {
                    //  滑动元素
                    let tabsTrans = this.$refs.tabsTotal
                    const offset = this.scrollOption.left - this.$refs.tabsItem[this.transIndex - 1].clientWidth - this.offset
                    if (this.trans_type) return false
                    this.trans_type = await this.handleListenerScrollBorder(tabsTrans, offset);
                    this.scrollOption.left = offset < 0 ? 0 : offset;
                    this.transIndex --

                    tabsTrans.scrollTo(this.scrollOption);
                }
                // this.handleClickTranslateTab()
            },
            /** 点击tab选项 */
            async handleClickTabTransItem (index, event) {
                this.$emit('update:tab_active', index)
                this.$emit('change')
                this.$set(this, 'tab_active', index)
                this.transIndex = index
                //  滑动元素
                let tabsTrans = this.$refs.tabsTotal
                //  获取滑动元素宽度
                let tabsTransWidth = tabsTrans.clientWidth
                //  点击的Tab元素
                let tabsItem = this.$refs.tabsItem[index]
                //  获取 点击的Tab元素宽度
                let tabsItemWidth = tabsItem.clientWidth
                //  获取 点击的Tab元素距离父级盒子左侧距离
                let tabsItemOffsetLeft = tabsItem.offsetLeft
                //  获取需要移动的位置
                const offset = tabsItemOffsetLeft - (tabsTransWidth - tabsItemWidth) / 2;
                this.scrollOption.left = offset < 0 ? 0 : offset;
               /* this.trans_type = await this.handleListenerScrollBorder(tabsTrans, offset);
                console.log('this.trans_type',this.trans_type)
                if (!this.trans_type) return false*/
                tabsTrans.scrollTo(this.scrollOption);
            },
            handleListenerScrollBorder (element, trans_left) {
                return new Promise(resolve => {
                    let tabsTransWidth = element.scrollWidth - element.clientWidth
                    let needWidth = tabsTransWidth - trans_left
                    needWidth = Math.abs(needWidth)  //允许误差在5px以内
                    if (trans_left >= tabsTransWidth || needWidth < 5) {
                        //滚动到了右边
                        resolve('right')
                    } else if (trans_left == 0) {
                        //滚动到了左边
                        resolve('left')
                    } else {
                        resolve('')
                    }
                })
            }
        },
        mounted (){
            if (this.initial_index <= this.list.length - 1) {
                this.tabsIndex = this.initial_index
            }
        }
    })
</script>
