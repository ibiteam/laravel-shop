import $http from '../utils/http'

export function categoryIndex(data) {
    return $http.doGet('manage/goods/category', data)
}
export function categoryUpdate(data) {
    return $http.doPost('manage/goods/category/update', data)
}

export function categoryEdit(data) {
    return $http.doGet('manage/goods/category/edit', data)
}

export function categoryDestroy(data) {
    return $http.doPost('manage/goods/category/destroy', data)
}

export function goodsIndex(data) {
    return $http.doGet('manage/goods/info', data)
}


export function goodsChangeStatus(data) {
    return $http.doPost('manage/goods/info/change/status', data)
}
