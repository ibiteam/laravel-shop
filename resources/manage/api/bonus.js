import $http from '../utils/http'

export function getBonus(data) {
    return $http.doGet('manage/marketing/bonus', data)
}

export function getUserBonus(data) {
    return $http.doGet('manage/marketing/bonus/user', data)
}
