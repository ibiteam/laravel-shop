<template>
    <el-tabs v-model="adCateName" :tab-position="tabPosition" type="card" @tab-click="handleClick">
        <div class="out-search">
            <div class="in-search">
                <el-input class="search-input" v-model="name" clearable placeholder="请输入分类名称"></el-input>
                <el-button type="primary" @click="getData()">查询</el-button>
            </div>
        </div>
        <el-tab-pane v-for="(item, index) in adCateNames" :key="index" :label="item.alias" :name="index">
            <div class="pad-20">
                <div class="phone-wrap">
                    <div class="sign-wrap">
                        <div>
                            <div class="head s_flex ai-ct">
                                <div class="left s_flex ai-ct" style="color:#333;">
                                    <span class="circle"></span>
                                    <span class="circle"></span>
                                    <span class="circle"></span>
                                    <span class="circle"></span>
                                    <span class="circle"></span>
                                    <i class="iconfont">&#xe699;</i>
                                </div>
                                <div class="midd s_flex ai-ct">1:43&nbsp;pm</div>
                                <div class="right s_flex ai-ct" style="color:#333;">
                                    <span>80%</span>
                                    <i class="iconfont">&#xe696;</i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="van-sticky">
                        <div id="no_header" class="home-header head-fixed s_flex"
                             style="background-color: rgba(255, 255, 255, 0);">
                            <div class="home-head-img" style="background-color: rgba(0, 0, 0, 0.2);">
                                <div class="home-logo view-img"
                                     style="background: url('https://cdn.toodudu.com/2022/08/08/EcRx74h2XTrnqQDz2l7LvVWInkrkeuF8ZICev4S8.png') center center / 100% 100% no-repeat;">
                                </div>
                            </div>
                            <div class="view-input s_flex flex_1" style="background-color: rgb(242, 242, 242);">
                                <div class="iconfont" style="font-size: 14px;"></div>
                                <div style="font-size: 14px;">涂料</div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-wrap">
                        <div v-for="val in list" :key="val.id" class="custom-item" @click="openDetail(val.id)" style="cursor:pointer;" :class="{ select_hover: (select_cate_id===val.id) }">
                            <img src="https://cdn.toodudu.com/uploads/2022/08/11/banner.png">
                            <div @click.stop="">
                                <p>{{ val.name }}
                                    <el-button
                                        size="small"
                                        style="margin-left: 10px;"
                                        @click="edit_ad_cate(val)">编辑分类
                                    </el-button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </el-tab-pane>
    </el-tabs>
    <div class="edit-wrap" v-if="editVisible">
        <div class="edit-con">
            <div class="tit">{{ad_cate.name}}
                <el-button type="danger" size="small" style="margin-left: 10px;" @click="add_app_ads(ad_cate.id)">新增</el-button>
                <el-button type="primary" size="small" style="margin-left: 10px;" @click="openDetail(ad_cate.id)">刷新</el-button>
            </div>
            <div style="font-size: 15px;color: red;margin-bottom: 10px">
                <p>建议尺寸：{{ ad_cate.width }} * {{ ad_cate.height }}，图片尺寸会按宽度 {{ ad_cate.width }} 进行等比比例</p>
            </div>
            <page-table :data="app_ads"
                        stripe
                        border
                        @change="handlePageChange"
            >
                <el-table-column label="标题" prop="name" width="100px"></el-table-column>
                <el-table-column label="广告图片" width="150px">
                    <template #default="scope">
                        <img :src="scope.row.image" alt="" style="width: 40px">
                    </template>
                </el-table-column>
                <el-table-column label="有效期" width="180px">
                    <template #default="scope">
                        <span v-if="scope.row.type == 2"> <span>长久广告</span> </span>
                        <span v-else>
                            <span>{{ scope.row.start_time }}</span> <br />
                            <span>{{ scope.row.end_time }}</span>
                        </span>
                    </template>
                </el-table-column>
                <el-table-column label="是否显示" width="90">
                    <template #default="scope">
                        <el-switch
                            v-model="scope.row.is_show"
                            :active-value="1" :inactive-value="0"
                            @click="toggle_status(scope.row.is_show, 'is_show', scope.row.id)">
                        </el-switch>
                    </template>
                </el-table-column>
                <el-table-column label="排序" prop="sort" width="80"></el-table-column>
                <el-table-column label="操作">
                    <template #default="scope">
                        <div style="display: flex;align-items: center;flex-wrap: wrap;">
                            <div style="margin-right: 10px">
                                <el-upload
                                    class="logo-uploader"
                                    :accept="'image/jpeg,image/jpg,image/png,image/gif'"
                                    :show-file-list="false"
                                    :http-request="(request) => uploadFile(request, scope.row.id)"
                                    :with-credentials="true"
                                >
                                    <el-button type="primary" size="small">更换图片</el-button>
                                </el-upload>
                            </div>
                            <el-button type="success" size="small" @click="editAd(scope.row)">编辑</el-button>
                            <el-button type="danger" size="small" @click="delAd(scope.row.id)">删除</el-button>
                        </div>
                    </template>
                </el-table-column>
            </page-table>
        </div>
    </div>
    <el-dialog
        :close-on-click-modal="false"
        title="编辑广告分类"
        v-model="handleUpdateTddCateShow"
        width="25%"
        :before-close="handleUpdateTddCateClose">
        <el-form>
            <el-form-item label="分类名称">
                <el-input v-model="updateTddCateForm.name"></el-input>
            </el-form-item>
            <el-form-item label="分类别名">
                <el-input v-model="updateTddCateForm.alias" disabled></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button type="primary" @click="submitUpdateTddCate()" :loading="submitUpdateLoading">确 定</el-button>
                <el-button @click="handleUpdateTddCateClose">取 消</el-button>
            </div>
        </template>
    </el-dialog>
    <el-dialog
        :close-on-click-modal="false"
        :title="ad_title"
        v-model="handleUpdateTddImgShow"
        width="24%"
        :before-close="handleUpdateTddImgClose">
        <el-form ref="subFormRef" :model="subForm" :rules="rules" label-width="130px">
            <el-form-item label="标题" prop="name">
                <el-input style="max-width: 541px;" v-model="subForm.name"></el-input>
            </el-form-item>
            <el-form-item label="链接">
                <LinkInput
                    style="width: 100%;"
                    :name="subForm.link"
                    :value="subForm.link"
                    @select="handleOpenLink(['url'], '')"
                />
            </el-form-item>
            <el-form-item label="广告类型" prop="type">
                <el-radio-group v-model="subForm.type" >
                    <el-radio :value="1">时间限制</el-radio>
                    <el-radio :value="2">长久广告</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="开始时间:" prop="start_time" v-if="subForm.type === 1" style="margin-left:70px;">
                <el-date-picker
                    v-model="subForm.start_time"
                    type="datetime"
                    placeholder="开始时间"
                    value-format="YYYY-MM-DD HH:mm:ss">
                </el-date-picker>
            </el-form-item>
            <el-form-item label="结束时间:" prop="end_time" v-if="subForm.type === 1" style="margin-left:70px;">
                <el-date-picker
                    v-model="subForm.end_time"
                    type="datetime"
                    placeholder="开始时间"
                    value-format="YYYY-MM-DD HH:mm:ss">
                </el-date-picker>
            </el-form-item>
            <el-form-item label="是否显示">
                <el-switch v-model="subForm.is_show" :active-value="1" :inactive-value="0" active-text="是" inactive-text="否"></el-switch>
            </el-form-item>
            <el-form-item label="排序" prop="sort">
                <el-input style="max-width: 541px;" v-model="subForm.sort"></el-input>
                <small>排序（数字越大靠前）</small>
            </el-form-item>
            <el-form-item label="广告图片:" prop="image">
                <el-upload
                    class="logo-uploader"
                    :accept="'image/jpeg,image/jpg,image/png,image/gif'"
                    :show-file-list="false"
                    :http-request="(request) => uploadImageFile(request)"
                    :with-credentials="true"
                >
                    <img style="height: 50px;width: 50px" v-if="subForm.image" :src="subForm.image" alt="">
                    <el-button v-else size="small" type="primary">上传文件</el-button>
                </el-upload>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button type="primary" @click="submitUpdateTddImg()"
                           :loading="submitUpdateImgLoading">确 定</el-button>
                <el-button @click="handleUpdateTddImgClose">取 消</el-button>
            </div>
        </template>
    </el-dialog>
    <LinkCenterDialog v-if="linkCenterDialogData.show" v-bind="{...linkCenterDialogData}" @close="handleLinkCenterDialogClose" @confirm="handleLinkCenterDialogConfirm"></LinkCenterDialog>
</template>
<script setup>
import { ref, reactive, getCurrentInstance, onMounted } from 'vue'
import PageTable from '@/components/common/PageTable.vue'
import Http from '@/utils/http.js'
import LinkInput from '@/pages/decoration/components/LinkInput.vue'
import LinkCenterDialog from '@/components/LinkCenter/Dialog.vue'
const cns = getCurrentInstance().appContext.config.globalProperties
// 响应式数据
const app_url = ref([])
const adCateName = ref('home')
const tabPosition = ref('left')
const editVisible = ref(false)
const list = ref([])
const adCateNames = ref([])
const ad_cate = ref([])
const app_ads = ref([])
const ad_cate_id = ref(0)
const name = ref('')
const handleUpdateTddCateShow = ref(false)
const submitUpdateLoading = ref(false)
const updateTddCateForm = reactive({
    alias: '',
    id: 0,
    name: '',
})
const select_cate_id = ref(0)
const ad_title = ref('')
const handleUpdateTddImgShow = ref(false)
const submitUpdateImgLoading = ref(false)
const subForm = ref({
    id: 0,
    name: '',
    image: '',
    link: '',
    type: 1,
    start_time: '',
    end_time: '',
    is_show: 1,
    sort: 0,
})
const subFormRef = ref(null)
// 路由中心弹窗
const linkCenterDialogData = reactive({
    show: false,
})
const rules = reactive({
    image: [{ required: true, message: '广告图片不能为空', trigger: 'blur' }],
    sort: [{ required: true, message: '请输入排序', trigger: 'blur' }],
    is_show: [{ required: true, message: '请设置是否显示', trigger: 'change' }],
    start_time: [{ required: true, message: '请选择开始时间', trigger: 'change' }],
    end_time: [{ required: true, message: '请选择结束时间', trigger: 'change' }],
});

// 生命周期钩子
onMounted(() => {
    getCateData()
})

// 打开路由弹窗
const handleOpenLink = (keys, not_for_data) => {
    linkCenterDialogData.show = true
    cns.$bus.emit('openLinkDialog', {not_for_data, keys, show: true})
}

// 接收路由中心弹窗数据
const handleLinkCenterDialogConfirm = (res) => {
    linkCenterDialogData.show = false
    subForm.value.link = res[0]?.h5_url
    console.log(subForm.value);
}

// 关闭路由中心弹窗
const handleLinkCenterDialogClose = () => {
    linkCenterDialogData.show = false
}

const defaultPage = {
    page: 1,
    per_page: 10
}

const pagination = reactive({ ...defaultPage })

const handlePageChange = (page, per_page) => {
    pagination.per_page = per_page
    openDetail(ad_cate_id.value)
}

// 切换状态
const toggle_status = (val, act, id) => {
    const info = {}
    info.id = id
    info.val = val
    info.sign = act
    Http.doPost('app_ads/toggle/status', info).then(res => {
        if (cns.$successCode(res.code)) {
            cns.$message.success('保存成功')
        } else {
            cns.$message.error('保存失败')
        }
    })
}

//编辑轮播分类
const submitUpdateTddCate = () => {
    submitUpdateLoading.value = true
    Http.doPost('app_ads/cate/update', updateTddCateForm).then(res => {
        submitUpdateLoading.value = false
        if (cns.$successCode(res.code)) {
            handleUpdateTddCateShow.value = false
            getData()
        } else {
            cns.$message.error(res.message)
        }
    })
}

const handleUpdateTddCateClose = () => {
    handleUpdateTddCateShow.value = false
}

// 切换分类
const handleClick = () => {
    editVisible.value = false
    ad_cate_id.value = 0
    getData()
}

const uploadFile = async (request, id) => {
    try {
        const res = await Http.doPost('upload', {
            file: request.file,
        })
        if (cns.$successCode(res.code)) {
            Http.doPost('app_ads/update/ad_image', {id: id, image:res.data.url}).then(res => {
                if (cns.$successCode(res.code)) {
                    openDetail(ad_cate_id.value)
                } else {
                    cns.$message.error(res.message)
                }
            })
        } else {
            cns.$message.error(res.message)
        }
    } catch (error) {
        console.error('Failed:', error)
    }
}

const uploadImageFile = async (request) => {
    try {
        const res = await Http.doPost('upload', {
            file: request.file,
        })
        if (cns.$successCode(res.code)) {
            subForm.value.image = res.data.url
        } else {
            cns.$message.error(res.message)
        }
    } catch (error) {
        console.error('Failed:', error)
    }
}

// 关闭图片弹窗
const handleUpdateTddImgClose = () => {
    resetFields()
    handleUpdateTddImgShow.value = false
}

const edit_ad_cate = (item) => {
    handleUpdateTddCateShow.value = true
    updateTddCateForm.name = item.name
    updateTddCateForm.id = item.id
    updateTddCateForm.alias = item.alias
}

const submitUpdateTddImg = () => {
    subFormRef.value.validate((valid) => {
        if (valid) {
            if (subForm.value.type === 1 && subForm.value.end_time <= subForm.value.start_time) {
                cns.$message.error('结束时间不能小于开始时间！')
                return
            }
            submitUpdateImgLoading.value = true
            Http.doPost('app_ads/update', subForm.value).then((res) => {
                submitUpdateImgLoading.value = false
                if (cns.$successCode(res.code)) {
                    handleUpdateTddImgShow.value = false
                    cns.$message.success('保存成功')
                    subFormRef.value.resetFields()
                    openDetail(ad_cate.value.id)
                } else {
                    cns.$message.error(res.message)
                }
            })
        } else {
            console.log('error submit!!')
            return false
        }
    })
}

const resetFields = () => {
    subForm.value = {
        id: 0,
        name: '',
        image: '',
        link: '',
        type: 1,
        start_time: '',
        end_time: '',
        is_show: 0,
        sort: 0,
    }
}

const add_app_ads = (ad_cate_id) => {
    resetFields()
    ad_title.value = '轮播图添加'
    handleUpdateTddImgShow.value = true
    subForm.value.ad_cate_id = ad_cate_id
}

const editAd = (row) => {
    subForm.value = row
    ad_title.value = '轮播图编辑'
    handleUpdateTddImgShow.value = true
    submitUpdateImgLoading.value = false
}

// 删除广告
const delAd = (id) => {
    Http.doPost('app_ads/delete', {id: id}).then(res => {
        if (cns.$successCode(res.code)) {
            // 删除后重新请求数据
            openDetail(ad_cate_id.value)
            cns.$message.success('图片删除成功');
        } else {
            cns.$message.error('图片删除失败');
        }
    });
}

const getData = () => {
    const info = { adCateName: adCateName.value, name: name.value };
    Http.doGet('app_ads/cates', info).then((res) => {
        if (cns.$successCode(res.code)) {
            list.value = res.data
            openDetail(ad_cate_id.value)
        } else {
            cns.$message.error('数据获取失败')
        }
    })
}

// 获取分类名称
const getCateData = () => {
    Http.doGet('app_ads/cate/names').then((res) => {
        if (cns.$successCode(res.code)) {
            adCateNames.value = res.data.adCateNames
            adCateName.value = res.data.adCateName
            getData()
            openDetail(ad_cate_id.value)
        } else {
            cns.$message.error(res.message)
        }
    })
}

const openDetail = (cate_id, page = 1) => {
    select_cate_id.value = cate_id
    if (!cate_id) {
        editVisible.value = false
        return false
    }
    ad_cate_id.value = cate_id
    subForm.value.ad_cate_id = cate_id
    Http.doGet('app_ads', { cate_id, page }).then((res) => {
        if (cns.$successCode(res.code)) {
            ad_cate.value = res.data.ad_cate
            console.log(ad_cate);
            app_ads.value = res.data
            editVisible.value = true
        } else {
            cns.$message.error('数据获取失败')
        }
    })
}

</script>

<style scoped lang="scss">
.carousel-wrap .select_hover {
    box-shadow: 0 0 4px 4px #e5e5e5
}

.one-upload input[type=file] {
    display: none;
}

.el-tabs__header {
    width: 150px;
}

.s_flex {
    display: flex;
}

.edit-wrap {
    position: absolute;
    width: 900px;
    height: 100%;
    top: 0;
    right: 0;
    z-index: 2000;
    background: #fff;
    box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.1);
}

.edit-wrap .mask {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 100;
}

.edit-wrap .edit-con {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #fff;
    z-index: 101;
    padding: 20px 20px 0px;
}

.edit-con .tit {
    color: #333;
    font-size: 18px;
    line-height: 18px;
    font-weight: bold;
    margin-bottom: 24px;
}

.edit-con .quick {
    padding: 10px 20px;
}

.edit-con .quick > div {
    margin-bottom: 15px;
}

.edit-con .quick > div:last-of-type {
    margin-bottom: 0;
}

.edit-con .quick > div span {
    margin-right: 10px;
    display: inline-block;
    width: 120px;
    text-align: right;
}

.edit-con > h5 {
    color: #999;
    font-size: 14px;
    line-height: 14px;
    margin-bottom: 10px;
}

.edit-con .img-wrap .img-content {
    font-size: 0;
}

.edit-con .img-wrap img {
    width: 300px;
}

.edit-con .img-wrap .img-item {
    margin-bottom: 10px;
    position: relative;
}

.img-item .model {
    position: absolute;
    left: 0;
    bottom: 0;
    top: 0;
    width: 300px;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    margin: auto;
    align-items: center;
    justify-content: center;
    display: none;
}

.img-item:hover .model {
    display: flex;
}

.img-item .model a {
    margin: 0 10px;
    cursor: pointer;
}

.home-header {
    height: 36px;
    line-height: 36px;
    padding: 4px 10px;
    margin-bottom: 12px;
}

.carousel-wrap {
    padding: 0 10px;
    margin-bottom: 0;
}

.carousel-wrap > div {
    margin-bottom: 12px;
    box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.1);
    text-align: center;
}

.custom-item p {
    text-align: center;
    padding-bottom: 10px;
}

.custom-item img {
    width: 80%;
}

.flex_1 {
    flex: 1;
}

.home-header .home-head-img {
    width: 35px;
    height: 35px;
    text-align: center;
    background-color: rgba(0, 0, 0, .2);
    border-radius: 100%;
    position: relative;
    overflow: hidden;
}

.home-header .home-head-img .view-img.home-logo {
    position: absolute;
    top: 0;
}

.home-header .view-input .iconfont {
    margin-right: 12px;
}

.home-header .view-input {
    height: 30px;
    line-height: 30px;
    padding: 0 10px;
    margin: 3px 5px;
    background-color: #fff;
    border-radius: 15px;
    font-size: 12px;
    color: #999;
}

.ai-ct {
    align-items: center;
}

.pad-20 {
    padding: 20px;
}

.phone-wrap {
    box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.2);
    width: 400px;
    /*min-height: 750px;*/
    max-height: 72vh;
    margin: 40px 0 27px 5vw;
    overflow-y: auto;
}

.sign-wrap {
    display: flex;
    position: relative;
    padding: 2px 0;
}

.sign-wrap .head {
    width: 375px;
    height: 18px;
    padding: 0 5px;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    color: #fff;
}

.sign-wrap .left {
    text-align: left;
}

.left .circle {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    border: 1px solid #333;
    margin-right: 1px;
    margin-top: 1px;
}

.left .circle:nth-child(1), .left .circle:nth-child(2), .left .circle:nth-child(3) {
    background: #333;
}

.midd {
    color: #333;
}

.out-search {
    padding-left: 20px
}

.in-search {
    width: 400px;
    margin: 40px 0 0px 5vw;
}

.search-input {
    width: 315px
}

.el-dialog__wrapper {
    margin-left: -800px;
}

small {
    color: #999999;
}
</style>
