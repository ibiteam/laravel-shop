import $http from '../utils/http'

export function orderIndex(data) {
    return $http.doGet('manage/order/info/index', data)
}
