import $http from '../utils/http'

export function getUserBonus(data) {
    return $http.doGet('manage/marketing/bonus', data)
}
