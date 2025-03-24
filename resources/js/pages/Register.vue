<template>
    <div>
        <PublicHeader title="注册账户">
            <template #right>
                <p>已有账号？ <router-link to="/login">请登录<i class="iconfont icon-xiangyoujiantou2" style="font-size: 14px;"></i></router-link></p>
            </template>
        </PublicHeader>
        <div class="register-container public-inner">
            <div class="register-form">
                <el-form :model="registerForm" :rules="registerRules" style="max-width: 600px" ref="registerFormRef" size="large" label-width="300px">
                    <el-form-item label="用户名" prop="account">
                        <el-input v-model="registerForm.account" placeholder="请输入用户名" maxlength="20"/>
                    </el-form-item>
                    <el-form-item label="设置密码" prop="password">
                        <el-input v-model="registerForm.password" type="password" placeholder="建议使用两种及以上组合" maxlength="20"/>
                    </el-form-item>
                    <el-form-item label="确认密码" prop="password_confirmation">
                        <el-input v-model="registerForm.password_confirmation" type="password" placeholder="请再次输入密码" maxlength="20"/>
                    </el-form-item>
                    <el-form-item label="手机号" prop="phone">
                        <el-input v-model="registerForm.phone"  placeholder="请输入手机号" maxlength="11"/>
                    </el-form-item>
                    <el-form-item label="短信验证码" prop="code">
                        <el-input v-model="registerForm.code" placeholder="请输入短信验证码" maxlength="6">
                            <template #append>
                                <span class="code-btn" :class="{'code-btn-can-send':registerForm.phone}" @click="sendPhoneCode">{{ sendCodeText }}</span>
                            </template>
                        </el-input>
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

    import { checkPhone, sendCode, registerOrPhoneLogin} from '@/api/user';
    import md5 from 'js-md5'

    const timerCode = ref(null);
    const secondCode = ref(0);
    const sendCodeText = ref('获取验证码');

    const registerForm = reactive({
        account: '',
        password: '',
        password_confirmation: '',
        phone: '',
        code:''
    })

    watch(registerForm.phone, (newValue, oldValue) => {
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

    const registerRules = reactive({
        account: [
            { required: true, message: '请输入用户名/登录手机', trigger: 'blur' }
        ],
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

    const registerFormRef = ref(null);

    const submitForm = () => {
        registerFormRef.value.validate((valid) => {
            if (valid) {
                const { phone } = registerForm;
                checkPhone(phone).then(res=>{
                    if (!res.data.is_register) {
                        let formData = {
                            account: registerForm.account,
                            password: md5(registerForm.password),
                            password_confirmation: md5(registerForm.password_confirmation),
                            phone: registerForm.phone,
                            code: registerForm.code
                        }
                        registerOrPhoneLogin({ info: formData, action:'register', is_register:0}).then(ret => {
                            if(ret.code == 200){
                                cns.$message.success('注册成功')
                            }else {
                                cns.$message.error(ret.message)
                            }
                        })
                    }else {
                        cns.$dialog.confirm({title: '提示', content: '该手机号已存在,是否去直接登录？', confirmButtonText: '去登录', cancelButtonText: '取消'}).then(() => {
                            router.push({name: 'login'})
                        }).catch(() => {})
                    }
                })

            }
        })
    }

    const sendPhoneCode = () => {
        if(secondCode.value>0) return
        registerFormRef.value.validateField('phone', (valid) => {
            if (valid) {
                checkPhone(registerForm.phone).then(res=>{
                    if (!res.data.is_register) {
                        let info = {
                            phone: registerForm.phone,
                            action: 'register'
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
                        cns.$dialog.confirm({title: '提示', content: '该手机号已存在,是否去直接登录？', confirmButtonText: '去登录', cancelButtonText: '取消'}).then(() => {
                            router.push({name: 'login'})
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
