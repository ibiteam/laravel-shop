
/** 手机号校验 **/
function isTelPhone (value) {
    let isPhone = /^(13|14|15|17|18|16|19)\d{9}$/
    if (!isPhone.test(value)) {
        return false
    } else {
        return true
    }
}

/** 密码类型校验 **/
function isPassWord (value) {
    let status = 0
    let isPwdLen = value.length >= 16 || value.length < 6
    let isTrim = /^[^\s].*[^\s]$/
    let isLetterNumber = /^(?!\d{6,8}$)(?! )(?=.*[A-Za-z])[a-zA-Z0-9_]|[^a-zA-Z0-9-=+_., *]{6,16}$/
    let test = /[`~!@#$%^&*()<>?:"{}\/;'[\]]/im
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
        var textarea = document.createElement('textarea');
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
    let email = /^([a-zA-Z\d])((\w|-)+\.?)+@([a-zA-Z\d]+\.)+[a-zA-Z]{2,6}$/
    if (!email.test(value)) {
        return false
    } else {
        return true
    }
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
    let lasttimer:number|null = null
    return function (args){
        const nowDate:number = Date.now()
        if(nowDate - lasttimer > delay){
            fnc.apply(this,args)
            lasttimer = nowDate
        }
    }
}

const debounce = (fnc,delay) => {
    let timer = null
    return function (args){
        if(timer) clearTimeout(timer)
        timer = setTimeout(() => {
            fnc.apply(this,args)
        },delay)
    }
}

export default {
    isTelPhone,
    isPassWord,
    isEmail,
    getPrivacyPhone,
    throttle,
    debounce
}
