# 状态码

### 状态码说明
| 状态码 | 说明        |
|:----|:----------|
| 200 | 请求成功 |
| 401 | 请求有误 |
| 403 | 未授权 |


### 使用

### 状态常量文件定义在 `@/utils/constants` 中

```js
import { isSuccess } from '@/utils/constants'

//调用
getLoginInfo().then(res => {
    if(isSuccess(res.code)){
        pageData.value = res.data?.config;
        if (res.data?.is_login) {
            router.push({name:'manage.home.index'})
        }
    } else {
        cns.$message.error(res.message);
    }
})
```
