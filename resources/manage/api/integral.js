import $http from '../utils/http'

export function getUserIntegral(data) {
    return $http.doGet('manage/user/integral', data)
}

export function getIntegralDetail(data) {
    return $http.doGet('manage/user/integral/detail', data)
}
