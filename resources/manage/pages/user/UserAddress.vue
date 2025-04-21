<template>
    <search-form :model="query">
        <el-form-item label="收货人" prop="consignee">
            <el-input
                v-model="query.consignee"
                placeholder="请输入收货人"
                clearable
                @keyup.enter="getData()"
            />
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="getData()">搜索</el-button>
            <el-button @click="resetSearch">重置</el-button>
        </el-form-item>
    </search-form>
    <page-table
        :data="tableData"
        :maxHeight="'700px'"
        v-loading="loading"
        @change="handlePageChange"
    >
        <el-table-column label="地址ID" prop="id"></el-table-column>
        <el-table-column label="收货人" prop="consignee"></el-table-column>
        <el-table-column label="收货手机号" prop="phone"></el-table-column>
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
    </page-table>
    <!--  添加用户  -->
    <el-dialog v-model="dialogFormVisible"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               :title="updateTitle"
               center
               width="500">
        <el-form :model="subForm" :rules="rules" ref="subFormRef">
            <el-form-item label="收货人" prop="consignee">
                <el-input v-model="subForm.consignee" autocomplete="off" />
            </el-form-item>
            <el-form-item label="手机号" prop="phone">
                <el-input v-model="subForm.phone" autocomplete="off" />
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
                <el-button @click="closePasswordDialog('subForm')">取消</el-button>
                <el-button type="primary" v-loading="updateLoading" @click="updateForm('subForm')">确认</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script setup lang="ts">
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';
import Http from '@/utils/http.js';
import SearchForm from '@/components/common/SearchForm.vue';
import PageTable from '@/components/common/PageTable.vue';

import { useRoute } from 'vue-router';
const route = useRoute();

const cns = getCurrentInstance().appContext.config.globalProperties

const defaultQuery = {
    user_id: 0, // 用户id - 从url中获取
    consignee: '', // 收货人
}
// 添加查询参数对象，增加搜索条件
const query = reactive({...defaultQuery});
/* 定义默认分页参数 */
const defaultPage = {
    page: 1,
    per_page: 10,
}
const pagination = reactive({...defaultPage})
/* 重置搜索条件 */
const resetSearch = () => {
    Object.assign(query, defaultQuery)
    Object.assign(pagination, defaultPage)
    getData()
}
const areasData = () => {
    Http.doGet('set/region/tree').then(res => {
        if (res.code === 200) {
            areas.value = res.data;
        }
    }).catch(() => {})
}

const getData = (page:number = defaultPage.page) => {
    loading.value = true;

    Http.doGet('user/address', { ...query, page: page, per_page: pagination.per_page }).then((res:any) => {
        loading.value = false;
        if (res.code === 200) {
            tableData.value = res.data
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        loading.value = false;
    })
}
/* 点击分页触发方法 */
const handlePageChange = (page:number,per_page:number) => {
    pagination.per_page = per_page
    getData(page)
}


const areas = ref([]);
const tableData = ref([]);
const updateTitle = ref('');
const subFormRef = ref(null);
const updateLoading = ref(false);
const loading = ref(false);
const dialogFormVisible = ref(false);
const defaultSubForm = {
    id: 0,
    user_id: query.user_id,
    address_detail: "",
    area: [],
    consignee: "",
    phone: "",
    province: "",
    city: "",
    district: "",
}
const subForm = ref({...defaultSubForm});

const rules = reactive({
    consignee: [
        { required: true, message: '请输入收货人', trigger: 'blur' },
    ],
    phone: [
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
const modifyAddress = (row) => {
    subForm.value = {
        id: row.id,
        user_id: query.user_id,
        address_detail: row.address_detail,
        area: row.area,
        consignee: row.consignee,
        phone: row.phone,
        province: row.province,
        city: row.city,
        district: row.district,
    }
    dialogFormVisible.value = true
    updateTitle.value = '编辑地址'
};

// 关闭弹窗
const closePasswordDialog = (form) => {
    subFormRef.value.resetFields()
    dialogFormVisible.value = false;
    Object.assign(subForm,defaultSubForm)
};
const updateForm = () => {
    updateLoading.value = true
    subFormRef.value.validate((valid) => {
        if (valid) {
            Http.doPost('user/address/update', subForm.value).then(function (res) {
                if (res.code === 200) {
                    dialogFormVisible.value = false;
                    cns.$message.success('保存成功');
                    getData();
                } else {
                    cns.$message.error(res.data.message);
                }
            }).catch(function () {
            });
            updateLoading.value = false;
        } else {
            updateLoading.value = false;
            return false;
        }
    })
};

const handleChange = (value) => {
    subForm.value.province = value[0];
    subForm.value.city = value[1];
    subForm.value.district = value[2];
}

onMounted( () => {
    // 解析URL查询字符串
    query.user_id = route.query.user_id;
    getData()
    areasData()
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
