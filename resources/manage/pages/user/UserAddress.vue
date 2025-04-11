<script setup>
import { Plus, Search, RefreshLeft} from '@element-plus/icons-vue';
import Page from '@/components/common/Pagination.vue'
import { getUserAddress, addressUpdate, getAreasData } from '@/api/user.js'
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties

// 添加查询参数对象，增加搜索条件
const queryParams = reactive({
    page: 1,
    number: 10,
    user_id: '1', // 用户id
    recipient_name: '', // 收货人
});

// 添加分页相关状态
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});

// 搜索方法
const handleSearch = () => {
    getData(1);
};

// 重置搜索条件
const resetSearch = () => {
    queryParams.user_id = '';
    getData(1);
};

// 页码改变
const handleCurrentChange = (val) => {
    getData(val);
}

// 每页条数改变
const handleSizeChange = (val) => {
    queryParams.number = val;
    pageInfo.per_page = val;
    getData(1);
}

const areasData = () => {
    getAreasData(queryParams).then(res => {
        if (res.code === 200) {
            areas.value = res.data;
        }
    }).catch(() => {})
}

const getData = (page = 1) => {
    loading.value = true;
    // 更新当前页码
    queryParams.page = page;

    getUserAddress(queryParams).then(res => {
        loading.value = false;
        if (res.code === 200) {
            tableData.value = res.data.list;
            // 更新分页信息
            setPageInfo(res.data.meta);
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
}

const updateForm = () => {
    updateLoading.value = true
    subFormRef.value.validate((valid, fields) => {
        if (valid) {
            addressUpdate(subForm.value).then(function (res) {
                if (res.code === 200) {
                    dialogFormVisible.value = false;
                    cns.$message.success('保存成功');
                    getData();
                } else {
                    cns.$message.error(res.data.message);
                }
            }).catch(function (res) {
            });
            updateLoading.value = false;
        } else {
            updateLoading.value = false;
            return false;
        }
    })
};

const modifyAddress = (row) => {
    subForm.value = {
        id: row.id,
        user_id: queryParams.user_id,
        address_detail: row.address_detail,
        area: row.area,
        recipient_name: row.recipient_name,
        recipient_phone: row.recipient_phone,
        province: row.province,
        city: row.city,
        district: row.district,
    }
    dialogFormVisible.value = true
    updateTitle.value = '编辑地址'
};

// 关闭弹窗
const closePasswordDialog = (form) => {
    subForm.value = {
        id: 0,
        address_detail: "",
        area: [],
        recipient_name: "",
        recipient_phone: "",
        province: "",
        city: "",
        district: "",
    }
    subFormRef.value.resetFields()
    dialogFormVisible.value = false;
};
const handleChange = (value) => {
    subForm.value.province = value[0];
    subForm.value.city = value[1];
    subForm.value.district = value[2];
}

onMounted( () => {
    getData()
    areasData()
});

const areas = ref([]);
const tableData = ref([]);
const updateTitle = ref('');
const subFormRef = ref(null);
const updateLoading = ref(false);
const loading = ref(false);
const dialogFormVisible = ref(false);
const subForm = ref({
    id: 0,
    user_id: queryParams.user_id,
    address_detail: "",
    area: [],
    recipient_name: "",
    recipient_phone: "",
    province: "",
    city: "",
    district: "",
});

const rules = reactive({
    recipient_name: [
        { required: true, message: '请输入收货人', trigger: 'blur' },
    ],
    recipient_phone: [
        { required: true, message: '请输入手机号', trigger: 'blur' },
    ],
    address_detail: [
        { required: true, message: '请输入详细地址', trigger: 'blur' },
        { min: 3, max: 100, message: '长度在 3 到 100 个字符', trigger: 'blur' }
    ],
    area: [
        { required: true, message: '请选择省市区', trigger: 'blur' },
    ],
})

</script>

<template>
    <div class="common-wrap">
        <el-header style="padding: 10px 0;">
            <!-- 添加搜索表单 -->
            <el-form :inline="true" :model="queryParams" class="search-form">
                <el-form-item label="收货人" prop="recipient_name">
                    <el-input
                        v-model="queryParams.recipient_name"
                        placeholder="请输入收货人"
                        clearable
                        @keyup.enter="handleSearch"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button :icon="Search" type="primary" @click="handleSearch">搜索</el-button>
                    <el-button :icon="RefreshLeft" @click="resetSearch">重置</el-button>
                </el-form-item>
            </el-form>
        </el-header>
        <el-table
            :data="tableData"
            stripe
            border
            v-loading="loading"
            style="width: 100%;">
            <el-table-column label="地址ID" prop="id"></el-table-column>
            <el-table-column label="收货人" prop="recipient_name"></el-table-column>
            <el-table-column label="收货手机号" prop="recipient_phone"></el-table-column>
            <el-table-column label="收货地址">
                <template #default="scope">
                    {{ scope.row.province_name + scope.row.city_name + scope.row.district_name + scope.row.address_detail }}
                </template>
            </el-table-column>
            <el-table-column label="创建时间" prop="created_at"></el-table-column>
            <el-table-column label="更新时间" prop="updated_at"></el-table-column>
            <el-table-column label="操作">
                <template #default="scope">
                    <el-button type="primary" @click="modifyAddress(scope.row)">编辑</el-button>
                </template>
            </el-table-column>
        </el-table>
        <!-- 添加分页组件 -->
        <Page :pageInfo="pageInfo" @sizeChange="handleSizeChange" @currentChange="handleCurrentChange" />
        <!--  添加用户  -->
        <el-dialog v-model="dialogFormVisible"
                   :close-on-click-modal="false"
                   :close-on-press-escape="false"
                   :title="updateTitle"
                   center
                   width="500">
            <el-form :model="subForm" :rules="rules" ref="subFormRef">
                <el-form-item label="收货人" prop="recipient_name">
                    <el-input v-model="subForm.recipient_name" autocomplete="off" />
                </el-form-item>
                <el-form-item label="手机号" prop="recipient_phone">
                    <el-input v-model="subForm.recipient_phone" autocomplete="off" />
                </el-form-item>
                <el-form-item label="省市区" prop="area">
                    <el-cascader v-model="subForm.area" :options="areas" @change="handleChange" />
                </el-form-item>
                <el-form-item label="详细地址" prop="address_detail">
                    <el-input v-model="subForm.address_detail" autocomplete="off" />
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="closePasswordDialog('subForm')">关闭</el-button>
                    <el-button type="primary" v-loading="updateLoading" @click="updateForm('subForm')">确认</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

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

.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 15px;
}

.header-picture {
    width: 36px;
    height: 36px;
    overflow: hidden;
    border-radius: 50%;
    background: #fff;
    padding: 1px;
    box-sizing: border-box;
}
.imgs {
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    overflow: hidden;
    border-radius: 50%;
}
.header-picture .imgs img {
    width: 100%;
    height: 100%;
}
.flex-user-information {
    display: flex;align-items: center;
}
.flex-user-information .header-user-names {
    margin: 0 5px;
    width: 90px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.flex-user-information .header-user-names span {
    font-size: 14px;
    font-weight: 400;
    color: #3D3D3D;
}
</style>
