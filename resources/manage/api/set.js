import $http from '../utils/http'

// 商店设置
export function shopConfigIndex(data) {
    return $http.doGet('manage/set/shop_config', data)
}
export function shopConfigUpdate(data) {
    return $http.doPost('manage/set/shop_config/update', data)
}

// 路由分类
export function routerCategoryIndex(data) {
    return $http.doGet('manage/set/router_category', data)
}
export function routerCategoryStore(data) {
    return $http.doPost('manage/set/router_category/store', data)
}
export function routerCategoryChangeShow(data) {
    return $http.doPost('manage/set/router_category/change_show', data)
}

// 路由列表
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
