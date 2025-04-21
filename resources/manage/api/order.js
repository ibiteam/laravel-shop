import $http from '../utils/http';
// 退款原因
export function applyRefundReasonIndex(data) {
    return $http.doGet('manage/order/apply_refund_reason', data);
}

export function applyRefundReasonStore(data) {
    return $http.doPost('manage/order/apply_refund_reason/store', data);
}

export function applyRefundReasonDestroy(data) {
    return $http.doPost('manage/order/apply_refund_reason/destroy', data);
}

// 退款申请
export function applyRefundIndex(data) {
    return $http.doGet('manage/order/apply_refund', data);
}

export function applyRefundDetail(data) {
    return $http.doGet('manage/order/apply_refund/detail', data);
}

export function applyRefundAgreeApply(data) {
    return $http.doPost('manage/order/apply_refund/agree_apply', data);  // 同意申请
}

export function applyRefundCloseApply(data) {
    return $http.doPost('manage/order/apply_refund/close_apply', data);  // 拒绝申请
}

export function applyRefundExecuteRefund(data) {
    return $http.doPost('manage/order/apply_refund/execute_refund', data);  // 执行退款
}

export function applyRefundRefuseRefund(data) {
    return $http.doPost('manage/order/apply_refund/refuse_refund', data);    // 拒绝退款
}

export function applyRefundConfirmReceipt(data) {
    return $http.doPost('manage/order/apply_refund/confirm_receipt', data);  // 确认收货
}

export function applyRefundQueryExpress(data) {
    return $http.doPost('manage/order/apply_refund/query_express', data);    // 查询快递
}
