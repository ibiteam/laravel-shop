import $http from '../utils/http'

export function accountLogin(data) {
    return $http.doPost('manage/login', data)
}

export function getLoginInfo() {
    return $http.doGet('manage/login')
}
