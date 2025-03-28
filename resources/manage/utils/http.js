import axios from 'axios'
import router from '@/router'
import dialog from "./dialog";
import { useCookies } from 'vue3-cookies'
const { cookies } = useCookies()
// 请求超时时间
axios.defaults.timeout = 15000
function generateUUID() {
    var d = new Date().getTime()
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = (d + Math.random() * 16) % 16 | 0
        d = Math.floor(d / 16)
        return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(36)
    })
    return uuid
}

if (!localStorage.getItem('visitorId')) {
    localStorage.setItem('visitorId', generateUUID())
}
// 如果用的JSONP，可以配置此参数带上cookie凭证，如果是代理和CORS不用设置
// axios.defaults.withCredentials = true
axios.defaults.baseURL = window.location.origin + '/api/'
// post请求头
axios.defaults.headers.Accept = 'application/json'
axios.defaults.headers.post['Content-Type'] = 'multipart/form-data'
axios.defaults.headers['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers['Access-From'] = 'pc'

// 请求拦截器
axios.interceptors.request.use(
    config => {
        let token = cookies.get('manage-token')
        let visitorId = ''
        if (localStorage.getItem('visitorId')) {
            visitorId = localStorage.getItem('visitorId')
        } else {
            visitorId = generateUUID()
            localStorage.setItem('visitorId', visitorId)
        }
        config.headers['Device-Id'] = visitorId
        config.headers['Authorization'] = token ? 'Bearer ' + token : ''
        return config
    },
    error => {
        return Promise.error(error)
    }
)

// 响应拦截器
axios.interceptors.response.use(
    response => {
        if (response.status === 200) {
            return Promise.resolve(response)
        } else {
            return Promise.reject(response)
        }
    },
    error => {
        return Promise.reject(error)
    }
)

/**
 * get方法，对应get请求
 * @param {String} url [请求的url地址]
 * @param {Object} params [请求时携带的参数]
 */
function doGet(url, param) {
    let params = param || {}

    return new Promise((resolve, reject) => {
        axios.get(url, {
            params: params
        })
            .then(res => {
                resolve(res.data)
            })
            .catch(err => {
                reject(err.data)
            })
    })
}

/**
 * post方法，对应post请求
 * @param {String} url [请求的url地址]
 */
function doPost(url, param) {
    let params = param || {}
    return new Promise((resolve, reject) => {
        axios.post(url, params)
            .then(res => {
                resolve(res.data)
            })
            .catch(err => {
                reject(err.data)
            })
    })
}

/**
 * 文件上传
 * @param url 请求地址
 * @param param 请求参数
 * @param method 请求方式
 * @returns {Promise<never>|Promise<unknown>}
 */
function doUpload(url, param, method = 'post') {
    let params = param || {};
    let formData = new FormData();

    if (!params.file) {
        return Promise.reject('File is required');
    }

    formData.append('file', params.file);
    formData.append('timeStamp', Math.floor(Date.now() / 1000));

    return axios({
        method,
        url,
        data: formData,
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
        .then(res => res.data)
        .catch(err => {
            return Promise.reject(err);
        });
}

export default {
    doUpload,
    doPost,
    doGet
}
