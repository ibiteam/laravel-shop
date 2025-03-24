<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,viewport-fit=cover"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="referrer" content="never">
    <link rel="shortcut icon" href="{{ shop_config('wap_logo_color') }}">
    <title>{{ shop_config(\App\Models\ShopConfig::SHOP_NAME) }}--管理中心</title>
    <link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2023/05/30/app.css?t=1685520761">
    {{--new element css--}}
    <link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2024/04/29/element2-15-14.css">
    <link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2023/05/31/nprogress.css?t=1685520761">
    <link rel="stylesheet" href="https://cdn.toodudu.com/uploads/2023/05/30/element-extends.css?t=1685520761">
    <link rel="stylesheet" href="{{ asset('css/fonts/element-iconfont.css')}}">
    <style>
        html, body, .wrapper {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            font-size: 12px;
            display: flex;
        }

        .s-flex { display: -webkit-box; display: -moz-box; display: -webkit-flex; display: -moz-flex; display: -ms-flexbox; display: flex; }
        .flex-1 { -prefix-box-flex: 1; -webkit-box-flex: 1; -webkit-flex: 1; -moz-box-flex: 1; -ms-flex: 1; flex: 1; }
        .flex-dir { flex-direction: column; }
        .flex-wrap { flex-wrap: wrap; }
        .flex-no-wrap { flex-wrap: nowrap; }
        .jc-ct { justify-content: center; }
        .ai-ct { align-items: center; }
        .ai-bl { align-items: baseline; }
        .jc-bt { justify-content: space-between; }
        .jc-ad { justify-content: space-around; }
        .jc-fe { justify-content: flex-end; }
        .ai-fe { align-items: flex-end; }
        .ai-fs { align-items: flex-start; }
        .fw_b { font-weight: bold }
        .bs-bb{box-sizing:border-box;}
        .cursorp, .cursorp * { cursor: pointer; }
        .img-set { width: 100%; height: 100%; font-size: 0; display: flex; align-items: center; justify-content: center; position: relative; }
        .img-set img { max-width: 100%; max-height: 100%; }

        #a_sidebar_to:after, .navbar-custom-menu:after {
            content: "";
            display: block;
            clear: both
        }

        .clickbtn {
            display: none;
        }

        #app {
            width: 100%;
            height: 100%;
            background: #F5F6FA;
            overflow-y: auto;
        }

        #app::-webkit-scrollbar {
            display: none;
        }

        @media (max-width: 767px) {
            .limitfix {
                overflow: hidden;
            }

            #navbar_head_manage_1 {
                display: none;
            }
        }

        [v-cloak] {
            display: none;
        }

        .le {
            float: left;
        }

        .rg {
            float: right;
        }

        #content-header {
            padding: 10px;
        }

        .one-file-upload input[type=file] {
            display: none;
        }

        .color-success {
            color: #67c23a;
        }

        .color-danger {
            color: #f56c6c;
        }

        .redstart {
            content: '*';
            color: #f56c6c;
            margin-top: -38px;
            margin-left: -12px;
            float: left;
        }

        .limitfix {
            display: flex;
        }

        .temp {
            flex: 1 1;
            white-space: nowrap;
            overflow: hidden;
            height: 50px;
        }

        #app .box-success {
            border-top-color: #409EFF;
        }

        @font-face {
            font-family: 'iconfont';  /* Project id 2575177 */
            src: url('//at.alicdn.com/t/c/font_2575177_jmc2vxj1hz.eot?t=1699597398145'); /* IE9 */
            src: url('//at.alicdn.com/t/c/font_2575177_jmc2vxj1hz.eot?t=1699597398145#iefix') format('embedded-opentype'), /* IE6-IE8 */ url('//at.alicdn.com/t/c/font_2575177_jmc2vxj1hz.woff2?t=1699597398145') format('woff2'),
            url('//at.alicdn.com/t/c/font_2575177_jmc2vxj1hz.woff?t=1699597398145') format('woff'),
            url('//at.alicdn.com/t/c/font_2575177_jmc2vxj1hz.ttf?t=1699597398145') format('truetype'),
            url('//at.alicdn.com/t/c/font_2575177_jmc2vxj1hz.svg?t=1699597398145#iconfont') format('svg');
        }

        .app-container {
            display: flex;
            flex-direction: column;
            /*height: 100%;*/
            /*overflow-x: hidden;*/
            /*overflow-y: auto;*/
            padding: 20px;
            box-sizing: border-box;
            background: #fff;
            border-radius: 20px;
        }

        /* 适用于 Firefox 浏览器 */
        .app-container::-moz-scrollbar {
            width: 2px; /* 设置滚动条的宽度 */
        }

        .s-flex, .s_flex {
            display: flex;
        }

        .ai-ct, .ai_ct {
            align-items: center;
        }

        .jc_ct, .jc-ct {
            justify-content: center;
        }

        .jc_bt, .jc-bt {
            justify-content: space-between;
        }

        .MT20 {
            margin-top: 20px;
        }

        .el-table .el-table__cell {
            padding: 8px 0;
        }

        .el-form-item__label {
            margin-bottom: 0;
        }

        .search-form .el-form-item {
            margin-bottom: 10px;
        }

        .el-card__body, .el-main {
            padding: 10px 0;
        }

        #content-header {
            padding: 0;
            height: auto !important;
        }

        .el-date-editor.el-input, .el-date-editor.el-input__inner {
            width: 100%;
        }

        :root {
            --manage-color: {{ $app_shop_color }};
        }

        .common-pagination-small,
        .common-pagination-small .el-pagination__sizes,
        .common-pagination-small .el-pager { display: none !important; }
        .common-pagination-small .el-pagination__jump { position: relative; }
        .common-pagination-small .el-pagination__jump::before { content: '当前第'; white-space: nowrap; background-color: #ffffff; font-size: 14px; color: #606266; box-sizing: border-box; position: absolute; left: -12px; bottom: 0; cursor: not-allowed; }
        .common-pagination-small .el-pagination__jump .el-input { pointer-events: none; }
        .common-pagination-small .el-pagination__jump .el-input input { pointer-events: none; border: none; }
        @media screen and (max-width: 800px) {
            .common-pagination { display: none; }
            .common-pagination-small { display: block !important; }
        }
        /* 列表搜索 header 头部样式 开始 */
        .common-header-search { height: auto; }
        .common-header-search .form-title {justify-content: space-between;}
        .common-header-search .form-title .header-search-form { transition: max-height 0.3s;flex-wrap: wrap;display: -webkit-box;display: -moz-box;display: -webkit-flex;display: -moz-flex;display: -ms-flexbox;display: flex; }
        .common-header-search .form-title .header-search-form.active { max-height: 3000vh !important; }
        .common-header-search .form-title .common-header-form__more { display: none !important; width: 100px; height: 30px; margin: 10px auto; border-radius: 6px; background-color: #0C54A6; color: #ffffff; cursor: pointer;align-items: center;justify-content: center; }
        .common-header-search .form-title .common-header-form__more em { margin-left: 6px; transition: all 0.3s; font-size: 14px; }
        .common-header-search .form-title .common-header-form__more.active em { transform: rotate(180deg); }
        .common-header-search .form-title .header-search-form .el-form-item label {font-size: 13px;}
        .common-header-search .form-title .header-search-form .el-form-item .el-input,
        .common-header-search .form-title .header-search-form .el-form-item .el-select {width: 200px;}
        .common-header-search .form-title .header-search-form .el-form-item .el-range-editor {width: 250px;}
        .common-header-search .form-title .header-search-form .el-form-item .el-range-separator {position: absolute;left: 107px;top: 3px;}
        @media screen and (max-width: 640px) {
            .common-header-search.mobile-header .form-title .header-search-form { max-height: 260px; overflow: hidden; }
            .common-header-search .form-title .common-header-form__more { display: flex !important; line-height: 3;align-items: center;justify-content: center; }
            .common-header-search .form-title .header-search-form .el-form-item { margin-bottom: 10px; }
            .common-header-search .form-title .header-search-form .el-form-item.is-error { margin-bottom: 22px; }
            .common-header-search .form-title .header-search-form .el-button { padding: 8px 10px; }
            .common-header-search .form-title .header-search-form .el-form-item__content { line-height: normal; }
            .common-header-search .form-title .header-search-form .el-form-item__label,
            .common-header-search .form-title .header-search-form .el-input__icon,
            .common-header-search .form-title .header-search-form .el-input__inner { height: 32px; line-height: 32px; }
            .common-header-search .form-title .header-search-submit .el-form-item__content { margin-left: 0 !important; }
            .common-header-search .form-title .header-search-submit .el-button+.el-button { margin-left: 0; }
        }
        /* 列表搜索 header 头部样式 结束 */
    </style>
</head>
<body class="skin-red-light sidebar-mini fixed">
<div class="wrapper">
    <div id="app" v-cloak>
        <div class="app-container">
            @yield('content')
        </div>
    </div>
</div>
<!-- jQuery 3.7.1  -->
<script src="https://cdn-files.ibisaas.com/static/js/jquery-3.7.1.min.js"></script>

<script src="https://cdn.toodudu.com/uploads/2023/05/31/vue2.6.10.min.js?t=1685520761"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/30/axios.js?t=1685520761"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/30/nprogress.js?t=1685520761"></script>
<!-- 引入组件库 -->
<script src="https://cdn.toodudu.com/uploads/2023/05/30/element-2.4.js?t=1685520761"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/30/xe-utils.min.js?t=1685520761"></script>
<script src="https://cdn.toodudu.com/uploads/2023/05/30/element-extends.min.js?t=1685520761"></script>

<script src="https://cdn.toodudu.com/uploads/2023/05/30/watermark.js?t=1685520761"></script>
<script type="text/javascript">
    window.onload = function () {
        //搜索nav导航事件
        setTimeout(function () {
            var now = getNow();
            var user_name = null
            if (!window.parent.admin) {
                user_name = decodeURI(getCookie('user_name'))
            } else {
                user_name = window.parent.admin
            }
            watermark({"watermark_txt": user_name + "<br>" + now + "<br>" + "{{ shop_config(\App\Models\ShopConfig::BANK_ACCOUNT) }}"});
        }, 0)
    }

</script>
<script type="text/x-template" id="one-file-upload">
    <el-upload
            class="one-file-upload"
            action="{{route('manage.common.upload')}}"
            :on-success="uploadSuccess"
            :on-remove="handleRemove"
            :limit="1"
            ref="fileU"
            :on-exceed="handleExceed"
            :before-upload="beforeUpload"
    >
        <el-button size="small" type="primary">点击上传</el-button>
        <div slot="tip" class="el-upload__tip">@{{tip}}</div>
        <div v-if="url">
            <img :src="url" v-if="url" style="width: 60px;">
        </div>
        <div v-else-if="upload_url">
            <img :src="upload_url" v-if="upload_url" style="width: 70px;">
        </div>
        <div v-else>
        </div>
    </el-upload>
</script>
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
                    this.$message.warning(response.message);
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
                if (error.response) {
                    switch (error.response.status) {
                        case 401:
                            vm.$confirm('登录已过期', '提示', {
                                confirmButtonText: '新页面登录',
                                showCancelButton: false,
                                type: 'warning'
                            }).then(function () {
                                window.open("{{ route('manage.login.form') }}");
                            }).catch(function () {
                            });
                    }
                }
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
                if (error.response) {
                    switch (error.response.status) {
                        case 401:
                            vm.$confirm('登录已过期', '提示', {
                                confirmButtonText: '新页面登录',
                                showCancelButton: false,
                                type: 'warning'
                            }).then(function () {
                                window.open("{{ route('manage.login.form') }}");
                            }).catch(function () {
                            });
                    }
                }
                reject(error);
            }).then(function () {
                NProgress.done();
            });
        });
    }

    /**
     * 是否是手机号
     * @param value
     * @returns bool
     */
    Vue.prototype.isTelPhone = function (value) {
        let isPhone = /^1[3456789]\d{9}$/
        return isPhone.test(value);
    }
    Vue.directive("tablesize", {
        bind(el, binding, vnode) {
            // 创建 ResizeObserver 实例
            const resizeObserver = new ResizeObserver(entries => {
                for (let entry of entries) {
                    // 当元素尺寸变化时，调用指令传入的回调函数，并传递元素的新尺寸
                    setColumnWidth(el, vnode);
                }
            });

            // 观察当前元素
            resizeObserver.observe(el);

            // 将 ResizeObserver 实例存储在元素上，以便在 unbind 钩子中可以访问
            el._resizeObserver = resizeObserver;
        },
        unbind(el) {
            // 停止观察并清理 ResizeObserver 实例
            if (el._resizeObserver) {
                el._resizeObserver.disconnect();
                delete el._resizeObserver;
            }
        }
    });
    function setColumnWidth(table, vnode) {
        //设置完后调用el-table方法更新布局
        vnode.child.doLayout()
    }
</script>
@include('manage._select_date')
<!--时间组件部分-->
<div class="picker-time">
    <script type="text/x-template" id="my-picker">
        <div class="">
            <div class="block le">
                <el-date-picker
                        v-model="start_time"
                        :picker-options="pickerOptionsStart"
                        align="right"
                        :type="dateType"
                        placeholder="选择日期时间"
                        @focus="selectedPickerStart"
                        :value-format="formatType">
                </el-date-picker>
            </div>
            <span class="le" style="line-height: 38px; margin: 0 10px;">——</span>
            <div class="block le">
                <el-date-picker
                        v-model="end_time"
                        :picker-options="pickerOptionsEnd"
                        align="right"
                        :type="dateType"
                        placeholder="选择日期时间"
                        @focus="selectedPickerEnd"
                        :value-format="formatType">
                </el-date-picker>
            </div>
        </div>
    </script>
    <script>
        Vue.component('my-picker', {
            props: ['start_date', 'end_date', "date_type"],
            data: function () {
                return {
                    pickerOptionsStart: {},
                    pickerOptionsEnd: {},
                    start_time: '',
                    end_time: '',
                    dateType: "datetime",
                    formatType: "yyyy-MM-dd",
                    testtime: "['12:00:00']"
                }
            },
            template: '#my-picker',
            methods: {
                /**获取日期选择器选择的值**/
                selectedPickerStart: function () {
                    if (this.end_time) {
                        var newdate = new Date(this.end_time).getTime();
                        this.pickerOptionsStart = Object.assign({}, this.pickerOptionsStart, {
                            disabledDate(time) {
                                return time.getTime() >= newdate;
                            }
                        });
                        var info = {
                            start_date: this.start_time,
                            end_date: this.end_time,
                        }
                        this.$emit('input', info);
                    }
                },
                selectedPickerEnd: function () {
                    if (this.start_time) {
                        var newdate = new Date(this.start_time).getTime();
                        this.pickerOptionsEnd = Object.assign({}, this.pickerOptionsEnd, {
                            disabledDate(time) {
                                return time.getTime() <= newdate;
                            }
                        });
                        var info = {
                            start_date: this.start_time,
                            end_date: this.end_time,
                        }
                        this.$emit('input', info);
                    }
                },
                blurPickerSet: function () {
                    this.selectedPickerStart();
                    this.selectedPickerEnd();
                },
            },
            mounted: function () {
                this.start_time = this.start_date;
                this.end_time = this.end_date;
                this.dateType = this.date_type;
                if (this.dateType == "datetime") {
                    this.formatType = "yyyy-MM-dd HH:mm:ss";
                }
                this.blurPickerSet();
            }
        });
    </script>
</div>
@yield('js')
@yield('css')
@stack('header_script')
</body>

</html>
