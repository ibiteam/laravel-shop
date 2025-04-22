<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="订单编号" prop="order_sn">
            <el-input
                v-model="query.order_sn"
                placeholder="请输入订单编号搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="商品名称" prop="goods_name">
            <el-input
                v-model="query.goods_name"
                placeholder="请输入商品名称搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="用户名" prop="user_name">
            <el-input
                v-model="query.user_name"
                placeholder="请输入用户名搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="审核状态" prop="status">
            <el-select v-model="query.status" placeholder="请选择审核状态" clearable>
                <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
        </el-form-item>
        <el-form-item label="评论开始时间" prop="evaluate_start_time">
            <el-date-picker
                v-model="query.evaluate_start_time"
                type="datetime"
                placeholder="请选择评论开始时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
        </el-form-item>
        <el-form-item label="评论结束时间" prop="evaluate_end_time">
            <el-date-picker
                v-model="query.evaluate_end_time"
                type="datetime"
                placeholder="请选择评论结束时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button @click="resetSearch">重置</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handleChange"
    >
        <el-table-column label="ID" prop="id" width="80px"></el-table-column>
        <el-table-column label="订单编号" prop="order.order_sn" width="150px"></el-table-column>
        <el-table-column label="商品名称" width="220px" show-overflow-tooltip>
            <template #default="scope">
                <img :src="scope.row.goods.image" width="50px" height="50px" alt="">
                <div class="goods-name">{{scope.row.goods.name}}</div>
            </template>
        </el-table-column>
        <el-table-column label="用户名" prop="user.user_name" width="200px"></el-table-column>
        <el-table-column label="星级" min-width="420px">
            <template #default="scope">
                <div class="s-flex ai-ct">
                    <div class="rate">综合评分：</div><el-rate  style="height: auto;" v-model="scope.row.rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                    <div class="rate">商品评分：</div><el-rate  style="height: auto;" v-model="scope.row.goods_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                </div>
                <div class="s-flex ai-ct">
                    <div class="rate">价格评分：</div><el-rate style="height: auto;" v-model="scope.row.price_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                    <div class="rate">商家服务评分：</div><el-rate style="height: auto;" v-model="scope.row.bus_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                </div>
                <div class="s-flex ai-ct">
                    <div class="rate">交货速度评分：</div><el-rate style="height: auto;" v-model="scope.row.delivery_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                    <div class="rate">服务评分：</div><el-rate style="height: auto;" v-model="scope.row.service_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                </div>
            </template>
        </el-table-column>
        <el-table-column label="审核状态" width="110px">
            <template #default="scope">
                <el-tag v-if="scope.row.status == 1" type="success">审核通过</el-tag>
                <el-tag v-else-if="scope.row.status == 2" type="danger">审核驳回</el-tag>
                <el-tag v-else type="info">未审核</el-tag>
            </template>
        </el-table-column>
        <el-table-column label="评价时间" prop="comment_at" width="160px"></el-table-column>
        <el-table-column fixed="right" label="操作" width="200px">
            <template #default="scope">
                <el-button type="primary" link @click="openDetailDialog(scope.row.id)">查看</el-button>
                <el-button type="primary" link v-if="scope.row.status != 1" @click="handleOrderEvaluateCheck(scope.row.id,1,'确定要审核通过吗？')">通过</el-button>
                <el-button type="primary" link v-if="scope.row.status != 2" @click="handleOrderEvaluateCheck(scope.row.id,2,'确定要审核驳回吗？')">驳回</el-button>
            </template>
        </el-table-column>
    </page-table>
    <!-- 评价详情弹窗 -->
    <el-dialog v-model="detailVisible" title="评价详情" width="600"  center :before-close="closeDetailDialog">
        <div v-loading="detailInitLoading">
            <el-form label-width="100px">
                <el-form-item label="订单编号"><span>{{detailData.order.order_sn}}</span></el-form-item>
                <el-form-item label="用户名"><span>{{detailData.user.user_name}}</span></el-form-item>
                <el-form-item label="商品信息">
                    <div>
                        <img :src="detailData.goods.image" width="50px" height="50px" alt="">
                        <div class="goods-name">{{detailData.goods.name}}</div>
                    </div>
                </el-form-item>
                <el-form-item label="评价内容"><span>{{detailData.comment}}</span></el-form-item>
                <el-form-item label="评价时间"><span>{{detailData.comment_at}}</span></el-form-item>
                <el-form-item label="评价图片">
                    <img v-for="(item,index) in detailData.images" :key="index" :src="item" width="50px" height="50px" alt="">
                </el-form-item>
                <el-form-item label="评价星级">
                    <div class="s-flex ai-ct">
                        <div class="rate">综合评分：</div><el-rate  style="height: auto;" v-model="detailData.rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                        <div class="rate">商品评分：</div><el-rate  style="height: auto;" v-model="detailData.goods_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                    </div>
                    <div class="s-flex ai-ct">
                        <div class="rate">价格评分：</div><el-rate style="height: auto;" v-model="detailData.price_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                        <div class="rate">商家服务评分：</div><el-rate style="height: auto;" v-model="detailData.bus_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                    </div>
                    <div class="s-flex ai-ct">
                        <div class="rate">交货速度评分：</div><el-rate style="height: auto;" v-model="detailData.delivery_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                        <div class="rate">服务评分：</div><el-rate style="height: auto;" v-model="detailData.service_rank" disabled text-color="#ff9900" score-template="{value}"></el-rate>
                    </div>
                </el-form-item>
                <el-form-item label="是否匿名">
                    <span v-if="detailData.is_anonymous">匿名</span>
                    <span v-else>非匿名</span>
                </el-form-item>
                <el-form-item label="审核状态">
                    <el-tag v-if="detailData.status == 1" type="success">审核通过</el-tag>
                    <el-tag v-else-if="detailData.status == 2" type="danger">审核驳回</el-tag>
                    <el-tag v-else type="info">未审核</el-tag>
                </el-form-item>
            </el-form>
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button type="primary" v-if="detailData.status != 1" @click="handleOrderEvaluateCheck(detailData.id,1,'确定要审核通过吗？')">审核通过</el-button>
                <el-button type="danger" v-if="detailData.status != 2" @click="handleOrderEvaluateCheck(detailData.id,2,'确定要审核驳回吗？')">审核驳回</el-button>
                <el-button type="info" @click="closeDetailDialog()">关闭</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script setup lang="ts">
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';
import Http from '@/utils/http';

const cns = getCurrentInstance().appContext.config.globalProperties

/* 定义搜索下拉数据 */
const statusOptions = [
    { label: '未审核', value: 0 },
    { label: '审核通过', value: 1 },
    { label: '审核驳回', value: 2 },
]
/* 定义表格数据 */
const tableData = ref([]);
const loading = ref(false);
/* 定义搜索参数 */
const defaultQuery = {
    order_sn: '',
    goods_name: '',
    user_name: '',
    evaluate_start_time: '',
    evaluate_end_time: '',
    status: null,
};
const query = reactive({...defaultQuery})
/* 定义默认分页参数 */
const defaultPage = {
    page: 1,
    per_page: 10,
}
const pagination = reactive({...defaultPage})
/* 重置搜索条件 */
const resetSearch = () => {
    Object.assign(query, defaultQuery)
    Object.assign(pagination, defaultPage)
    getData()
}
/* 获取分页数据 */
const getData = (page:number = defaultPage.page) => {
    loading.value = true
    Http.doGet('order/evaluate', { ...query, page: page, per_page: pagination.per_page }).then((res:any) => {
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
/* 点击分页触发方法 */
const handleChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}

/* 查看弹窗开始 */
const detailVisible = ref(false)
const detailInitLoading = ref(false);
const detailData = ref({})
const openDetailDialog = (orderEvaluateId: number) => {
    detailInitLoading.value = true
    Http.doGet('order/evaluate/detail', {id: orderEvaluateId}).then((res: any) => {
        detailInitLoading.value = false
        if (cns.$successCode(res.code)) {
            detailData.value = res.data
            detailVisible.value = true
        } else {
            cns.$message.error(res.message)
        }
    })
}
const closeDetailDialog = () => {
    detailVisible.value = false;
    detailData.value = [];
    detailInitLoading.value = false;
}
/* 查看弹窗结束 */
const handleOrderEvaluateCheck = (orderEvaluateId:number,status:number,message:string) => {
    cns.$confirm(message, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true,
    }).then(() => {
        Http.doPost('order/evaluate/check', {id: orderEvaluateId,status: status}).then(res => {
            if (cns.$successCode(res.code)) {
                cns.$message.success(res.message)
                getData(pagination.page)
            } else {
                cns.$message.error(res.message)
            }
        })
    })
}

onMounted(() => {
    getData();
})
</script>
<style scoped lang="scss">
.rate {
    width: 100px;
    text-align: right;
    font-size: 11px;
}
.goods-name {
    width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
