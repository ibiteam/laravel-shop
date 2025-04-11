import $http from '../utils/http'

export function appDecoration(data) {
    return $http.doGet('manage/set/app_decoration', data)
}

/**
 * 移动端装修初始化
 * @param {
 *      id: 1
 * } 
 * @returns 
 */
export function appDecorationInit(data) {
    return $http.doGet('manage/set/app_decoration/decoration', data)
}

/**
 * 移动端装修保存
 * @param {
 *      button_type: 1, // 1-保存草稿 2-预览 3-发布
 *      id: 1,
 *      title: 'TDK标题',
 *      keywords: 'TDK关键字',
 *      description: 'TDK描述'
 *      data: [// 移动端装修数据]
 * } 
 * @returns 
 */
export function appDecorationSave(data) {
    return $http.doPost('manage/set/app_decoration/decoration/save', data)
}

/**
 * 移动端装修 获取商品列表
 * @param {
 *      keywords: '关键词：商品货号/商品id'
 *      goods_id: '商品id'
 *      category_id: '分类id'
 *      page: 1,
 *      number: 10,
 * } 
 * @returns 
 */
export function decorationGoodsList(data) {
    return $http.doPost('manage/set/app_decoration/goods/list', data)
}

/**
 * 移动端装修 导入商品id
 * @param {
 *      goods_ids: []
 * } 
 * @returns 
 */
export function decorationGoodsImport(data) {
    return $http.doPost('manage/set/app_decoration/goods/import', data)
}


/**
 * 移动端装修 为您推荐获取商品列表
 * @returns 
 */
export function decorationRecommendData() {
    return $http.doGet('manage/set/app_decoration/recommend/data')
}

