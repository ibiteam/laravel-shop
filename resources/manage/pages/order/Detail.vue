<script setup>
import { useRoute, useRouter } from 'vue-router';
import {getCurrentInstance, onMounted, reactive, ref, watch} from 'vue';
import { orderDetail, orderShipEdit, orderShipUpdate, orderAddressEdit, orderAddressUpdate } from '@/api/order.js';
import _ from 'lodash';

const cns = getCurrentInstance().appContext.config.globalProperties

const router = useRouter();
const route = useRoute();
import { useCommonStore } from '@/store'
const commonStore = useCommonStore()
/* 订单明细数据 */
const orderItems = ref([]);
/* 订单数据 */
const orderData = ref([]);
const orderInfo = ref({});
/* 订单时间节点数据 */
const orderTimeData = ref([]);
/* 订单费用信息 */
const orderAmountData = ref([]);
/* 订单支付信息 */
const orderPayData = ref([]);
/* 订单购买者信息 */
const orderBuyerInfo = ref({});
/* 订单收货地址信息 */
const orderConsigneeInfo = ref({});
/* 积分名称 */
const integralName = ref('')
/* 商品合计金额 */
const orderGoodsAmount = ref('')

const loading = ref(false)

const getData = (orderId) => {
    orderDetail({id:orderId}).then(res => {
        if (cns.$successCode(res.code)) {
            integralName.value = res.data.integral_name
            orderGoodsAmount.value = res.data.order_amount_data.goods_amount
            orderInfo.value = res.data.order_info

            orderPayData.value = res.data.order_pay_data
            orderItems.value = res.data.order_items
            orderData.value = [res.data.order_info]
            orderTimeData.value = [res.data.order_time]
            orderAmountData.value = [res.data.order_amount_data]
            orderBuyerInfo.value = res.data.order_buyer_info
            orderConsigneeInfo.value = res.data.order_consignee_info
        } else {
            cns.$message.error(res.message)
        }
    })
}

/* 发货修改开始 */
const shipFormDialogVisible = ref(false)
const shipFormInitLoading = ref(false)
const submitShipFormLoading = ref(false)
const shipFormRef = ref(null);
const shipForm = reactive({
    id: 0,
    ship_status: 1,
    ship_company_id: null,
    ship_no: '',
})
const shipFormRules = ref({
    ship_status: [
        { required: true, message: '请选择发货状态', trigger: 'change' },
    ],
})
const shipCompany = ref([]);
const openShipFormDialog = (orderId) => {
    shipFormDialogVisible.value = true
    shipFormInitLoading.value = true;
    orderShipEdit({ id: orderId }).then(res => {
        shipFormInitLoading.value = false;
        if (cns.$successCode(res.code)) {
            shipForm.id = res.data.order_info.id;
            shipForm.ship_status = res.data.order_info.ship_status;

            shipCompany.value = res.data.ship_company;
        }
    });
};
const submitShipForm = _.throttle(() => {
    shipFormRef.value.validate((valid) => {
        if (valid) {
            let orderId = shipForm.id
            submitShipFormLoading.value = true;
            orderShipUpdate(shipForm).then(res => {
                submitShipFormLoading.value = false;
                if (cns.$successCode(res.code)) {
                    getData(orderId)
                    closeShipFormDialog()
                    cns.$message.success(res.message)
                } else {
                    cns.$message.error(res.message)
                }
            });
        }
    });
}, 1000);
const closeShipFormDialog = () => {
    shipCompany.value = [];
    shipFormDialogVisible.value = false
    shipFormInitLoading.value = false
    submitShipFormLoading.value = false

    shipForm.id = 0
    shipForm.ship_status = 1
    shipForm.ship_company_id = null
    shipForm.ship_no = ''
}
/* 发货修改结束 */
/* 修改收货地址开始 */
const addressFormDialogVisible = ref(false)
const addressFormInitLoading = ref(false)
const addressFormSubmitLoading = ref(false);
const addressFormRef = ref(null);
const addressForm = reactive({
    id: 0,
    consignee: '',
    phone: '',
    address: '',
    region: [],
});
const addressFormRules = ref({
    consignee: [
        { required: true, message: '请输入收货人', trigger: 'blur' },
    ],
    phone: [
        { required: true, message: '请输入收货人手机号', trigger: 'blur' },
    ],
    address: [
        { required: true, message: '请输入收货人地址', trigger: 'blur' },
    ],
    region: [
        { required: true, message: '请选择省份', trigger: 'change' }
    ]
})
const regionData = ref([])
const openAddressFormDialog = (orderId) => {
    addressFormDialogVisible.value = true
    addressFormInitLoading.value = true;
    addressForm.id = orderId;
    orderAddressEdit({ id: orderId }).then(res => {
        addressFormInitLoading.value = false;
        if (cns.$successCode(res.code)) {
            // 收货地址信息
            addressForm.consignee = res.data.info.consignee;
            addressForm.phone = res.data.info.phone;
            addressForm.address = res.data.info.address;
            addressForm.region = [res.data.info.province_id,res.data.info.city_id,res.data.info.district_id];

            regionData.value = res.data.regions;
        }
    });
}
const submitAddressForm = _.throttle(() => {
    console.log('addressForm',addressForm);
    addressFormRef.value.validate((valid) => {
        if (valid) {
            if (addressForm.region.length != 3) {
                cns.$message.error('请选择省市区')
                return false;
            }
            let orderId = addressForm.id;
            let requestAddressData = {
                id: addressForm.id,
                consignee: addressForm.consignee,
                phone: addressForm.phone,
                address: addressForm.address,
                province_id: addressForm.region[0],
                city_id: addressForm.region[1],
                district_id: addressForm.region[2],
            }
            addressFormSubmitLoading.value = true;
            orderAddressUpdate(requestAddressData).then(res => {
                addressFormSubmitLoading.value = false;
                if (cns.$successCode(res.code)) {
                    getData(orderId)
                    closeAddressFormDialog()
                    cns.$message.success(res.message)
                } else {
                    cns.$message.error(res.message)
                }
            });
        }
    });
}, 1000);
const closeAddressFormDialog = () => {
    regionData.value = [];
    addressFormDialogVisible.value = false
    addressFormInitLoading.value = false
    addressFormSubmitLoading.value = false

    addressForm.id = 0
    addressForm.consignee = '';
    addressForm.phone = '';
    addressForm.address = '';
    addressForm.province_id = '';
    addressForm.city_id = '';
    addressForm.district_id = '';
}
/* 修改收货地址结束 */

onMounted(() => {
    let title = '订单详情'
    if (route.params.no){
        title = "订单详情-" + route.params.no
        commonStore.updateVisitedViewsTitle(route, title)
    }
    getData(route.query.id)
})



</script>

<template>
    <div class="order-detail bg-fff manage-public-wrap pd20">
        <p class="tip-title"><a @click="router.push({name:'manage.order.index'})">订单详情 </a><span>></span><span> c20240680001</span></p>
        <div class="detail-item">
            <div class="detail-title">商品信息</div>
            <el-table :data="orderItems" stripe border style="width: 100%;">
                <el-table-column label="明细ID" prop="id"></el-table-column>
                <el-table-column label="货号" prop="goods_no" width="300"></el-table-column>
                <el-table-column label="商品ID" prop="goods_id"></el-table-column>
                <el-table-column label="商品名称" prop="goods_name"></el-table-column>
                <el-table-column label="商品规格" width="280">
                    <template #default="scope">
                        <div v-if="scope.row.sku_data.length > 0">
                            <span v-for="(item,index) in scope.row.sku_data">{{ item.key }}:{{ item.value }};</span>
                        </div>
                        <div v-else>暂无</div>
                    </template>
                </el-table-column>
                <el-table-column label="价格" prop="goods_price"></el-table-column>
                <el-table-column label="商品数量">
                    <template #default="scope">
                        <div>{{ scope.row.goods_number }}{{ scope.row.unit }}</div>
                    </template>
                </el-table-column>
                <el-table-column :label="integralName" prop="goods_integral"></el-table-column>
                <el-table-column label="库存" prop="goods_stock"></el-table-column>
                <el-table-column label="小计" prop="goods_amount"></el-table-column>
            </el-table>
            <div class="order-amount" style="text-align: right;">合计：{{ orderGoodsAmount }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-title">订单信息</div>
            <el-table :data="orderData" stripe border style="width: 100%;">
                <el-table-column label="订单状态" prop="order_status_message"></el-table-column>
                <el-table-column label="付款状态" prop="pay_status_message"></el-table-column>
                <el-table-column label="发货状态">
                    <template #default="scope">
                        {{ scope.row.ship_status_message }}
                        <el-button v-if="scope.row.ship_status !== 2" link type="primary" @click="openShipFormDialog(scope.row.order_id)">编辑</el-button>
                        <span></span>
                        <!--  todo operate: 查看物流处理 -->
                        <el-button v-if="scope.row.ship_status === 1" link type="primary">查看物流</el-button>
                    </template>
                </el-table-column>
                <el-table-column label="订单来源" prop="referer"></el-table-column>
                <el-table-column label="支付方式" prop="payment_method"></el-table-column>
                <el-table-column label="订单留言">
                    <template #default="scope">
                        <div v-if="scope.row.remark">{{ scope.row.remark }}</div>
                        <div v-else>无</div>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <div class="detail-item">
            <div class="detail-title">订单时间节点</div>
            <el-table :data="orderTimeData" stripe border style="width: 100%;">
                <el-table-column label="下单时间" prop="created_at"></el-table-column>
                <el-table-column label="付款时间" prop="created_at"></el-table-column>
                <el-table-column label="发货时间" prop="shipped_at"></el-table-column>
                <el-table-column label="收货时间" prop="received_at"></el-table-column>
            </el-table>
        </div>
        <div class="detail-item">
            <div class="detail-title">费用信息</div>
            <el-table :data="orderAmountData" stripe border style="width: 100%;">
                <el-table-column label="总金额">
                    <template #default="scope">
                        <span class="order-amount">{{ scope.row.order_amount }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="商品费用">
                    <template #default="scope">
                        +{{ scope.row.goods_amount }}
                    </template>
                </el-table-column>
                <el-table-column label="运费">
                    <template #default="scope">
                        +{{ scope.row.shipping_fee }}
                    </template>
                </el-table-column>
                <el-table-column label="应付金额">
                    <template #default="scope">
                        <span class="order-amount">{{ scope.row.order_amount }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="已付金额">
                    <template #default="scope">
                        +{{ scope.row.money_paid }}
                    </template>
                </el-table-column>
                <el-table-column :label="integralName" prop="integral"></el-table-column>
            </el-table>
        </div>
        <div class="detail-item">
            <div class="detail-title">支付信息</div>
            <el-table :data="orderPayData" stripe border style="width: 100%;">
                <el-table-column label="支付流水号" prop="transaction_no"></el-table-column>
                <el-table-column label="支付来源" prop="payment_name"></el-table-column>
                <el-table-column label="支付状态" prop="pay_message"></el-table-column>
                <el-table-column label="备注" prop="remark"></el-table-column>
                <el-table-column label="支付金额" prop="amount"></el-table-column>
                <el-table-column label="添加时间" prop="created_at"></el-table-column>
            </el-table>
        </div>
        <div style="display: grid;gap: 30px;grid-template-columns: repeat(2, 1fr);">
            <div class="detail-item">
                <div class="detail-title">购买者信息</div>
                <div class="detail-cont">
                    <div>
                        <span>用&nbsp;&nbsp;户&nbsp;&nbsp;名：</span>
                        <span>{{ orderBuyerInfo.user_name }}</span>
                    </div>
                    <div>
                        <span>注册类型：</span>
                        <span>{{ orderBuyerInfo.type }}</span>
                    </div>
                    <div>
                        <span>联系电话：</span>
                        <span>{{ orderBuyerInfo.phone }}</span>
                    </div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-title s-flex ai-ct">收货地址<el-button class="ml-10" size="small" type="primary" @click="openAddressFormDialog(orderInfo.order_id)">编辑</el-button></div>
                <div class="detail-cont">
                    <div>
                        <span>收&nbsp;&nbsp;货&nbsp;&nbsp;人：</span>
                        <span>{{ orderConsigneeInfo.consignee }}</span>
                    </div>
                    <div>
                        <span>收货地址：</span>
                        <span>{{ orderConsigneeInfo.address }}</span>
                    </div>
                    <div>
                        <span>手&nbsp;&nbsp;机&nbsp;&nbsp;号：</span>
                        <span>{{ orderConsigneeInfo.phone }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 发货修改 -->
    <el-dialog v-model="shipFormDialogVisible" title="发货修改" width="500" center :before-close="closeShipFormDialog">
        <div v-loading="shipFormInitLoading" class="s-flex jc-ct">
            <el-form :model="shipForm" ref="shipFormRef" :rules="shipFormRules" label-width="auto" style="width: 100%;" size="default">
                <el-form-item label="发货状态" prop="ship_status">
                    <el-radio-group v-model="shipForm.ship_status">
                        <el-radio :value="1">已发货</el-radio>
                        <el-radio :value="0">未发货</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="快递公司" prop="ship_company_id" :rules="[{ required: shipForm.ship_status == 1, message: '请选择快递公司', trigger: 'blur'}]">
                    <el-select v-model="shipForm.ship_company_id">
                        <el-option v-for="item in shipCompany" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                </el-form-item>
                <el-form-item label="快递单号" prop="ship_no" :rules="[{ required: shipForm.ship_status == 1, message: '请输入快递单号', trigger: 'blur'}]">
                    <el-input v-model="shipForm.ship_no" />
                </el-form-item>
            </el-form>
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closeShipFormDialog()">取消</el-button>
                <el-button type="primary" :loading="submitShipFormLoading" @click="submitShipForm()">确定</el-button>
            </div>
        </template>
    </el-dialog>
    <!-- 查看物流 -->
    <!-- 编辑收货地址 -->
    <el-dialog v-model="addressFormDialogVisible" title="编辑收货地址" width="500" center :before-close="closeAddressFormDialog">
        <div v-loading="addressFormInitLoading" class="s-flex jc-ct">
            <el-form :model="addressForm" ref="addressFormRef" :rules="addressFormRules" label-width="auto" style="width: 100%;" size="default">
                <el-form-item label="收货人" prop="consignee">
                    <el-input v-model="addressForm.consignee" />
                </el-form-item>
                <el-form-item label="省市区" prop="region">
                    <el-cascader v-model="addressForm.region" :options="regionData" clearable />
                </el-form-item>
                <el-form-item label="详细地址" prop="address">
                    <el-input v-model="addressForm.address" />
                </el-form-item>
                <el-form-item label="手机号" prop="phone">
                    <el-input v-model="addressForm.phone" />
                </el-form-item>
            </el-form>
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closeAddressFormDialog()">取消</el-button>
                <el-button type="primary" :loading="addressFormSubmitLoading" @click="submitAddressForm()">确定</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<style scoped lang="scss">
.order-detail{
    min-height: 100%;
    .tip-title{
        a{
            color: #333;
            cursor: pointer;
            &:hover{
                color: #3D9CFF;
            }
        }
    }
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
}
.order-amount {
    color: var(--red-color);
}
</style>
