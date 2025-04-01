import $http from '../utils/http'

export function folderIndex(data) {
    return $http.doGet('manage/material/folder', data)
}

export function folderList(data) {
    return $http.doGet('manage/material/folder/list', data)
}

export function materialIndex(data) {
    return $http.doGet('manage/material', data)
}

export function rename(data) {
    return $http.doPost('manage/material/rename', data)
}
export function newFile(data) {
    return $http.doPost('manage/material/new/file', data)
}
export function newFolder(data) {
    return $http.doPost('manage/material/new/folder', data)
}

export function destory(data) {
    return $http.doPost('manage/material/destory', data)
}

export function batchDestory(data) {
    return $http.doPost('manage/material/batch/destory', data)
}

export function batchMove(data) {
    return $http.doPost('manage/material/batch/move', data)
}

export function move(data) {
    return $http.doPost('manage/material/move', data)
}
