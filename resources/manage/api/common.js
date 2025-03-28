import $http from '../utils/http'

export function fileUpload(data) {
    return $http.doPost('manage/upload', data)
}
