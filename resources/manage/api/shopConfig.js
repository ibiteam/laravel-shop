import $http from '../utils/http'

export function shopConfigIndex(data) {
    return $http.doGet('manage/set/shop_config', data)
}

export function shopConfigUpdate(data) {
    return $http.doPost('manage/set/shop_config/update', data)
}
