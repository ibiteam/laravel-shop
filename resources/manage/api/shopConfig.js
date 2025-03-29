import $http from '../utils/http'

export function siteInfo(data) {
    return $http.doGet('manage/set/shop_config/site_info', data)
}
export function siteLogo(data) {
    return $http.doGet('manage/set/shop_config/site_logo', data)
}

export function shopConfigUpdate(data) {
    return $http.doPost('manage/set/shop_config/update', data)
}
