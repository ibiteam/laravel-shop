import $http from '../utils/http'

export function linkTreeList() {
    return $http.doGet('manage/set/router_category/tree')
}

export function linkTableData(data) {
    return $http.doGet(data.url, data)
}
