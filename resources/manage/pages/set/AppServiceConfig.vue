<script setup>
import Http from '@/utils/http'
import PageTable from '@/components/common/PageTable.vue'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import SearchForm from '@/components/common/SearchForm.vue'
import { ref, reactive, getCurrentInstance, onMounted } from 'vue';

const cns = getCurrentInstance().appContext.config.globalProperties;

const searchForm = reactive({
    name: '',
    alias: '',
    desc: '',
    is_enable: -1,
    per_page: 10,
    page: 1
});
const defaultPage = {
    page: 1,
    per_page: 10
};

const pagination = reactive({ ...defaultPage });

const handlePageChange = (page, per_page) => {
    pagination.per_page = per_page;
    getData(page);
};
const serviceInfoForm = ref({});
const serviceInfoDialogVisible = ref(false);
const tableData = ref([]);
const loading = ref(false);
const storeDialogVisible = ref(false);
const storeDialogTitle = ref('');
const submitFormRef = ref(null);
const submitLoading = ref(false);
const submitForm = reactive({
    id: 0,
    name: '',
    is_enable: 1,
    desc: '',
    is_record: 1,
    error_number: 0,
    stop_number: 0,
    config: [],
});
const submitFormRules = reactive({
    router_category_id: [{ required: true, message: '请选择所属分类', trigger: 'blur' }],
    name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
    alias: [{ required: true, message: '请输入别名', trigger: 'blur' }],
    h5_url: [{ required: true, message: '请输入H5地址', trigger: 'blur' }],
});

const openStoreDialog = (row = {}) => {
    storeDialogTitle.value = row.id > 0 ? '编辑' : '添加';
    if (row.id) {
        submitForm.id = row.id;
        submitForm.name = row.name;
        submitForm.alias = row.alias;
        submitForm.is_enable = row.is_enable;
        submitForm.desc = row.desc;
        submitForm.is_record = row.is_record;
        submitForm.error_number = row.error_number;
        submitForm.stop_number = row.stop_number;
        submitForm.config = row.config;
    } else {
        submitForm.id = 0;
        submitForm.name = '';
        submitForm.alias = '';
        submitForm.is_enable = 1;
        submitForm.desc = '';
        submitForm.is_record = 1;
        submitForm.error_number = 0;
        submitForm.stop_number = 0;
        submitForm.config = [];
    }
    storeDialogVisible.value = true;
};

const closeStoreDialog = () => {
    storeDialogTitle.value = '';
    submitForm.id = 0;
    submitForm.name = '';
    submitForm.alias = '';
    submitForm.is_enable = 1;
    submitForm.desc = '';
    submitForm.is_record = 1;
    submitForm.error_number = 0;
    submitForm.stop_number = 0;
    submitForm.config = [];
    storeDialogVisible.value = false;
};

/* 提交 */
const onSubmit = () => {
    submitFormRef.value.validate((valid) => {
        if (valid) {
            submitLoading.value = true;
            Http.doPost('app_service/update',submitForm).then(res => {
                submitLoading.value = false;
                if (cns.$successCode(res.code)) {
                    closeStoreDialog();
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

const toggleStatus = (row, sign) => {
    Http.doPost('app_service/toggle/status', {
        id: row.id,
        sign: sign
    }).then(res => {
        if (cns.$successCode(res.code)) {
            cns.$message.success(res.message);
        } else {
            cns.$message.error(res.message);
        }
    });
};

const getData = (page = 1) => {
    loading.value = true;
    searchForm.page = page;
    Http.doGet('app_service', searchForm).then(res => {
        loading.value = false;
        if (cns.$successCode(res.code)) {
            tableData.value = res.data;
        } else {
            cns.$message.error(res.message);
        }
    }).catch(() => {
        loading.value = false;
        cns.$message.error('获取数据失败');
    });
};

// 页码改变
const examine = (row) => {
    serviceInfoDialogVisible.value = true
    serviceInfoForm.value = row
};

onMounted(() => {
    getData();
});
</script>
<template>
    <div>
        <search-form :model="searchForm">
            <el-form-item label="名称" prop="name">
                <el-input v-model="searchForm.name" clearable placeholder="请输入" @keyup.enter="getData()" />
            </el-form-item>
            <el-form-item label="别名" prop="alias">
                <el-input v-model="searchForm.alias" clearable placeholder="请输入" @keyup.enter="getData()" />
            </el-form-item>
            <el-form-item label="描述" prop="desc">
                <el-input v-model="searchForm.desc" clearable placeholder="请输入" @keyup.enter="getData()" />
            </el-form-item>
            <el-form-item label="是否启用">
                <el-select v-model="searchForm.is_enable" placeholder="请选择">
                    <el-option label="全部" :value="-1"></el-option>
                    <el-option label="启用" :value="1"></el-option>
                    <el-option label="不启用" :value="0"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="getData()">搜索</el-button>
            </el-form-item>
        </search-form>
        <page-table
            :data="tableData"
            stripe
            border
            @change="handlePageChange"
            v-loading="loading">
            <el-table-column label="ID" prop="id"></el-table-column>
            <el-table-column label="名称" prop="name"></el-table-column>
            <el-table-column label="别名" prop="alias"></el-table-column>
            <el-table-column label="配置">
                <template #default="scope">
                    <el-button type="primary" @click="examine(scope.row)">查看</el-button>
                </template>
            </el-table-column>
            <el-table-column label="描述" prop="sort"></el-table-column>
            <el-table-column label="是否显示" prop="is_enable">
                <template #default="scope">
                    <el-switch
                        v-model="scope.row.is_enable"
                        :active-value="1" :inactive-value="0"
                        active-color="#13ce66" inactive-color="#ff4949"
                        @click="toggleStatus(scope.row, 'is_enable')">
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="是否记录" prop="is_record">
                <template #default="scope">
                    <el-switch
                        v-model="scope.row.is_record"
                        :active-value="1" :inactive-value="0"
                        active-color="#13ce66" inactive-color="#ff4949"
                        @click="toggleStatus(scope.row,'is_record')">
                    </el-switch>
                </template>
            </el-table-column>
            <el-table-column label="异常数量" prop="error_name"></el-table-column>
            <el-table-column label="停止数量" prop="stop_name"></el-table-column>
            <el-table-column label="操作">
                <template #default="scope">
                    <el-button link type="primary" size="large" @click="openStoreDialog(scope.row)">编辑</el-button>
                </template>
            </el-table-column>
        </page-table>
        <el-dialog
            width="700" center :before-close="closeStoreDialog"
            v-model="storeDialogVisible" :title="storeDialogTitle">
            <div class="s-flex jc-ct">
                <el-form :model="submitForm" ref="submitFormRef" :rules="submitFormRules" label-width="auto" style="width: 480px" size="default">
                    <el-form-item label="别名" prop="alias">
                        <el-input v-model="submitForm.alias" :disabled = 'submitForm.id > 0'/>
                    </el-form-item>
                    <el-form-item label="名称" prop="name">
                        <el-input v-model="submitForm.name" />
                    </el-form-item>
                    <div v-if="submitForm.alias === 'ibi_chat'">
                        <el-form-item label="请求地址">
                            <el-input v-model="submitForm.config.host"
                                      :rows="2"
                                      placeholder="请输入内容"></el-input>
                        </el-form-item>
                        <el-form-item label="平台Id">
                            <el-input v-model="submitForm.config.platform_id"
                                      :rows="2"
                                      placeholder="请输入内容"></el-input>
                        </el-form-item>
                        <el-form-item label="平台Secret">
                            <el-input v-model="submitForm.config.platform_secret"
                                      :rows="2"
                                      placeholder="请输入内容"></el-input><br>
                        </el-form-item>
                    </div>
                    <div v-else-if="submitForm.alias === 'geo_amap'">
                        <el-form-item label="请求地址">
                            <el-input v-model="submitForm.config.host"
                                      :rows="2"
                                      placeholder="请输入内容"></el-input>
                        </el-form-item>
                        <el-form-item label="平台Key">
                            <el-input v-model="submitForm.config.key"
                                      :rows="2"
                                      placeholder="请输入内容"></el-input>
                        </el-form-item>
                    </div>
                    <div v-else-if="submitForm.alias === 'kuai_di_100'">
                        <el-form-item label="请求地址">
                            <el-input v-model="submitForm.config.host"
                                      :rows="2"
                                      placeholder="请输入内容"></el-input>
                        </el-form-item>
                        <el-form-item label="平台Key">
                            <el-input v-model="submitForm.config.key"
                                      :rows="2"
                                      placeholder="请输入内容"></el-input>
                        </el-form-item>
                        <el-form-item label="Customer">
                            <el-input v-model="submitForm.config.customer"
                                      :rows="2"
                                      placeholder="请输入内容"></el-input>
                        </el-form-item>
                    </div>
                    <el-form-item label="是否启用" prop="is_enable">
                        <el-switch
                            v-model="submitForm.is_enable"
                            :active-value="1" :inactive-value="0"
                            active-color="#13ce66" inactive-color="#ff4949">
                        </el-switch>
                    </el-form-item>
                    <el-form-item label="描述" prop="desc">
                        <el-input v-model="submitForm.desc" />
                    </el-form-item>
                    <el-form-item label="是否记录" prop="is_record">
                        <el-switch
                        v-model="submitForm.is_record"
                        :active-value="1" :inactive-value="0"
                        active-color="#13ce66" inactive-color="#ff4949">
                        </el-switch>
                    </el-form-item>
                    <el-form-item label="异常数量" prop="error_number">
                        <el-input v-model="submitForm.error_number" />
                        <div>0为无限制</div>
                    </el-form-item>
                    <el-form-item label="停止数量" prop="stop_number">
                        <el-input v-model="submitForm.stop_number" />
                        <div>0为无限制</div>
                    </el-form-item>
                </el-form>
            </div>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="closeStoreDialog()">取消</el-button>
                    <el-button type="primary" :loading="submitLoading" @click="onSubmit()">提交</el-button>
                </div>
            </template>
        </el-dialog>

        <el-dialog
            width="700" center v-model="serviceInfoDialogVisible" title="配置信息">
            <div class="s-flex jc-ct">
                <el-form :model="serviceInfoForm" label-width="auto" style="width: 480px" size="default">
                    <template v-if="serviceInfoForm.alias == 'ibi_chat'">
                        <el-form-item label="请求地址">
                            <el-input disabled v-model="serviceInfoForm.config.host" />
                        </el-form-item>
                        <el-form-item label="平台Id">
                            <el-input disabled v-model="serviceInfoForm.config.platform_id" />
                        </el-form-item>
                        <el-form-item label="平台Secret">
                            <el-input disabled v-model="serviceInfoForm.config.platform_secret" />
                        </el-form-item>
                    </template>
                    <template v-else-if="serviceInfoForm.alias == 'geo_amap'">
                        <el-form-item label="请求地址">
                            <el-input disabled v-model="serviceInfoForm.config.host" />
                        </el-form-item>
                        <el-form-item label="平台Key">
                            <el-input disabled v-model="serviceInfoForm.config.key" />
                        </el-form-item>
                    </template>
                    <template v-else-if="serviceInfoForm.alias == 'kuai_di_100'">
                        <el-form-item label="请求地址">
                            <el-input disabled v-model="serviceInfoForm.config.host" />
                        </el-form-item>
                        <el-form-item label="平台Key">
                            <el-input disabled v-model="serviceInfoForm.config.key" />
                        </el-form-item>
                        <el-form-item label="Customer">
                            <el-input disabled v-model="serviceInfoForm.config.customer" />
                        </el-form-item>
                    </template>
                </el-form>
            </div>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="serviceInfoDialogVisible = false">关闭</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<style scoped lang="scss">

</style>
