import $http from '../utils/http'

export function orderIndex(data) {
    return $http.doGet('manage/order/info/index', data)
}

export function orderDetail(data) {
    return $http.doGet('manage/order/info/detail', data)
}

export function orderShipEdit(data) {
    return $http.doGet('manage/order/info/ship/edit', data)
}

export function orderShipUpdate(data) {
    return $http.doPost('manage/order/info/ship/update', data)
}

export function orderAddressEdit(data) {
    return $http.doGet('manage/order/info/address/edit', data)
}

export function orderAddressUpdate(data) {
    return $http.doPost('manage/order/info/address/update', data)
}
