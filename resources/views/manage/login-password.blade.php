<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF8">
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$config['shop_name']}}-后台管理中心登录</title>
    <meta name="description" content="{{$config['shop_keywords']}}">
    <meta name="keywords" content="{{$config['shop_description']}}">
    <link rel="shortcut icon" href="{{$config['shop_icon']}}">
    <link rel="stylesheet" href="/css/element-plus-index.css">
    <script src="/js/jquery-3.7.1.min.js"></script>
    <script src="/js/vue-3.js"></script>
    <script src="/js/element-plus-index.full.js"></script>
    <script src="/js/element-plus-icons-vue.js"></script>
</head>
<body class="page-index">
<style>
    * {-webkit-box-sizing: content-box;-moz-box-sizing: content-box;box-sizing: content-box;}
    html {width: 100%;height: 100%;}
    [v-cloak] {display: none;}
    a {background: transparent;text-decoration: none;color: #333;}
    img {border: 0;vertical-align: middle;-ms-interpolation-mode: bicubic}

    body {
        background-image: url('/images/manage/login-bg-img.png');
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }
    .box {
        padding: 0 5px;
    }

    .bg-img {
        margin: 100px auto;
    }

    .wrap {
        width: 2410px;
        overflow: hidden;
        margin: 0 auto;
    }

    .manage-shop {
        border-radius: 20px;
        width: 900px;
        background-color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0px 0px 40px 0px rgba(120, 178, 238, 0.2000);
    }

    .top_head {
        width: 900px;
        overflow: hidden;
        margin: 0 auto;
        padding: 40px 30px;
        display: flex;
        justify-content: center;
        justify-items: center;
    }

    .top_img {
        width: 185px;
        height: 68px;
    }

    .top_img img {
        max-width: 185px;
        max-height: 68px;
    }

    .top_icon {
        width: 80px;
        height: 77px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding-left: 20px;
    }

    .top_icon_line {
        display: block;
        width: 2px;
        height: 14px;
        color: #84ACC7;
        border-right: solid #84ACC7 1px;
    }

    .top_title {
        height: 77px;
        font-size: 34px;
        color: #333333;
        font-weight: bold;
        line-height: 77px;
        text-align: center;
    }

    .login_title {
        font-size: 20px;
        font-weight: 400;
        text-align: center;
    }

    .other_login {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;
    }

    .other_line {
        width: 90px;
        height: 0px;
        border-bottom: 2px solid #E8E8E8;
    }

    .other_title {
        margin: 0 20px;
        color: #777777;
    }

    .login_footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #597DA3;
        font-size: 12px;
    }

    .login_footer_title {
        text-align: center;
        padding: 0 40px
    }

    .ding_talk {
        width: 60px;
        height: 60px;
        background: #0177F6;
        border-radius: 14px;
        text-align: center;
        line-height: 60px;
    }

    .work_wechat {
        width: 60px;
        height: 60px;
        background: #ffffff;
        border-radius: 14px;
        text-align: center;
        line-height: 60px;
        border: 1px solid #EFEFEF;
    }

    @-webkit-keyframes bounce-down {
        25% {-webkit-transform: translateY(-20px);}
        50%, 100% {-webkit-transform: translateY(0);}
        75% {-webkit-transform: translateY(20px);}
    }

    @keyframes bounce-down {
        25% {transform: translateY(-20px);}
        50%, 100% {transform: translateY(0);}
        75% {transform: translateX(20px);}
        90% {transform: translateY(20px);}
    }

    @keyframes mymove {
        25% {transform: translateY(-20px);}
        50%, 100% {transform: translateY(0);}
        75% {transform: translateX(20px);}
        90% {transform: translateY(20px);}
    }

    .bg-img-1 {
        width: 111px;
        height: 96px;
        position: absolute;
        right: 126px;
        bottom: 145px;
        background-image: url('/images/manage/login-don1.png');
        background-repeat: no-repeat;
        -webkit-animation: bounce-down 3s linear infinite;
        animation: bounce-down 3s linear infinite;
    }

    .bg-img-2 {
        width: 63px;
        height: 82px;
        position: absolute;
        top: 300px;
        left: 148px;
        background-image: url('/images/manage/login-don2.png');
        background-repeat: no-repeat;
        -webkit-animation: bounce-down 4s linear infinite;
        animation: bounce-down 4s linear infinite;
    }

    .bg-img-3 {
        width: 109px;
        height: 163px;
        position: absolute;
        top: 28px;
        right: 315px;
        background-image: url('/images/manage/login-don3.png');
        background-repeat: no-repeat;
        -webkit-animation: mymove 5s linear infinite;
        animation: mymove 5s linear infinite;
    }

    .bg-img-4 {
        width: 102px;
        height: 114px;
        position: absolute;
        left: 273px;
        bottom: 451px;
        background-image: url('/images/manage/login-don4.png');
        background-repeat: no-repeat;
        -webkit-animation: mymove 5s linear infinite;
        animation: mymove 5s linear infinite;
    }

    .el-input__inner {height: 48px !important;line-height: 48px !important;}
    .el-input__icon {line-height: 48px !important;}
    .el-input__suffix {line-height: 48px !important;}
    .el-input__prefix {display: flex;align-items: center;height: 100%;}
</style>

<div id="app" class="login_index" v-cloak>
    <div class="bg-img">
        <div class="top_head">
            @if($config['shop_logo'])
                <div class="top_img"><img alt="{{$config['shop_name']}}" src="{{$config['shop_logo']}}" height="77"></div>
            @else
                <div class="top_img"><img alt="{{$config['shop_name']}}" src="/images/manage/login-logo-sitting.png" height="77"></div>
            @endif
            <div class="top_icon">
                <div class="top_icon_line"></div>
            </div>
            <div class="top_title">后台管理系统</div>
        </div>
        <div class="wrap clearfix manage-shop">
            @if($config['shop_manage_login_image'])
                <image src="{{$config['shop_manage_login_image']}}" style="width:420px;height: 560px;"/>
            @else
                <image src="/images/manage/login-left-bg-img.png" style="width:420px;height: 560px;"/>
            @endif
            <div style="width:480px;">
                <el-form ref="loginFormRef" :model="loginForm" @submit.prevent>
                    @if(shop_config(\App\Models\ShopConfig::CAN_PASSWORD_LOGIN, false))
                        <div class="box login_title" style="width:300px;border-radius: 2px;-webkit-border-radius: 2px;margin: 0px auto;margin-bottom: 20px;">欢迎来到{{$config['shop_name']}}</div>
                        <div class="box" style="width:360px;border-radius: 2px;-webkit-border-radius: 2px;margin: 0px auto;margin-bottom: 20px;">
                            <el-input v-model="loginForm.username" placeholder="用户名" :prefix-icon="User" style="width: 358px;"></el-input>
                        </div>
                        <div class="box" style="width:360px;border-radius: 2px;-webkit-border-radius: 2px;margin: 0px auto;margin-bottom: 20px;">
                            <el-input v-model="loginForm.password" placeholder="密码" :prefix-icon="Lock" :type="passwordVisible ? 'text' : 'password'" style="width: 358px;">
                                <template #suffix>
                                    <el-icon
                                        class="cursor-pointer"
                                        @mousedown="passwordVisible = true"
                                        @mouseup="passwordVisible = false"
                                        @mouseleave="passwordVisible = false">
                                        <component :is="passwordVisible ? 'View' : 'Hide'" />
                                    </el-icon>
                                </template>
                            </el-input>
                        </div>
                        <div class="box" style="width:360px;margin: 0px auto 30px;">
                            <el-button id="login-submit-button"
                                type="primary"
                                style="width: 100%; height: 48px;"
                                @click="handleLogin"
                                :loading="loading">登录</el-button>
                            <input type="hidden" value="" class="validate">
                            @csrf
                        </div>
                        @if(!empty($other_login_methods))
                            <div class="other_login">
                                <div class="other_line"></div>
                                <div class="other_title">其他方式登录</div>
                                <div class="other_line"></div>
                            </div>
                        @endif
                    @endif
                    <div style="display: flex;justify-content: space-around;padding:0 65px;">
                        @foreach($other_login_methods as $other_login_method)
                            <a class="box" href="{{ $other_login_method['redirect'] }}" style="text-align: center;font-size: 12px;color: #333;">
                                <div class="{{$other_login_method['class']}}"><img src="{{ $other_login_method['icon'] }}" alt=""></div>
                                <p>{{ $other_login_method['name'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </el-form>
            </div>
        </div>
        <div class="login_footer">
            @if($config['bank_account'] && $config['icp_number'])
                <div class="login_footer_title">{{ $config['bank_account'] }} @ 版权所有 {{ $config['icp_number'] }}</div>
            @endif
            @if($config['shop_address'])
                <div class="login_footer_title">{{ $config['shop_address'] }}</div>
            @endif
            @if($config['service_mobile'])
                <div class="login_footer_title">联系电话 ：{{ $config['service_mobile'] }}</div>
            @endif
        </div>
    </div>
    <div class="bg-img-1"></div>
    <div class="bg-img-2"></div>
    <div class="bg-img-3"></div>
    <div class="bg-img-4"></div>
</div>

<script src="{{ url('/js/axios.js') }}"></script>
<script src="https://cstaticdun.126.net/load.min.js?t={$time}"></script>
<script src="/js/jsencrypt.min.js"></script>
<script>
    const { createApp, ref, onMounted } = Vue;
    const { ElMessage } = ElementPlus;

    const { User, Lock, View, Hide } = ElementPlusIconsVue;

    const app = createApp({
        setup() {
            const loginForm = ref({
                username: '',
                password: ''
            });
            const passwordVisible = ref(false);
            const loading = ref(false);
            const captchaIns = ref(null);
            const validateData = ref('');
            const net_east_is_check = ref(@json(config('custom.net_east_yi_dun.enable')));
            const loginFormRef = ref(null);

            const rsaEncrypt = (val) => {
                let tmp_public_key = @json(shop_config(\App\Models\ShopConfig::MANAGE_LOGIN_RSA_PUBLIC_KEY, ''));
                if (!tmp_public_key) {
                    return val
                }
                let rsaEncrypt = new JSEncrypt();
                rsaEncrypt.setPublicKey(tmp_public_key);
                return rsaEncrypt.encrypt(val);
            };

            const initCaptcha = () => {
                initNECaptcha({
                    captchaId: '{{ config('custom.net_east_yi_dun.slider_captcha') }}',
                    element: '#login-submit-button',
                    mode: 'popup',
                    onReady: (instance) => {},
                    onVerify: (err, data) => {
                        if (err) return;
                        validateData.value = data.validate;
                        submitLogin(validateData.value);
                    }
                }, (instance) => {
                    captchaIns.value = instance;
                }, (err) => {
                    ElMessage.error('验证码初始化失败，请重试');
                });
            };

            const handleLogin = () => {
                const username = loginForm.value.username;
                const password = loginForm.value.password;

                if (!username || username.length < 2) {
                    ElMessage.error('用户名不正确');
                    return;
                }

                if (password.length < 6) {
                    ElMessage.error('密码格式不正确');
                    return;
                }

                const passwordPattern = /^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z0-9\W_!@#$%^&*`~()-+=]+$)(?![a-z0-9]+$)(?![a-z\W_!@#$%^&*`~()-+=]+$)(?![0-9\W_!@#$%^&*`~()-+=]+$)[a-zA-Z0-9\W_!@#$%^&*`~()-+=]/;
                if (!passwordPattern.test(password)) {
                    ElMessage.error('密码必须包含大写字母，小写字母，数字，特殊字符`@#$%^&*`~()-+=`中的任意三种。请联系管理员进行有修改。');
                    return;
                }

                loading.value = true;

                if (net_east_is_check.value == 1) {
                    captchaIns.value && captchaIns.value.popUp();
                } else {
                    submitLogin();
                }
            };

            const submitLogin = async (validateData = '') => {
                try {
                    const encryptedPassword = rsaEncrypt(loginForm.value.password);
                    const response = await axios.post('{{route('manage.login.password')}}', {
                        'user_name': loginForm.value.username,
                        'password': encryptedPassword,
                        'validate': validateData
                    });

                    loading.value = false;
                    const res = response.data;

                    if (res.code == 200) {
                        window.location.href = '{{route('manage.home')}}';
                    } else {
                        captchaIns.value && captchaIns.value.refresh();
                        ElMessage.error(res.message);
                    }
                } catch (error) {
                    loading.value = false;
                    captchaIns.value && captchaIns.value.refresh();
                    ElMessage.error('登录失败，请重试');
                }
            };

            onMounted(() => {
                if (net_east_is_check.value == 1) {
                    initCaptcha();
                }
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            });

            return {
                loginForm,
                passwordVisible,
                loading,
                handleLogin,
                loginFormRef,
                User,
                Lock,
                View,
                Hide
            };
        }
    });
    // 注册 Element Plus
    app.use(ElementPlus);

    // 注册必要的图标组件
    app.component('User', User);
    app.component('Lock', Lock);
    app.component('View', View);
    app.component('Hide', Hide);
    app.mount('#app');
</script>
</body>
</html>
