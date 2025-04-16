<template>
    <div>
        <el-header style="padding-top: 10px;">
            <el-form :inline="true" :model="searchForm" class="search-form">
                <el-form-item label="名称/权限值" prop="keywords">
                    <el-input v-model="searchForm.keywords" clearable placeholder="请输入" @keyup.enter="getData()" />
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="getData()">搜索</el-button>
                </el-form-item>
            </el-form>
        </el-header>

        <el-main>
            <div class="box-main">
                <div class="tree-box">
                    <el-tree
                        :data="permissionData"
                        node-key="id"
                        :default-expanded-keys="expandedArr"
                        :default-checked-keys="checkedArr"
                        :props="{children:'children', label:'display_name'}"
                        @node-click="treeChoose">
                    </el-tree>
                </div>
                <el-form v-if="submitForm.id > 0" :model="submitForm" ref="submitFormRef" :rules="submitFormRules"
                         label-position="top" status-icon label-width="auto" size="default">
                    <el-form-item label="所属分类" prop="parent_id">
                        <el-cascader v-model="submitForm.parent_id" placeholder="未选择默认顶级分类" style="width: 400px;"
                                     :options="permissionData" clearable
                                     :props="{ checkStrictly: true, value:'id', label:'display_name', emitPath:false}">
                        </el-cascader>
                    </el-form-item>
                    <el-form-item label="名称" prop="display_name">
                        <el-input v-model="submitForm.display_name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="权限值(该权限所代表的权限值)" prop="name">
                        <el-input v-model.number="submitForm.name" disabled></el-input>
                    </el-form-item>
                    <el-form-item label="图标地址" prop="icon">
                        <el-input v-model.number="submitForm.icon"></el-input>
                    </el-form-item>
                    <el-form-item label="排序(数字越大越靠前)" prop="sort">
                        <el-input v-model.number="submitForm.sort"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" :loading="submitLoading" @click="onSubmit()">提交</el-button>
                    </el-form-item>
                </el-form>
            </div>

        </el-main>

    </div>
</template>

<script setup>
import { Plus, Search } from '@element-plus/icons-vue';
import { permissionIndex, permissionStore } from '@/api/set.js';
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    keywords: ''
});

const permissionData = ref([]);
const expandedArr = ref([]);    // 默认展开第一行
const checkedArr = ref([]);     // 默认选中
const loading = ref(false);
const submitFormRef = ref(null);
const submitLoading = ref(false);
const submitForm = reactive({
    id: 0,
    parent_id: 0,
    name: '',
    display_name: '',
    icon: '',
    sort: 0
});

const submitFormRules = reactive({
    display_name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
    name: [{ required: true, message: '请输入权限值', trigger: 'blur' }],
});

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            permissionStore(submitForm).then(res => {
                submitLoading.value = false;
                if (cns.$successCode(res.code)) {
                    cns.$message.success(res.message);
                    getData();
                } else {
                    cns.$message.error(res.message);
                }
            });
        } else {
            cns.$message.error('表单验证失败');
            return false;
        }
    });
};

const getData = () => {
    loading.value = true;
    permissionIndex(searchForm).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            permissionData.value = res.data;
            if (res.data.length) {
                expandedArr.value.push(res.data[0].id);
                if (res.data[0].children) {
                    setExpandedArr(res.data[0].children);
                }
            }

        } else {
            cns.$message.error(res.message);
        }
    }).catch(() => {
        loading.value = false;
        cns.$message.error('获取数据失败');
    });
};

const setExpandedArr = (arr) => {
    for (let i = 0; i < arr.length; i++) {
        if (arr[i].children && arr[i].children.length) {
            expandedArr.value.push(arr[i].id);
        } else {
            setExpandedArr(arr[i].children);
        }
    }
};

// tree点击事件
const treeChoose = (data) => {
    submitForm.id = data.id;
    submitForm.parent_id = data.parent_id;
    submitForm.name = data.name;
    submitForm.display_name = data.display_name;
    submitForm.icon = data.icon;
    submitForm.sort = data.sort;
};

onMounted(() => {
    getData();
});
</script>

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

.el-range-editor.el-input__inner {
    width: 250px;
}

.el-range-separator {
    position: absolute;
    left: 107px;
    top: 3px;
}

.box-main {
    min-width: 1250px;
    display: flex;
}

.box-main .el-tree {
    width: 600px;
}

.box-main .el-form {
    flex: 1;
    padding: 0 20px;
}

.box-main .tree-box {
    height: 800px;
    overflow-y: auto;
}

.box-main .el-form .el-input {
    width: 400px;
}
</style>
