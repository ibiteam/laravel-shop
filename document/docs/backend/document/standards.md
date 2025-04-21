# 开发规范

## 前端

1. 共用样式放到 `resources/manage/assets/css/common.css` 样式中

```css
.el-switch {
    --el-switch-on-color:var(--el-color-success);
}
```

2. 视图文件放在`resources/manage/pages/`中,遵循文件夹模型小写下划线命名，列表用`index.vue`,详情用`detail.vue`,编辑默认用弹框，表单太多就新建`update.vue`
```sh
pages/order/index.vue
pages/order_log/index.vue
pages/order_log/update.vue
import Order from '@/pages/order/Index.vue'
import OrderDetail from '@/pages/order/Detail.vue'
import OrderUpdate from '@/pages/order/Update.vue'
```

3. 页面布局第一个是标签是`<template>` 页面标签,第二个是`<script setup lang="ts">` js代码,第三个是`<style scoped lang="scss">` css样式,搜索使用`SearchForm`组件来处理,表格分页数据使用`PageTable`来显示
组件引用统一使用小写`kebab-case`（短横线连接）的方式,组件注册使用驼峰的方式

```js
<template>
    <search-form :model="query">
            <el-form-item label="订单编号" prop="no">
                <el-input
                    v-model="query.no"
                    placeholder="请输入订单编号搜索"
                    clearable
                    @keyup.enter="getData()"
                />
            </el-form-item>     
            <el-form-item>
                <el-button  type="primary" @click="getData()">搜索</el-button>
            </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handleChange"
    >
        <el-table-column label="订单ID" prop="id"></el-table-column>
        <el-table-column label="订单编号" prop="no"></el-table-column>
    </page-table>
</template>
<script setup lang="ts">
import { ref, reactive, getCurrentInstance, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PageTable from '@/components/common/PageTable.vue'
import SearchForm from '@/components/common/SearchForm.vue'
import { OrderStatus,PayStatus,ShipStatus } from '@/enums/model'
import Http from '@/utils/http';
const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

const tableData = ref({})
const defaultPage = {
    page: 1,
    per_page: 10,
}
const pagination = reactive({...defaultPage})
const loading = ref(false)

const orderStatusOptions = [
    {label: '未确认', value: OrderStatus.Unconfirmed},
    {label: '已确认', value: OrderStatus.Confirmed},
    {label: '已取消', value: OrderStatus.Canceled},
]
const payStatusOptions = [
    {label: '待支付', value: PayStatus.Unpaid},
    {label: '已支付', value: PayStatus.Paid},
]
const shipStatusOptions = [
    {label: '未发货', value: ShipStatus.Unshipped},
    {label: '已发货', value: ShipStatus.Shipped},
    {label: '已收货', value: ShipStatus.Received},
]

const sourceOptions = [
    {label: 'PC端', value: 'pc'},
    {label: 'H5端', value: 'h5'},
    {label: 'APP端', value: 'app'},
    {label: '微信小程序', value: 'wechat_mini'},
]
const defaultQuery = {
    no: '',
    user_keywords: '',
    goods_id: '',
    goods_name: '',
    consignee_keywords: '',
    order_status: null,
    pay_status: null,
    shipping_status: null,
    done_start_time: null,
    done_end_time: null,
    source: null,
}
// 添加查询参数对象，增加搜索条件
const query = reactive({...defaultQuery})

const getData = (page=defaultPage.page) => {
    loading.value = true
    Http.doGet('order/info/index', { ...query, page: page, per_page: pagination.per_page }).then((res:any) => {
        loading.value = false
        if (cns.$successCode(res.code)) {
            tableData.value = res.data
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false
    })
}
const handleChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}
const openDetail = (row:any) => {
    router.push({name:'manage.order.detail', query: {id: row.id},params:{no: row.no}})
}

onMounted( () => {
    getData()
})
</script>
<style scoped lang="scss">

</style>
```

4. 页面通用常量定义好常量文件和enum

5. 字段名用小写_的方式，变量名和函数等用小驼峰，类名/文件名/组件名用大驼峰

| 内容               | 推荐写法                 |
|:-----------------|:---------------------|
| 文件名              | `UserProfile.vue`    |
| 类名               | `UserProfile`        |
| HTML 标签名、模板中组件引用 | `<user-profile />`   |
| 组件注册             | `import UserProfile` |
| 字段名              | `user_name`          |
