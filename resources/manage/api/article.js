// 文章分类
import $http from '@/utils/http.js';

export function articleCategoryIndex(data) {
    return $http.doGet('manage/article/article_category', data)
}
export function articleCategoryInfo(data) {
    return $http.doGet('manage/article/article_category/info', data)
}
export function articleCategoryStore(data) {
    return $http.doPost('manage/article/article_category/store', data)
}
export function articleCategoryDestroy(data) {
    return $http.doPost('manage/article/article_category/destroy', data)
}
export function articleCategoryChangeShow(data) {
    return $http.doPost('manage/article/article_category/change_show', data)
}
export function articleCategoryMove(data) {
    return $http.doPost('manage/article/article_category/move', data)
}

// 文章列表
export function articleIndex(data) {
    return $http.doGet('manage/article/article', data)
}
export function articleInfo(data) {
    return $http.doGet('manage/article/article/info', data)
}
export function articleStore(data) {
    return $http.doPost('manage/article/article/store', data)
}
export function articleChangeField(data) {
    return $http.doPost('manage/article/article/change_field', data)
}
export function articleCopy(data) {
    return $http.doPost('manage/article/article/copy', data)
}
export function articleDestroy(data) {
    return $http.doPost('manage/article/article/destroy', data)
}
export function articleUpdateCover(data) {
    return $http.doPost('manage/article/article/update_cover', data)
}
export function articleDeleteCover(data) {
    return $http.doPost('manage/article/article/delete_cover', data)
}
