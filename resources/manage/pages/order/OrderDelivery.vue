<template>
    <search-form :model="query" :label-width="100">
        <el-form-item label="运单号" prop="delivery_no">
            <el-input
                v-model="query.delivery_no"
                placeholder="请输入运单号搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="订单编号" prop="order_sn">
            <el-input
                v-model="query.order_sn"
                placeholder="请输入订单编号搜索"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item label="发货开始时间" prop="created_start_time">
            <el-date-picker
                v-model="query.created_start_time"
                type="datetime"
                placeholder="请选择发货开始时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
        </el-form-item>
        <el-form-item label="发货结束时间" prop="created_end_time">
            <el-date-picker
                v-model="query.created_end_time"
                type="datetime"
                placeholder="请选择发货结束时间"
                value-format="YYYY-MM-DD HH:mm:ss"
            >
            </el-date-picker>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button @click="resetSearch">重置</el-button>
            <el-button type="warning" @click="openImportDialog">导入发货</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        v-loading="loading"
        @change="handleChange"
    >
        <el-table-column label="ID" prop="id" width="80px"></el-table-column>
        <el-table-column label="运单号" prop="delivery_no" width="180px"></el-table-column>
        <el-table-column label="订单编号" prop="order.order_sn" width="180px"></el-table-column>
        <el-table-column label="快递公司" prop="ship_company.name"></el-table-column>
        <el-table-column label="快递单号" width="240px">
            <template #default="scope">
                <span>{{ scope.row.ship_no }}</span>
                <el-button v-if="scope.row.ship_no" class="s-flex ai-ct" link type="primary" @click="openLogisticsDialog(scope.row.id)">查看物流</el-button>
            </template>
        </el-table-column>
        <el-table-column label="状态">
            <template #default="scope">
                <span v-if="scope.row.status === 0">待收货</span>
                <span v-else-if="scope.row.status === 1">已收货</span>
                <span v-else>--</span>
            </template>
        </el-table-column>
        <el-table-column label="操作人" prop="admin_user.user_name"></el-table-column>
        <el-table-column label="备注">
            <template #default="scope">
                <span v-if="scope.row.remark">{{ scope.row.remark }}</span>
                <span v-else>无</span>
            </template>
        </el-table-column>
        <el-table-column label="发货时间" prop="shipped_at" width="160px"></el-table-column>
        <el-table-column label="收货时间" prop="received_at" width="160px"></el-table-column>
        <el-table-column label="更新时间" prop="updated_at" width="160px"></el-table-column>
        <el-table-column label="操作"width="160px">
            <template #default="scope">
                <el-button link type="primary" size="large" @click="handleDelete(scope.row.id)">删除</el-button>
            </template>
        </el-table-column>
    </page-table>
    <!-- 查看物流 -->
    <el-dialog v-model="logisticsVisible" title="查看物流轨迹" width="600"  center :before-close="closeLogisticsDialog">
        <div style="height: 60vh;overflow: auto" v-loading="logisticsInitLoading">
            <el-timeline :reverse="false">
                <el-timeline-item v-for="(shipItem, index) in logisticsData" :key="index" :timestamp="shipItem.time">
                    {{ shipItem.context }}
                </el-timeline-item>
            </el-timeline>
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button type="info" @click="closeLogisticsDialog()">关闭</el-button>
            </div>
        </template>
    </el-dialog>
    <!-- 导入发货 -->
    <el-dialog v-model="importVisible" title="导入发货" width="600" center :before-close="closeImportDialog">
        <div class="detail-item">
            <div class="detail-title">下载模板</div>
            <el-button ><a href="/files/excel/发货模板.xlsx">点击下载模板</a></el-button>
        </div>
        <div class="detail-item">
            <div class="detail-title">导入发货</div>
            <el-upload
                accept=".xlsx"
                action=""
                :show-file-list="false"
                :http-request="(request) => submitImport(request)"
                :with-credentials="true">
                <el-button :loading="importFileLoading" type="primary">选择文件</el-button>
            </el-upload>
        </div>
        <div class="detail-item">
            <div class="detail-title">导入结果</div>
            <div v-if="importSuccessNumber > 0">
                <span class="import-response import-response-success">导入成功 {{ importSuccessNumber }} 条数据</span>
            </div>
            <div v-if="importErrorData.length > 0">
                <div>
                    <span class="import-response import-response-error">导入失败 {{ importErrorData.length }} 条数据</span>
                </div>
                <el-table :data="importErrorData" stripe border style="width: 100%;">
                    <el-table-column label="错误行数" prop="line"></el-table-column>
                    <el-table-column label="错误信息" prop="message"></el-table-column>
                </el-table>
            </div>
        </div>
    </el-dialog>
</template>
<script setup lang="ts">
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';
import Http from '@/utils/http';

const cns = getCurrentInstance().appContext.config.globalProperties

/* 定义表格数据 */
const tableData = ref([]);
const loading = ref(false);
/* 定义搜索参数 */
const defaultQuery = reactive({
    delivery_no: '',
    order_sn: '',
    created_start_time: '',
    created_end_time: '',
    page: 1,
    number: 10,
});
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
const getData = (page: number = defaultPage.page) => {
    loading.value = true;
    Http.doGet('order/delivery', { ...query, page: page, per_page: pagination.per_page }).then((res: any) => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}
/* 点击分页触发方法 */
const handleChange = (page:number, per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}
/* 物流轨迹开始 */
const logisticsVisible = ref(false)
const logisticsData = ref([])
const logisticsInitLoading = ref(false)
const openLogisticsDialog = (orderId: number) => {
    logisticsInitLoading.value = true
    logisticsVisible.value = true
    Http.doGet('order/delivery/express/query',{id: orderId}).then((res: any) => {
        logisticsInitLoading.value = false
        if (cns.$successCode(res.code)) {
            logisticsData.value = res.data
        } else {
            cns.$message.error(res.message)
        }
    })
}
const closeLogisticsDialog = () => {
    logisticsVisible.value = false;
    logisticsData.value = [];
    logisticsInitLoading.value = false
}
/* 物流轨迹结束 */
/* 导入发货开始 */
const importVisible = ref(false)
const importSuccessNumber = ref(0)
const importErrorNumber = ref(0)
const importErrorData = ref([]);
const importFileLoading = ref(false)
const openImportDialog = () => {
    importVisible.value = true;
    importSuccessNumber.value = 0
    importErrorNumber.value = 0
    importErrorData.value = [];
    importFileLoading.value = false;
}
const submitImport = (request) => {
    importFileLoading.value = true
    importSuccessNumber.value = 0
    importErrorNumber.value = 0
    importErrorData.value = [];
    Http.doPost('order/delivery/import', { import_file: request.file }).then((res: any) => {
        importFileLoading.value = false
        if (cns.$successCode(res.code)) {
            importSuccessNumber.value = res.data.success_number
            importErrorNumber.value = res.data.error_number
            importErrorData.value = res.data.error_data
            if (res.data.success_number > 0) {
                getData(1);
            }
        } else {
            cns.$message.error(res.message)
        }
    })
}
const closeImportDialog = () => {
    importVisible.value = false;
    importFileLoading.value = false;
    importSuccessNumber.value = 0
    importErrorNumber.value = 0
    importErrorData.value = [];
}
/* 导入发货结束 */
/* 删除发货开始 */
const handleDelete = (id) => {
    cns.$confirm('此操作将永久删除该发货单, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true,
    }).then(() => {
        Http.doPost('order/delivery/destroy', {id: id}).then(res => {
            if (cns.$successCode(res.code)) {
                cns.$message.success(res.message)
                getData(pagination.page)
            } else {
                cns.$message.error(res.message)
            }
        })
    })
}
/* 删除发货结束 */

onMounted(() => {
    getData();
})
</script>
<style scoped lang="scss">
.detail-item{
    padding: 15px 0;
    .detail-cont{
        border: 1px solid #E5E5E5;
        padding: 15px;
        min-height: 100px;
        border-radius: 10px;
        >div{
            line-height: 40px;
            color: #666;
            font-size: 14px;
        }
    }
}
.detail-title{
    font-size: 18px;
    font-weight: bold;
    line-height: 30px;
    color: #333;
    position: relative;
    padding-left: 10px;
    margin-bottom: 15px;
    &:before{
        content: '';
        position: absolute;
        width: 4px;
        height: 16px;
        background: #3D9CFF;
        left: 0;
        top: 7px;
        border-radius: 2px;
        z-index: 1;
    }
}
.import-response {
    margin-bottom: 5px;
    font-size: 18px;
    line-height: 40px;
}
.import-response-success {
    color: #67C23AFF;
}
.import-response-error {
    color: var(--red-color);
}
</style>
