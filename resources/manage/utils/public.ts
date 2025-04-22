import { ElMessage } from 'element-plus';
/** 手机号校验 **/
export const  isTelPhone = function(value) {
    const isPhone = /^(13|14|15|17|18|16|19)\d{9}$/
    return isPhone.test(value);
}

/** 密码类型校验 **/
function isPassword (value) {
    let status = 0
    const isPwdLen = value.length >= 16 || value.length < 6
    const isTrim = /^[^\s].*[^\s]$/
    const isLetterNumber = /^(?!\d{6,8}$)(?! )(?=.*[A-Za-z])[a-zA-Z0-9_]|[^a-zA-Z0-9-=+_., *]{6,16}$/
    const test = /[`~!@#$%^&*()<>?:"{}\/;'[\]]/im
    if (isPwdLen || !isTrim.test(value)) {
        status = 1
    } else if (!isLetterNumber.test(value) && (!isLetterNumber.test(value) || test.test(value))) {
        status = 2
    } else if (test.test(value)) {
        status = 3
    } else {
        status = 0
    }
    return status
}
export function copyText(text){ //复制到剪切栏
    if (navigator.clipboard) {
        // clipboard api 复制
        navigator.clipboard.writeText(text);
    } else {
        const textarea = document.createElement('textarea');
        document.body.appendChild(textarea);
        // 隐藏此输入框
        textarea.style.position = 'fixed';
        textarea.style.clip = 'rect(0 0 0 0)';
        textarea.style.top = '10px';
        // 赋值
        textarea.value = text;
        // 选中
        textarea.select();
        // 复制
        document.execCommand('copy', true);
        // 移除输入框
        document.body.removeChild(textarea);
    }
}

export function isEmail(value) {
    const email = /^([a-zA-Z\d])((\w|-)+\.?)+@([a-zA-Z\d]+\.)+[a-zA-Z]{2,6}$/
    return email.test(value);
}

/**
 * 获取加密的手机号
 * phone
 * 例如 13355558888 -> 133****8888
 **/

export function getPrivacyPhone(phone){
    return phone.substring(0, 3) + '****' + phone.substring(7);
}

const throttle = (fnc,delay) => {
    let last_timer:number|null = null
    return function (...args){
        const nowDate:number = Date.now()
        if(nowDate - last_timer > delay){
            fnc.apply(this,args)
            last_timer = nowDate
        }
    }
}

const debounce = (fnc,delay) => {
    let timer = null
    return function (...args){
        if(timer) clearTimeout(timer)
        timer = setTimeout(() => {
            fnc.apply(this,args)
        },delay)
    }
}

const acceptsImg = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']
const acceptsVideo = ['video/mp4']
const verifyFile = (file, type= 'img') => {
    if(type === 'video'){
        if(acceptsVideo.indexOf(file.type) === -1){
            ElMessage.error("仅支持mp4格式上传");
            return false;
        }
        const isLt100M = file.size / 1024 / 1024 <= 100;
        if (!isLt100M) {
            ElMessage.error("仅支持mp4格式上传，单个视频不得超过100M!");
            return false;
        }
        return true
    } else if(type === 'img'){
        if(acceptsImg.indexOf(file.type) === -1){
            ElMessage.error("仅支持.png .jpg .jpeg .gif格式上传");
            return false;
        }
        const isLt5M = file.size / 1024 / 1024 <= 5;
        if (!isLt5M) {
            ElMessage.error("支持 .png .jpg .jpeg .gif格式，单个图片不得超过5M!");
            return false;
        }
        return true
    }
    return true
}

export default {
    isTelPhone,
    isPassword,
    isEmail,
    getPrivacyPhone,
    throttle,
    debounce,
    verifyFile
}
