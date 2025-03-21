@extends('admin.app_website_decoration.layout.layout')

@section('content')
    <!--头部固定模版-->
    <div class="home-header public-width">
        <div class="home-header-content">
            <public-mark-template title="此版块不支持修改" height="100%" alias="page_nav"
                                  is_no_setting="true"></public-mark-template>
            <div class="ucenter-header">
                <div class="head-info-box s-flex">
                    <div class="head-tx">
                        <img src="https://cdn.toodudu.com/uploads/2022/01/12/portait.jpeg" alt="">
                    </div>
                    <div class="user-info flex-1">
                        <div class="s-flex jc-bt">
                            <div class="info-name" style="margin-top: 38px">
                                <span>登录/</span><span>注册</span>
                            </div>
                            <div class="info-set">
                                <img src="https://cdn.toodudu.com/uploads/2023/10/20/set.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="company-box no-company s-flex jc-bt">
                    <div class="ellipsis-1">
                        企业采购价更低，赶快认证吧！
                    </div>
                    <div class="cert-btn">去认证</div>
                </div>
            </div>
        </div>
    </div>
    <!-- 订单中心 -->
    <mine-order v-if="dataTempalteOrder" :temp_item="dataTempalteOrder[0]" :temp_index="dataTempalteOrderIndex"
                :temp_list="contentForm" :temp_asset_name="dataTempalteAsset[0] && dataTempalteAsset[0].data.name"
                :upload_size_validate="upload_size"
                :options="public_options_down" @save="handleTemplateSettingSave"></mine-order>
    <!-- 资产中心 -->
    <mine-asset v-if="dataTempalteAsset" :temp_item="dataTempalteAsset[0]" :temp_index="dataTempalteAssetIndex"
                :temp_list="contentForm"
                :temp_order_name="dataTempalteOrder[0] && dataTempalteOrder[0].data.order_data.name"
                :upload_size_validate="upload_size"
                :options="public_options_down" @save="handleTemplateSettingSave"></mine-asset>
    <!--拖拽模版-->
    <vuedraggable tag="div" v-model="contentForm" group="name" chosenClass="temp-drag-chosen" filter=".temp-nocan-drag"
                  handle=".public-mark" :forceFallback="false" animation="300"
                  class="temp-drag-perview home-drag-perview mine-drag-perview" @end="handleTemplatePreviewDratEnd"
                  id="tempDragPerview">
        <div class="temp-not-nest" v-for="(parent, value) in contentForm"
             v-if="parent.component_name != 'home_nav' && parent.component_name != 'label'"
             :ref="`tempitem${parent.item_id}`" :key="value" :data-value="parent.type" :data-index="value"
             :upload_size_validate="upload_size"
             @click="tempActiveId = parent.id">
            <!-- 自定义模块 -->
            <mine-custom :temp_item="parent" :temp_index="value" v-if="parent.component_name == 'mine_custom'"
                         :temp_list="contentForm" :active_index="tempActiveId" :options="public_options_down"
                         :upload_size_validate="upload_size"
                         :temp_order_name="dataTempalteOrder[0].data.order_data.name"
                         :temp_asset_name="dataTempalteAsset[0].data.name"
                         @save="handleTemplateSettingSave"></mine-custom>
            <!-- 常买常逛 -->
            <buy-and-sell :temp_item="parent" :temp_index="value" v-if="parent.component_name == 'buy_and_sell'"
                          :temp_list="contentForm" :active_index="tempActiveId" :options="public_options_down"
                          :upload_size_validate="upload_size"
                          :temp_order_name="dataTempalteOrder[0].data.order_data.name"
                          :temp_asset_name="dataTempalteAsset[0].data.name"
                          @save="handleTemplateSettingSave"></buy-and-sell>
            <!-- 轮播广告 -->
            <home-advertising-one :temp_item="parent" :temp_index="value"
                                  v-if="parent.component_name == 'advertising_one'" :temp_list="contentForm"
                                  :active_index="tempActiveId" :options="public_options_down"
                                  @save="handleTemplateSettingSave"></home-advertising-one>
            <!-- 为您推荐 -->
            <mine-recommend :temp_item="parent" :temp_index="value" v-if="parent.component_name == 'recommend_left'"
                            :temp_list="contentForm" :active_index="tempActiveId" :options="public_options_down"
                            :temp_order_name="dataTempalteOrder[0].data.order_data.name"
                            :temp_asset_name="dataTempalteAsset[0].data.name"
                            :upload_size_validate="upload_size"
                            @save="handleTemplateSettingSave"></mine-recommend>
        </div>
    </vuedraggable>
    <!-- 标签栏 -->
    <template>
        <div class="bottom-nav-box-fix" v-if="dataTempalteLabel[0]"></div>
        <div class="bottom-nav-box">
            <bottom-nav-bar :temp_item="dataTempalteLabel[0]" :temp_index="dataTempalteLabelIndex" :is_no_setting="true"
                            :upload_size_validate="upload_size"
                            :temp_list="contentForm" :options="public_options_down"></bottom-nav-bar>
        </div>
    </template>
@endsection
@section('js')
    <!--加载动画-->
    @include('admin.web_decoration.components.save-load')
    <!--模块遮罩组件-->
    @include('admin.app_website_decoration.components.template-mark-setting')
    <!--广告轮播组件-->
    @include('admin.app_website_decoration.components.carousel-swiper')
    <!--标签栏组件-->
    @include('admin.app_website_decoration.components.bottom-nav-bar')
    <!--订单中心组件-->
    @include('admin.app_website_decoration.components.mine.mine-order')
    <!--资产中心组件-->
    @include('admin.app_website_decoration.components.mine.mine-asset')
    <!--自定义模块组件-->
    @include('admin.app_website_decoration.components.mine.mine-custom')
    <!--常买常逛组件-->
    @include('admin.app_website_decoration.components.mine.buy-and-sell')
    <!--广告位1拖拽组件-->
    @include('admin.app_website_decoration.components.home.home-advertising-one')
    <!--为您推荐拖拽组件-->
    @include('admin.app_website_decoration.components.mine.mine-recommend')
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
                    dataTempalteOrder: [],
                    dataTempalteOrderIndex: 0,
                    dataTempalteAsset: [],
                    dataTempalteAssetIndex: 0,
                    dataTempalteLabel: [],
                    dataTempalteLabelIndex: 0,
                    pickerOptions: {    //  日期选择范围限制
                        disabledDate: (time) => {
                            return time.getTime() < Date.now() - (24 * 60 * 60 * 1000)
                        }
                    },
                    drawerKey: Math.round(new Date() / 1000),
                    publicListItem: {
                        //  网站头部
                        web_top: {
                            "type": "",
                            "title": "",
                            "desc": "",
                            "sort": "",
                            "image": ""
                        },
                        //  网站搜索
                        web_search: {
                            "keyword": "",
                            "url": "",
                            "sort": "",
                            "style": "#666666"
                        },
                        //  轮播广告
                        banner: {
                            "image": "",
                            "url": "",
                            "time_type": "2",
                            "start_time": "",
                            "end_time": "",
                            "sort": "1",
                            "is_show": 1,
                            "time_arr": []
                        },
                        //  网站分类
                        web_category: {
                            "icon": "https://cdn.toodudu.com/uploads/2023/07/31/fenlei.png",
                            "category_id": "",
                            "category_url": "",
                            "child_category_id": "",
                            "sort": "1",
                            "recommend_product_is_show": "1",
                            "recommend_products": [],
                            "recommend_shop_is_show": "1",
                            "recommend_shop": [],
                            "category_selected_data": [],
                            "child_selected_data": [],
                            "slide_name": ""
                        },
                        //  公告
                        home_notice: {
                            "title": "",
                            "url": "",
                            "sort": ""
                        },
                        //  新手指南
                        home_guide: {
                            "is_show": "1",
                            "type": "2",
                            "menu_name": "",
                            "content": [
                                {
                                    "image": "",
                                    "name": "",
                                    "url": "",
                                    "sort": ""
                                }
                            ],
                            "article_cat_ids": "",
                            "article_cat_selected_data": [
                                {
                                    "label": "",
                                    "value": null
                                }
                            ]
                        },
                        //  侧边广告
                        home_site_ad: {
                            "image": "",
                            "url": "",
                            "start_time": "",
                            "end_time": "",
                            "time_type": "2",
                            "sort": "",
                            "is_show": '1',
                            "time_arr": []
                        },
                        //  右侧导航
                        right_nav: {
                            "icon": "",
                            "name": "",
                            "type": "",
                            "url": "",
                            "sort": "",
                            "is_show": 1
                        },
                        //  广告位
                        top_advertisement: {
                            "image": "",
                            "url": "",
                            "start_time": "",
                            "end_time": "",
                            "time_type": 2,
                            "sort": '',
                            "is_show": 1,
                            "time_arr": []
                        },
                        //  价格指数
                        price_index: {},
                        //  资讯文章
                        zixun_article: {},
                        //  频道广场
                        channel_square: {
                            image: "",
                            url: "",
                            sort: 1
                        },
                        //  列表样式1/2
                        list_style: {
                            "cat_id": '',
                            "cat_alias": "",
                            "sort": 10,
                            "attr_info": {
                                "fixed_start_attr_info": [],
                                cate_attr_info: [],
                                "fixed_end_attr_info": [],
                            },
                            "goods_ids": [],
                            "hot_goods_ids": [],
                            is_ziying: -1,
                            is_ziying_two: -1
                        },
                        //  图文样式1
                        image_text_style_one: {
                            "cat_id": "",
                            "cat_alias": "",
                            "sort": '',
                            "goods_ids": [],
                            is_ziying: -1,
                            is_ziying_two: -1
                        },
                        //  图文样式2
                        image_text_style_two: {
                            "keyword": "",
                            "type": 1,
                            "value": "",
                            "sort": '',
                            is_ziying: -1,
                            is_ziying_two: -1
                        },
                        //  优质店铺
                        good_shop: {
                            seller_id: '',
                            sort: '',
                            good_ids: '',
                        },
                        //  VR云工厂
                        vr_factory: {},
                        //  宣传广告
                        publicity_advertising: {
                            "name": "",
                            "title": "",
                            "desc": "",
                            "url": "",
                            "icon": "",
                            "bg_image": ""
                        },
                        //  多多资讯
                        dd_information: {},
                        //  为你推荐
                        recommend_info: {},
                        //  底部导航
                        bottom_nav: {
                            "icon": "",
                            "title": "",
                            "sort": "",
                            "items": [
                                {
                                    "title": "",
                                    "url": "",
                                    "sort": ""
                                }
                            ]
                        },
                        //  二维码
                        bottom_qrcode: {
                            "type": "",
                            "title": "",
                            "desc": "",
                            "sort": "",
                            "qrcode": ""
                        },
                        //  服务条款
                        bottom_service_terms: {
                            "title": "",
                            "url": "",
                            "sort": ""
                        },
                        //  备案信息
                        bottom_filing: {
                            "icon": "",
                            "title": "",
                            "url": "",
                            "sort": ""
                        },
                        //  版权信息
                        bottom_copyright: {
                            "title": "",
                            "url": "",
                            "sort": ""
                        },
                        //  认证信息
                        bottom_certification: {
                            "title": "",
                            "url": "",
                            "sort": ""
                        }
                    },
                    shop_color: '{{ $app_shop_color }}',
                    shop_tel: "{{ shop_config(\App\Models\ShopConfig::SERVICE_PHONE) ?? '' }}",
                    web_nav_name: '',
                    web_nav_alias: '',
                    setting_info: {},   //  设置弹窗数据
                    public_options_down: [],    //  公共下拉数据
                    mine_msg_error: null,
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
                            this.mine_msg_error && this.index_msg_error.close()
                            this.mine_msg_error = this.$message.error(res.message)
                        }
                        this.save_loading = false
                    })
                },
                /** 页面初始化设置 */
                initPageSet() {

                    console.log('dataTempalteForm', this.dataTempalteForm)
                    //  获取订单中心数据
                    this.dataTempalteOrder = this.dataTempalteForm.item_data.filter(item => item.component_name == 'order_center')
                    //  获取订单中心索引
                    this.dataTempalteOrderIndex = this.dataTempalteForm.item_data.findIndex(item => item.component_name == 'order_center')
                    //  获取资产中心数据
                    this.dataTempalteAsset = this.dataTempalteForm.item_data.filter(item => item.component_name == 'my_asset')
                    //  获取资产中心索引
                    this.dataTempalteAssetIndex = this.dataTempalteForm.item_data.findIndex(item => item.component_name == 'my_asset')
                    //  获取底部标签组件数据
                    this.dataTempalteLabel = this.dataTempalteForm.not_for_data.filter(item => item.component_name == 'label')
                    //  获取底部标签组件索引
                    this.dataTempalteLabelIndex = this.dataTempalteForm.not_for_data.findIndex(item => item.component_name == 'label')
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
                initPageData() {
                    //  编辑时，获取装修数据
                    this.item_data = this.dataTempalteForm.item_data

                    this.contentForm = JSON.parse(JSON.stringify(this.item_data))
                    let list = this.contentForm.filter(item => (item.is_nav == 1 && item.is_show == 1))
                    if (list.length > 15) {
                        this.contentForm.length && this.contentForm.map(item => {
                            item.is_nav = '0'
                        });
                        list = list.slice(0, 15)
                        list.forEach(item => {
                            this.contentForm.forEach(child => {
                                if (item.id == child.id) {
                                    child.is_nav = '1'
                                }
                            })
                        })
                    }
                    this.contentForm = this.contentForm.filter(item => (item.component_name != 'order_center' && item.component_name != 'my_asset' && item.component_name))
                    this.contentForm.length && this.contentForm.map(item => {
                        // item.content.alias_name = item.content.name
                        item.alias_name = item.name
                        item.tabIndex = 0
                        item.slideIndex = 0
                        item.zixunIndex = 0
                        /*if (item.type == 36) {
                            item.data.item_data.ordinary_trader.table_data.some(child => {
                                child.
                            })
                        }*/
                    })
                    // this.formatListStyleData();
                    this.$set(this, 'contentForm', this.contentForm)
                    this.newContentForm = JSON.parse(JSON.stringify(this.contentForm))
                    window.onload = () => {
                        if ($('body').height() < 1080) {
                            $('.template-group').css('height', '400px')
                        }
                    }
                    //  监听 Ctrl+S 保存
                    document.addEventListener('keydown', this.listenerKeydowm, false)
                    const preview = document.getElementById('tempDragPerview')
                    preview && preview.addEventListener('wheel', (event) => {
                        const colorPickers = document.querySelectorAll('.el-color-picker')
                        colorPickers.forEach(item => {
                            item.__vue__.hide()
                        })
                    }, false)
                    // this.initTemplateData()
                },
                /** 获取页面数据 */
                getPageData() {
                    this.doPost('{!! route('manage.app_web_decoration.decoration') !!}', {id: this.dataTempalteForm.web_decoration.id}).then(res => {
                        if (res.code == 200) {
                            this.$set(this.dataTempalteForm, 'fixed_data', res.data.fixed_data);
                            this.$set(this.dataTempalteForm, 'item_data', res.data.item_data);
                            this.initPageData()
                        } else {
                            this.mine_msg_error && this.index_msg_error.close()
                            this.mine_msg_error = this.$message.error(res.message)
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
                    console.log('this.dataTempalteForm.right_assembly', this.dataTempalteForm.right_assembly, field)
                    item = this.dataTempalteForm.right_assembly.find(value => value.component_name == dragitem.component_name)
                    item.content.name = item.name
                    //  生成唯一id，用来设置选中
                    // item.id = Math.round(new Date() / 1000) + 'add'
                    item.id = ''
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
                    if (diffname.length) {
                        copyItem.name = diffnumber.length != 0 ? `${item.name}(${diffnumber.length})` : copyItem.name
                    }
                    copyItem.alias_name = copyItem.name
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
                    //  启动监听拖拽上传
                    /*const has_basic_photo = this.contentForm.filter(item => {
                        if (item.act_type == 22 || (item.act_type >= 6 && item.act_type <= 10)) {
                            return item.basic_data.find(child => child.type == 3 || child.type == 5 || child.type == 6 || child.type == 7)
                        }
                    })
                    if (has_basic_photo.length) {
                        const [element] = this.$refs['basic_template']
                        element && element.handleAddListenerDropImage()
                    }*/
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
                /** 格式化瀑布流样式列表数据 */
                handleFormatListStyle() {

                },
                /*************************************************************** 设置相关 **************************************************************/
                /** 设置保存回调 */
                handleTemplateSettingSave(params) {
                    const {content, data, index} = params
                    if (data.component_name == 'order_center') {
                        this.dataTempalteOrder[0].content = content
                        this.dataTempalteOrder[0].data = data
                    } else if (data.component_name == 'my_asset') {
                        this.dataTempalteAsset[0].content = content
                        this.dataTempalteAsset[0].data = data
                    } else if (isNaN(index)) {
                        this.contentForm.map(item => {
                            if (item.component_name == index) {
                                item.content = content
                                item.data = data
                            }
                        })
                    } else {
                        this.$set(this.contentForm[index], 'content', content)
                        this.$set(this.contentForm[index], 'data', data)
                    }
                    console.log(this.contentForm)
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
                        let info = {
                            id: this.dataTempalteForm.web_decoration.id,
                            data: JSON.parse(JSON.stringify(this.contentForm))
                        }
                        info.data.unshift(this.dataTempalteAsset[0])
                        info.data.unshift(this.dataTempalteOrder[0])
                        if (!info.data.length) {
                            this.$confirm('页面未装修，请装修后再发布！', '', {
                                confirmButtonText: '确定',
                                showCancelButton: false,
                                type: 'warning'
                            }).then(() => {
                            }).catch(() => {
                            });
                            this.is_can_save = true
                            return false
                        }

                        let is_save = 'ctrl'
                        if (type == 'save') {
                            is_save = await this.isSureSave()
                        }
                        if (is_save == 'ctrl' || is_save == 'confirm') {
                            this.save_loading = true
                            this.doPost('{!! route('manage.app_web_decoration.decoration_store') !!}', info).then(res => {
                                if (res.code == 200) {
                                    if (is_save == 'ctrl') {
                                        this.is_can_save = true
                                        this.$message.success('保存成功！')
                                        this.getPageData()
                                    } else {
                                        //  跳转至<网站装修>页面
                                        parent.closeSelf('网站装修', '{!! route('manage.app_web_decoration.index') !!}');
                                    }
                                } else {
                                    this.mine_msg_error && this.index_msg_error.close()
                                    this.mine_msg_error = this.$message.error(res.message)
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
                this.handleFormatListStyle()
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
        /* 顶部导航 */
        .home-header,
        .home-header .home-header-content {
            height: 355px;
            background: #fff;
            position: relative;
        }

        .ucenter-header {
            background-image: url("https://cdn.toodudu.com/uploads/2023/10/26/mine_back.png");
            background-repeat: no-repeat;
            background-size: 100% auto;
            width: 100%;
            height: 100%;
            background-color: #F8F8F8;
        }

        .head-info-box {
            padding-top: 80px
        }

        .head-info-box .head-tx {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #fff;
            overflow: hidden;
            box-sizing: border-box;
            margin-top: 8px;
            margin-left: 30px;
        }

        .head-info-box .head-tx img {
            width: 100px;
            height: 100px;
        }

        .head-info-box .user-info {
            margin-left: 20px;
        }

        .head-info-box .user-info .info-name {
            color: #333333;
            font-weight: 600;
            font-size: 32px;
            margin-top: 10px;
        }

        .head-info-box .user-info .info-name span {
            color: #333333;
            font-weight: 600;
            font-size: 32px;
        }

        .head-info-box .user-info .info-set {
            width: 33px;
            height: 36px;
            margin-right: 40px;
            margin-top: 38px
        }

        .head-info-box .user-info .info-set > img {
            width: 100%;
            height: 100%;
        }

        .company-box {
            margin: 40px 20px 20px;
            height: 125px;
            background-image: url("https://cdn.toodudu.com/uploads/2023/10/26/company_back.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            padding: 0 20px 0 40px;
            box-sizing: border-box;
            align-items: center;
        }

        .company-box.no-company {
            background-image: url("https://cdn.toodudu.com/uploads/2023/10/20/company_no_cert.png");
            height: 120px;
        }

        .company-box.no-company > div:nth-child(1) {
            padding-top: 0;
        }

        .company-box > div:nth-child(1) {
            font-size: 28px;
            color: #5D3909;
            padding-top: 30px;
        }

        .company-box .cert-btn {
            width: 150px;
            height: 60px;
            background: linear-gradient(270deg, #FF7C41, #FF3D19);
            border-radius: 30px;
            color: #FFFFFF;
            font-size: 26px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .template-content-box {
            height: calc(100vh - 60px);
            background-color: #f2f2f2;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .mine-drag-perview {
            margin: 0 auto;
        }

        .mine-drag-perview .temp-item {
            padding: 0 20px;
        }

        .bottom-nav-box {
            width: 754px;
            height: 100px;
            position: fixed !important;
            bottom: -2px;
            left: 50%;
            margin-left: -380px;
            z-index: 1000;
        }

        .bottom-nav-box-fix {
            width: 750px;
            height: 100px;
            margin: 0 auto
        }
    </style>
@endsection
