import $http from '../utils/http'

export function fileUpload(data) {
    return $http.doUpload('manage/upload', data)
}
