import $http from '../utils/http'

export function appDecoration(data) {
    return $http.doGet('manage/set/app_decoration', data)
}
