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

export function brandIndex(data) {
    return $http.doGet('manage/goods/brand', data)
}

export function brandEdit(data) {
    return $http.doGet('manage/goods/brand/edit', data)
}

export function brandUpdate(data) {
    return $http.doPost('manage/goods/brand/update', data)
}

export function brandDestroy(data) {
    return $http.doPost('manage/goods/brand/destroy', data)
}
