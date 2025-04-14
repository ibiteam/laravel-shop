<template>
    <el-container class="decoration-layout-container" id="decorationLayoutContainer">
        <el-aside class="decoration-layout-aside" width="300px">
            <slot name="aside-content"></slot>
        </el-aside>
        <el-main class="decoration-layout-main">
            <el-header class="decoration-layout-header s-flex ai-ct jc-bt" height="50px">
                <div class="header-left s-flex ai-ct">
                    <!-- <el-link :underline="false" class="header-left-back"><em class="iconfont icon-to_left"></em>返回</el-link>
                    <el-divider direction="vertical" /> -->
                    <span v-if="pageName" class="fs16 fw-b">{{ pageName }}装修</span>
                    <el-divider v-if="time" direction="vertical" />
                    <span v-if="time" class="co-999 fs12">发布时间：{{ time }}</span>
                </div>
                <div class="header-right">
                    <el-popover placement="bottom" :width="500" trigger="click">
                        <template #reference>
                            <el-button text>历史记录</el-button>
                        </template>
                        <PublicPageTable
                            v-loading="history.loading"
                            :data="history.data"
                            :pageInfo="pageInfo"
                            minHeight="400px"
                            @sizeChange="handleSizeChange"
                            @currentChange="handleCurrentChange"
                            style="width: 100%; min-height: 400px;">
                            <el-table-column property="created_at" width="180" label="操作时间" />
                            <el-table-column property="admin_user_name" label="操作人" />
                            <el-table-column label="操作">
                                <template #default="scope">
                                    <el-popconfirm title="确定还原到当前版本吗？" placement="bottom" confirm-button-text="确定" cancel-button-text="取消" @confirm="handleResetDecoration(scope.row)">
                                        <template #reference>
                                            <el-button link type="primary" size="large">编辑</el-button>
                                        </template>
                                    </el-popconfirm>
                                </template>
                            </el-table-column>
                        </PublicPageTable>
                    </el-popover>
                    
                    <el-button text @click.stop="emit('pageSetting')">页面配置</el-button>
                    <el-button @click.stop="emit('pageSave', {button_type: 1})" type="primary">保存草稿</el-button>
                    <el-button plain @click.stop="emit('pageSave', {button_type: 2})">预览</el-button>
                    <el-button @click.stop="emit('pageSave', {button_type: 3})" type="danger">发布</el-button>
                </div>
            </el-header>
            <div class="main-content-wrapper">
                <slot name="main-content"></slot>
            </div>
        </el-main>
    </el-container>
</template>

<script setup>
import { ref, reactive, getCurrentInstance, watch } from 'vue'
import PublicPageTable from '@/components/common/PublicPageTable.vue';
import { appDecorationHistory,appDecorationHistoryRestore } from '@/api/decoration.js'

const cns = getCurrentInstance().appContext.config.globalProperties
const emit = defineEmits(['pageSetting', 'pageSave'])
const props = defineProps({
    pageName: {
        type: String,
        default: ''
    },
    time: {
        type: [String, null],
        default: null
    },
    id: {
        type: String,
        default: ''
    }
})

const history = reactive({
    loading: false,
    data: [],
})

// 添加分页相关状态
const pageInfo = reactive({
    number: 10,
    total: 0,
    current_page: 1,
})

// 页码改变
const handleCurrentChange = (val) => {
    getHistory(val);
}

// 每页条数改变
const handleSizeChange = (val) => {
    pageInfo.per_page = val;
    getHistory(1);
}

// 获取历史记录
const getHistory = (params = {page: 1}) => {
    if (history.loading) return
    const {page} = params;
    history.loading = true
    appDecorationHistory({id: props.id, page, number: pageInfo.per_page}).then(res => {
        if (cns.$successCode(res.code)) {
            history.data = res.data.list;
            // // 更新分页信息
            history.total = res.data.meta.total;
            history.per_page = Number(res.data.meta.per_page);
            history.current_page = res.data.meta.current_page;
        } else {
            cns.$message.error(res.message)
        }
        history.loading = false
    }).catch(() => {
        history.loading = false
    })
}

// 点击还原
const handleResetDecoration = (row) => {
    const { log_id } = row
    appDecorationHistoryRestore({log_id}).then(res => {
        if (cns.$successCode(res.code)) {
            cns.$bus.emit('layoutReload')
        } else {
            cns.$message.error(res.message)
        }
    })
}

watch(() => props.id, (newValue) => {
    if (newValue) {
        getHistory()
    }
})

</script>

<style lang='scss' scoped>
.decoration-layout-container {
    width: 100%;
    height: 100%;
    overflow: hidden;
    position: relative;
    .decoration-layout-aside {
        position: relative;
        border-right: 1px solid #D8D8D8;
        box-sizing: border-box;
    }
    .decoration-layout-header {
        background: #fff;
        border-bottom: 1px solid #D8D8D8;
        box-sizing: border-box;
        user-select: none;
        .header-left,.header-left-back {
            color: #333;
        }
    }
    .decoration-layout-main{
        padding: 0;
        .main-content-wrapper {
            position: relative;
            height: calc(100% - 50px);
        }
    }
}
</style>