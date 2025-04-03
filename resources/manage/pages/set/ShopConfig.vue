<script setup>
import { Plus, Delete } from '@element-plus/icons-vue'
import { shopConfigIndex, shopConfigUpdate } from '@/api/set.js';
import { fileUpload } from '@/api/common.js';
import { ref, reactive, onMounted, computed, getCurrentInstance } from 'vue';
const cns = getCurrentInstance().appContext.config.globalProperties

const firstActiveName = ref('site_setup')
const secondActiveName = ref('site_info')
let inputFrom = reactive({});
const tabPosition = ref('left');
const loading = ref(false);
const inputFromRef = ref(null);

const tab_label = computed(() => {
    switch (secondActiveName.value) {
        case 'site_info':
            return '站点信息';
        case 'site_logo':
            return '站点logo';
        case 'smtp_service':
            return '邮件服务';
        case 'group_integral':
            return '积分设置';
        case 'group_search':
            return '搜索设置';
        case 'group_goods':
            return '商品设置';
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
    shopConfigIndex({group_name:group_name}).then(res => {
        if (cns.$isSuccCode(res.code)) {
            Object.assign(inputFrom, res.data);
        } else {
            cns.$message.error(res.message);
        }
    });
};

const uploadFile = async (request, type) => {
    try {
        const res = await fileUpload({ file: request.file });
        if (cns.$isSuccCode(res.code)) {
            inputFrom[type] = res.data.url;
        } else {
            cns.$message.error(res.message)
        }
    } catch (error) {
        console.error('Failed:', error);
    }
};

const handleRemoveOne = (type) => {
    inputFrom[type] = '';
};

// 提交表单
const submitForm = () => {
    inputFromRef.value.validate((valid) => {
        if (valid) {
            inputFrom.title = secondActiveName.value;
            inputFrom.tab_label = tab_label;
            loading.value = true;
            shopConfigUpdate(inputFrom).then(res => {
                if (cns.$isSuccCode(res.code)) {
                    cns.$message.success('提交成功');
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
    <div class="shopConfig-wrap">
        <el-main>
            <el-tabs v-model="firstActiveName" type="card" @tab-click="firstHandleClick" class="shopConfig-tab">
                <el-tab-pane label="站点设置" name="site_setup">
                    <el-tabs v-model="secondActiveName" :tab-position="tabPosition" type="card" @tab-click="secondHandleClick" class="childConfig-tab">
                        <el-tab-pane label="站点信息" name="site_info">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="240px" class="demo-compactForm">
                                <div style="margin:0 auto 0 50px;width: 550px">
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
                                        <el-button type="primary" @click="submitForm()" :class="{disable:loading}" :loading="loading">提交</el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="站点logo" name="site_logo">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="240px" class="demo-compactForm">
                                <div style="margin:0 auto 0 50px;width: 550px;">
                                    <el-card class="box-card" style="margin-bottom: 30px;">
                                        <div slot="header" class="clearfix">
                                            <span style="font-size:20px;">网站logo</span>
                                        </div>
                                        <el-form-item label="网站logo：" prop="shop_logo">
                                            <div v-if="inputFrom.shop_logo" class="logo-uploader-preview">
                                                <img :src="inputFrom.shop_logo" class="logo" alt=""/>
                                                <el-icon class="logo-uploader-icon logo-uploader-icon-delete">
                                                    <Delete @click="handleRemoveOne('shop_logo')"/>
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
                                            <span style="width: 100%"><small>平台完整的logo，推荐尺寸280*100</small></span>
                                        </el-form-item>

                                        <el-form-item label="站点图标：" prop="shop_icon">
                                            <div v-if="inputFrom.shop_icon" class="logo-uploader-preview">
                                                <img :src="inputFrom.shop_icon" class="logo" alt=""/>
                                                <el-icon class="logo-uploader-icon logo-uploader-icon-delete">
                                                    <Delete @click="handleRemoveOne('shop_icon')"/>
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
                                                <img :src="inputFrom.shop_manage_login_image" class="logo" alt=""/>
                                                <el-icon class="logo-uploader-icon logo-uploader-icon-delete">
                                                    <Delete @click="handleRemoveOne('shop_manage_login_image')"/>
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
                                    <el-button style="margin-top: 20px;" type="primary" @click="submitForm('inputFrom')" :class="{disable:loading}" :loading="loading">提交</el-button>
                                </el-form-item>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="积分设置" name="group_integral">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="240px" class="demo-compactForm">
                                <div style="margin:0 auto 0 50px;width: 550px">
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
                                        <el-input v-model="inputFrom.integral_name" placeholder="请输入积分名称" show-word-limit maxlength="10"></el-input>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-button type="primary" @click="submitForm()" :class="{disable:loading}" :loading="loading">提交</el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="搜索设置" name="group_search">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="240px" class="demo-compactForm">
                                <div style="margin:0 auto 0 50px;width: 550px">
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
                                        <el-button type="primary" @click="submitForm()" :class="{disable:loading}" :loading="loading">提交</el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="商品设置" name="group_goods">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="240px" class="demo-compactForm">
                                <div style="margin:0 auto 0 50px;width: 550px">
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
                                        <el-button type="primary" @click="submitForm()" :class="{disable:loading}" :loading="loading">提交</el-button>
                                    </el-form-item>
                                </div>
                            </el-form>
                        </el-tab-pane>
                    </el-tabs>
                </el-tab-pane>
                <el-tab-pane label="系统对接" name="system_docking">
                    <el-tabs v-model="secondActiveName" :tab-position="tabPosition" type="card" @tab-click="secondHandleClick">
                        <el-tab-pane label="邮件服务" name="smtp_service">
                            <el-form :model="inputFrom" ref="inputFromRef" label-width="240px" class="demo-compactForm">
                                <div style="margin:0 auto 0 100px;width: 1000px">
                                    <el-form-item label="发送邮件服务器地址(SMTP)：" prop="smtp_host">
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
                                        <el-button type="primary" @click="submitForm()" :class="{disable:loading}" :loading="loading">提交</el-button>
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
.shopConfig-wrap{
    background: #ffffff;
    border-radius: 4px;
    :deep(.shopConfig-tab){
        width: 800px;
    }
    :deep(.shopConfig-tab>.el-tabs__header){
        background: #F5F7FA;
        border: 1px solid #E4E7ED;
    }
    :deep(.shopConfig-tab>.el-tabs__header .el-tabs__item){
        border: none!important;
    }
    :deep(.shopConfig-tab>.el-tabs__header .el-tabs__item.is-active){
        background: #ffffff;
    }
    :deep(.shopConfig-tab>.el-tabs__header .el-tabs__nav){
        border: none;
    }
    :deep(.el-tabs--left.el-tabs--card .el-tabs__nav){
        border-bottom: none;
    }
    :deep(.childConfig-tab .el-tabs__nav){
        width: 175px;
    }
    :deep(.childConfig-tab .el-tabs__nav .el-tabs__item){
        justify-content: center;
    }
}
    :deep(.logo-uploader .el-upload){
        border: none;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: var(--el-transition-duration-fast);
    }
    :deep(.logo-uploader .logo-uploader-icon){
        font-size: 28px;
        color: #8c939d;
        width: 80px;
        height: 80px;
        text-align: center;
    }
    :deep(.logo-uploader .logo-uploader-icon.logo-uploader-icon-plus){
        border: 1px dashed #dcdfe6;
    }
    :deep(.logo-uploader-preview){
        width: 80px;
        height: 80px;
        border-radius: 6px;
        position: relative;
    }
    :deep(.logo-uploader-preview .logo){
        max-width: 80px;
        max-height: 80px;
        width: auto;
        height: auto;
    }
    :deep(.logo-uploader-preview .logo-uploader-icon.logo-uploader-icon-delete){
        position: absolute;
        display: none;
        background: rgba(0, 0, 0, 0.5);
        color: #ffffff;
        top: 0;
        left: 0;
        cursor: pointer;
    }
    :deep(.logo-uploader-preview:hover){
        .logo-uploader-icon-delete{
            display: flex;
        }
    }
    :deep(.el-table__row .cell){
        display: flex;
        align-items: center;
    }
</style>
