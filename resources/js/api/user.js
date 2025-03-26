import $http from '../utils/http'

export function accountLogin(data) {
    return $http.doPost('pc/auth/login-by-password', data)
}

export function checkUsername(account) {
    return $http.doGet('pc/auth/check-name', {account})
}
export function checkPhone(phone) {
    return $http.doGet('pc/auth/check-phone', {phone})
}

export function registerOrPhoneLogin({info, action, is_register=0}) {
    const URL = action == 'register' ? is_register == 0 ? 'pc/auth/login-register-by-phone' : 'pc/auth/register' : 'pc/auth/login-by-phone'
    return $http.doPost(URL, info)
}

export function sendCode(info) {
    if(info.action == 'password-edit') delete info.phone
    return $http.doPost('pc/sms-action', info)
}

export function updatePassword(info, action) {
    const URL = action == 'password-forget' ? 'pc/auth/forget-password' : 'pc/auth/edit-password'
    if(action == 'password-edit') delete info.phone
    return $http.doPost(URL, info)
}

export function checkLogin() {
    return $http.doGet('pc/auth/check-login')
}
