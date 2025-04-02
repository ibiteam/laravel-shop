import $http from '../utils/http'

export function appDecoration(data) {
    return $http.doGet('manage/app_decoration', data)
}
