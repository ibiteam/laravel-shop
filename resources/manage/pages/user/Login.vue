<template>
    <div class="login-body-wrap">
        <div class="bg-img">
            <div class="top-head">
                <div class="top-img">
                    <img :alt="pageData.shop_name" :src="pageData.shop_logo || defaultImages.logo" height="77" >
                </div>
                <div class="top-icon">
                    <div class="top-icon-line"></div>
                </div>
                <div class="top-title">后台管理系统</div>
            </div>
            <div class="manage-shop">
                <img :src="pageData.shop_manage_login_image || defaultImages.bgImage" style="width:420px;height: 560px;"/>
                <div style="width:480px;">
                    <el-form ref="loginFormRef" :model="loginForm" :rules="loginRules" size="large">
                        <div class="login-title" style="width:300px;margin: 0px auto;margin-bottom: 36px;">欢迎来到 {{ pageData.shop_name}}</div>
                        <el-form-item label="" prop="username">
                            <el-input v-model="loginForm.username" placeholder="请输入用户名" maxlength="20"/>
                        </el-form-item>
                        <el-form-item label="" prop="password">
                            <el-input v-model="loginForm.password" placeholder="请输入密码" maxlength="20" :type="passwordVisible ? 'text' : 'password'">
                                <template #suffix>
                                    <i class="iconfont cu-p" :class="{'icon-yanjing_yincang': !passwordVisible, 'icon-yanjing_xianshi': passwordVisible}" @click="changePasswordShow"></i>
                                </template>
                            </el-input>
                        </el-form-item>
                        <div style="width:360px;margin: 0 auto 30px;padding-top: 15px;">
                            <el-button id="login-submit-button" type="primary" style="width: 100%; height: 48px;" @click="handleLogin" :loading="loading">登录</el-button>
                        </div>
                    </el-form>
                </div>
            </div>
            <div class="login-footer">
                <div class="login-footer-title" v-if="pageData.bank_account&&pageData.icp_number">{{ pageData.bank_account }} @ 版权所有 {{ pageData.icp_number }}</div>
                <div class="login-footer-title" v-if="pageData.shop_address">{{ pageData.shop_address }}</div>
                <div class="login-footer-title" v-if="pageData.service_mobile">{{ pageData.service_mobile }}</div>
            </div>
        </div>
        <div class="bg-img-1"></div>
        <div class="bg-img-2"></div>
        <div class="bg-img-3"></div>
        <div class="bg-img-4"></div>
    </div>
</template>

<script setup>
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
const cns = getCurrentInstance().appContext.config.globalProperties
import { accountLogin, getLoginInfo } from '@/api/user.js';
import { useRouter } from 'vue-router'
import _ from 'lodash'
// 导入图片资源
import logoImage from '@/assets/images/user/login-logo-sitting.png'
import leftBgImage from '@/assets/images/user/login-left-bg-img.png'

const loginForm = reactive({ username: '', password: '' });
const passwordVisible = ref(false);
const loading = ref(false);
const loginFormRef = ref(null);
const pageData = ref({})
const router = useRouter()
// 添加默认图片资源
const defaultImages = { logo: logoImage, bgImage: leftBgImage }

const validatePassword = (rule, value, callback) => {
    if (!value) {
        return callback(new Error('请输入登录密码'));
    }
    if (value.length < 6) {
        return callback(new Error('密码格式不正确'));
    }
    const passwordPattern = /^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z0-9\W_!@#$%^&*`~()-+=]+$)(?![a-z0-9]+$)(?![a-z\W_!@#$%^&*`~()-+=]+$)(?![0-9\W_!@#$%^&*`~()-+=]+$)[a-zA-Z0-9\W_!@#$%^&*`~()-+=]/;
    if (!passwordPattern.test(value)) {
        return callback(new Error('密码必须包含大写字母，小写字母，数字，特殊字符`@#$%^&*`~()-+=`中的任意三种。请联系管理员进行有修改。'))
    }else {
        return callback();
    }
}

const loginRules = reactive({
    username: [
        { required: true, message: '请输入用户名', trigger: 'blur' }
    ],
    password: [
        { required: true, message: '请输入登录密码', trigger: 'blur' },
        { validator: validatePassword, trigger: 'blur' }
    ]
})
const handleLogin = _.throttle(() => {
    loginFormRef.value.validate((valid) => {
        if (valid) {
            loading.value = true;
            submitLogin();
        }else {
        }
    })
},1000)

const changePasswordShow = () => {
    passwordVisible.value = !passwordVisible.value;
}

const submitLogin = () => {
    accountLogin({user_name:loginForm.username,password:loginForm.password}).then(res=>{
        loading.value = false;
        if (res.code == 200) {
            cns.$cookies.set('manage-token', res.data.token, res.data.expires_at)
            router.push({name:'home'})
        } else {
            cns.$message.error(res.message);
        }
    })
};

onMounted(() => {
    getLoginInfo().then(res => {
        if(res.code == 200){
            pageData.value = res.data?.config;
            if (res.data?.is_login) {
                router.push({name:'home'})
            }
        } else {
            cns.$message.error(res.message);
        }
    })
})
</script>

<style scoped lang="scss">
.login-body-wrap {
    width: 100vw;
    height: 100vh;
    background-image: url('@/assets/images/user/login-bg-img.png');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    display: flex;
    justify-content: center;
    .bg-img{
        padding-top: 10vh;
    }
}

.manage-shop {
    border-radius: 20px;
    width: 900px;
    background-color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 0 40px 0 rgba(120, 178, 238, 0.2000);
    :deep(.el-form-item){
        width: 360px;
        margin: 0 auto 24px;
        i{
            font-size: 20px;
        }
        .el-input__inner {height: 48px !important;line-height: 48px !important;}
        .el-input__icon {line-height: 48px !important;}
        .el-input__suffix {line-height: 48px !important;}
        .el-input__prefix {display: flex;align-items: center;height: 100%;}
    }
    :deep(.el-button span){
        font-size: 16px;
    }
}

.top-head {
    width: 900px;
    overflow: hidden;
    margin: 0 auto;
    padding: 40px 30px;
    display: flex;
    justify-content: center;
    justify-items: center;
}

.top-img {
    width: 185px;
    height: 68px;
}

.top-img img {
    max-width: 185px;
    max-height: 68px;
}

.top-icon {
    width: 80px;
    height: 77px;
    display: flex;
    justify-content: center;
    align-items: center;
    padding-left: 20px;
}

.top-icon-line {
    display: block;
    width: 2px;
    height: 14px;
    color: #84ACC7;
    border-right: solid #84ACC7 1px;
}

.top-title {
    height: 77px;
    font-size: 34px;
    color: #333333;
    font-weight: bold;
    line-height: 77px;
    text-align: center;
}

.login-title {
    font-size: 20px;
    font-weight: 400;
    text-align: center;
}

.login-footer {
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

.login-footer-title {
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
    background-image: url('@/assets/images/user/login-don1.png');
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
    background-image: url('@/assets/images/user/login-don2.png');
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
    background-image: url('@/assets/images/user/login-don3.png');
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
    background-image: url('@/assets/images/user/login-don4.png');
    background-repeat: no-repeat;
    -webkit-animation: mymove 5s linear infinite;
    animation: mymove 5s linear infinite;
}

</style>
