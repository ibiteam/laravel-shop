import $http from '../utils/http'

export function getUserCoupon(data) {
    return $http.doGet('manage/marketing/coupon', data)
}
