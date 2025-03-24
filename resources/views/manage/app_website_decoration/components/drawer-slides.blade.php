<style>
    /*Slide切换按钮*/
    .drawer-slides { width: 1155px; height: 36px; padding: 0 20px 0 0; margin: 0 auto; position: relative; overflow: hidden; }
    .drawer-slides .drawer-slide-btn { color: #DEDEDE; position: absolute; top: 8px; }
    .drawer-slides .drawer-slide-btn.cursorp:hover { color: #409EFF; }
    .drawer-slides .drawer-slide-btn.left { left: 0px; }
    .drawer-slides .drawer-slide-btn.right { right: 0px; }
    .drawer-slides .drawer-slides-scrollbar { width: 1112px; overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        box-sizing: content-box;
        height: 100%;
        padding-bottom: 15px;
        position: relative;
        -webkit-user-select: none;
        user-select: none; }
    .drawer-slides .drawer-slide-tans { transition: left 0.3s ease; position: absolute; }
    .drawer-slides .slide-item { width: auto; line-height: 1; padding: 8px 16px; margin: 0 12px; flex-shrink: 0; font-size: 14px; border-radius: 4px; }
    .drawer-slides .slide-item:hover,
    .drawer-slides .slide-item.active { background-color: #409EFF; color: #ffffff; }
</style>
<!--装修模版--按钮切换组件-->
<script type="text/x-template" id="drawer-slides">
    <div class="drawer-slides" :style="{ width: width + 'px', height: height + 'px' }">
        <!--<div class="drawer-slide-btn left" :class="{ 'cursornot': transLeftOver, 'cursorp': !transLeftOver }" @click="handleClickSlideBtn('left')">
            <em class="iconfont">&#xe79a;</em>
        </div>-->
        <div class="drawer-slide-btn right" v-if="is_show_btn" :class="{ 'cursornot': transRightOver, 'cursorp': !transRightOver }" @click="handleClickSlideBtn('right')">
            <em class="iconfont">&#xe79b;</em>
        </div>
        <div class="drawer-slides-scrollbar s-flex ai-ct" ref="slideTotal" :style="{ width: (width - 24) + 'px' }">
            <div class="slide-item cursorp" :class="{ active: slide_active == index }" v-for="(item, index) in list" ref="slideItem" @click="handleClickSlideItem(index)"><p>@{{ item[target] || default_text }}</p></div>
            <!--<div class="drawer-slide-tans s-flex ai-ct flex-no-wrap" ref="slideTrans">
            </div>-->
        </div>
    </div>
</script>
<script>
    /**
     * name 设置弹窗--按钮切换组件
     * **/
    Vue.component('drawer-slides', {
        props: {
            width: {
                type: Number,
                default: 1115
            },
            height: {
                type: Number,
                default: 36
            },
            target: {
                type: String,
                default: 'slide_name'
            },
            default_text: {
                type: String,
                default: '推荐分类'
            },
            slide_active: {
                type: Number,
                default: 0
            },
            list: [Array, Object],
            initial_index: {
                type: Number,
                default: 0
            },
            target: {
                type: String,
                default: ''
            },
            is_show_btn: {
                type: Boolean,
                default: true
            }
        },
        data () {
            return{
                transTotalWidth: 0, //  滑动总长度
                transLastWidth: 0, //  最大偏移量
                transIndexOffset: 0,  //  滑动索引距离
                transRightOver: false,   //  是否超出右侧偏移量
                transLeftOver: false,   //  是否超出左侧偏移量
                slideIndex: 0,
                scrollOption: {
                    top: 0,
                    left: 0,
                    behavior: "smooth"
                },
                offset: 36,
                type_source: '',    //  滚动条滚动到那边的顶端
                height: '32px'
            }
        },
        template: '#drawer-slides',
        methods: {
            handleClickSlideItem (index) {
                this.$set(this, 'slide_active', index)
                this.$emit('update:slide_active', index);
                this.slideIndex = index
                //  滑动元素
                let slideTrans = this.$refs.slideTotal
                //  获取滑动元素宽度
                let slideTransWidth = slideTrans.clientWidth
                //  点击的Tab元素
                let slideItem = this.$refs.slideItem[index]
                //  获取 点击的Tab元素宽度
                let slideItemWidth = slideItem.clientWidth
                //  获取 点击的Tab元素距离父级盒子左侧距离
                let slideItemOffsetLeft = slideItem.offsetLeft
                //  获取需要移动的位置
                const offset = slideItemOffsetLeft - (slideTransWidth - slideItemWidth) / 2;
                this.scrollOption.left = offset < 0 ? 0 : offset;
                slideTrans.scrollTo(this.scrollOption);
                this.$emit('change', index);
            },
            /** 点击slide切换 */
            handleClickSlideBtn (type) {
                if (this.$refs.slideItem[this.slideIndex + 1]) {
                    //  滑动元素
                    let slideTotal = this.$refs.slideTotal
                    const offset = this.scrollOption.left + this.$refs.slideItem[this.slideIndex + 1].clientWidth + this.offset
                    this.scrollOption.left = offset < 0 ? 0 : offset;
                    slideTotal.scrollTo(this.scrollOption);
                }
            },

        },
        mounted (){
            if (this.initial_index <= this.list.length - 1) {
                this.slideIndex = this.initial_index
            }
        }
    })
</script>
