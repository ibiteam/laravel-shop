<script setup lang="ts">

import { RefreshLeft, Search } from '@element-plus/icons-vue';
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import PublicPageTable from '@/components/common/PublicPageTable.vue';
import { orderEvaluateCheck, orderEvaluateDetail, orderEvaluateIndex } from '@/api/order.js';

const cns = getCurrentInstance().appContext.config.globalProperties
const router = useRouter()

const tableData = ref([]);
const loading = ref(false);

const statusOptions = [
    { label: '未审核', value: 0 },
    { label: '审核通过', value: 1 },
    { label: '审核驳回', value: 2 },
]

const queryParams = reactive({
    order_no: '',
    goods_name: '',
    user_name: '',
    evaluate_start_time: '',
    evaluate_end_time: '',
    status: null,
    page: 1,
    number: 10,
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

// 重置搜索条件
const resetSearch = () => {
    queryParams.order_no = '';
    queryParams.goods_name = '';
    queryParams.user_name = '';
    queryParams.evaluate_start_time = '';
    queryParams.evaluate_end_time = '';
    queryParams.status = null;
    queryParams.page = 1;
    queryParams.number = 10;
    getData(1);
};

// 添加分页相关状态
const pageInfo = reactive({
    per_page: 10,
    total: 0,
    current_page: 1,
})

// 页码改变
const handleCurrentChange = (val) => {
    getData(val);
}

// 每页条数改变
const handleSizeChange = (val) => {
    pageInfo.per_page = val;
    getData(1);
}

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
}

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;
    queryParams.number = pageInfo.per_page;

    orderEvaluateIndex(queryParams).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data.list;
            // 更新分页信息
            setPageInfo(res.data.meta);
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}
/* 查看弹窗开始 */
const detailVisible = ref(false)
const detailInitLoading = ref(false);
const detailData = ref({})
const openDetailDialog = (orderEvaluateId) => {
    detailInitLoading.value = true
    orderEvaluateDetail({id: orderEvaluateId}).then(res => {
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
        orderEvaluateCheck({id: orderEvaluateId,status: status}).then(res => {
            if (cns.$successCode(res.code)) {
                cns.$message.success(res.message)
                getData(pageInfo.current_page)
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

<template>
    <div class="common-wrap">
        <el-header style="padding: 10px 0;height: auto;">
            <!-- 添加搜索表单 -->
            <el-form :inline="true" :model="queryParams" class="search-form" label-width="100px">
                <el-form-item label="订单编号" prop="order_no">
                    <el-input
                        v-model="queryParams.order_no"
                        placeholder="请输入订单编号搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="商品名称" prop="goods_name">
                    <el-input
                        v-model="queryParams.goods_name"
                        placeholder="请输入商品名称搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="用户名" prop="user_name">
                    <el-input
                        v-model="queryParams.user_name"
                        placeholder="请输入用户名搜索"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item label="审核状态" prop="status">
                    <el-select v-model="queryParams.status" placeholder="请选择审核状态" clearable>
                        <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="评论开始时间" prop="evaluate_start_time">
                    <el-date-picker
                        v-model="queryParams.evaluate_start_time"
                        type="datetime"
                        placeholder="请选择评论开始时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item label="评论结束时间" prop="evaluate_end_time">
                    <el-date-picker
                        v-model="queryParams.evaluate_end_time"
                        type="datetime"
                        placeholder="请选择评论结束时间"
                        value-format="YYYY-MM-DD HH:mm:ss"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                    <el-button :icon="RefreshLeft" @click="resetSearch">重置</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <PublicPageTable
            :data="tableData"
            v-loading="loading"
            :pageInfo="pageInfo"
            @sizeChange="handleSizeChange"
            @currentChange="handleCurrentChange"
            style="width: 100%;">
            <el-table-column label="ID" prop="id" width="80px"></el-table-column>
            <el-table-column label="订单编号" prop="order.no" width="150px"></el-table-column>
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
        </PublicPageTable>
        <!-- 评价详情弹窗 -->
        <el-dialog v-model="detailVisible" title="评价详情" width="600"  center :before-close="closeDetailDialog">
            <div v-loading="detailInitLoading">
                <el-form label-width="100px">
                    <el-form-item label="订单编号"><span>{{detailData.order.no}}</span></el-form-item>
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
    </div>
</template>

<style scoped lang="scss">
.search-form {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 10px;

    :deep(.el-select) {
        width: 200px;
    }

    :deep(.el-input) {
        width: 200px;
    }
}
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
