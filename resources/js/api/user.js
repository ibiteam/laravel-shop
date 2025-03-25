import $http from '../utils/http'

export function accountLogin(data) {
    return $http.doPost('home/auth/login-by-password', data)
}

export function checkUsername(account) {
    return $http.doGet('home/auth/check-name', {account})
}
export function checkPhone(phone) {
    return $http.doGet('home/auth/check-phone', {phone})
}

export function registerOrPhoneLogin({info, action, is_register=0}) {
    const URL = action == 'register' ? is_register == 0 ? 'home/auth/login-register-by-phone' : 'home/auth/register' : 'home/auth/login-by-phone'
    return $http.doPost(URL, info)
}

export function sendCode(info) {
    if(info.action == 'password-edit') delete info.phone
    return $http.doPost('home/sms-action', info)
}

export function updatePassword(info, action) {
    const URL = action == 'password-forget' ? 'home/auth/forget-password' : 'home/auth/edit-password'
    if(action == 'password-edit') delete info.phone
    return $http.doPost(URL, info)
}

export function checkLogin() {
    return $http.doGet('home/auth/check-login')
}
