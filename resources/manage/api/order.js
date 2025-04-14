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
export function orderQueryExpress(data) {
    return $http.doGet('manage/order/info/express/query', data)
}

export function orderDeliveryIndex(data) {
    return $http.doGet('manage/order/delivery', data)
}

// 退款原因
export function applyRefundReasonIndex(data) {
    return $http.doGet('manage/order/apply_refund_reason', data)
}
export function applyRefundReasonStore(data) {
    return $http.doPost('manage/order/apply_refund_reason/store', data)
}
export function applyRefundReasonDestroy(data) {
    return $http.doPost('manage/order/apply_refund_reason/destroy', data)
}

// 退款申请
export function applyRefundIndex(data) {
    return $http.doGet('manage/order/apply_refund', data)
}
