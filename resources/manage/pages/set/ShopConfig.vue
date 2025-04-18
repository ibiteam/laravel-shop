<script setup>
import { Plus, Delete } from '@element-plus/icons-vue';
import { shopConfigIndex, shopConfigUpdate, shopConfigSearchArticle } from '@/api/set.js';
import { fileUpload } from '@/api/common.js';
import { ref, reactive, onMounted, computed, getCurrentInstance } from 'vue';
import { getConfigAxios } from '@/api/home.js';

const cns = getCurrentInstance().appContext.config.globalProperties;
import { useCommonStore } from '@/store';

const commonStore = useCommonStore();

const firstActiveName = ref('site_setup');
const secondActiveName = ref('site_info');
let inputFrom = reactive({});
const tabPosition = ref('left');
const loading = ref(false);
const inputFromRef = ref(null);
const userAgreementData = ref([]);
const userCancelAgreementData = ref([]);
const privacyPolicyData = ref([]);
const aboutUsData = ref([]);


const tab_label = computed(() => {
    switch (secondActiveName.value) {
        case 'site_info':
            return '站点信息';
        case 'site_logo':
            return '站点Logo';
        case 'smtp_service':
            return '邮件服务';
        case 'group_integral':
            return '积分设置';
        case 'group_search':
            return '搜索设置';
        case 'group_goods':
            return '商品设置';
        case 'group_refund_after_sales':
            return '退款售后';
        case 'group_articles':
            return '文章设置';
        default:
            return '';
    }
});

const firstHandleClick = (tab, event) => {
    if (firstActiveName.value === 'site_setup') {
        secondActiveName.value = 'site_info';
    } else if (firstActiveName.value === 'system_docking') {
        secondActiveName.value = 'smtp_service';
    }
    secondHandleClick(tab, event);
};

const secondHandleClick = (tab, event) => {
    inputFrom = reactive({});
    setInfo(secondActiveName.value);
};

const setInfo = (group_name) => {
    shopConfigIndex({ group_name: group_name }).then(res => {
        if (cns.$successCode(res.code)) {
            Object.assign(inputFrom, res.data.configs);

            if (group_name === 'group_articles' && res.data.group_data) {
                // 文章设置
                userAgreementData.value = res.data.group_data.user_agreement;
                userCancelAgreementData.value = res.data.group_data.user_cancel_agreement;
                privacyPolicyData.value = res.data.group_data.privacy_policy;
                aboutUsData.value = res.data.group_data.about_us;
            }
        } else {
            cns.$message.error(res.message);
        }
    });
};

const uploadFile = async (request, type) => {
    try {
        const res = await fileUpload({ file: request.file });
        if (cns.$successCode(res.code)) {
            inputFrom[type] = res.data.url;
        } else {
            cns.$message.error(res.message);
        }
    } catch (error) {
        console.error('Failed:', error);
    }
};

const handleRemoveOne = (type) => {
    inputFrom[type] = '';
};

const remoteSearchArticle = async (query, type) =>{
    if (query !== '') {
        try {
            const res = await shopConfigSearchArticle({ keywords: query });
            if (cns.$successCode(res.code)) {
                if (type === 'user_agreement') {
                    userAgreementData.value = res.data;
                }
                if (type === 'user_cancel_agreement') {
                    userCancelAgreementData.value = res.data;
                }
                if (type === 'privacy_policy') {
                    privacyPolicyData.value = res.data;
                }
                if (type === 'about_us') {
                    aboutUsData.value = res.data;
                }
            }
        } catch (error) {
            console.error('Failed:', error);
        }
    }
};

// 提交表单
const submitForm = () => {
    inputFromRef.value.validate((valid) => {
        if (valid) {
            inputFrom.title = secondActiveName.value;
            inputFrom.tab_label = tab_label;
            loading.value = true;
            shopConfigUpdate(inputFrom).then(res => {
                if (cns.$successCode(res.code)) {
                    cns.$message.success('提交成功');
                    getConfigAxios().then(ret => {
                        if (cns.$successCode(ret.code)) {
                            commonStore.updateShopConfig(ret.data.shop_config);
                            commonStore.updateAdminUser(ret.data.admin_user);
                            const root = document.documentElement;
                            root.style.setProperty('--manage-color', ret.data.shop_config.manage_color);
                            root.style.setProperty('--manage-color-over', ret.data.shop_config.mouse_move_color);
                        } else {
                            cns.$message.error(ret.message);
                        }
                    });
                } else {
                    cns.$message.error(res.message);
                }
                loading.value = false;
            }).catch(err => {
                cns.$message.error('提交异常');
                loading.value = false;
            });
        } else {
            cns.$message.error('表单验证失败');
            return false;
        }
    });
};

onMounted(() => {
    firstHandleClick();
});
</script>

<template>
    <div class="common-wrap shopConfig-wrap">
        <el-main>
            <el-tabs v-model="firstActiveName" type="border-card" @tab-click="firstHandleClick" class="shopConfig-tab">
                <el-tab-pane label="站点设置" name="site_setup">
                    <el-tabs v-model="secondActiveName" :tab-position="tabPosition" type="card"
                             @tab-click="secondHandleClick" class="childConfig-tab">
                        <el-tab-pane label="站点信息" name="site_info">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="180px">
                                <div style="margin:0 auto 0 50px;width: 800px">
                                    <el-form-item label="站点名称：" prop="shop_name">
                                        <el-input v-model="inputFrom.shop_name" placeholder="站点名称"></el-input>
                                    </el-form-item>
                                    <el-form-item label="公司名称：" prop="bank_account">
                                        <el-input v-model="inputFrom.bank_account" placeholder="公司名称"></el-input>
                                    </el-form-item>
                                    <el-form-item label="公司地址：" prop="shop_address">
                                        <el-input v-model="inputFrom.shop_address" placeholder="公司地址"></el-input>
                                    </el-form-item>
                                    <el-form-item label="客服热线：" prop="service_mobile">
                                        <el-input v-model="inputFrom.service_mobile" placeholder="客服热线"></el-input>
                                    </el-form-item>
                                    <el-form-item label="ICP备案号：" prop="icp_number">
                                        <el-input v-model="inputFrom.icp_number" placeholder="ICP备案号"></el-input>
                                    </el-form-item>
                                    <el-form-item label="站点主题色：" prop="shop_color">
                                        <el-color-picker v-model="inputFrom.shop_color"></el-color-picker>
                                    </el-form-item>
                                    <el-form-item label="总后台主题色：" prop="manage_color">
                                        <el-color-picker v-model="inputFrom.manage_color"></el-color-picker>
                                    </el-form-item>
                                    <el-form-item label="鼠标移入背景色：" prop="mouse_move_color">
                                        <el-color-picker v-model="inputFrom.mouse_move_color"></el-color-picker>
                                    </el-form-item>
                                    <el-form-item label="网站首页置灰：" prop="is_gray">
                                        <el-switch
                                            v-model="inputFrom.is_gray"
                                            active-color="#13ce66" inactive-color="#EBE9E9"
                                            :active-value="'1'" :inactive-value="'0'"
                                        ></el-switch>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-button type="primary" :class="{disable:loading}" :loading="loading"
                                                   @click="submitForm()">提交
                                        </el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="站点Logo" name="site_logo">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="180px">
                                <div style="margin:0 auto 0 50px;width: 800px;">
                                    <el-card class="box-card" style="margin-bottom: 30px;">
                                        <div slot="header" class="clearfix">
                                            <span style="font-size:20px;">网站logo</span>
                                        </div>
                                        <el-form-item label="网站logo：" prop="shop_logo">
                                            <div v-if="inputFrom.shop_logo" class="logo-uploader-preview">
                                                <img :src="inputFrom.shop_logo" class="logo" alt="" />
                                                <el-icon class="logo-uploader-icon logo-uploader-icon-delete">
                                                    <Delete @click="handleRemoveOne('shop_logo')" />
                                                </el-icon>
                                            </div>
                                            <el-upload
                                                class="logo-uploader"
                                                accept="image/jpeg,image/jpg,image/png"
                                                action=""
                                                :show-file-list="false"
                                                :http-request="(request) => uploadFile(request, 'shop_logo')"
                                                :with-credentials="true"
                                                v-else
                                            >
                                                <el-icon class="logo-uploader-icon logo-uploader-icon-plus">
                                                    <Plus />
                                                </el-icon>
                                            </el-upload>
                                            <span style="width: 100%"><small>平台网站logo，推荐尺寸280*100</small></span>
                                        </el-form-item>

                                        <el-form-item label="站点图标：" prop="shop_icon">
                                            <div v-if="inputFrom.shop_icon" class="logo-uploader-preview">
                                                <img :src="inputFrom.shop_icon" class="logo" alt="" />
                                                <el-icon class="logo-uploader-icon logo-uploader-icon-delete">
                                                    <Delete @click="handleRemoveOne('shop_icon')" />
                                                </el-icon>
                                            </div>
                                            <el-upload
                                                class="logo-uploader"
                                                accept="image/jpeg,image/jpg,image/png"
                                                action=""
                                                :show-file-list="false"
                                                :http-request="(request) => uploadFile(request, 'shop_icon')"
                                                :with-credentials="true"
                                                v-else
                                            >
                                                <el-icon class="logo-uploader-icon logo-uploader-icon-plus">
                                                    <Plus />
                                                </el-icon>
                                            </el-upload>
                                            <span style="width: 100%"><small>文件后缀.ico，推荐尺寸：16*16</small></span>
                                        </el-form-item>
                                    </el-card>
                                    <el-card class="box-card" style="">
                                        <div slot="header" class="clearfix">
                                            <span style="font-size:20px;">背景图</span>
                                        </div>
                                        <el-form-item label="后台登页背景：" prop="shop_manage_login_image">
                                            <div v-if="inputFrom.shop_manage_login_image" class="logo-uploader-preview">
                                                <img :src="inputFrom.shop_manage_login_image" class="logo" alt="" />
                                                <el-icon class="logo-uploader-icon logo-uploader-icon-delete">
                                                    <Delete @click="handleRemoveOne('shop_manage_login_image')" />
                                                </el-icon>
                                            </div>
                                            <el-upload
                                                class="logo-uploader"
                                                accept="image/jpeg,image/jpg,image/png"
                                                action=""
                                                :show-file-list="false"
                                                :http-request="(request) => uploadFile(request, 'shop_manage_login_image')"
                                                :with-credentials="true"
                                                v-else
                                            >
                                                <el-icon class="logo-uploader-icon logo-uploader-icon-plus">
                                                    <Plus />
                                                </el-icon>
                                            </el-upload>
                                            <span style="width: 100%"><small>推荐尺寸420*560</small></span>
                                        </el-form-item>
                                    </el-card>
                                </div>
                                <el-form-item>
                                    <el-button type="primary" :class="{disable:loading}" :loading="loading"
                                               style="margin-top: 20px;" @click="submitForm()">提交
                                    </el-button>
                                </el-form-item>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="积分设置" name="group_integral">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="180px">
                                <div style="margin:0 auto 0 50px;width: 800px">
                                    <el-form-item label="是否开启积分：" prop="is_open_integral">
                                        <el-switch
                                            v-model="inputFrom.is_open_integral"
                                            active-color="#13ce66"
                                            inactive-color="#EBE9E9"
                                            :active-value="'1'"
                                            :inactive-value="'0'"
                                        ></el-switch>
                                        <span class="co-999" style="width: 100%"><small>商品添加时是否展示积分</small></span>
                                    </el-form-item>
                                    <el-form-item label="积分名称：" prop="integral_name">
                                        <el-input v-model="inputFrom.integral_name" placeholder="请输入积分名称"
                                                  show-word-limit maxlength="10"></el-input>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-button type="primary" :class="{disable:loading}" :loading="loading"
                                                   @click="submitForm()">提交
                                        </el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="搜索设置" name="group_search">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="180px">
                                <div style="margin:0 auto 0 50px;width: 800px">
                                    <el-form-item label="搜索方式：" prop="search_driver">
                                        <el-radio v-model="inputFrom.search_driver" label="1">数据库</el-radio>
                                        <el-radio v-model="inputFrom.search_driver" label="2">MeiliSearch</el-radio>
                                    </el-form-item>
                                    <!--<el-form-item label="请求地址：" prop="search_host">
                                        <el-input v-model="inputFrom.search_host" placeholder="请求地址"></el-input>
                                    </el-form-item>
                                    <el-form-item label="请求密钥：" prop="search_key">
                                        <el-input v-model="inputFrom.search_key" placeholder="请求密钥"></el-input>
                                    </el-form-item>-->
                                    <el-form-item>
                                        <el-button type="primary" :class="{disable:loading}" :loading="loading"
                                                   @click="submitForm()">提交
                                        </el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="商品设置" name="group_goods">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="180px">
                                <div style="margin:0 auto 0 50px;width: 800px">
                                    <el-form-item label="是否展示销量：" prop="is_show_sales_volume">
                                        <el-switch
                                            v-model="inputFrom.is_show_sales_volume"
                                            active-color="#13ce66"
                                            inactive-color="#EBE9E9"
                                            :active-value="'1'"
                                            :inactive-value="'0'"
                                        ></el-switch>
                                        <span class="co-999" style="width: 100%"><small>商品展示列表是否显示销量</small></span>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-button type="primary" :class="{disable:loading}" :loading="loading"
                                                   @click="submitForm()">提交
                                        </el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="退款售后" name="group_refund_after_sales">
                            <el-form :model="inputFrom" ref="inputFromRef">
                                <div style="margin:0 auto 0 50px;" >
                                    <div style="padding: 10px 20px; margin-bottom: 30px; max-width: 1100px; background: #f6e8d5;color: #666;">
                                        <p>注：</p>
                                        <p>
                                            用户提交售后申请后需要商家处理退款订单，请提前协商好填写，核实之前需要注意3个时间点的设定，核实后将不能更改。 此设定防止买家来不及付款而导致订单过期自动取消；给自己预留足够时间放货，防止买家在放货时间到达后没有货权发生纠纷；同时预留买家足够的时间来确认货权。
                                        </p>
                                    </div>
                                    <div class="refund-item">
                                        卖家发货：未发货状态下，如买家申请退款，但卖家实际已发货，卖家须在买家申请退款之时起
                                        <el-input v-model="inputFrom.seller_shipped_time"></el-input>
                                        小时内，点击“已发货”按钮，超时未点击，交易流程正常进行。
                                    </div>
                                    <div class="refund-item">
                                        卖家处理：卖家自退款提交之日起
                                        <el-input v-model="inputFrom.seller_confirm_time"></el-input>
                                        小时内，不响应退款申请的，默认达成退款申请，按退款申请中的金额退款给买家。
                                    </div>
                                    <div class="refund-item">
                                        买家修改申请：买家在卖家拒绝退款后
                                        <el-input v-model="inputFrom.buyer_change_time"></el-input>
                                        小时内，未操作修改申请，退款流程关闭，交易正常进行。
                                    </div>
                                    <div class="refund-item">
                                        买家退货：卖家同意退货退款后，买家须在
                                        <el-input v-model="inputFrom.buyer_refund_time"></el-input>
                                        天内上传物流信息，否则退款流程关闭，交易正常进行。
                                    </div>
                                    <div class="refund-item">
                                        卖家收货：买家提交物流信息后，卖家须在
                                        <el-input v-model="inputFrom.seller_receive_time"></el-input>
                                        天内确认收货，卖家超时未确认收货的，默认卖家收到买家退货且无异议，按退款中申请的约定直接退款给买家。
                                    </div>
                                    <div class="refund-item">
                                        售后时效：买家自收货后
                                        <el-input v-model="inputFrom.after_sales_timeliness"></el-input>
                                        天内将超过申请售后最长时效，超时将不能申请。
                                    </div>
                                    <div class="refund-item">
                                        退款限制：买家申请售后支持的最大退款金额
                                        <el-input v-model="inputFrom.after_sales_max_money"></el-input>
                                        元
                                    </div>
                                    <div>
                                        <el-button type="primary" :class="{disable:loading}" :loading="loading"
                                                   @click="submitForm()">提交
                                        </el-button>
                                    </div>
                                </div>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="文章设置" name="group_articles">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="180px">
                                <div style="margin:0 auto 0 50px;width: 800px">
                                    <el-form-item label="用户协议：" prop="user_agreement">
                                        <el-select
                                            v-model="inputFrom.user_agreement" placeholder="请输入文章Id|文章标题"
                                            clearable @clear="handleRemoveOne('user_agreement')"
                                            filterable remote reserve-keyword
                                            :remote-method="(e) => remoteSearchArticle(e, 'user_agreement')">
                                            <el-option
                                                v-for="item in userAgreementData"
                                                :key="item.value" :label="item.label" :value="item.value">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item label="用户注销协议：" prop="user_cancel_agreement">
                                        <el-select
                                            v-model="inputFrom.user_cancel_agreement" placeholder="请输入文章Id|文章标题"
                                            clearable @clear="handleRemoveOne('user_cancel_agreement')"
                                            filterable remote reserve-keyword
                                            :remote-method="(e) => remoteSearchArticle(e, 'user_cancel_agreement')">
                                            <el-option
                                                v-for="item in userCancelAgreementData"
                                                :key="item.value" :label="item.label" :value="item.value">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item label="隐私政策：" prop="privacy_policy">
                                        <el-select
                                            v-model="inputFrom.privacy_policy" placeholder="请输入文章Id|文章标题"
                                            clearable @clear="handleRemoveOne('privacy_policy')"
                                            filterable remote reserve-keyword
                                            :remote-method="(e) => remoteSearchArticle(e, 'privacy_policy')">
                                            <el-option
                                                v-for="item in privacyPolicyData"
                                                :key="item.value" :label="item.label" :value="item.value">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item label="关于我们：" prop="about_us">
                                        <el-select
                                            v-model="inputFrom.about_us" placeholder="请输入文章Id|文章标题"
                                            clearable @clear="handleRemoveOne('about_us')"
                                            filterable remote reserve-keyword
                                            :remote-method="(e) => remoteSearchArticle(e, 'about_us')">
                                            <el-option
                                                v-for="item in aboutUsData"
                                                :key="item.value" :label="item.label" :value="item.value">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-button type="primary" :class="{disable:loading}" :loading="loading"
                                                   @click="submitForm(inputFrom)">提交
                                        </el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                    </el-tabs>
                </el-tab-pane>
                <el-tab-pane label="系统对接" name="system_docking">
                    <el-tabs v-model="secondActiveName" :tab-position="tabPosition" type="card"
                             @tab-click="secondHandleClick" class="childConfig-tab">
                        <el-tab-pane label="邮件服务" name="smtp_service">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="180px">
                                <div style="margin:0 auto 0 50px;width: 800px">
                                    <el-form-item label="服务器地址(SMTP)：" prop="smtp_host">
                                        <el-input v-model="inputFrom.smtp_host"></el-input>
                                    </el-form-item>
                                    <el-form-item label="服务器端口：" prop="smtp_port">
                                        <el-input v-model="inputFrom.smtp_port"></el-input>
                                    </el-form-item>
                                    <el-form-item label="邮件账号：" prop="smtp_user">
                                        <el-input v-model="inputFrom.smtp_user"></el-input>
                                    </el-form-item>
                                    <el-form-item label="邮件密码：" prop="smtp_pass">
                                        <el-input v-model="inputFrom.smtp_pass" type="password"></el-input>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-button type="primary" :class="{disable:loading}" :loading="loading"
                                                   @click="submitForm()">提交
                                        </el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                    </el-tabs>
                </el-tab-pane>
            </el-tabs>
        </el-main>
    </div>
</template>

<style scoped lang="scss">
.shopConfig-wrap {
    :deep(.el-tabs--left.el-tabs--card .el-tabs__item.is-left.is-active){
        border-right: 1px solid var(--el-border-color-light);
    }
    :deep(.shopConfig-tab) {
        width: 1300px;
    }

    :deep(.shopConfig-tab>.el-tabs__header) {
        background: #F5F7FA;
        border: 1px solid #E4E7ED;
    }

    :deep(.shopConfig-tab>.el-tabs__header .el-tabs__item) {
        border: none !important;
    }

    :deep(.shopConfig-tab>.el-tabs__header .el-tabs__item.is-active) {
        background: #ffffff;
    }

    :deep(.shopConfig-tab>.el-tabs__header .el-tabs__nav) {
        border: none;
    }

    :deep(.el-tabs--left.el-tabs--card .el-tabs__nav) {
        border-bottom: none;
    }

    :deep(.childConfig-tab .el-tabs__nav) {
        width: 150px;
    }

    :deep(.childConfig-tab .el-tabs__nav .el-tabs__item) {
        justify-content: center;
    }
}

:deep(.logo-uploader .el-upload) {
    border: none;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: var(--el-transition-duration-fast);
}

:deep(.logo-uploader .logo-uploader-icon) {
    font-size: 28px;
    color: #8c939d;
    width: 80px;
    height: 80px;
    text-align: center;
}

:deep(.logo-uploader .logo-uploader-icon.logo-uploader-icon-plus) {
    border: 1px dashed #dcdfe6;
}

:deep(.logo-uploader-preview) {
    width: 80px;
    height: 80px;
    border-radius: 6px;
    position: relative;
}

:deep(.logo-uploader-preview .logo) {
    max-width: 80px;
    max-height: 80px;
    width: auto;
    height: auto;
}

:deep(.logo-uploader-preview .logo-uploader-icon.logo-uploader-icon-delete) {
    position: absolute;
    display: none;
    background: rgba(0, 0, 0, 0.5);
    color: #ffffff;
    top: 0;
    left: 0;
    cursor: pointer;
}

:deep(.logo-uploader-preview:hover) {
    .logo-uploader-icon-delete {
        display: flex;
    }
}

:deep(.el-table__row .cell) {
    display: flex;
    align-items: center;
}

:deep(.refund-item) {
    margin-bottom: 20px;
}

:deep(.refund-item .el-input) {
    width: 60px;
    margin: 0 5px;
}

:deep(.refund-item .el-input .el-input__inner) {
    text-align: center;
}
</style>
