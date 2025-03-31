import $http from '../utils/http'

/* goods category api interface start */
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

/* goods api interface start */
export function goodsIndex(data) {
    return $http.doGet('manage/goods/info', data)
}

export function goodsChangeStatus(data) {
    return $http.doPost('manage/goods/info/change/status', data)
}

export function goodsDetailInit(data) {
    return $http.doGet('manage/goods/info/edit', data)
}

export function goodsUpdate(data) {
    return $http.doPost('manage/goods/info/update', data)
}

/* goods parameter template api interface start */
export function getGoodsParameterTemplate() {
    return $http.doGet('manage/goods/parameter/template/small/index')
}

export function goodsParameterTemplateStore(data) {
    return $http.doPost('manage/goods/parameter/template/store',data)
}

export function goodsParameterTemplateUpdate(data) {
    return $http.doPost('manage/goods/parameter/template/update',data)
}

export function goodsParameterTemplateDestroy(data) {
    return $http.doPost('manage/goods/parameter/template/destroy',data)
}

/* goods sku template api interface start */
export function getGoodsSkuTemplate() {
    return $http.doGet('manage/goods/sku/template/small/index')
}

export function goodsSkuTemplateStore(data) {
    return $http.doPost('manage/goods/sku/template/store',data)
}

export function goodsSkuTemplateUpdate(data) {
    return $http.doPost('manage/goods/sku/template/update',data)
}

export function goodsSkuTemplateDestroy(data) {
    return $http.doPost('manage/goods/sku/template/destroy',data)
}
