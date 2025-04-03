import $http from '../utils/http'

export function getMenuAxios() {
    return $http.doGet('manage/home/menus')
}

export function getHomeDashboardAxios() {
    return $http.doGet('manage/home/dashboard')
}

export function homeCollectMenuAxios(id) {
    return $http.doPost('manage/home/collect_menu',{id:id})
}

export function clearCacheAxios() {
    return $http.doGet('manage/home/clear_cache')
}
