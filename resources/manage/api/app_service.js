import $http from '../utils/http'

export function getAppService(data) {
    return $http.doGet('manage/set/app_service', data)
}

export function getAppServiceUpdate(data) {
    return $http.doPost('manage/set/app_service/update', data)
}

export function getAppServiceToggleStatus(data) {
    return $http.doPost('manage/set/app_service/toggle/status', data)
}

export function getAppServiceLog(data) {
    return $http.doGet('manage/set/app_service_log', data)
}
