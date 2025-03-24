<style>
    .basic-tabs { width: 100%; height: 50px; overflow: hidden; -webkit-user-select: none; -moz-user-select: none; user-select: none; vertical-align: middle; }
    .basic-tabs .tab-btn { width: 24px; height: 24px; background-color: rgba(0, 0, 0, 0.3); border-radius: 999px; position: absolute; top: 12px; cursor: pointer; }
    .basic-tabs .tab-btn.left { left: 0; }
    .basic-tabs .tab-btn.right { right: 0; }
    .basic-tabs .tab-btn em { color: #ffffff; }
    .basic-tabs .tab-item:hover::before, .basic-tabs .tab-item.active::before { bottom: 1px; }
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

    .basic-tabs-radius { height: 40px; margin: 10px 0; border-bottom: none; }
    .basic-tabs-radius .tab-item { padding: 10px 20px; background-color: #F8F8F8; border: 1px solid #EEEEEE; border-radius: 16px; }
    .basic-tabs-radius .tab-item.active,
    .basic-tabs-radius .tab-item:hover { background-color: #ffffff; border-color: var(--main-color); color: var(--main-color); }
    .basic-tabs-radius .tab-item:not(:last-child) { margin-right: 20px; }
    .basic-tabs-radius .tab-item.active::before,
    .basic-tabs-radius .tab-item:hover::before { display: none; }

</style>
<!--设置弹窗--按钮切换组件-->
<script type="text/x-template" id="basic-tabs">
    <div class="basic-tabs" :class="custom_class" :style="{ width }">
        <div class="tab-btn right s-flex ai-ct jc-ct" :style="{ top: icon_top }" @click="handleClickTranslateTab"><em class="iconfont" style="width: 12px; height: 16px;">&#xe777;</em></div>
        <div class="basic-tabs-scrollbar s-flex ai-ct" :style="{ width: child_width }" ref="tabsTotal">
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
    Vue.component('basic-tabs', {
        props: {
            tab_active: {
                type: Number,
                default: 0
            },
            list: {
                type: Array,
                default: () => { return [] }
            },
            width: {
                type: String,
                default: '100%'
            },
            child_width: {
                type: String,
                default: '320px'
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
            }
        },
        template: '#basic-tabs',
        methods: {
            /** 点击tab切换 */
            handleClickTranslateTab (index) {
                if (this.$refs.tabsItem[this.tabsIndex + 1]) {
                    //  滑动元素
                    let tabsTrans = this.$refs.tabsTotal
                    const offset = this.scrollOption.left + this.$refs.tabsItem[this.tabsIndex + 1].clientWidth + this.offset
                    this.scrollOption.left = offset < 0 ? 0 : offset;
                    tabsTrans.scrollTo(this.scrollOption);
                }
                // this.handleClickTranslateTab()
            },
            /** 点击tab选项 */
            handleClickTabTransItem (index, event) {
                this.$emit('update:tab_active', index)
                this.$emit('change')
                this.$set(this, 'tab_active', index)
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
                tabsTrans.scrollTo(this.scrollOption);
            },
        },
        mounted (){
            console.log('list',this.list)
            if (this.initial_index <= this.list.length - 1) {
                this.tabsIndex = this.initial_index
            }
        }
    })
</script>
