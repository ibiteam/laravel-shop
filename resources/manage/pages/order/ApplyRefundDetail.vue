<template>
    <div v-loading="detailFormLoading" class="detail-wrap fs14">
        <div>退款详情</div>
        <div class="step-wrap s-flex jc-ad">
            <div class="step flex-1 s-flex ai-ct jc-ct complete">
                <span>1</span>买家申请{{ detail.type == 0 ? '仅退款' : '退货退款' }}
            </div>
            <div class="step flex-1" :class="{active:detail.status==0,complete:detail.status!=0}">
                <span>2</span>卖家处理退款申请
            </div>
            <div class="step flex-1" v-if="detail.type == 1"
                 :class="{active:detail.status==2||detail.status==3,complete:detail.status==1||detail.status==4||detail.status==5||detail.status==6}">
                <span>3</span>买家退货
            </div>
            <div class="step flex-1"
                 :class="{complete:detail.status==1||detail.status==4||detail.status==5||detail.status==6}">
                <span>{{ detail.type == 0 ? '3' : '4' }}</span>退款完毕
            </div>
        </div>
        <div class="content s-flex">
            <div class="content-left">
                <div style="margin-bottom: 10px;border-bottom: 1px solid #eee;padding-bottom: 10px;">
                    <div class="status" style="margin-bottom: 10px;">
                        {{ status[detail.status] }}
                        <span v-if="(detail.status==0||detail.status==3) && (end_time >= server_time)">
                            <img src="@/assets/images/refund/time-icon.png">还剩
                            <span class="co-red">
                                <template v-if="countdown_data.day&&countdown_data.day!=='00'">{{ countdown_data.day }}天</template>
                                {{ countdown_data.hour }}时{{ countdown_data.minute }}分{{ countdown_data.second }}秒
                            </span>
                        </span>
                    </div>
                    <h5 v-if="detail.status==5||detail.status==6">退款{{ detail.status == 5 ? '成功' : '关闭' }}时间:{{ detail.end_time }}</h5>
                    <h5 v-if="detail.result">{{ detail.result }}</h5>
                    <h5 v-if="detail.status==5">退款金额：{{ detail.format_money }}</h5>
                    <h5 v-if="detail.status==5 && detail.integral">退款积分：{{ detail.integral }}积分</h5>
                </div>

                <ul style="margin-bottom: 10px;" v-if="detail.status==0">
                    <template v-if="detail.type == 0">
                        <li>如果未发货，请点击“同意退款”退款给买家</li>
                        <li>如果实际已发货，请主动与买家联系</li>
                        <li>如果您逾期未响应申请，视作同意买家申请，系统会自动退款给买家</li>
                    </template>
                    <template v-else>
                        <li>您同意退款后，买家将按照您给出的退货地址退货</li>
                        <li>如果您拒绝退款，买家可以修改退款申请后再次发起</li>
                        <li>如果您逾期未响应申请，视作同意买家申请，系统会自动退款给买家</li>
                    </template>
                </ul>

                <div class="address" style="margin-bottom: 5px;padding-top: 10px;"
                     v-if="(detail.status==2||detail.status==3)&&shopAddress">
                    <div class="s-flex ai-ct" style="padding-bottom: 5px;"><img
                        src="@/assets/images/refund/location-icon.png" alt=""><span>退货地址</span></div>
                    <p class="co-333 fs14">收货人：{{ shopAddress.consignee }}
                        &#12288;&#12288;&#12288;&#12288;{{ shopAddress.phone }}</p>
                    <p class="co-333 fs14">{{ shopAddress.province }}&#12288;{{ shopAddress.city }}&#12288;{{ shopAddress.district }}&#12288;{{ shopAddress.address }}</p>
                </div>

                <div class="btn" style="margin-bottom: 10px;">
                    <el-button type="danger" :loading="agreeBtnLoading" @click="agree" v-if="detail.status==0&&detail.type==0">同意退款</el-button>
                    <el-button type="danger" @click="agreeOpen" v-if="detail.status==0&&detail.type==1">同意退款</el-button>
                    <el-button type="info" :loading="refuseBtnLoading" @click="refuse(1)" v-if="detail.status==0&&detail.type==0">已发货</el-button>
                    <el-button type="info" @click="openRefuse" v-if="detail.status==0&&detail.type==1">拒绝退款</el-button>
                    <el-button type="danger" :loading="receiveBtnLoading" @click="receive" v-if="detail.status==3">确认收货</el-button>
                </div>

                <div class="fs16 co-333" style="padding: 20px 0 10px;" v-if="log&&log.length">协商记录</div>
                <div v-if="log&&log.length">
                    <div class="log-item s-flex" v-for="(item,i) in log" :key="`${i}log`">
                        <div class="img-box">
                            <img v-if="item.avatar" :src="item.avatar" alt="">
                            <img v-else src="@/assets/images/portait.jpeg" alt="">
                        </div>
                        <div class="flex-1">
                            <div class="s-flex jc-bt"><span>{{ item.user_name }}</span><span>{{ item.add_time }}</span></div>
                            <div>{{ item.action }}</div>
                            <div v-if="item.money">退款金额：{{ item.money }}</div>
                            <div v-if="item.integral">退款积分：{{ item.integral }}积分</div>
                            <div v-if="item.number">退款数量：{{ item.number }}</div>
                            <div v-if="item.reason">退款原因：{{ item.reason }}</div>
                            <div v-if="item.result">退款描述：{{ item.result }}</div>
                            <template v-if="item.certificate">
                                <div class="log-img" v-if="item.certificate">
                                    <el-image
                                        style="width: 87px; height: 87px" :src="c"
                                        v-for="c in item.certificate"
                                        :preview-src-list="item.certificate">
                                    </el-image>
                                </div>
                            </template>

                            <template v-if="item.applyRefundShip">
                                <div>物流公司：{{ item.applyRefundShip.ship_company.name }}</div>
                                <div>物流单号：{{ item.applyRefundShip.no }}</div>
                                <div>联系电话：{{ item.applyRefundShip.phone }}</div>
                                <div class="log-img" v-if="item.applyRefundShip.certificate">
                                    <el-image
                                        style="width: 87px; height: 87px" :src="c"
                                        v-for="c in item.applyRefundShip.certificate"
                                        :preview-src-list="item.applyRefundShip.certificate">
                                    </el-image>
                                </div>
                                <div v-if="item.applyRefundShip.description">
                                    补充描述：{{ item.applyRefundShip.description }}
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-right">
                <div style="padding: 0 10px;">退款详情</div>
                <div class="good s-flex">
                    <div class="good-img">
                        <img :src="detail.goods_image" v-if="detail.goods_image" alt="">
                    </div>
                    <div>
                        <div class="elli-2">
                            <span>{{ detail.goods_name }}</span>
                        </div>
                        <div>
                            <span style="font-size: 12px;color: #ccc">{{ detail.goods_attr }}</span>
                        </div>
                    </div>
                </div>
                <div class="order" style="padding: 0 10px;">
                    <p><span>买&#12288;&#12288;家：</span>{{ detail.buyer_name }}</p>
                    <p><span>订单编号：</span>{{ detail.order_sn }}</p>
                    <p><span>成交时间：</span>{{ detail.created_at }}</p>
                    <p><span>单&#12288;&#12288;价：</span>{{ detail.goods_price }}<template v-if="detail.goods_integral">+{{detail.goods_integral}}积分</template>*{{ detail.goods_number }}</p>
                    <p><span>商品总价：</span>{{ detail.goods_amount }}</p>
                    <div class="slide"></div>
                    <p v-if="detail.no"><span>退款编号：</span>{{ detail.no }}</p>
                    <p v-if="detail.format_money"><span>退款金额：</span>{{ detail.format_money }}</p>
                    <p v-if="detail.integral"><span>退款积分：</span>{{ detail.integral }}积分</p>
                    <p v-if="detail.number"><span>退款数量：</span>{{ detail.number }}</p>
                    <p v-if="detail.reason"><span>退款原因：</span>{{ detail.reason }}</p>
                    <div class="log-img" style="margin-bottom: 10px;"
                         v-if="detail.certificate&&detail.certificate.length">
                        <el-image
                            style="width: 87px; height: 87px" :src="c"
                            v-for="c in detail.certificate"
                            :preview-src-list="detail.certificate">
                        </el-image>
                    </div>
                    <p v-if="detail.description"><span>说&#12288;&#12288;明：</span>{{ detail.description }}</p>
                </div>
                <div class="ship" v-if="detail.isShipped">
                    <div class="co-333 fs14 title s-flex ai-ct" style="padding: 0 10px;" @click="openShip">
                        <span>物流跟踪</span>
                        <i class="iconfont icon-to_right" v-if="!showShip"></i>
                    </div>
                    <div v-if="showShip">
                        <div class="ship-title">
                            <span>物流单号：{{ shipCode }}</span>
                            <i class="iconfont icon-close2" @click="showShip = false"></i>
                        </div>
                        <div class="ship-wrap" v-loading="loadingSip">
                            <template v-if="!loadingSip">
                                <dl class="ship-track" v-if="shipList&&shipList.length">
                                    <dd v-for="(k,i) in shipList" :key="k">
                                        <p>{{ k.context }}</p>
                                        <h6>{{ k.time }}</h6>
                                    </dd>
                                </dl>
                                <p v-else class="text-center co-999">暂无物流信息</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <el-dialog title="拒绝退款" v-model="refuseVisible" width="600px">
            <el-form ref="refuseFormRef" :rules="reasonRule" :model="refuseForm">
                <el-form-item label="原因：" label-width="100px" prop="refuseReason">
                    <el-input v-model="refuseForm.refuseReason" autocomplete="off" type="textarea" rows="5"
                              maxlength="200" show-word-limit placeholder="拒绝退款原因"></el-input>
                </el-form-item>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="refuseVisible = false">取 消</el-button>
                <el-button type="primary" :loading="refuseBtnLoading" @click="refuse">确 定</el-button>
            </div>
        </el-dialog>
        <el-dialog title="同意退款" v-model="agreeVisible" width="600px">
            <p style="padding-bottom: 20px;">确定同意退款吗？</p>
            <el-form ref="agreeFormRef" :rules="agreeRule" :model="agreeForm">
                <el-form-item label="退款类型：" label-width="100px" prop="type">
                    <el-select v-model="agreeForm.type" style="width: 300px;">
                        <el-option :value="1" label="退货退款"></el-option>
                        <el-option :value="0" label="仅退款"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="退款金额：" label-width="100px" prop="price">
                    <el-input v-model="agreeForm.price" autocomplete="off" style="width: 300px;"
                              @paste.native.capture.prevent="inputPress" maxlength="10">
                        <template slot="prepend">￥</template>
                    </el-input>
                </el-form-item>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="agreeVisible = false">取 消</el-button>
                <el-button type="primary" :loading="agreeBtnLoading" @click="agree">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, getCurrentInstance, onMounted, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Http from '@/utils/http';
import { tabRemove } from '@/router/tabs';
import { useCommonStore } from '@/store'

const router = useRouter();
const route = useRoute();
const commonStore = useCommonStore()

const cns = getCurrentInstance().appContext.config.globalProperties;
const detailFormLoading = ref(false);

const showShip = ref(false);
const refuseVisible = ref(false);
const agreeVisible = ref(false);
const shopAddress = ref({});
const refuseForm = ref({
    refuseReason: ''
});
const agreeForm = ref({ // 同意退款form
    type: '', // 类型
    price: '', // 改价价格
    integral:''
});
const agreeFormRef = ref(null);
const refuseFormRef = ref(null);
const log = ref([]);
const detail = ref({});
const agreeBtnLoading = ref(false)
const refuseBtnLoading = ref(false)
const receiveBtnLoading = ref(false)
const status = ref({ // 状态配置
    0: '请处理退款申请',
    1: '卖家已拒绝退款',
    2: '等待买家退货',
    3: '买家退货',
    4: '卖家已收货',
    5: '退款成功',
    6: '退款关闭'
});
const reasonRule = ref({
    refuseReason: [
        { required: true, message: '请填写拒绝退款原因', trigger: 'blur' }
    ]
});
const validatePrice = (rules, value, callback) => {
    if (!value) {
        callback(new Error('请填写退款金额'));
    } else if (!/^\d+(\.\d{1,2})?$/.test(value)) {
        callback(new Error('退款金额只能输入数字,最多两位小数'));
    } else {
        callback();
    }
};
const agreeRule = ref({
    type: [
        { required: true, message: '请选择退款类型', trigger: 'change' }
    ],
    price: [
        { required: true, validator: validatePrice, trigger: 'blur' }
    ]
});
const shipList = ref([]);
const shipCode = ref('');
const loadingSip = ref(false);
const countdown_data = ref({
    day: 0,
    hour: 0,
    minute: 0,
    second: 0
});
const countdown_time = ref(null);
const end_time = ref(0);
const server_time = ref(0);

const getData = () => {
    detailFormLoading.value = true;
    Http.doGet('apply_refund/detail', { id: route.params.id }).then(res => {
        detailFormLoading.value = false;
        if (cns.$successCode(res.code)) {
            log.value = res.data.log;
            if (res.data.status == 0 || res.data.status == 3) { // 卖家待处理/待收货 显示倒计时
                /*倒计时*/
                if (res.data.time > 0) {
                    end_time.value = res.data.time;
                    server_time.value = res.data.server_time;
                    Countdown_new(end_time.value, server_time.value);
                    countdown_time.value = setInterval(() => {
                        if (end_time.value == server_time.value) {
                            clearInterval(countdown_time.value);
                            getData();
                        } else {
                            Countdown_new(end_time.value, server_time.value);
                            end_time.value--;
                        }
                    }, 1000);
                }
            }
            shopAddress.value = res.data.shopAddress;
            detail.value = res.data;
        } else {
            cns.$message.error(res.message);
            router.push({ name: 'manage.apply_refund.index' });
        }
    }).catch(() => {
        detailFormLoading.value = false;
        cns.$message.error('获取数据失败');
    });
};

const inputPress = () => {
    return false;
};

const openShip = () => {
    showShip.value = true;
    loadingSip.value = true;
    let params = {
        id: route.params.id
    };
    Http.doPost('apply_refund/query_express', params).then((res) => {
        loadingSip.value = false;
        if (res.code === 200) {
            shipList.value = res.data.ship_list;
            shipCode.value = res.data.ship_no;
        } else {
            cns.$message.error(res.message);
        }
    });
};

const Countdown_new = (val, nowTime) => {
    countdown_data.value.day = parseInt((val - nowTime) / 86400) > 0 ? Charall(parseInt((val - nowTime) / 86400)) : '00'
    countdown_data.value.hour = parseInt(((val - nowTime) % 86400) / 3600) > 0 ? Charall(parseInt((val - nowTime)% 86400 / 3600)) : '00'
    countdown_data.value.minute = parseInt((((val - nowTime) % 86400) % 3600) / 60) > 0 ? Charall(parseInt((((val - nowTime) % 86400) % 3600) / 60)) : '00'
    countdown_data.value.second = parseInt((((val - nowTime) % 86400) % 3600) % 60) > 0 ? Charall(parseInt((((val - nowTime) % 86400) % 3600) % 60)) : '00'
};

const Charall = (val) => {
    if (val < 10) {
        return '0' + val;
    } else {
        return val;
    }
};

const agreeOpen = () => {
    agreeVisible.value = true;
    nextTick(() => {
        agreeFormRef.value.clearValidate();
    });
    agreeForm.value.type = '';
    agreeForm.value.price = detail.value.money;
    agreeForm.value.integral = detail.value.integral
};

const agree = () => {
    if (agreeBtnLoading.value) {
        return;
    }
    agreeBtnLoading.value = true;
    if (detail.value.type == 0) { // 仅退款
        let params = {
            id: route.params.id,
            integral:detail.value.integral,
        };
        Http.doPost('apply_refund/execute_refund', params).then((res) => {
            agreeBtnLoading.value = false;
            if (cns.$successCode(res.code)) {
                cns.$message.success(res.message);
                getData();
                nextTick(() => {
                    router.replace({
                        name: 'manage.apply_refund.index'
                    });
                    tabRemove('manage.apply_refund.detail')
                });
            } else {
                getData();
                cns.$message.error(res.message);
            }
        });
    } else {
        agreeFormRef.value.validate((validated) => { // 退货退款
            if (validated) {
                if (agreeForm.value.type == 0) {
                    let params = {
                        id: route.params.id,
                        money: agreeForm.value.price,
                        integral:agreeForm.value.integral,
                    };
                    Http.doPost('apply_refund/execute_refund', params).then((res) => {
                        agreeBtnLoading.value = false;
                        if (cns.$successCode(res.code)) {
                            cns.$message.success(res.message);
                            getData();
                            agreeVisible.value = false;
                            nextTick(() => {
                                router.replace({
                                    name: 'manage.apply_refund.index'
                                });
                                tabRemove('manage.apply_refund.detail')
                            });
                        } else {
                            getData();
                            cns.$message.error(res.message);
                        }
                    });
                } else {
                    let params = {
                        id: route.params.id,
                        type: agreeForm.value.type,
                        money: agreeForm.value.price,
                        integral:agreeForm.value.integral,
                    };
                    Http.doPost('apply_refund/agree_apply', params).then((res) => {
                        agreeBtnLoading.value = false;
                        if (cns.$successCode(res.code)) {
                            cns.$message.success(res.message);
                            getData();
                            agreeVisible.value = false;
                        } else {
                            getData();
                            cns.$message.error(res.message);
                        }
                    });
                }
            } else {
                agreeBtnLoading.value = false;
            }
        });
    }
};

const openRefuse = () => {
    refuseVisible.value = true;
    refuseForm.value.refuseReason = '';
    nextTick(() => {
        refuseFormRef.value.clearValidate();
    });
};

const refuse = (e) => {
    if (refuseBtnLoading.value) {
        return;
    }
    refuseBtnLoading.value = true;
    if (e === 1) { // 已发货操作
        let params = {
            id: route.params.id
        };
        Http.doPost('apply_refund/close_apply', params).then((res) => {
            refuseBtnLoading.value = false
            if (cns.$successCode(res.code)) {
                cns.$message.success(res.message);
                getData();
            } else {
                getData();
                cns.$message.error(res.message);
            }
        });
    } else {
        refuseFormRef.value.validate((validated) => { // 拒绝退款操作
            if (validated) {
                let params = {
                    id: route.params.id,
                    result: refuseForm.value.refuseReason
                };
                Http.doPost('apply_refund/refuse_refund', params).then((res) => {
                    refuseBtnLoading.value = false
                    if (cns.$successCode(res.code)) {
                        cns.$message.success(res.message);
                        getData();
                        refuseVisible.value = false;
                    } else {
                        getData();
                        cns.$message.error(res.message);
                    }
                });

            }else{
                refuseBtnLoading.value = false
            }
        });
    }
};

const receive = () => {
    if (receiveBtnLoading.value) {
        return;
    }
    receiveBtnLoading.value = true;
    let params = {
        id: route.params.id
    };
    Http.doPost('apply_refund/confirm_receipt', params).then((res) => {
        receiveBtnLoading.value = false;
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
            getData();
            nextTick(() => {
                router.replace({
                    name: 'manage.apply_refund.index'
                });
                tabRemove('manage.apply_refund.detail')
            });
        } else {
            getData();
            cns.$message.error(res.message);
        }
    });
};

onMounted(() => {
    let title = '退款详情'
    if (route.query.no) {
        title = '退款详情-' + route.query.no;
        commonStore.updateVisitedViewsTitle(route, title);
    }
    getData();
});
</script>

<style scoped lang="scss">
.content-left {
    width: 800px;
    padding: 25px 30px;
    margin-right: 10px;
    border: 1px solid #EEEEEE;
    height: fit-content;
}

.content-left .address {
    padding: 0 0 20px;
    line-height: 24px;
}

.content-left .address > div img {
    width: 18px;
    height: 18px;
    margin-right: 12px;
    margin-bottom: 5px;
}

.content-left .el-button--danger {
    width: 160px;
    background: #E1251B;
    height: 40px;
}

.content-left .el-button--info {
    width: 160px;
    background: #FFF;
    height: 40px;
    border-color: #E1251B;
    color: #E1251B;
}

.content-left h5 {
    font-size: 14px;
    color: #777;
    font-weight: normal;
    padding: 2px 0 2px;
    word-break: break-all;
}

.content-left ul {
    padding: 0 20px 10px;
}

.content-left li {
    font-size: 14px;
    color: #999;
    font-weight: normal;
    padding: 5px 0;
    list-style: disc;
}

.content-left .log-item .img-box {
    width: 50px;
    height: 50px;
    background: #FFFFFF;
    border: 1px solid #E0E0E0;
    border-radius: 50%;
    margin-right: 20px;
    font-size: 0;
    overflow: hidden;
}

.content-left .log-item img {
    width: 50px;
    height: 50px;
}

.content-left .log-item .flex-1 div span {
    font-size: 12px;
    color: #333;
}

.content-left .log-item .flex-1 div {
    margin-bottom: 7px;
    font-size: 14px;
    color: #333;
    line-height: 22px;
    word-break: break-all;
}

.content-left .log-item {
    border-bottom: 1px solid #EEEEEE;
    padding-bottom: 10px;
    padding-top: 20px;
}

.content-left .log-item:last-of-type {
    border: none;
}

.content-left .status {
    font-size: 18px;
    color: #333;
    font-weight: bold;
}

.content-left .status span img {
    width: 15px;
    margin-left: 20px;
    margin-right: 5px;
}

.content-left .status span {
    font-size: 16px;
    color: #E1251B;
}

.log-img .el-image {
    margin-right: 10px;
    border: 1px solid #EEEEEE;
}

.content-right {
    width: 360px;
    padding: 20px 20px;
    border: 1px solid #EEEEEE;
    height: fit-content;
}

/*物流追踪样式*/
.content-right .ship {
    padding: 10px 0;
    cursor: pointer;
}

.content-right .ship .title i {
    font-size: 18px;
    font-weight: bold;
    margin: 0 5px;

}

.content-right .ship-wrap {
    max-height: 500px;
    min-height: 120px;
    overflow: auto;
    padding-top: 20px;
    padding-bottom: 20px;
    background: #FBFBFB;
}

.ship-track dt {
    display: flex;
    line-height: 78px;
    text-align: center;
    margin-bottom: 10px;
    border-bottom: 1px solid #e5e5e6;
}

.ship-track dd {
    //height: 70px;
    position: relative;
    margin-bottom: 10px;
}

.ship-track dd::before {
    content: '';
    width: 0;
    height: calc(100% - 20px);
    border-left: 1px solid #CCCCCC;
    position: absolute;
    left: 20px;
    top: 23px;
}

.ship-track dd h6 {
    display: block;
    width: 250px;
    padding: 0 20px;
    margin-left: 20px;
    font-size: 12px;
    color: #999;
    font-weight: normal;
}

.ship-track dd p {
    width: 250px;
    padding-left: 20px;
    margin-left: 20px;
    padding-bottom: 10px;
    color: #333;
    font-size: 12px;
    line-height: 20px;
    position: relative;
}

.ship-track dd p::before {
    content: '';
    width: 12px;
    height: 12px;
    border-radius: 100%;
    background: #FFFFFF;
    border: 1px solid #CCCCCC;
    position: absolute;
    left: -6px;
    top: 3px;
}

.ship-track :nth-of-type(1) p::before {
    content: '';
    width: 12px;
    height: 12px;
    background: #E1251B;
    border-color: #E1251B;
    border-radius: 100%;
    position: absolute;
    left: -6px;
    top: 3px;
}

.ship-track dd:nth-of-type(1) p {
    color: #333333;
}

.ship-track dd:last-child p {
    border-left: none;
}

.content-right .ship-title {
    display: flex;
    margin-top: 10px;
    justify-content: space-between;
    width: 320px;
    height: 40px;
    font-size: 12px;
    color: #333;
    background: #F8F8F8;
    line-height: 40px;
    padding: 0 14px;
    border: 1px solid #E6E6E6;
}

.content-right .ship-title i {
    font-size: 12px;
    font-weight: bold;
    color: #333;
}

.content-right .good {
    padding: 10px 10px;
}

.content-right .good .good-img {
    width: 60px;
    height: 60px;
    margin-right: 10px;
    border: 1px solid #EEEEEE;
    font-size: 0;
}

.content-right .good .good-img img {
    width: 58px;
    height: 58px;
}

.content-right .good .elli-2 {
    font-size: 12px;
    color: #333;
    line-height: 18px;
    height: 36px;
    overflow: hidden;
}

.content-right .order {
    font-size: 12px;
    color: #333;
}

.content-right .order p {
    padding-bottom: 8px;
    word-break: break-all;
    line-height: 1.5
}

.content-right .order span {
    color: #777777;
    display: inline-block;
    width: 5em;
    text-align: right;
}

.content-right .slide {
    padding-top: 20px;
    margin-bottom: 20px;
    border-bottom: 1px solid #EEEEEE;
}

.detail-wrap {
    padding: 20px 20px;
    background-color: #fff;
}

.step-wrap {
    max-width: 1190px;
    padding-top: 20px;
    margin-bottom: 20px;
}

.step-wrap .step {
    height: 40px;
    background: #E8E8E8;
    text-align: center;
    margin-right: 20px;
    line-height: 40px;
    color: #999;
    position: relative;
}

.step-wrap .step:after {
    content: '';
    width: 0;
    height: 0;
    position: absolute;
    border-style: solid;
    border-width: 20px 0 20px 10px;
    border-color: transparent transparent transparent #E8E8E8;
    right: -10px;
}

.step-wrap .step:before {
    content: '';
    width: 0;
    height: 0;
    position: absolute;
    border-style: solid;
    border-width: 20px 0 20px 10px;
    border-color: transparent transparent transparent #fff;
    left: 0;
}

.step-wrap .step span {
    display: inline-block;
    width: 18px;
    height: 18px;
    background: #CCCCCC;
    border-radius: 50%;
    line-height: 18px;
    font-size: 16px;
    color: #fff;
    margin-right: 7px;
}

.step-wrap .step.active {
    background: #FF8080;
    color: #FFFFFF;
}

.step-wrap .step.complete {
    background: #FFBABA;
    color: #FFFFFF;
}

.step-wrap .step.complete span, .step-wrap .step.active span {
    background: #fff;
    color: #FF8080;
}

.step-wrap .step.active:after {
    content: '';
    border-color: transparent transparent transparent #FF8080;
}

.step-wrap .step.complete:after {
    content: '';
    border-color: transparent transparent transparent #FFBABA;
}

.step-wrap .step:last-of-type:after {
    display: none;
}

.step-wrap .step:first-of-type:before {
    display: none;
}
</style>
