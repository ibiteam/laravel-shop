import $http from '../utils/http'

// 支付方式
export function paymentMethodIndex(data) {
    return $http.doGet('manage/set/payment', data)
}
export function paymentMethodChangeField(data) {
    return $http.doPost('manage/set/payment/change/field', data)
}
export function paymentMethodUpdate(data) {
    return $http.doPost('manage/set/payment/update', data)
}
// 交易流水表
export function transactionIndex(data) {
    return $http.doGet('manage/set/payment/transaction',data)
}
export function transactionRefund(data) {
    return $http.doPost('manage/set/payment/transaction/refund',data)
}
// 快递公司
export function shipCompanyIndex(data) {
    return $http.doGet('manage/set/ship_company', data)
}
export function shipCompanyEdit(data) {
    return $http.doGet('manage/set/ship_company/edit', data)
}
export function shipCompanyUpdate(data) {
    return $http.doPost('manage/set/ship_company/update', data)
}
export function shipCompanyChangeStatus(data) {
    return $http.doPost('manage/set/ship_company/change_status', data)
}
