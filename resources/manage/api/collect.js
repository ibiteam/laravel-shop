import $http from '../utils/http'

export function getGoodsCollect(data) {
    return $http.doGet('manage/goods/collect', data)
}
