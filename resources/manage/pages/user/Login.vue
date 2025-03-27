<template>
    <div class="login-body-wrap">
        <div class="bg-img">
            <div class="top_head">
<!--                @if($config['shop_logo'])-->
<!--                <div class="top_img"><img alt="{{$config['shop_name']}}" src="{{$config['shop_logo']}}" height="77"></div>-->
<!--                @else-->
<!--                <div class="top_img"><img alt="{{$config['shop_name']}}" src="/images/manage/login-logo-sitting.png" height="77"></div>-->
<!--                @endif-->
                <div class="top_icon">
                    <div class="top_icon_line"></div>
                </div>
                <div class="top_title">后台管理系统</div>
            </div>
            <div class="wrap clearfix manage-shop">
<!--                @if($config['shop_manage_login_image'])-->
<!--                <image src="{{$config['shop_manage_login_image']}}" style="width:420px;height: 560px;"/>-->
<!--                @else-->
<!--                <image src="/images/manage/login-left-bg-img.png" style="width:420px;height: 560px;"/>-->
<!--                @endif-->
                <div style="width:480px;">
                    <el-form ref="loginFormRef" :model="loginForm" @submit.prevent>
                        <div class="box login_title" style="width:300px;border-radius: 2px;-webkit-border-radius: 2px;margin: 0px auto;margin-bottom: 20px;">欢迎来到 {{$config['shop_name']}}</div>
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
                    </el-form>
                </div>
            </div>
<!--            <div class="login_footer">-->
<!--                @if($config['bank_account'] && $config['icp_number'])-->
<!--                <div class="login_footer_title">{{ $config['bank_account'] }} @ 版权所有 {{ $config['icp_number'] }}</div>-->
<!--                @endif-->
<!--                @if($config['shop_address'])-->
<!--                <div class="login_footer_title">{{ $config['shop_address'] }}</div>-->
<!--                @endif-->
<!--                @if($config['service_mobile'])-->
<!--                <div class="login_footer_title">联系电话 ：{{ $config['service_mobile'] }}</div>-->
<!--                @endif-->
<!--            </div>-->
        </div>
        <div class="bg-img-1"></div>
        <div class="bg-img-2"></div>
        <div class="bg-img-3"></div>
        <div class="bg-img-4"></div>
    </div>
</template>

<script setup>
import { ref, reactive, getCurrentInstance, watch } from 'vue';
const cns = getCurrentInstance().appContext.config.globalProperties
import { accountLogin } from '@/api/user.js';
import md5 from 'js-md5'

const activeType = ref('1');
const timerCode = ref(null);
const secondCode = ref(0);
const sendCodeText = ref('获取验证码');

const loginForm = reactive({
    account: '',
    password: '',
    phone: '',
    code:''
})

watch(loginForm.phone, (newValue, oldValue) => {
    sendCodeText.value = '获取验证码';
    secondCode.value = 0;
})

const validatePhone = (rule, value, callback) => {
    if (!value) {
        return callback(new Error('请输入手机号'));
    }
    if (!cns.$public.isTelPhone(value)){
        return callback(new Error('手机号码格式不正确'));
    }

    return callback();
}

const loginRules = reactive({
    account: [
        { required: true, message: '请输入用户名/登录手机', trigger: 'blur' }
    ],
    password: [
        { required: true, message: '请输入登录密码', trigger: 'blur' }
    ],
    phone: [
        { validator: validatePhone, trigger: 'blur' }
    ],
    code: [
        { required: true, message: '请输入短信验证码', trigger: 'blur'}
    ]
})

const loginFormRef = ref(null);

const submitForm = () => {
    loginFormRef.value.validate((valid) => {
        if (valid) {
            const { account, password } = loginForm;
            accountLogin({account,password:md5(password)}).then(res => {
                if(res.code == 200){
                    cns.$cookies.set('pc-token', res.data.token, res.data.expires_at)
                    cns.$message.success('登录成功')
                }else {
                    cns.$message.error(res.message)
                }
            })
        }
    })
}

</script>

<style scoped lang="scss">
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
