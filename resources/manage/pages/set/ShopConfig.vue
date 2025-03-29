<script setup>
import { Plus, Delete } from '@element-plus/icons-vue'
import { siteInfo, siteLogo, shopConfigUpdate } from '@/api/shopConfig.js'
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
        case 'logo_icon':
            return '站点logo';
        default:
            return '';
    }
});

const firstHandleClick = (tab, event) => {
    if (firstActiveName.value === 'site_setup') {
        secondActiveName.value = 'site_info';
    } else if (firstActiveName.value === 'system_docking') {
        secondActiveName.value = 'smtp';
    }
    secondHandleClick(tab, event);
};

const secondHandleClick = (tab, event) => {
    inputFrom = reactive({});
    setInfo(secondActiveName.value);
};

const setInfo = (type) => {
    let fetchFunction;
    switch (type) {
        case 'site_info':
            fetchFunction = siteInfo;
            break;
        case 'logo_icon':
            fetchFunction = siteLogo;
            break;
        default:
            fetchFunction = () => Promise.resolve({ code: 200, data: {} });
    }
    fetchFunction().then(res => {
        if (res.code === 200) {
            Object.assign(inputFrom, res.data);
        } else {
            cns.$message.error(res.message);
        }
    });
};

const uploadFile = async (request, type) => {
    try {
        const res = await fileUpload({ file: request.file });

        if (res.code === 200) {
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
                if (res.code === 200) {
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
    <el-main style="margin-top: 20px;">
        <el-tabs v-model="firstActiveName" type="card" @tab-click="firstHandleClick">
            <el-tab-pane label="站点设置" name="site_setup">
                <el-tabs v-model="secondActiveName" :tab-position="tabPosition" type="card" @tab-click="secondHandleClick">
                    <el-tab-pane label="站点信息" name="site_info">
                        <el-form :model="inputFrom" ref="inputFromRef" label-width="240px" class="demo-compactForm">
                            <div style="margin:0 auto 0 100px;width: 800px">
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
                                <el-form-item label="网站首页置灰：" prop="is_gray">
                                    <el-switch
                                        v-model="inputFrom.is_gray"
                                        active-color="#13ce66" inactive-color="#EBE9E9"
                                        :active-value="'1'" :inactive-value="'0'"
                                    ></el-switch>
                                </el-form-item>
                                <el-form-item>
                                    <el-button type="primary" @click="submitForm()" :class="{disable:loading}" class="contract_sub " :loading="loading">提交</el-button>
                                </el-form-item>
                            </div>
                        </el-form>
                    </el-tab-pane>
                    <el-tab-pane label="站点logo" name="logo_icon">
                        <el-form :model="inputFrom" ref="inputFromRef" label-width="240px" class="demo-compactForm">
                            <div style="margin:0 auto 0 100px;width: 800px;">
                                <el-card class="box-card" style="margin-bottom: 30px;">
                                    <div slot="header" class="clearfix">
                                        <span style="font-size:20px;">网站logo</span>
                                    </div>
                                    <el-form-item label="网站logo：" prop="shop_logo">
                                        <div v-if="!inputFrom.shop_logo">
                                            <el-upload
                                                class="one-file-upload"
                                                accept="image/jpeg,image/jpg,image/png"
                                                :show-file-list="false"
                                                list-type="picture-card"
                                                action=""
                                                :http-request="(request) => uploadFile(request, 'shop_logo')"
                                                :with-credentials="true"
                                            >
                                                <div class="icon-button">
                                                    <i class="el-icon-plus"></i>
                                                </div>
                                            </el-upload>
                                        </div>
                                        <div v-if="inputFrom.shop_logo" class="img-one" style="width: 80px;height: 80px;margin-right: 10px">
                                            <img :src="inputFrom.shop_logo" style="width: 80px;height: 80px">
                                            <span class="deleteBtn" @click="handleRemoveOne('shop_logo')">
                                                <i class="el-icon-delete"></i>
                                            </span>
                                        </div>
                                        <span><small>平台完整的logo，推荐尺寸280*100</small></span>
                                    </el-form-item>
                                    <el-form-item label="站点图标：" prop="shop_icon">
                                        <div v-if="!inputFrom.shop_icon">
                                            <el-upload
                                                class="one-file-upload"
                                                accept="image/jpeg,image/jpg,image/png"
                                                :show-file-list="false"
                                                list-type="picture-card"
                                                action=""
                                                :http-request="(request) => uploadFile(request, 'shop_icon')"
                                                :with-credentials="true"
                                            >
                                                <div class="icon-button">
                                                    <i class="el-icon-plus"></i>
                                                </div>
                                            </el-upload>
                                        </div>
                                        <div v-if="inputFrom.shop_icon" class="img-one" style="width: 80px;height: 80px;margin-right: 10px">
                                            <img :src="inputFrom.shop_icon" style="width: 80px;height: 80px">
                                            <span class="deleteBtn" @click="handleRemoveOne('shop_icon')">
                                                <i class="el-icon-delete"></i>
                                            </span>
                                        </div>
                                        <span><small>文件后缀.ico，推荐尺寸：16*16</small></span>
                                    </el-form-item>
                                </el-card>
                                <el-card class="box-card" style="">
                                    <div slot="header" class="clearfix">
                                        <span style="font-size:20px;">背景图</span>
                                    </div>
                                    <el-form-item label="后台登页背景：" prop="shop_manage_login_image">
                                        <div v-if="!inputFrom.shop_manage_login_image">
                                            <el-upload
                                                class="one-file-upload"
                                                accept="image/jpeg,image/jpg,image/png"
                                                :show-file-list="false"
                                                list-type="picture-card"
                                                action=""
                                                :http-request="(request) => uploadFile(request, 'shop_manage_login_image')"
                                                :with-credentials="true"
                                            >
                                                <div class="icon-button">
                                                    <i class="el-icon-plus"></i>
                                                </div>
                                            </el-upload>
                                        </div>
                                        <div v-if="inputFrom.shop_manage_login_image" class="img-one" style="width: 80px;height: 80px;margin-right: 10px">
                                            <img :src="inputFrom.shop_manage_login_image" style="width: 80px;height: 80px">
                                            <span class="deleteBtn" @click="handleRemoveOne('shop_manage_login_image')">
                                                <i class="el-icon-delete"></i>
                                            </span>
                                        </div>
                                        <span><small>推荐尺寸420*560</small></span>
                                    </el-form-item>
                                </el-card>
                            </div>
                            <el-form-item class="">
                                <el-button style="margin-top: 20px;" type="primary" @click="submitForm('inputFrom')" :class="{disable:loading}" class="contract_sub" :loading="loading">提交</el-button>
                            </el-form-item>
                        </el-form>
                    </el-tab-pane>
                </el-tabs>
            </el-tab-pane>
            <el-tab-pane label="系统对接" name="system_docking">
                <el-tabs v-model="secondActiveName" :tab-position="tabPosition" type="card" @tab-click="secondHandleClick">
                    <el-tab-pane label="邮件服务" name="smtp">
                        <el-form :model="inputFrom" ref="inputFromRef" label-width="240px" class="demo-compactForm">
                            <div style="margin:0 auto 0 100px;width: 1000px">
                                <el-form-item label="发送邮件服务器地址(SMTP)：" prop="smtp_host">
                                    <el-input v-model="inputFrom.smtp_host"></el-input>
                                </el-form-item>
                                <el-form-item label="服务器端口：" prop="smtp_port">
                                    <el-input v-model="inputFrom.smtp_port"></el-input>
                                </el-form-item>
                                <el-form-item class="">
                                    <el-button type="primary" @click="submitForm()" :class="{disable:loading}" class="contract_sub " :loading="loading">提交</el-button>
                                </el-form-item>
                            </div>
                        </el-form>
                    </el-tab-pane>
                </el-tabs>
            </el-tab-pane>
        </el-tabs>
    </el-main>
</template>

<style scoped lang="scss">
    .logo-uploader .logo {
        width: 80px;
        height: 80px;
        display: block;
    }
    :deep(.el-table__row .cell){
        display: flex;
        align-items: center;
    }
</style>

<style>
.logo-uploader .el-upload {
    border: 1px dashed var(--el-border-color);
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: var(--el-transition-duration-fast);
}

.logo-uploader .el-upload:hover {
    border-color: var(--el-color-primary);
}

.el-icon.logo-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 80px;
    height: 80px;
    text-align: center;
}
</style>
