import $http from '../utils/http'

export function getConfigAxios() {
    return $http.doGet('manage/home/config')
}

export function getHomeDashboardAxios() {
    return $http.doGet('manage/home/dashboard')
}

export function getCollectMenuAxios() {
    return $http.doGet('manage/home/collect_manage')
}

export function homeCollectMenuAxios(id) {
    return $http.doPost('manage/home/collect_menu',{id:id})
}

export function clearCacheAxios() {
    return $http.doGet('manage/home/clear_cache')
}
