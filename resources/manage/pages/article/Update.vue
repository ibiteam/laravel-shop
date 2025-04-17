<template>
    <!--新增编辑文章-->
    <div v-loading="detailFormLoading" class="s-flex jc-ct">
        <el-form :model="submitForm" ref="submitFormRef" :rules="submitFormRules" label-width="100px">
            <el-form-item label="文章标题" prop="title">
                <el-input v-model="submitForm.title" />
            </el-form-item>
            <el-form-item label="文章内容" prop="content">
                <!--内容编辑器-->
                <Editor v-model="submitForm.content" height="500px" min-height="500px" @change="handleChangeContent" />
            </el-form-item>
            <el-form-item label="文章分类" prop="parent_id">
                <el-cascader v-model="submitForm.parent_id" placeholder="顶级分类" style="width: 400px;"
                             filterable clearable :options="treeCategories"
                             :props="{ value: 'id', label: 'name', checkStrictly: true, emitPath:false }">
                </el-cascader>
            </el-form-item>
            <el-form-item label="关键字" prop="keywords">
                <el-input v-model="submitForm.keywords" />
            </el-form-item>
            <el-form-item label="摘要" prop="description">
                <el-input type="textarea" resize="none" v-model="submitForm.description"></el-input>
            </el-form-item>
            <el-form-item label="封面" prop="cover">
                <el-input v-model="submitForm.cover" />
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
            <el-form-item label="上传文件" prop="file_url">
                <el-input v-model="submitForm.file_url" />
            </el-form-item>
            <div class="">
                <el-button type="primary" :loading="submitLoading" @click="onSubmit()">提交</el-button>
                <el-button @click="closeArticleForm">返回</el-button>
            </div>
        </el-form>
    </div>
</template>

<script setup>
import Editor from '@/components/good/Editor.vue';
import { articleInfo, articleStore, articleUpdateCover, articleDeleteCover } from '@/api/article.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCommonStore } from '@/store';

const commonStore = useCommonStore();
const router = useRouter();
const route = useRoute();

const cns = getCurrentInstance().appContext.config.globalProperties;

const detailFormLoading = ref(false);
const isInitContent = ref(true);
const submitFormRef = ref(null);
const submitLoading = ref(false);
const treeCategories = ref([]);
const submitForm = reactive({
    id: 0,
    content: '',
    article_category_id: 0,
    title: '',
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
        callback(new Error('文章内容不能为空'));
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
    cover: [{ required: true, message: '请输入封面', trigger: 'blur' }],
    description: [{ required: true, message: '请输入描述', trigger: 'blur' }],
    keywords: [{ required: true, message: '请输入关键字', trigger: 'blur' }]
});

const handleChangeContent = () => {
    if (!isInitContent.value) {
        submitFormRef.value.validateField('content');
    } else {
        isInitContent.value = false;
    }
};

/* 新增编辑 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            articleStore(submitForm).then(res => {
                submitLoading.value = false;
                if (cns.$successCode(res.code)) {

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
        // 关闭当前页？

        // 打开文章列表页
        router.push({ name: 'manage.article.index' });
    });
};

const getArticleInfo = () => {
    detailFormLoading.value = true;
    articleInfo({ id: route.params.id }).then(res => {
        detailFormLoading.value = false;
        if (cns.$successCode(res.code)) {
            console.log(res.data);
            treeCategories.value = res.data.tree_categories;

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

</style>
