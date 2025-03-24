<style>
    .carousel-swiper { width: 240px; margin: 0 auto; position: relative; overflow: hidden; }
    .carousel-swiper .carousel-item { width: 100%; transform: translate3d(0, 0, 0); background-color: #fff; position: absolute; left: 0; top: 0; z-index: -1; }
    .carousel-swiper .carousel-item.prev { visibility: hidden; transform: var(--prev-dir); transition: all .55s; z-index: -1; /*opacity: 0;*/ }
    .carousel-swiper .carousel-item.next { visibility: hidden; transform: var(--next-dir); transition: all .55s; z-index: -1; /*opacity: 0;*/ }
    .carousel-swiper .carousel-item.active { visibility: visible; transform: scale(1) translate3d(0, 0, 0); transition: all .5s; z-index: 1; opacity: 1; }
    .carousel-swiper .carousel-item .trader-image-icon { position: relative; }
    .carousel-swiper .carousel-item .trader-image-icon .trader-image-wechat { width: 20px; height: 20px; margin-left: 10px; position: relative; top: 2px; }
    .carousel-swiper .carousel-btn { display: none; width: 25px; height: 35px; background-color: rgba(0, 0, 0, 0.3); position: absolute; top: 50%; transform: translate(0, -50%); z-index: 9; }
    .carousel-swiper .carousel-btn em { color: #ffffff; }
    .carousel-swiper .carousel-btn.carousel-left { left: 0; }
    .carousel-swiper .carousel-btn.carousel-left { border-radius: 0px 18px 18px 0px; left: 0; }
    .carousel-swiper .carousel-btn.carousel-right { border-radius: 18px 0 0 18px; right: 0; }
    .carousel-swiper:hover .carousel-btn { display: flex; }
    .carousel-swiper .carousel-indicators { position: absolute; left: 50%; bottom: 10px; transform: translate(-50%); z-index: 2; }
    .carousel-swiper .carousel-indicators li { width: 12px; height: 2px; margin: 4px; border-radius: 100%; background-color: rgba(172, 100, 40, 0.5); }
    .carousel-swiper .carousel-indicators li.active { background-color: rgba(172, 100, 40, 1); }
    .carousel-item:hover:before { content: ''; width: 100%; height: 100%; background: rgba(255,255,255,0.15); position: absolute; display: block; left: 0; top: 0; z-index: 1000; }
    .carousel-swiper .trader-item-qr { display: none; width: 118px; height: 118px; padding: 5px; background-color: #ffffff; box-shadow: 0 2px 12px 0 rgba(0,0,0,.1); position: absolute; right: -126px; top: -2px; }
    .carousel-swiper .trader-item-qr .trader-item-arrow { display: none; width: 0; height: 0; border: 6px solid transparent; border-right-color: #ebeef5; border-left-width: 0; filter: drop-shadow(0 2px 12px rgba(0,0,0,.03)); position: absolute; left: -6px; top: 4px; }
    .carousel-swiper .trader-item-qr .trader-item-arrow:before { content: ''; border: 6px solid transparent; border-right-color: #ffffff; border-left-width: 0; position: absolute; left: 1px; bottom: -6px; }
    .carousel-swiper .trader-image-icon:hover .trader-item-qr { display: block; z-index: 1; }
    .carousel-swiper .trader-image-icon:hover::after { content: ''; width: 20px; height: 120%; position: absolute; right: -10px; top: 50%; z-index: 2; transform: translate(0, -50%); }
    .carousel-swiper .trader-image-icon i { display: inline-block; height: 20px; line-height: 20px; width: 40px; background: linear-gradient(270deg, #F4D083, #FBEEB7); border-radius: 10px; font-style: normal; font-weight: bold; font-size: 12px; color: #AC6428; text-align: center; margin-left: 2px; cursor: default; }
    .trader-item-box:nth-child(3n+3) .trader-image-icon:hover .trader-item-qr { right: inherit; left: -118px; }


</style>
<!--设置弹窗--按钮切换组件-->
<script type="text/x-template" id="carousel-swiper">
    <div class="carousel-swiper s-flex" ref="carouselCoverflowBox" :style="carouselStyle" @mouseenter="clearInterval" @mouseleave="play">
        <div class="carousel-item s-flex ai-bl flex-wrap" v-for="(item, carouselIndex) in list" :class="computedSetClass(carouselIndex)">
            <slot :row="item" :index="carouselIndex"></slot>
        </div>
        <template v-if="is_show_btn">
            <div class="carousel-btn carousel-left cursorp s-flex ai-ct jc-ct" @click="handleClickCheckCarousel('left')"><em class="iconfont" style="width: 18px;">&#xebb9;</em></div>
            <div class="carousel-btn carousel-right cursorp s-flex ai-ct jc-ct" @click="handleClickCheckCarousel('right')"><em class="iconfont" style="width: 10px;">&#xe777;</em></div>
        </template>
        <ul class="carousel-indicators s-flex ai-ct" v-if="is_show_dots">
            <li v-for="(indicators, indicatorsIndex) in carouselList" @click="carouselIndex = indicatorsIndex" class="cursorp" :class="{ active: indicatorsIndex == carouselIndex }"></li>
        </ul>
    </div>
</script>
<script>
    /**
     * name 轮播组件
     * list Tab数据
     * initial_index 指定选中项
     * **/
    Vue.component('carousel-swiper', {
        props: {
            list: {
                type: Array,
                default: () => { return [] }
            },
            initial_index: {
                type: Number,
                default: 0
            },
            width: {
                type: Number,
                default: 100
            },
            height: {
                type: String,
                default: '100'
            },
            is_autoplay: {   //  是否开启自动轮播
                type: Boolean,
                default: true
            },
            trans_autoplay: {   //  轮播间隔执行时间
                type: Number,
                default: 3000
            },
            is_show_btn: { //  是否展示切换按钮
                type: Boolean,
                default: true
            },
            is_show_dots: { //  是否展示切换按钮
                type: Boolean,
                default: true
            },
            is_slot: { //  是否使用插槽
                type: Boolean,
                default: false
            },
            target: {
                type: String,
                default: 'image'
            },
            mode: { //  轮播方向 horizontal / vertical
                type: String,
                default: 'horizontal'
            },
            type: { //  字段类型
                type: String,
                default: 'image'
            }
        },
        data () {
            return {
                carouselList: [],
                indicatorsList: [],
                carouselIndex: 0,
                carouselLeftIndex: 0,
                carouselRightIndex: 0,
                count_time: 0,
                tran_timer: null,
                is_can_click: true,

            }
        },
        computed: {
            carouselStyle () {
                return {
                    width: this.width + 'px',
                    height: !isNaN(this.height) ? this.height + 'px' : this.height,
                    perspective: this.width + 'px',
                    '--prev-dir': this.mode == 'horizontal' ? 'translate3d(-100%, 0, 0)' : 'translate3d(0, -100%, 0)',
                    '--next-dir': this.mode == 'horizontal' ? 'translate3d(100%, 0, 0)' : 'translate3d(0, 100%,  0)',
                }
            },
            computedSetClass () {
                return function (index) {
                    let next = this.carouselIndex === (this.carouselList.length - 1) ? 0 : this.carouselIndex + 1;
                    let prev = this.carouselIndex === 0 ? this.carouselList.length - 1 : this.carouselIndex - 1;
                    switch (index) {
                        case this.carouselIndex:
                            return 'active';
                        case next:
                            return 'next';
                        case prev:
                            return 'prev';
                        default:
                            return '';
                    }
                }
            }
        },
        template: '#carousel-swiper',
        methods: {
            initCarouselStart () {
                this.carouselList = JSON.parse(JSON.stringify(this.list))
                this.carouselLeftIndex = this.carouselIndex - 1 ? this.list.length - 1 : this.carouselIndex - 1
                this.carouselRightIndex = this.carouselIndex + 1 > (this.list.length - 1) ? this.list.length - 1 : this.carouselIndex + 1
                this.play()
            },
            play () {
                this.clearInterval()
                if (this.carouselList.length > 1 && this.is_autoplay) {
                    setTimeout(() => {
                        if (this.tran_timer) return false
                        this.tran_timer = this.setInterval(() => {
                            this.handleClickCheckCarousel('right')
                        }, this.trans_autoplay)
                    }, 3000)
                }
            },
            handleClickCheckCarousel (type) {
                if (type == 'left') {
                    this.carouselIndex = this.carouselIndex === 0 ? this.carouselList.length - 1 : this.carouselIndex - 1;
                } else {
                    this.carouselIndex = ++this.carouselIndex % this.carouselList.length;
                }
                this.$emit('change', this.carouselIndex)
                this.$emit('update:initial_index', this.carouselIndex)
            },
            setRequestAnimation (fn) {
                window.requestAnimation = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.oRequestAnimationFrame || this.fallback(fn)
            },

            /**
             * @callback   执行回调方法
             * @interval   执行延时器间隔时间
             */
            setInterval (callback, interval) {
                let start = 0, end = 0
                // 循环函数
                const loop = () => {
                    // 该方法调用必须在 callback 之前，确保打印的 'self.tran_timer'  'clearInterval' 的值一样
                    end = Date.now()
                    this.tran_timer = requestAnimation(loop);
                    // 判断时间超过 interval 就执行函数，并重置时间
                    if(end - start >= interval) {
                        // 让起始时间、结束时间相同
                        start = end
                        this.count_time += 1
                        // 执行回调
                        callback && callback();
                    }
                }
                // 执行 requestAnimationFrame
                this.tran_timer = requestAnimation(loop);
                // 返回 tran_timer
                return this.tran_timer;
            },
            clearInterval () {
                cancelAnimationFrame(this.tran_timer);
                clearInterval(this.tran_timer);
                setTimeout(() => { this.tran_timer = null }, 200)
            },
            fallback (callback) {
                let currTime = Date.now(),
                    timeToCall = Math.max(0, 16 - (currTime - this.lastTime)),
                    id = setTimeout(function() {
                        callback(currTime + timeToCall);
                    }, timeToCall);
                this.lastTime = currTime + timeToCall;
                return id;
            }
        },
        mounted (){
            this.setRequestAnimation()
            if (this.initial_index <= this.list.length - 1) {
                this.carouselIndex = this.initial_index
            }
            setTimeout(() => {
                this.initCarouselStart()
            }, 200)
        }
    })
</script>
