import $http from '../utils/http'

export function accountLogin(data) {
    return $http.doPost('manage/login', data)
}

export function getLoginInfo() {
    return $http.doGet('manage/login')
}

export function accountLogout() {
    return $http.doGet('manage/logout')
}

export function getUserIndex(data) {
    return $http.doGet('manage/user/index', data)
}

export function userUpdate(data) {
    return $http.doPost('manage/user/update', data)
}

export function getUserAddress(data) {
    return $http.doGet('manage/user/address', data)
}
export function addressUpdate(data) {
    return $http.doPost('manage/user/address/update', data)
}

export function getAreasData(data) {
    return $http.doPost('v1/region', data)
}

// 微信服务号
export function wechatUserIndex(data) {
    return $http.doGet('manage/user/wechat', data)
}
