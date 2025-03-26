@extends('manage.app_website_decoration.layout.layout')

@section('content')
    <!--头部固定模版-->
    <home-navigation v-if="dataTempalteNavigation" :temp_item="dataTempalteNavigation"
                     :temp_index="dataTempalteNavigation.component_name" :options="public_options_down"
                     @save="handleTemplateSettingSave"></home-navigation>

    <!--拖拽模版-->
    <vuedraggable tag="div" v-model="contentForm" group="name" chosenClass="temp-drag-chosen" filter=".temp-nocan-drag"
                  handle=".public-mark" :forceFallback="false" animation="300"
                  class="temp-drag-perview home-drag-perview"
                  @end="handleTemplatePreviewDratEnd" id="tempDragPerview"
                  v-if="dataTempalteNavigation.data"
                  :style="{ backgroundImage: `url(${dataTempalteNavigation.data.base_data.home_bg_image})` }"
    >
        <div class="temp-not-nest" v-for="(parent, value) in contentForm"
             v-if="parent.component_name != 'home_nav' && parent.component_name != 'label'"
             :ref="`tempitem${parent.item_id}`" :key="value" :data-type="parent.component_name" :data-index="value"
             @click="tempActiveId = parent.id">
            <!--Banner-->
            <home-advertising-one :temp_item="parent" :temp_index="value" :is_ad="true"
                                  v-if="parent.component_name == 'advertising_one'" :temp_list="contentForm"
                                  :active_index="tempActiveId" :options="public_options_down"
                                  :upload_size_validate="upload_size"
                                  @save="handleTemplateSettingSave"></home-advertising-one>
            <!-- 菜单金刚区 -->
            <home-menu-template :temp_item="parent" :temp_index="value" v-if="parent.component_name == 'quick_link'"
                                :temp_list="contentForm" :active_index="tempActiveId" :options="public_options_down"
                                :upload_size_validate="upload_size"
                                @save="handleTemplateSettingSave"></home-menu-template>
            <!-- 新闻 -->
            <home-new-template :temp_item="parent" :temp_index="value" v-if="parent.component_name == 'news'"
                               :temp_list="contentForm" :active_index="tempActiveId" :options="public_options_down"
                               :upload_size_validate="upload_size"
                               @save="handleTemplateSettingSave"></home-new-template>
            <!-- 频道广场 -->
            <home-channel-template :temp_item="parent" :temp_index="value"
                                   v-if="parent.component_name == 'channel_square'" :temp_list="contentForm"
                                   :active_index="tempActiveId" :options="public_options_down"
                                   :upload_size_validate="upload_size"
                                   @save="handleTemplateSettingSave"></home-channel-template>
            <!-- 主题广告 -->
            <home-theme-template :temp_item="parent" :temp_index="value"
                                 v-if="parent.component_name == 'theme_advertising'" :temp_list="contentForm"
                                 :active_index="tempActiveId" :options="public_options_down"
                                 :upload_size_validate="upload_size"
                                 @save="handleTemplateSettingSave"></home-theme-template>
            <!-- 热力榜 -->
            <home-hot-template :temp_item="parent" :temp_index="value" v-if="parent.component_name == 'hot_list'"
                               :temp_list="contentForm" :active_index="tempActiveId"
                               :upload_size_validate="upload_size"
                               @save="handleTemplateSettingSave"></home-hot-template>
            <!-- 广告位2 -->
            <home-advertising-two :temp_item="parent" :temp_index="value"
                                  v-if="parent.component_name == 'advertising_two'" :temp_list="contentForm"
                                  :active_index="tempActiveId" :options="public_options_down"
                                  :upload_size_validate="upload_size"
                                  @save="handleTemplateSettingSave"></home-advertising-two>
            <!-- 广告位3 -->
            <home-advertising-three :temp_item="parent" :temp_index="value"
                                    v-if="parent.component_name == 'advertising_three'" :temp_list="contentForm"
                                    :active_index="tempActiveId" :options="public_options_down"
                                    :upload_size_validate="upload_size"
                                    @save="handleTemplateSettingSave"></home-advertising-three>
            <!-- 限时抢购 -->
            <home-time-shopping :temp_item="parent" :temp_index="value" v-if="parent.component_name == 'flash_sale'"
                                :temp_list="contentForm" :active_index="tempActiveId" :options="public_options_down"
                                :upload_size_validate="upload_size"
                                @save="handleTemplateSettingSave"></home-time-shopping>
            <!-- 品牌精选 -->
            <home-brand-template :temp_item="parent" :temp_index="value" v-if="parent.component_name == 'brand_choice'"
                                 :temp_list="contentForm" :active_index="tempActiveId" :options="public_options_down"
                                 :upload_size_validate="upload_size"
                                 @save="handleTemplateSettingSave"></home-brand-template>
            <!-- 推荐商家 -->
            <home-recommend-seller :temp_item="parent" :temp_index="value"
                                   v-if="parent.component_name == 'recommend_seller'" :temp_list="contentForm"
                                   :active_index="tempActiveId" :options="public_options_down"
                                   :upload_size_validate="upload_size"
                                   @save="handleTemplateSettingSave"></home-recommend-seller>
            <!-- 为您推荐 -->
            <home-recommend-template :temp_item="parent" :temp_index="value"
                                     v-if="parent.component_name == 'recommend_theme'" :temp_list="contentForm"
                                     :active_index="tempActiveId" :options="public_options_down"
                                     :upload_size_validate="upload_size"
                                     @save="handleTemplateSettingSave"></home-recommend-template>
            <!-- 热销商品 -->
            <hot-sale-goods :temp_item="parent" :temp_index="value" v-if="parent.component_name == 'hot_sale_good'"
                            :temp_list="contentForm" :active_index="tempActiveId" :options="public_options_down"
                            :upload_size_validate="upload_size"
                            @save="handleTemplateSettingSave"></hot-sale-goods>
        </div>
    </vuedraggable>
    <!-- 大屏广告位 -->
    <slide-advertising v-if="dataTempalteLargeScreen" :temp_item="dataTempalteLargeScreen"
                       :temp_index="dataTempalteLargeScreen.component_name" title="大屏广告位" top="65px"
                       :upload_size_validate="upload_size"
                       @save="handleTemplateSettingSave" :is_no_seting="false" :temp_list="contentForm"
                       :options="public_options_down"></slide-advertising>
    <!-- 侧边广告 -->
    <slide-advertising v-if="dataTempalteSideAdvertising" :temp_item="dataTempalteSideAdvertising"
                       :temp_index="dataTempalteSideAdvertising.component_name" title="侧边广告位" top="175px"
                       :upload_size_validate="upload_size"
                       @save="handleTemplateSettingSave" :is_no_seting="false" :temp_list="contentForm"
                       :options="public_options_down"></slide-advertising>
    <!-- 二楼广告位 -->
    <slide-advertising v-if="dataTempalteSecondAdvertisement" :temp_item="dataTempalteSecondAdvertisement"
                       :temp_index="dataTempalteSecondAdvertisement.component_name" title="二楼广告位" top="285px"
                       :upload_size_validate="upload_size"
                       @save="handleTemplateSettingSave" :is_no_seting="false" :temp_list="contentForm"
                       :options="public_options_down"></slide-advertising>
    <!-- 标签栏 -->
    <bottom-nav-bar v-if="dataTempalteLabel" :temp_item="dataTempalteLabel"
                    :temp_index="dataTempalteLabel.component_name" :is_no_setting="false" :temp_list="contentForm"
                    :upload_size_validate="upload_size"
                    :options="public_options_down" @save="handleTemplateSettingSave"></bottom-nav-bar>
@endsection
@section('js')
    <!--侧边广告固定组件-->
    @include('manage.app_website_decoration.components.home.slide-advertising')
    <!--导航栏固定组件-->
    @include('manage.app_website_decoration.components.home.home-navigation')
    <!--广告位1拖拽组件-->
    @include('manage.app_website_decoration.components.home.home-advertising-one')
    <!--广告位2拖拽组件-->
    @include('manage.app_website_decoration.components.home.home-advertising-two')
    <!--广告位3拖拽组件-->
    @include('manage.app_website_decoration.components.home.home-advertising-three')
    <!--金刚区拖拽组件-->
    @include('manage.app_website_decoration.components.home.home-menu')
    <!--频道广场拖拽组件-->
    @include('manage.app_website_decoration.components.home.home-channel')
    <!--主题广告拖拽组件-->
    @include('manage.app_website_decoration.components.home.home-theme')
    <!--限时抢购拖拽组件-->
    @include('manage.app_website_decoration.components.home.home-time-shopping')
    <!--推荐商家拖拽组件-->
    @include('manage.app_website_decoration.components.home.home-recommend-seller')
    <!--为您推荐拖拽组件-->
    @include('manage.app_website_decoration.components.home.home-recommend')
    <!--热销商品拖拽组件-->
    @include('manage.app_website_decoration.components.home.hot-sale-goods')
    <!--加载动画-->
    @include('manage.app_website_decoration.components.save-load')
    <!--模块遮罩组件-->
    @include('manage.app_website_decoration.components.template-mark-setting')
    <!--Header Tab组件-->
    @include('manage.app_website_decoration.components.basic-tabs-line')
    <!--广告轮播组件-->
    @include('manage.app_website_decoration.components.carousel-swiper')
    <!--价格公共组件-->
    @include('manage.app_website_decoration.components.form-price')
    <!--标签栏组件-->
    @include('manage.app_website_decoration.components.bottom-nav-bar')
    <script>
        const vm = new Vue({
            el: '#app',
            data() {
                return {
                    activity_template: {},   //  装修基本配置
                    item_data: null,   //  装修编辑数据
                    componentsIconLeft: {}, //  左侧组件列表数据
                    componentsIconRight: {},   //  右侧数据

                    tempActiveId: null,  //  模块选中id
                    is_start: false,    //  当页面组件为第一个时，为 true
                    is_show_warn: false,    //  是否展示右上角页面数据保存提示
                    tabIndex: 0,    //  tab选中索引值
                    is_load_source: false,    //  左侧数据加载
                    is_check_source: true,    //  是否展示素材列表
                    contentForm: [],    //  页面面板中展示的模块数据
                    newContentForm: [],    //  拷贝页面面板中展示的模块数据，用于拖拽基础组件时，对比新添加的基础组件
                    controlOnStart: true,   //  是否允许 vuedraggable 开始 put 行为实现嵌套
                    options: {animation: 500},    //  拖拽配置
                    dragType: '',   //  拖拽组件类型

                    /** 提交保存加载动画 **/
                    save_loading: false,
                    is_can_save: true,  //  节流保存
                    is_show_dialog: false,     //  是否展示设置弹窗


                    direction: 'rtl',
                    templateSetForm: {},    //  设置弹窗表单数据,
                    templateSetFormName: '',    //  设置弹窗表单字段
                    templateSetFormType: '',    //  设置弹窗表单模版类型
                    templateSetFormTarget: '',    //  设置弹窗表单模版表单字段
                    templateSetFormTitle: '',    //  设置弹窗表单title
                    templateSetRule: {
                        tel: [
                            {required: true, message: '请输入客服电话', trigger: 'blur'},
                        ],
                        goods_id: [
                            {required: true, message: '请选择关联商品', trigger: 'change'},
                        ],
                        //  网站搜索 -- 网站LOGO
                        //  网站搜索 -- 产品提示文字
                        pro_waning_text: [
                            {required: true, message: '请输入产品提示文字', trigger: 'blur'},
                            {max: 20, message: '提示文字不能超过20个字符', trigger: 'blur'},
                        ],
                        //  网站搜索 -- 店铺提示文字
                        seller_waning_text: [
                            {required: true, message: '请输入店铺提示文字', trigger: 'blur'},
                            {max: 20, message: '提示文字不能超过20个字符', trigger: 'blur'},
                        ],
                        //  网站搜索 -- 广告位
                        activity_img: [
                            {required: true, message: '请上传图片', trigger: 'change'},
                        ],
                        bottom_navigation: {
                            items: {
                                url: [
                                    {required: true, message: '请输入标题名称', trigger: 'blur'}
                                ]
                            }
                        }
                    },
                    value: '',
                    cateList: [],
                    dataTempalteForm: {
                        web_decoration: @json($app_website_data),
                        item_data: @json($data),
                        not_for_data: @json($not_for_data),
                        left_assembly: @json($component_icon),
                        right_assembly: @json($component_value)
                    },
                    dataTempalteNavigation: {},
                    dataTempalteLabel: {},
                    dataTempalteLargeScreen: {},
                    dataTempalteRedEnvelope: {},
                    dataTempalteSideAdvertising: {},
                    dataTempalteSecondAdvertisement: {},
                    pickerOptions: {    //  日期选择范围限制
                        disabledDate: (time) => {
                            return time.getTime() < Date.now() - (24 * 60 * 60 * 1000)
                        }
                    },
                    drawerKey: Math.round(new Date() / 1000),
                    shop_color: '{{ $app_shop_color }}',
                    shop_tel: "",
                    web_nav_name: '',
                    web_nav_alias: '',
                    setting_info: {},   //  设置弹窗数据
                    public_options_down: [],    //  公共下拉数据
                    upload_size: 2
                }
            },
            components: {vuedraggable},
            computed: {
                /** 查询组件是否达到添加次数 **/
                computedTempIsExist() {
                    return function (index, type, limit, field_name) {
                        const existList = this.contentForm.filter(item => item && item.component_name == type)
                        if (limit > 0 && existList.length >= limit) {
                            return index
                        } else {
                            return -1
                        }
                    }
                },
                computedDataToArray() {
                    return function (value) {
                        let list = [], arr = []
                        if (value == '') return []
                        const value_type = Object.prototype.toString.call(value).slice(8, -1)
                        if (value_type != 'Array') {
                            if ((value + '').indexOf(',') > -1) {
                                list = value.split(',')
                            } else {
                                list = [value]
                            }
                            list.forEach(item => {
                                arr.push(item * 1)
                            })
                            return arr
                        } else {
                            return value
                        }
                    }
                },
                /** 针对分类选择值转为单个字符串 */
                computedCascaderSelectedToString() {
                    return function (value) {
                        let id = null
                        if (Object.prototype.toString.call(value).slice(8, -1) == 'Array') {
                            id = value[value.length - 1]
                        } else {
                            id = value
                        }
                        return id
                    }
                },
                /** 计算列表样式选中属性个数 */
                computedSelectAttrCount() {
                    return function (arr1, arr2, arr3) {
                        let list = [];
                        (arr1 && arr1.length) && (list = arr1.filter(item => item.is_show));
                        (arr2 && arr2.length) && (list = list.concat(arr2.filter(item => item.is_show)));
                        (arr3 && arr3.length) && (list = list.concat(arr3.filter(item => item.is_show)));
                        return list.length
                    }
                },
                computedDataType() {
                    return function (value) {
                        const type = Object.prototype.toString.call(value).slice(8, -1)
                        return type
                    }
                },
            },
            watch: {
                //  监听页面背景设置切换
                pageBasicTabIndex(value) {
                    /*if (value) {
                        this.activity_template.bg_color = ''
                    } else {
                        this.activity_template.pc_bg_img = ''
                        this.activity_template.app_bg_img = ''
                    }*/
                },
                contentForm: {
                    handler(value) {
                    },
                    immediate: true,
                    deep: true
                }
            },
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
                /** 获取公共下拉数据 */
                getPublicDownOptions() {
                    this.doGet('{!! route('manage.app_web_decoration.router_options') !!}').then(res => {
                        if (res.code == 200) {
                            this.public_options_down = res.data
                        } else {
                            this.$message.error(res.message)
                        }
                    })
                    this.save_loading = false
                },
                /** 页面初始化设置 */
                initPageSet() {
                    document.querySelector('body').classList.add('template-edit-page')
                    //  设置页面背景默认选中值
                    const info = this.activity_template
                    this.dataTempalteForm.left_assembly.data_component && this.dataTempalteForm.left_assembly.data_component.map(item => item.is_can_drap = true)
                    this.dataTempalteForm.left_assembly.advertising_component && this.dataTempalteForm.left_assembly.advertising_component.map(item => item.is_can_drap = true)
                    switch (this.dataTempalteForm.web_decoration.alias) {
                        case 'home':
                            this.web_nav_name = '首页网站导航'
                            this.web_nav_alias = 'pc_class'
                            break
                        case 'supermarket':
                            this.web_nav_name = '超市网站导航'
                            this.web_nav_alias = 'pc_shop_site_nav'
                            break
                        case 'market':
                            this.web_nav_name = '集市网站导航'
                            this.web_nav_alias = 'pc_crop_site_nav'
                            break
                        default:
                            this.web_nav_name = ''
                            this.web_nav_alias = ''
                            break
                    }
                    this.initPageData()
                },
                /** 页面初始化数据 */
                async initPageData() {
                    //  编辑时，获取装修数据
                    this.item_data = this.dataTempalteForm.item_data
                    //  初始化页面固定组件
                    await this.initPageFixedData()
                    this.contentForm = JSON.parse(JSON.stringify(this.item_data))
                    this.contentForm.length && this.contentForm.map(item => {
                        item.tabIndex = 0
                        item.slideIndex = 0
                        item.zixunIndex = 0
                        if (item.data && item.data.component_name == 'recommend_theme') {
                            item.data.recommend_good_data_left = []
                            item.data.recommend_good_data_right = []
                            if (item.data.data.recommend_good_data && item.data.data.recommend_good_data.length) {
                                item.data.data.recommend_good_data.some((child, childIndex) => {
                                    if ((childIndex + 1) % 2 == 0) {
                                        item.data.recommend_good_data_right.push(child)
                                    } else {
                                        item.data.recommend_good_data_left.push(child)
                                    }
                                })
                            }
                        }
                        if (item.data && item.data.component_name == 'quick_link') {
                            // 格式化后的数据
                            item.data.list = []
                            // 总数据长度
                            let total = item.data.items.length
                            // 一屏数据个数
                            let slide = item.data.number * item.data.height
                            if (item.data.height != 0) {
                                //  根据总数据按照一屏数据个数对数据进行格式化
                                if (!item.data.list) item.data.list = []
                                for (let i = 0; i < total; i += slide) {
                                    item.data.list.push(item.data.items.slice(i, i + slide));
                                }
                            } else {
                                item.data.list = item.data.items
                            }
                        }
                        if (item.data && item.data.component_name == 'news') {
                            let total = item.data.items.length
                            if (item.data.style != 1) {
                                if (!item.data.list) item.data.list = []
                                for (let i = 0; i < total; i += (item.data.style * 1)) {
                                    item.data.list.push(item.data.items.slice(i, i + (item.data.style * 1)));
                                }
                            } else {
                                item.data.list = item.data.items
                            }
                        }
                    })
                    this.$set(this, 'contentForm', this.contentForm)
                    this.newContentForm = JSON.parse(JSON.stringify(this.contentForm))
                    window.onload = () => {
                        if ($('body').height() < 1080) {
                            $('.template-group').css('height', '400px')
                        }
                    }
                    //  监听 Ctrl+S 保存
                    document.addEventListener('keydown', this.listenerKeydowm, false)
                    // this.initTemplateData()
                },
                /** 初始化页面固定组件 */
                initPageFixedData() {
                    return new Promise(resolve => {
                        //  获取导航组件数据
                        const dataTempalteNavigation = this.dataTempalteForm.not_for_data.filter(item => item.component_name == 'home_nav')
                        this.dataTempalteNavigation = dataTempalteNavigation.length ? dataTempalteNavigation[0] : {}
                        if (this.dataTempalteNavigation && this.dataTempalteNavigation.data && this.dataTempalteNavigation.data.is_show_nav_data) {
                            const left_data = this.dataTempalteNavigation.data.nav_data.items.filter(item => item.fixed_position == 'left')
                            const right_data = this.dataTempalteNavigation.data.nav_data.items.filter(item => item.fixed_position == 'right')
                            if (left_data.length) this.dataTempalteNavigation.data.nav_data_left = left_data[0]
                            if (right_data.length) this.dataTempalteNavigation.data.nav_data_right = right_data[0]
                            this.dataTempalteNavigation.data.nav_data_tabs = this.dataTempalteNavigation.data.nav_data.items.filter(item => !item.fixed_position)
                        }

                        //  获取底部标签组件数据
                        const dataTempalteLabel = this.dataTempalteForm.not_for_data.filter(item => item.component_name == 'label')
                        this.dataTempalteLabel = dataTempalteLabel.length ? dataTempalteLabel[0] : {}

                        // large_screen
                        //  获取大屏广告组件数据 large_screen
                        const dataTempalteLargeScreen = this.dataTempalteForm.not_for_data.filter(item => item.component_name == 'large_screen')
                        this.dataTempalteLargeScreen = dataTempalteLargeScreen.length ? dataTempalteLargeScreen[0] : {}

                        //  获取红包签到组件数据 red_envelope
                        const dataTempalteRedEnvelope = this.dataTempalteForm.not_for_data.filter(item => item.component_name == 'red_envelope')
                        this.dataTempalteRedEnvelope = dataTempalteRedEnvelope.length ? dataTempalteRedEnvelope[0] : {}

                        //  获取侧边广告组件数据 side_advertising
                        const dataTempalteSideAdvertising = this.dataTempalteForm.not_for_data.filter(item => item.component_name == 'side_advertising')
                        this.dataTempalteSideAdvertising = dataTempalteSideAdvertising.length ? dataTempalteSideAdvertising[0] : {}

                        //  获取二楼广告组件数据 second_advertisement
                        const dataTempalteSecondAdvertisement = this.dataTempalteForm.not_for_data.filter(item => item.component_name == 'second_advertisement')
                        this.dataTempalteSecondAdvertisement = dataTempalteSecondAdvertisement.length ? dataTempalteSecondAdvertisement[0] : {}


                        resolve(true)
                    })
                },
                /** 获取页面数据 */
                getPageData() {
                    this.doPost('{!! route('manage.app_web_decoration.decoration') !!}', {id: this.dataTempalteForm.web_decoration.id}).then(res => {
                        if (res.code == 200) {
                            this.$set(this.dataTempalteForm, 'app_website_data', res.data.app_website_data);
                            this.$set(this.dataTempalteForm, 'item_data', res.data.data);
                            this.$set(this.dataTempalteForm, 'not_for_data', res.data.not_for_data);
                            this.initPageData()
                        } else {
                            this.$message.error(res.message)
                        }
                    })
                },
                /*************************************************************** 拖拽相关 **************************************************************/
                /** 点击切换素材开关 **/
                handleClickCheckSourceSwitch() {
                    this.is_check_source = !this.is_check_source
                    if (this.is_check_source) {
                        $('.template-content-source').css({
                            'width': '260px',
                            'height': 'calc(100% - 60px)'
                        });
                        $('.template-content-source .template-source-switch').css('top', '50px');
                    } else {
                        $('.template-content-source').css({
                            'width': '160px',
                            // 'width': '0',
                            'height': '66px'
                        })
                        $('.template-content-source .template-source-switch').css('top', 0)
                    }
                },
                /** 克隆组件 **/
                handleTempDragClone(dragitem, field) {
                    //  获取左侧拖拽数据，对比右侧数据，从而得到要加入到页面模块的数据
                    let item
                    this.dragType = field
                    //  如果拖拽的是固定组件
                    item = this.dataTempalteForm.right_assembly.find(value => value.component_name == dragitem.component_name)
                    // item.content.name = item.name
                    //  生成唯一id，用来设置选中
                    item.id = Math.round(new Date() / 1000) + 'add'
                    /*//  获取模块标题，过滤掉素材组件
                    item.name = item.name && item.act_type != 'source' ? item.name : this.getTemplateName(item.act_type, item)*/
                    //  判断拖拽的模版标题是否一致，如有标题一致情况，则重命名标题(1)，diffname对比名称是否相同，diffnumber获取相同类型组件的个数
                    const diffname = this.contentForm.filter(temp => temp.name == item.name)
                    const diffNextName = this.contentForm.filter(temp => temp.name == `${item.name}(${diffname.length + 1})` || temp.name == `${item.name}(${diffname.length - 1})`)
                    let diffnumber = this.contentForm.filter(temp => temp.type == item.type)
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
                    return copyItem
                },
                /** 监听开始拖拽以实现嵌套 **/
                handleDragTempStart(event) {
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
                handleDragTempEnd(event) {
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
                    }, 200)
                },
                /** 数据展示区域拖拽结束 */
                handleTemplatePreviewDratEnd() {
                    //  防止拖拽影响设置表单样式
                    const dragElementParent = document.querySelectorAll('.temp-not-nest')
                    dragElementParent.forEach(item => item.style.transform = '')
                },
                /** 监听组件移动，判断是否可以嵌套板块 **/
                handleDragTempMove(event, original) {
                    this.is_drop_source = false
                    //  默认阻止操作设置基础组件坐标位置，所有判断条件通过后打开，若条件未通过将会被拦截
                    this.drag_is_basic = false
                    //  被拖拽的元素对象
                    const dragitem = event.draggedContext.element
                    //  目标元素对象
                    let relateditem = event.relatedContext.element
                    //  判断当前组件是否可以添加到，要拖放的组件中
                    if (dragitem && relateditem && dragitem.act_type >= 0) {
                        //  判断目标元素是否为左侧图标菜单元素，如果是则阻止拖拽
                        if (event.related.className.indexOf('template-icon-list') > -1) return false
                        //  如果拖拽的元素是基础组件 或者 如果拖拽的元素是自定义组件，则拦截自定义组件嵌套自定义组件
                        if (dragitem.act_type == 22) {
                            //  基础组件嵌套基础组件，或者自定义样式组件，则通过，否则阻止拖拽
                            if (relateditem.act_type != 22 && (relateditem.act_type < 6 || relateditem.act_type > 10)) {
                                //  禁止嵌入组件的警告样式
                                if (event.related.className.indexOf('temp-item') > -1) {
                                    event.related.classList.add('temp-error')
                                }
                                if (event.related.className.indexOf('temp-not-nest') > -1) {
                                    event.related.classList.add('temp-error')
                                }
                                //  鼠标拖拽位置距离顶部高度
                                /*const dragTop = event.draggedRect.top
                                //  目标元素高度
                                const relatedHeight = event.related.offsetHeight
                                //  如果鼠标位置超过元素高度，则可以添加，否则阻止拖拽
                                if (dragTop >= relatedHeight) return false*/
                            }
                            /*this.basic_axes_x = original.pageX
                            this.basic_axes_y = original.pageY*/
                        } else {
                            const tempErrorElement = document.querySelectorAll('.temp-item')
                            tempErrorElement.forEach(item => {
                                item.classList.add('temp-error')
                            })
                        }
                    } else {
                        if (dragitem.act_type != 22) {
                            const tempErrorElement = document.querySelectorAll('.temp-item')
                            tempErrorElement.forEach(item => {
                                item.classList.add('temp-error')
                            })
                        }
                    }
                    //  拖拽的是否为基础组件
                    this.drag_is_basic = dragitem.act_type == 22
                    return true
                },
                /*************************************************************** 设置相关 **************************************************************/
                /** 设置保存回调 */
                handleTemplateSettingSave(params) {
                    const {content, data, index} = params
                    if (isNaN(index)) {
                        this.dataTempalteForm.not_for_data.map(item => {
                            if (item.component_name == index) {
                                item.content = content
                                item.data = data
                            }
                        })
                    } else {
                        this.$set(this.contentForm[index], 'content', content)
                        this.$set(this.contentForm[index], 'data', data)
                    }
                },
                /** 点击退出装修 **/
                handleExitAllTemplate() {
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
                /** 点击或Ctrl+S 保存 **/
                async handleSaveAllTemplate(type) {
                    if (this.is_can_save) {
                        this.is_can_save = false
                        /*if (!this.contentForm.length) {
                            this.$confirm('页面未装修，请装修后再发布！', '', {
                                confirmButtonText: '确定',
                                showCancelButton: false,
                                type: 'warning'
                            }).then(() => {}).catch(() => {});
                            this.is_can_save = true
                            return false
                        }*/
                        this.contentForm.map(item => {
                            if (isNaN(item.id)) item.id = ''
                        })

                        let info = {
                            id: this.dataTempalteForm.web_decoration.id,
                            data: this.contentForm
                        }
                        info.data = info.data.concat(this.dataTempalteForm.not_for_data)

                        let is_save = 'ctrl'
                        if (type == 'save') {
                            is_save = await this.isSureSave()
                        }
                        if (is_save == 'ctrl' || is_save == 'confirm') {
                            this.save_loading = true
                            /*console.log('info',info)
                            return*/
                            this.doPost('{!! route('manage.app_web_decoration.decoration_store') !!}', info).then(res => {
                                if (res.code == 200) {
                                    if (is_save == 'ctrl') {
                                        this.is_can_save = true
                                        this.$message.success('保存成功！')
                                        this.getPageData()
                                    } else {
                                        //  跳转至<网站装修>页面
                                        parent.closeSelf('移动端网站装修', '{!! route('manage.app_web_decoration.index') !!}');
                                    }
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
                },
                /** 询问是否确定保存 **/
                isSureSave() {
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
                listenerKeydowm(event) {
                    if (event.keyCode == 83 && (navigator.platform.match("Mac") ? event.metaKey : event.ctrlKey)) {
                        if (this.setting_show) return false
                        this.handleSaveAllTemplate('ctrl')
                        event.stopPropagation();
                        event.preventDefault();
                    }
                },
            },
            mounted() {
                this.save_loading = true
                this.initPageSet()
                this.getPublicDownOptions()
            },
            destroyed() {
                document.querySelector('body').classList.remove('template-edit-page')
            },
            created() {
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

        .template-content-box { /*height: calc(100vh - 60px);*/
            background-color: #f2f2f2;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .temp-drag-perview.home-drag-perview {
            height: calc(100vh - 226px - 98px - 100px);
            background-size: 750px auto;
            background-position: top center;
            background-repeat: no-repeat;
        }

        .temp-drag-perview.home-drag-perview::-webkit-scrollbar {
            display: none;
        }

        .home-drag-perview .temp-item {
            padding: 0 20px;
        }


        /* 标签栏 */
        .temp-item.template-footer {
            padding: 0;
        }

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
