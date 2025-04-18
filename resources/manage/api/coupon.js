import $http from '../utils/http'

export function getCoupon(data) {
    return $http.doGet('manage/marketing/coupon', data)
}

export function getUserCoupon(data) {
    return $http.doGet('manage/marketing/coupon/user', data)
}
