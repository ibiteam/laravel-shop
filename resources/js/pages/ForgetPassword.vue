<template>
    <div>
        <PublicHeader title="密码找回">
            <template #right>
                <p>没有账号？ <router-link to="/register">去注册<i class="iconfont icon-xiangyoujiantou2" style="font-size: 14px;"></i></router-link></p>
            </template>
        </PublicHeader>
        <div class="register-container public-inner">
            <div class="register-form">
                <el-form :model="passwordForm" :rules="passwordRules" style="max-width: 600px" ref="passwordFormRef" size="large" label-width="300px">
                    <el-form-item label="手机号" prop="phone">
                        <el-input v-model="passwordForm.phone"  placeholder="请输入手机号" maxlength="11"/>
                    </el-form-item>
                    <el-form-item label="短信验证码" prop="code">
                        <el-input v-model="passwordForm.code" placeholder="请输入短信验证码" maxlength="6">
                            <template #append>
                                <span class="code-btn" :class="{'code-btn-can-send':passwordForm.phone}" @click="sendPhoneCode">{{ sendCodeText }}</span>
                            </template>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="设置密码" prop="password">
                        <el-input v-model="passwordForm.password" type="password" placeholder="建议使用两种及以上组合" maxlength="20"/>
                    </el-form-item>
                    <el-form-item label="确认密码" prop="password_confirmation">
                        <el-input v-model="passwordForm.password_confirmation" type="password" placeholder="请再次输入密码" maxlength="20"/>
                    </el-form-item>
                    <div class="s-flex jc-ct" style="padding: 50px 0;"> <el-button type="primary" @click="submitForm">注册</el-button></div>
                </el-form>
            </div>
        </div>
        <PublicFooter></PublicFooter>
    </div>
</template>

<script setup>
import { ref, getCurrentInstance, reactive, watch } from 'vue';
import PublicHeader from '@/components/PublicHeader.vue';
import PublicFooter from '@/components/PublicFooter.vue';
const cns = getCurrentInstance().appContext.config.globalProperties
import { useRouter } from 'vue-router';
const router = useRouter();

import { checkPhone, sendCode, updatePassword } from '@/api/user';
import md5 from 'js-md5'

const timerCode = ref(null);
const secondCode = ref(0);
const sendCodeText = ref('获取验证码');

const passwordForm = reactive({
    new_password: '',
    new_password_confirmation: '',
    phone: '',
    code:''
})

watch(passwordForm.phone, (newValue, oldValue) => {
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

const passwordRules = reactive({
    password: [
        { required: true, message: '请输入登录密码', trigger: 'blur' }
    ],
    password_confirmation: [
        { required: true, message: '请输入确认密码', trigger: 'blur' }
    ],
    phone: [
        { validator: validatePhone, trigger: 'blur' }
    ],
    code: [
        { required: true, message: '请输入短信验证码', trigger: 'blur'}
    ]
})

const passwordFormRef = ref(null);

const submitForm = () => {
    passwordFormRef.value.validate((valid) => {
        if (valid) {
            const { phone } = passwordForm;
            checkPhone(phone).then(res=>{
                if (res.data.is_register) {
                    let formData = {
                        password: md5(passwordForm.password),
                        password_confirmation: md5(passwordForm.password_confirmation),
                        phone: passwordForm.phone,
                        code: passwordForm.code
                    }
                    updatePassword({info:formData, action:'password-forget'}).then(ret => {
                        if(ret.code == 200){
                            cns.$message.success('操作成功')
                            router.push({name: 'login'})
                        }else {
                            cns.$message.error(ret.message)
                        }
                    })
                }else {
                    cns.$dialog.confirm({title: '提示', content: '该手机号不存在,是否去注册？', confirmButtonText: '去注册', cancelButtonText: '取消'}).then(() => {
                        router.push({name: 'register'})
                    }).catch(() => {})
                }
            })

        }
    })
}

const sendPhoneCode = () => {
    if(secondCode.value>0) return
    passwordFormRef.value.validateField('phone', (valid) => {
        if (valid) {
            checkPhone(passwordForm.phone).then(res=>{
                if (res.data.is_register) {
                    let info = {
                        phone: passwordForm.phone,
                        action: 'password-forget'
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
                    cns.$dialog.confirm({title: '提示', content: '该手机号不存在,是否去注册？', confirmButtonText: '去注册', cancelButtonText: '取消'}).then(() => {
                        router.push({name: 'register'})
                    }).catch(() => {})
                }
            })

        }
    })
}
</script>

<style scoped lang="scss">
.register-container{
    min-height: 400px;
    width: 440px;
    padding-top: 50px;
    .el-form-item{
        display: block;
    }
    :deep(.el-form-item__label){
        justify-content: flex-start;
    }
    :deep(.el-form-item--large .el-form-item__label){
        line-height: 32px;
        height: 32px;
    }
    :deep( .el-form-item.is-required>.el-form-item__label:before){
        display: none;
    }
    :deep(.el-input-group__append){
        padding: 0 0;
        span{
            padding: 0 20px;
        }
    }
    :deep(.el-button--primary){
        width: 60%;
        border-radius: 0;
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
</style>
