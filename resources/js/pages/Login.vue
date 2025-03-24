<template>
    <div class="login-body-wrap">
        <div class="wrap header-wrap">
            <div class="logo">
                <a href="/" title="多多商城">
                    <img alt="" src="https://testcdn.ibisaas.com/2025/02/08/5bpXJOozRKBAO7rqv77jAmDX6id5Bo8C2YYkonyQ.png">
                </a>
            </div>
        </div>
        <div class="center-wrap">
            <div class="wrap clearfix" style="position: relative;z-index: 2">
                <div class="center-bg"></div>
                <div class="form-wrap">
                    <div class="common" id="common">
                        <div class="logo-text">欢迎来到多多商城！</div>
                        <el-tabs v-model="activeType" class="demo-tabs">
                            <el-tab-pane label="密码登录" name="1"></el-tab-pane>
                            <el-tab-pane label="短信登录" name="2"></el-tab-pane>
                        </el-tabs>
                        <el-form :model="loginForm" :rules="loginRules" label-width="auto" style="max-width: 600px" ref="loginFormRef" size="large">
                            <el-form-item label="" prop="account" v-if="activeType == '1'">
                                <el-input v-model="loginForm.account" placeholder="请输入用户名/登录手机" maxlength="20"/>
                            </el-form-item>
                            <el-form-item label="" prop="password" v-if="activeType == '1'">
                                <el-input v-model="loginForm.password" type="password" placeholder="请输入登录密码" maxlength="20"/>
                            </el-form-item>
                            <el-form-item label="" prop="phone" v-if="activeType == '2'">
                                <el-input v-model="loginForm.phone" placeholder="请输入手机号" maxlength="11"/>
                            </el-form-item>
                            <el-form-item label="" prop="code" v-if="activeType == '2'">
                                <el-input v-model="loginForm.code" placeholder="请输入短信验证码" maxlength="6">
                                    <template #append>
                                        <span class="code-btn" :class="{'code-btn-can-send':loginForm.phone}" @click="sendPhoneCode">{{ sendCodeText }}</span>
                                    </template>
                                </el-input>
                            </el-form-item>
                            <div class="s-flex jc-ct"> <el-button type="primary" @click="submitForm">登 录</el-button></div>
                        </el-form>
                    </div>
                    <div class="other-entry" id="other-entry">
                        <RouterLink :to="{name: 'forgetPassword'}">忘记密码</RouterLink>
                        <RouterLink :to="{name: 'register'}">免费注册</RouterLink>
                    </div>
                </div>
            </div>
        </div>
        <PublicFooter></PublicFooter>
    </div>
</template>

<script setup>
import { ref, reactive, getCurrentInstance, watch } from 'vue';
import PublicFooter from '@/components/PublicFooter.vue';
const cns = getCurrentInstance().appContext.config.globalProperties
import { accountLogin, checkPhone, sendCode, registerOrPhoneLogin} from '@/api/user';
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
            if(activeType.value == '1'){
                const { account, password } = loginForm;
                accountLogin({account,password:md5(password)}).then(res => {
                    if(res.code == 200){
                        cns.$message.success('登录成功')
                    }else {
                        cns.$message.error(res.message)
                    }
                })
            }else {
                const { phone, code } = loginForm;
                checkPhone(phone).then(res=>{
                    if (res.data.is_register) {
                        registerOrPhoneLogin({info:{phone,code}, action:'login'}).then(ret => {
                            if(ret.code == 200){
                                cns.$message.success('登录成功')
                            }else {
                                cns.$message.error(ret.message)
                            }
                        })
                    }
                })

            }
        }
    })
}

const sendPhoneCode = () => {
    if(secondCode.value>0) return
    loginFormRef.value.validateField('phone', (valid) => {
        if (valid) {
            checkPhone(loginForm.phone).then(res=>{
                if (res.data.is_register) {
                    let info = {
                        phone: registerForm.phone,
                        action: 'login'
                    }
                    sendCode(info).then(ret => {
                        if (ret.code == 200) {
                            secondCode.value = 60;
                            timerCode.value = setInterval( ()=> {
                                if (secondCode.value == 1) {
                                    sendCodeText.value = '重新获取'
                                    secondCode.value = 0
                                    clearInterval(timerCode.value)
                                    timerCode.value = null
                                } else {
                                    secondCode.value--
                                    sendCodeText.value =  secondCode.value + 's后重新获取'
                                }
                            }, 1000)
                            cns.$message.success('验证码发送成功')
                        }else {
                            cns.$message.info(ret.message)
                        }
                    })
                }else {
                    cns.$message.error('该手机号未注册')
                }
            })

        }
    })
}
</script>

<style scoped lang="scss">
.login-body-wrap {
    width: 100%;
    min-width: 1440px;
    height: 100%;
    min-height: 100vh;
    background: url('https://cdn.toodudu.com/2024/05/24/ZOIWKKG4TKVDs3cDXqhXLNlW3atMO7JiJZ1M6Jfe.png') center center no-repeat;
    background-size: cover;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    overflow-x: hidden;
    :deep(.el-tabs__nav-wrap:after){
        display: none;
    }
    :deep(.el-tabs__nav){
        width: 100%;
        justify-content: center;
        .el-tabs__active-bar{
            height: 3px;
            transform: scaleX(0.6);
        }
        .el-tabs__item{
            font-weight: bold;
        }
    }
    :deep(.el-form){
        padding-top: 15px;
        padding-bottom: 20px;
        .el-input__wrapper{
            border: none;
            box-shadow: none;
            background: var(--page-bg-color);
        }
        .el-button--primary{
            width: 100%;
        }
        .el-input-group__append, .el-input-group__prepend{
            background: var(--page-bg-color);
            box-shadow: none;
        }
        .code-btn{
            color: var(--color-text-desc);
            background: none;
            cursor: not-allowed;
        }
        .code-btn-can-send{
            color: var(--color-text);
            cursor: pointer;
        }
    }
}

.wrap {
    width: 1440px;
    overflow: hidden;
    margin: 0 auto;
    height: 130px;
}

.header-wrap {
    height: auto !important;
}

.logo {
    padding: 50px 0px 0;
    font-size: 0;
    float: left;
}

.logo a img {
    height: 70px;
    width: auto;
    font-size: 0;
}

.center-wrap .wrap {
    height: auto;
    min-height: 850px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.center-wrap .wrap .center-bg {
    width: 1000px;
    height: 700px;
    background: url('https://cdn.toodudu.com/2024/05/30/U8AyZzebD3rqrN1QvQFMWXNJoSMO2uWQrYsb0Wpd.png') center no-repeat;
    background-size: cover;
}

.center-wrap .wrap .form-wrap {
    width: 400px;
    height: 450px;
    background: #fff;
    border-radius: 10px;
    position: relative;
    box-shadow: 0px 4px 20px 0px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-sizing: border-box;
    margin-right: 20px;
}

.common {
    padding: 48px 55px 0;
    position: relative;
}

.common .logo-text {
    color: #333;
    font-size: 20px;
    font-weight: bolder;
    text-align: center;
    margin-bottom: 8px;
}
.other-entry {
    display: flex;
    justify-content: center;
}

.other-entry a {
    padding: 0 16px;
    color: #767676;
    font-size: 14px;
    line-height: 20px;
    cursor: pointer;
    text-align: center;
    position: relative;
}

.other-entry a:not(:last-child)::before {
    content: '';
    display: block;
    width: 0;
    height: 10px;
    border-left: 1px solid #D8D8D8;
    position: absolute;
    right: 0;
    top: 5px;
}

.other-entry a {
    text-decoration: none;
}

.other-entry a:hover {
    color: var(--main-color);
}

@keyframes toa {
    0% {
        opacity: 0;

    }
    15% {
        opacity: 1;
        top: 80px;
    }
    70% {
        opacity: 1;
        top: 80px;
    }
    80% {
        opacity: 0;
        top: 80px;
    }
    100% {
        opacity: 0;
        top: -40px;
    }

}

@media screen and (max-width: 1440px) and (min-width: 1024px) {
    .login-body-wrap {
        min-width: 100%;
    }

    .wrap {
        width: 100vw;
    }

    .center-wrap .wrap .center-bg {
        width: calc(100% - 480px);
    }
}

@media screen and (max-width: 1024px) {
    .wrap {
        width: fit-content;
    }

    .login-body-wrap {
        min-width: 100%;
    }

    .center-wrap .wrap {
        justify-content: center;
    }

    .center-wrap .wrap .form-wrap {
        margin-right: 0;
    }
}
</style>
