import $http from '../utils/http'

export function getGoodsViews(data) {
    return $http.doGet('manage/goods/views', data)
}
