<template>
    <!--新增编辑文章-->
    <div v-loading="detailFormLoading" class="s-flex jc-ct">
        <el-form ref="submitFormRef" :model="submitForm" :rules="submitFormRules" label-width="120px">
            <el-form-item label="标题" prop="title">
                <el-input v-model="submitForm.title" />
            </el-form-item>
            <el-form-item label="内容" prop="content">
                <div style="width: 100%;height: 500px;background: #f2f2f2;" v-if="detailFormLoading"></div>
                <Editor v-model="submitForm.content" style="height: 500px;" placeholder="请输入内容" v-else />
            </el-form-item>
            <el-form-item label="分类" prop="article_category_id">
                <el-cascader v-model="submitForm.article_category_id" placeholder="顶级分类"
                             filterable clearable :options="treeCategories"
                             :props="{ value: 'id', label: 'name', checkStrictly: true, emitPath:false }">
                </el-cascader>
            </el-form-item>
            <el-form-item label="关键字" prop="keywords">
                <el-input v-model="submitForm.keywords" />
            </el-form-item>
            <el-form-item label="摘要" prop="description">
                <el-input type="textarea" rows="3" v-model="submitForm.description"></el-input>
            </el-form-item>
            <el-form-item label="封面：" prop="cover">
                <div v-if="submitForm.cover" class="logo-uploader-preview">
                    <img :src="submitForm.cover" class="logo" alt="" />
                    <el-icon class="logo-uploader-icon logo-uploader-icon-delete">
                        <Delete @click="handleRemoveCover" />
                    </el-icon>
                </div>
                <el-upload
                    class="logo-uploader"
                    accept="image/jpeg,image/jpg,image/png"
                    action=""
                    :show-file-list="false"
                    :http-request="(request) => uploadFile(request)"
                    :with-credentials="true"
                    v-else>
                    <el-icon class="logo-uploader-icon logo-uploader-icon-plus">
                        <Plus />
                    </el-icon>
                </el-upload>
                <span class="co-999 fs14" style="width: 100%"><small>建议尺寸220*150或者350*238</small></span>
            </el-form-item>
            <el-form-item label="是否置顶" prop="is_top">
                <el-switch v-model="submitForm.is_top" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item label="是否登录" prop="is_login">
                <el-switch v-model="submitForm.is_login" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item label="是否推荐" prop="is_recommend">
                <el-switch v-model="submitForm.is_recommend" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item label="是否显示" prop="is_show">
                <el-switch v-model="submitForm.is_show" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item label="作者" prop="author">
                <el-input v-model="submitForm.author" />
            </el-form-item>
            <el-form-item label="排序" prop="sort">
                <el-input v-model="submitForm.sort" />
            </el-form-item>
            <el-form-item label="点击次数" prop="click_count">
                <el-input v-model="submitForm.click_count" />
            </el-form-item>
            <!--<el-form-item label="上传文件" prop="file_url">
                <el-input v-model="submitForm.file_url" />
            </el-form-item>-->
            <div class="">
                <el-button type="primary" :loading="submitLoading" @click="onSubmit()">提交</el-button>
                <el-button @click="closeArticleForm">返回</el-button>
            </div>
        </el-form>
    </div>
</template>

<script setup>
import Editor from '@/components/good/Editor.vue';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCommonStore } from '@/store';
import { Delete, Plus } from '@element-plus/icons-vue';
import { tabRemove } from '@/router/tabs.js';
import Http from '@/utils/http';

const commonStore = useCommonStore();
const router = useRouter();
const route = useRoute();

const cns = getCurrentInstance().appContext.config.globalProperties;

const detailFormLoading = ref(true);
const submitFormRef = ref(null);
const submitLoading = ref(false);
const treeCategories = ref([]);
const submitForm = ref({
    id: 0,
    article_category_id: 0,
    title: '',
    content: '',
    cover: '',
    keywords: '',
    description: '',
    author: '',
    is_top: 0,
    is_login: 0,
    is_show: 1,
    is_recommend: 0,
    click_count: 0,
    sort: 0,
    file_url: '',
    goods_category_id: 0
});

const validateContent = (rule, value, callback) => {
    if (submitForm.value.content == '' || submitForm.value.content == '<p style="color: rgb(51, 51, 51); line-height: 2;"><br></p>' || submitForm.value.content == '<p style="line-height: 2; color: rgb(51, 51, 51);"><br></p>') {
        callback(new Error('内容不能为空'));
    } else {
        callback();
    }
};

const submitFormRules = reactive({
    content: [
        { required: true, message: '请输入内容', trigger: 'blur' },
        { validator: validateContent, trigger: 'blur' }
    ],
    title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
    article_category_id: [{ required: true, message: '请选择分类', trigger: 'blur' }],
    cover: [{ required: true, message: '请上传封面', trigger: 'blur' }],
    description: [{ required: true, message: '请输入描述', trigger: 'blur' }],
    keywords: [{ required: true, message: '请输入关键字', trigger: 'blur' }]
});

const uploadFile = async (request) => {
    try {
        const res = await Http.doUpload('upload', { file: request.file });
        if (cns.$successCode(res.code)) {
            submitForm.value.cover = res.data.url;
        } else {
            cns.$message.error(res.message);
        }
    } catch (error) {
        console.error('Failed:', error);
    }
};

const handleRemoveCover = () => {
    submitForm.value.cover = '';
};

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            Http.doPost('article/update', submitForm.value).then(res => {
                submitLoading.value = false;
                if (cns.$successCode(res.code)) {
                    tabRemove(String(router.currentRoute.value.name), { name: 'manage.article.index' });
                } else {
                    cns.$message.error(res.message);
                }
            });
        } else {
            submitLoading.value = false;
            cns.$message.error('表单验证失败');
            return false;
        }
    });
};

const closeArticleForm = () => {
    cns.$confirm('关闭后页面数据将不会保存，确定要继续操作吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
        center: true
    }).then(() => {
        tabRemove(String(router.currentRoute.value.name), { name: 'manage.article.index' });
    });
};

const getArticleInfo = () => {
    detailFormLoading.value = true;
    Http.doGet('article/info', { id: route.params.id }).then(res => {
        detailFormLoading.value = false;
        if (cns.$successCode(res.code)) {
            treeCategories.value = res.data.tree_categories;
            submitForm.value = { ...res.data.article };
            console.log(submitForm.value.content);
        } else {
            cns.$message.error(res.message);
            router.push({ name: 'manage.article.index' });
        }
    }).catch(() => {
        detailFormLoading.value = false;
        cns.$message.error('获取数据失败');
    });
};

onMounted(() => {
    let title = '添加文章';
    if (Number(route.params.id) > 0) {
        title = '编辑文章-' + route.params.id;
    }
    commonStore.updateVisitedViewsTitle(route, title);
    getArticleInfo();
});
</script>

<style scoped lang="scss">
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
</style>
