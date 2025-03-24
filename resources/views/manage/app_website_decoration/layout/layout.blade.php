<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,viewport-fit=cover"/>
    <link rel="shortcut icon" href="{{ shop_config('wap_logo_color') }}">
    <title>{{ shop_config(\App\Models\ShopConfig::SHOP_NAME) }}--管理中心</title>
    <!-- 引入样式 -->
    <link rel="stylesheet" href="{{ asset('css/fonts/element-iconfont.css')}}">
    <!--<link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2023/05/30/app.css?t=1685520761">-->
    <link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2023/05/31/nprogress.css?t=1685520761">
    <link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2023/05/30/draggableResizable.css?key=1.0"/>
    <link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2023/07/19/reset.css?key=1.1"/>
    <link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2023/06/25/element-2.15.13.css?key=1.0"/>
    <link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2023/05/30/swiper.css"/>
    <!--页面基础样式-->
    @include('manage.app_website_decoration.style.home-template-style')
    @include('manage.app_website_decoration.style.dialog-setting')
    <style>
        @font-face {
            font-family: 'iconfont';  /* Project id 2575177 */
            src: url('//at.alicdn.com/t/c/font_2575177_n87zlf775xk.eot?t=1712649951651'); /* IE9 */
            src: url('//at.alicdn.com/t/c/font_2575177_n87zlf775xk.eot?t=1712649951651#iefix') format('embedded-opentype'), /* IE6-IE8 */ url('//at.alicdn.com/t/c/font_2575177_n87zlf775xk.woff2?t=1712649951651') format('woff2'),
            url('//at.alicdn.com/t/c/font_2575177_n87zlf775xk.woff?t=1712649951651') format('woff'),
            url('//at.alicdn.com/t/c/font_2575177_n87zlf775xk.ttf?t=1712649951651') format('truetype'),
            url('//at.alicdn.com/t/c/font_2575177_n87zlf775xk.svg?t=1712649951651#iconfont') format('svg');
        }

        [v-cloak] {
            display: none;
        }

        /*首页每个模块公共头部设置*/
        .publix-box {
            box-shadow: 0 0 12px 0 rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            background-color: #ffffff;
            overflow: hidden;
        }

        .public-title {
            display: flex;
            height: 92px;
            line-height: 92px;
            padding: 0 20px;
            margin: 0 auto;
            background-color: #ffffff;
            position: relative;
        }

        .public-title .public_text,
        .public-title .public-text {
            width: 500px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            font-weight: bold;
            font-size: 30px;
            color: #010101;
        }

        .public-title .public-text span {
            font-size: 22px;
            color: #999999;
        }

        .public-title img {
            width: 100%;
            height: 100%;
        }

        .public-title .look_more,
        .public-title .look-more {
            display: table;
            height: 100%;
            line-height: 32px;
            text-align: right;
            font-size: 24px;
            color: #666666;
            position: absolute;
            top: 0;
            right: 20px;
        }

        .public-title .look_more .look_more_view,
        .public-title .look-more .look-more-view {
            line-height: 96px; /*padding-right: 30px;*/ /*background: url("https://cdn.toodudu.com/uploads/2023/10/20/home-enter-other.png") no-repeat center right;*/
            background-size: 24px;
            color: #666666;
        }

        .public-title .look_more .look_more_view em,
        .public-title .look-more .look-more-view em {
            font-size: 20px;
        }

        .public-title .look_more .look_more_text,
        .public-title .look-more .look-more-text {
            display: block;
            width: 26px;
            height: 26px;
            margin: 34px 0;
            background: url("https://cdn.toodudu.com/uploads/2023/10/20/sy_jt.png") center no-repeat;
            background-size: 100% 100%;
            position: absolute;
            right: 0;
            top: 2px;
        }

        .public-box {
            margin-top: 10px;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
        }

        .public-title-bgimage.public-title {
            position: relative;
        }

        .public-title-bgimage.public-title img {
            width: 100%;
            height: 123px;
            position: absolute;
            top: 0;
            left: 0;
        }

        .public-title-bgimage.public-title .look_more_view {
            top: -6px;
        }


        /*删除后会影响弹窗图片预览样式优先级*/
        .el-popover {
            z-index: 2004 !important;
        }
    </style>
    @yield('css')
</head>

<body>
<div class="template-editor" v-load="save_loading" element-loading-text="保存中..." id="app" class="app" v-cloak>
    <!--Header-->
    <div class="template-header s-flex ai-ct jc-bt" v-if="dataTempalteForm">
        <div class="template-header-left s-flex ai-ct">
            <div class="template-header-logo s-flex ai-ct">
                <img :src="dataTempalteForm.shop_logo" alt="" style="height: 30px;">
                <p>移动端页面装修</p>
            </div>
            <div class="template-header-warning">您正在装修【@{{dataTempalteForm.web_decoration.name}}】页面</div>
        </div>
        <div class="template-header-right s-flex">
            <div class="template-header-btn" @click="handleExitAllTemplate">退出装修</div>
            <div class="template-header-btn primary" @click="handleSaveAllTemplate('save')">保存并发布</div>
            <div class="template-save-warning s-flex ai-ct jc-ct" v-if="is_show_warn">
                <p>按键Ctrl/Command+S，实时保存/发布，数据不丢失</p>
                <em class="iconfont cursorp" @click="is_show_warn = false">&#xe643;</em>
            </div>
        </div>
    </div>
    <!--Components-->
    <div class="template-content-source" v-if="dataTempalteForm && dataTempalteForm.left_assembly">
        <div class="template-source-switch" @click="handleClickCheckSourceSwitch">
            <p>@{{ is_check_source ? '收起' : '展开' }}</p>
            <em class="iconfont">@{{ is_check_source ? '&#xe71a;' : '&#xe71b;' }}</em>
        </div>
        <div class="template-content-check s-flex" v-if="is_check_source">
            <div class="check-item" :class="{ active: tabIndex == index }" v-for="(item, index) in ['页面设计']"
                 :key="index" @click="tabIndex = index">@{{item}}
            </div>
        </div>
        <div class="template-content-box backg-color-white" style="background-color: #ffffff; overflow-y:hidden;"
             v-if="is_check_source">
            <template>
                <div class="template-content-warning s-flex ai-ct jc-ct" v-if="tabIndex != 2">
                    <em class="iconfont">&#xe715;</em>
                    <p>@{{ tabIndex == 0? '鼠标单击可以拖动组件至页面中任意位置哦~' :
                        '鼠标单击拖动素材至页面中，可直接装扮页面哦~'}}</p>
                </div>
                <div class="template-group" v-load="is_load_source">
                    <!--产品组件/数据组件/广告组件-->
                    <div class="template-group-icon">
                        <dl v-if="dataTempalteForm.left_assembly.advertisement_component && dataTempalteForm.left_assembly.advertisement_component.length">
                            <dt>
                                <h1>广告组件</h1>
                            </dt>
                            <dd>
                                <vuedraggable
                                        v-model="dataTempalteForm.left_assembly.advertisement_component"
                                        tag="div"
                                        class="template-group-list s-flex flex-wrap jc-bt"
                                        animation="1000"
                                        :options="{ group: { name: 'name', pull: 'clone', put: false }, sort: false}"
                                        filter=".disabled"
                                        :clone="(dragitem) => handleTempDragClone(dragitem, 'advertisement_component')"
                                        @start="handleDragTempStart"
                                        @end="handleDragTempEnd"
                                        :move="handleDragTempMove">
                                    <div class="template-icon-list"
                                         v-for="(item, index) in dataTempalteForm.left_assembly.advertisement_component"
                                         :index="index"
                                         :class="{ disabled: computedTempIsExist(index, item.component_name, item.limit, 'advertisement_component') == index }">
                                        <template>
                                            <div class="s-flex ai-ct"><em class="iconfont" v-html="item.icon">
                                                </em>
                                                <p>@{{item.name}}</p>
                                            </div>
                                        </template>
                                    </div>
                                </vuedraggable>
                            </dd>
                        </dl>
                        <dl v-if="dataTempalteForm.left_assembly.data_component && dataTempalteForm.left_assembly.data_component.length">
                            <dt>
                                <h1>数据组件</h1>
                            </dt>
                            <dd>
                                <vuedraggable
                                        v-model="dataTempalteForm.left_assembly.data_component"
                                        tag="div"
                                        class="template-group-list s-flex flex-wrap jc-bt"
                                        animation="1000"
                                        :options="{ group: { name: 'name', pull: 'clone', put: false }, sort: false}"
                                        filter=".disabled"
                                        :clone="(dragitem) => handleTempDragClone(dragitem, 'data_component')"
                                        @start="handleDragTempStart"
                                        @end="handleDragTempEnd"
                                        :move="handleDragTempMove">
                                    <div class="template-icon-list"
                                         v-for="(item, index) in dataTempalteForm.left_assembly.data_component"
                                         :index="index"
                                         :class="{ disabled: computedTempIsExist(index, item.component_name, item.limit, 'data_component') == index }">
                                        <template>
                                            <div class="s-flex ai-ct"><em class="iconfont" v-html="item.icon">
                                                </em>
                                                <p>@{{item.name}}</p>
                                            </div>
                                        </template>
                                    </div>
                                </vuedraggable>
                            </dd>
                        </dl>
                    </div>
                </div>
            </template>
        </div>
        <div class="template-content-text" v-else>页面组件</div>
    </div>
    <!--Content-->
    <div class="template-content-box">
        @yield('content')
    </div>
</div>
</body>

<!-- jQuery 2.2.3 -->
<script src="https://cdn-files.ibisaas.com/static/js/jquery-3.7.1.min.js"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/31/vue2.6.10.min.js?t=1685520761"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/30/axios.js?t=1685520761"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/30/nprogress.js?t=1685520761"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/30/Sortable.min.js"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/30/vuedraggable.js"></script>
<script src="https://at.alicdn.com/t/c/font_2575177_hm5ka3eztx7.js"></script>
<script src="https://cdn.toodudu.com/uploads/2023/06/21/element-2.15.13.js"></script>
<script src="https://cdn.toodudu.com/uploads/2023/06/27/echarts2.0.js"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/30/swiper.js"></script>
<script>
    //服务端请求配置
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    Vue.component('one-file-upload', {
        props: ['tip', 'upload_url', 'type', 'size', 'size_type', 'callback', 'img_width', 'img_hight'],
        data: function () {
            return {
                url: "",
            }
        },
        template: '#one-file-upload',
        methods: {
            uploadSuccess: function (response) {
                if (response.code == 200) {
                    this.url = response.data.file;
                    this.$emit('input', this.url);
                    if (this.callback) {
                        this.callback();
                    }
                } else {
                    alert(response.msg);
                }

            },
            handleExceed(files, fileList) {
                this.$message.warning(`请先将上传的图片移除后再上传`);
            },
            handleRemove(file) {
                if (file.status === "success") {
                    this.url = '';
                    this.$emit('input', this.url);
                }
            },
            beforeUpload(file) {
                this.size = parseInt(this.size)
                if (this.type && this.type == 'image') {
                    var isImg = true;
                    if (file.type !== 'image/jpeg' && file.type !== 'image/jpg' && file.type !== 'image/png' && file.type !== 'image/gif' && file.type !== 'image/bmp' && file.type !== 'image/x-icon' && file.type != "application/pdf") {
                        isImg = false;
                        this.$message.error('上传文件不符合图片格式！');
                        return false;
                    }
                    var sizeFlag = false
                    if (this.size) {
                        this.size_type = this.size_type ? this.size_type : 'M';  //size_type 无值则默认为M
                        if (this.size_type == 'kb') {
                            sizeFlag = file.size / 1024 < this.size;
                        } else {
                            sizeFlag = file.size / 1024 / 1024 < this.size;
                        }
                        if (!sizeFlag) {
                            this.$message.error('图片大小不能超过' + this.size + this.size_type);
                            return false;
                        }
                    } else {
                        sizeFlag = true
                    }

                    // 验证图片比例
                    let isScale = new Promise((resolve, reject) => {
                        const img = new Image();
                        // 加载src
                        const _URL = window.URL || window.webkitURL;
                        img.src = _URL.createObjectURL(file);
                        img.onload = () => {
                            const width = img.width;
                            const height = img.height;
                            if (this.img_width && this.img_hight) {
                                if (width / height != this.img_width / this.img_hight) {
                                    this.$message.error('图片比例应为：' + this.img_width + ': ' + this.img_hight + '，上传图片比例为：' + width + '：' + height);
                                    reject()
                                } else {
                                    resolve()
                                }
                            } else {
                                resolve()
                            }
                        }
                    }).then(() => {
                        return file
                    }, () => {
                        return Promise.reject()
                    })

                    return isImg && sizeFlag && isScale;
                }
            },
            clearU: function () {
                this.$refs['fileU'].clearFiles()
                this.url = '';
                this.upload_url = '';
            }
        }

    });
    /**
     * get请求
     * @param url
     * @param params
     * @returns {Promise<any>}
     */
    Vue.prototype.doGet = function (url, params = {}) {
        return new Promise((resolve, reject) => {
            NProgress.start();
            axios.get(url, {
                params: params
            }).then((res) => {
                if (res.status == 200) {
                    if (res.data.code == 422) {
                        /*var message='';
                        for ( params in res.data.message){
                            for (let err in res.data.message[params]){
                                message+=res.data.message[params][err]+'<br>';
                            }
                        }
                        vm.$notify.error({
                            title: '参数验证错误',
                            dangerouslyUseHTMLString: true,
                            message: message
                        });*/

                        var message = ''
                        var params
                        for (params in res.data.message) {
                            for (const err in res.data.message[params]) {
                                (+err != 'NaN') && (message += res.data.data[params][err])
                            }
                        }
                        vm.$notify.error({
                            title: '参数验证错误',
                            dangerouslyUseHTMLString: true,
                            message: message
                        })
                    } else if (res.data.code == 403) {
                        vm.$message.error(res.data.message);
                        reject(res.data);
                        return;
                    } else if (res.data.code == 302) {
                        vm.$confirm(res.data.message, '提示', {
                            confirmButtonText: '确定',
                            showCancelButton: false,
                            type: 'warning'
                        }).then(function () {
                            window.open("{{shop_config('host')}}");
                        }).catch(function () {
                        });
                    }
                    resolve(res.data);
                } else {
                    vm.$message.error('请求出错了');
                }

            }).catch(function (error) {
                reject(error);
            }).then(function () {
                NProgress.done();
            });
        });
    }
    /**
     * post请求
     * @param url
     * @param params
     * @returns {Promise<any>}
     */
    Vue.prototype.doPost = function (url, params = {}) {
        return new Promise((resolve, reject) => {
            NProgress.start();
            axios.post(url, params).then((res) => {
                if (res.status == 200) {
                    if (res.data.code == 422) {
                        /*var message='';
                        for ( params in res.data.message){
                            for (let err in res.data.message[params]){
                                message+=res.data.message[params][err]+'<br>';
                            }
                        }
                        vm.$notify.error({
                            title: '参数验证错误',
                            dangerouslyUseHTMLString: true,
                            message: message
                        });*/
                        var message = ''
                        var params
                        for (params in res.data.message) {
                            for (const err in res.data.message[params]) {
                                (+err != 'NaN') && (message += res.data.message[params][err])
                            }
                        }
                        vm.$notify.error({
                            title: '参数验证错误',
                            dangerouslyUseHTMLString: true,
                            message: message
                        })
                    } else if (res.data.code == 403) {
                        vm.$message.error(res.data.message);
                        reject(res.data);
                        return;
                    } else if (res.data.code == 302) {
                        vm.$confirm(res.data.message, '提示', {
                            confirmButtonText: '确定',
                            showCancelButton: false,
                            type: 'warning'
                        }).then(function () {
                            window.open("{{shop_config('host')}}");
                        }).catch(function () {
                        });
                    }
                    resolve(res.data);
                } else {
                    vm.$message.error('请求出错了');
                }


            }).catch(function (error) {
                reject(error);
            }).then(function () {
                NProgress.done();
            });
        });
    }
    /**
     * 清除表单校验
     */
    Vue.prototype.clearFormValidate = function (formName, parent = this) {
        if (parent.$refs[formName].length) {
            parent.$refs[formName][0].clearValidate()
        } else {
            parent.$refs[formName].clearValidate()
        }
    }

    /**
     * 是否是手机号
     * @param value
     * @returns bool
     */
    Vue.prototype.isTelPhone = function (value) {
        let isPhone = /^1[3456789]\d{9}$/
        if (!isPhone.test(value)) {
            return false
        } else {
            return true
        }
    }

</script>
@yield('js')

</body>

</html>
