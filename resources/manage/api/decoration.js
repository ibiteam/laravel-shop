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