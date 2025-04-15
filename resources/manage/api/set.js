import $http from '../utils/http'

// 商店设置
export function shopConfigIndex(data) {
    return $http.doGet('manage/set/shop_config', data)
}
export function shopConfigUpdate(data) {
    return $http.doPost('manage/set/shop_config/update', data)
}

// 访问地址分类
export function routerCategoryIndex(data) {
    return $http.doGet('manage/set/router_category', data)
}
export function routerCategoryInfo(data) {
    return $http.doGet('manage/set/router_category/info', data)
}
export function routerCategoryStore(data) {
    return $http.doPost('manage/set/router_category/store', data)
}
export function routerCategoryDestroy(data) {
    return $http.doPost('manage/set/router_category/destroy', data)
}
export function routerCategoryChangeShow(data) {
    return $http.doPost('manage/set/router_category/change_show', data)
}
export function routerCategoryGetPages(data) {
    return $http.doGet('manage/set/router_category/pages', data)
}

// 访问地址
export function routerIndex(data) {
    return $http.doGet('manage/set/router', data)
}
export function routerCategories(data) {
    return $http.doGet('manage/set/router/categories', data)
}
export function routerStore(data) {
    return $http.doPost('manage/set/router/store', data)
}
export function routerChangeShow(data) {
    return $http.doPost('manage/set/router/change_show', data)
}

// 管理员列表
export function adminUserIndex(data) {
    return $http.doGet('manage/set/admin_user', data)
}
export function adminUserRoles(data) {
    return $http.doGet('manage/set/admin_user/roles', data)
}
export function adminUserStore(data) {
    return $http.doPost('manage/set/admin_user/store', data)
}
export function adminUserChangeStatus(data) {
    return $http.doPost('manage/set/admin_user/change_status', data)
}

// 权限菜单
export function permissionIndex(data) {
    return $http.doGet('manage/set/permission', data)
}
export function permissionStore(data) {
    return $http.doPost('manage/set/permission/store', data)
}

// 角色管理
export function roleIndex(data) {
    return $http.doGet('manage/set/role', data)
}
export function roleInfo(data) {
    return $http.doGet('manage/set/role/info', data)
}
export function roleStore(data) {
    return $http.doPost('manage/set/role/store', data)
}
export function roleChangeShow(data) {
    return $http.doPost('manage/set/role/change_show', data)
}
export function roleDestroy(data) {
    return $http.doPost('manage/set/role/destroy', data)
}

// 管理员日志
export function adminOperationLogIndex(data) {
    return $http.doGet('manage/set/admin_operation_log', data)
}

// 支付方式
export function paymentMethodIndex(data) {
    return $http.doGet('manage/set/payment', data)
}
export function paymentMethodChangeField(data) {
    return $http.doPost('manage/set/payment/change/field', data)
}
export function paymentMethodEdit(data) {
    return $http.doGet('manage/set/payment/edit', data)
}
export function paymentMethodUpdate(data) {
    return $http.doPost('manage/set/payment/update', data)
}
// 交易流水表
export function transactionIndex(data) {
    return $http.doGet('manage/set/payment/transaction',data)
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
