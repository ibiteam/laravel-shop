<template>
    <div class="manage-public-wrap" style="height: 100%;">
        <div class="manage-public-cont" style="height: 100%;">
            <div id="listener-drag-upload" style="position:relative;">
                <input type="file" style="width:100%;height:100%; position: absolute; left: 0; top: 0; border: none; outline: none;" hidden :style="{zIndex: is_index ? '1' : '-1'}" multiple/>
                <div class="material-content">
                    <el-tabs v-model="tabValue" class="demo-tabs" @tab-click="changTab">
                        <el-tab-pane label="图片" name="1"></el-tab-pane>
                        <el-tab-pane label="视频" name="2"></el-tab-pane>
                    </el-tabs>
                </div>
                <div class="s-flex main-material-wrap">
                    <div class="left-menu">
                        <el-tree
                            style="max-width: 260px"
                            :allow-drop="allowDrop"
                            :allow-drag="allowDrag"
                            :data="data"
                            draggable
                            default-expand-all
                            node-key="id"
                            @node-drag-start="handleDragStart"
                            @node-drag-enter="handleDragEnter"
                            @node-drag-leave="handleDragLeave"
                            @node-drag-over="handleDragOver"
                            @node-drag-end="handleDragEnd"
                            @node-drop="handleDrop"
                        >
                            <template #default="{ node, data }">
                                <div class="custom-tree-node">
                                    <i class="iconfont" style="color: var(--main-color);margin-right: 5px;">&#xe600;</i>
                                    <span>{{ node.label }}(5)</span>
                                </div>
                            </template>
                        </el-tree>
                    </div>
                    <div class="right-mater">
                        <div class="search-form s-flex flex-wrap">
                            <div class="search-form-item">
                                <span>素材名称</span>
                                <el-input v-model="searchForm.name" placeholder="请输入素材名称"></el-input>
                            </div>
                            <div class="search-form-item">
                                <span>添加人</span>
                                <el-input v-model="searchForm.username" placeholder="请输入素材名称"></el-input>
                            </div>
                            <div class="search-form-item">
                                <span>类型</span>
                                <el-select placeholder="请选择" v-model="searchForm.type" style="width: 150px;">
                                    <el-option label="全部" value="0"></el-option>
                                    <el-option label="文件夹" value="1"></el-option>
                                    <el-option label="素材" value="2"></el-option>
                                </el-select>
                            </div>
                            <div class="search-form-item">
                                <span>更新时间</span>
                                <el-date-picker v-model="searchForm.time" type="daterange" style="width: 300px;" start-placeholder="开始日期" value-format="yyyy-MM-dd" end-placeholder="结束日期"/>
                            </div>
                            <div class="search-form-item">
                                <el-button type="primary">查询</el-button>
                                <el-button type="danger">上传文件</el-button>
                                <el-button type="default" @click="openAddFolder">新建文件夹</el-button>
                            </div>
                        </div>
                        <div class="material-ctrl" style="padding: 10px 0;">
                            <el-button type="default" size="small">批量删除</el-button>
                            <el-button type="default" size="small">批量移动</el-button>
                            <el-select placeholder="排序方式" v-model="searchForm.sort" style="width: 150px;margin-left: 10px;" size="small">
                                <el-option label="默认排序" value="0"></el-option>
                                <el-option label="最近更新在前" value="1"></el-option>
                                <el-option label="最近更新在后" value="2"></el-option>
                            </el-select>
                        </div>
                        <div class="material-list">
                            <el-table :data="tableList" style="width: 100%" row-key="id" ref="materialTableRef">
                                <el-table-column type="selection" width="55" />
                                <el-table-column prop="name" label="素材名称" width="220">
                                    <template #default="scope">
                                        <div class="s-flex ai-ct">
                                            <div v-if="scope.row.is_folder == 1">
                                                <div class="material-table-item-name">
                                                    <i class="iconfont folder" style="color: var(--main-color);margin-right: 5px;">&#xe600;</i>
                                                    {{scope.row.name}}
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div class="material-table-item-name">
                                                    <div class="material-img">
                                                        <img :src="scope.row.url" alt="" />
                                                    </div>
                                                    {{scope.row.name}}
                                                </div>
                                            </div>
                                            <i class="iconfont edit-icon" @click="editMaterial(scope.row)">&#xe79a;</i>
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="username" label="添加人" width="150"/>
                                <el-table-column prop="px" label="尺寸" width="120">
                                    <template #default="scope">
                                        {{scope.row.is_folder == 1?'--':scope.row.px}}
                                    </template>
                                </el-table-column>
                                <el-table-column prop="size" label="大小" width="100">
                                    <template #default="scope">
                                        {{scope.row.is_folder == 1?'--':scope.row.size}}
                                    </template>
                                </el-table-column>
                                <el-table-column prop="update_time" label="更新时间"/>
                                <el-table-column prop="id" label="操作">
                                    <template #default="scope">
                                        <el-button type="text" size="small" @click="moveTo(scope.row)">移动至</el-button>
                                        <el-button type="text" size="small" @click="handleDelete(scope.row.id)">删除</el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>
                    </div>
                </div>
            </div>
            <el-dialog v-model="editMaterialVisible" :title="editMaterialTitle" width="500" center>
                <div class="edit-material-form" style="padding-top: 30px;">
                    <el-form :model="currentCtrlMaterial" ref="ctrlMaterialRef" label-width="120px" :rules="ctrlMaterialRules">
                        <el-form-item label="上级文件夹" prop="parent_id" v-if="editMaterialType == 'move' || editMaterialType == 'new-folder'">
                            <el-tree-select
                                v-model="currentCtrlMaterial.parent_id"
                                :data="data"
                                check-strictly
                                placeholder="请选择上一级文件夹"
                                :render-after-expand="false"
                                style="width: 240px"
                            />
                        </el-form-item>
                        <el-form-item label="名称" prop="name" v-if="editMaterialType == 'new-folder'|| editMaterialType == 'only-name'">
                            <el-input v-model="currentCtrlMaterial.name" placeholder="请输入文件/文件夹名称" style="width: 240px"/>
                        </el-form-item>
                    </el-form>
                </div>
                <div class="s-flex ai-ct jc-ct">
                    <el-button type="primary" @click="handleEditMaterial">确定</el-button>
                    <el-button type="default" @click="editMaterialVisible = false">取消</el-button>
                </div>
            </el-dialog>
        </div>
    </div>
</template>
<script setup>
import {ref ,getCurrentInstance,watch} from 'vue';
const cns = getCurrentInstance().appContext.config.globalProperties

const tabValue = ref('1');
const searchForm = ref({
    name: '',
    username:'',
    type: '',
    time: '',
    sort: ''
})
const tableList = ref([{
    id: 1,
    name: '1.jpg',
    url: 'https://cdn.feidoodoo.com/2024/12/26/1735193709520_小猫.jpg',
    type: '1',
    px:'400*400',
    size: '1.2M',
    update_time: '2021-01-01 00:00:00',
    username: 'admin',
    is_folder: 0
},{
    id: 2,
    name: '文件夹',
    url: '',
    type: '1',
    size: '1.2M',
    update_time: '2021-01-01 00:00:00',
    username: 'admin',
    is_folder: 1
}])
const data = ref([
    {
        label: '商品图',
        children: [
            {
                label: '生鲜',
                children: [
                    {
                        label: '肉类',
                    },
                    {
                        label: '海鲜',
                    }
                ],
            },
            {
                label: '水果',
                children: [
                    {
                        label: '热带水果',
                    },
                    {
                        label: '北方水果',
                    }
                ],
            },
        ],
    },
    {
        label: '活动图',
        children: [
            {
                label: '双十'
            },
            {
                label: '双十一'
            },
        ],
    },
    {
        label: '服务',
        children: [
            {
                label: '售后服务'
            },
            {
                label: '售前服务'
            },
        ],
    }
])

const currentCtrlMaterial = ref({})
const ctrlMaterialRef = ref(null);
const ctrlMaterialRules = ref({
    parent_id: [
        { required: true, message: '请选择上级文件夹', trigger: 'blur'}
    ],
    name: [
        { required: true, message: '请输入文件名', trigger: 'blur'}
    ]
});
const editMaterialVisible = ref(false);
const editMaterialTitle = ref('');
const editMaterialType = ref('');

const editMaterial = (item) => {
    currentCtrlMaterial.value = item;
    editMaterialTitle.value = item.is_folder == 1 ? '修改文件名':'修改文件夹名';
    editMaterialVisible.value = true;
    editMaterialType.value = 'only-name'
}

const openAddFolder = () => {
    currentCtrlMaterial.value = {
        parent_id: 0,
        name: ''
    }
    editMaterialTitle.value = '新建文件夹';
    editMaterialVisible.value = true;
    editMaterialType.value = 'new-folder'
}
const moveTo = (item) => {
    currentCtrlMaterial.value = item
    editMaterialTitle.value = '移动至';
    editMaterialVisible.value = true;
    editMaterialType.value = 'move'
}
const handleEditMaterial = () => {
    ctrlMaterialRef.value.validate((valid) => {
        if (valid) {
            cns.$message.success('修改成功')
            editMaterialVisible.value = false;
        } else {
            return false;
        }
    })
}
const handleDelete = (id) => {
    cns.$dialog.confirm({ message:'此操作将永久删除该文件, 是否继续?', title:'提示' }).then(() => {
        cns.$message.success('删除成功')
    }).catch(() => {
        cns.$message.error('操作失败')
    })
}
const changTab = (value) => {
    tabValue.value = value;
}
const materialTableRef = ref(null);
const handleDragStart = (node, ev) => {
    console.log('drag start', node)
}
const handleDragEnter = (draggingNode, dropNode, ev) => {
    console.log('tree drag enter:', dropNode.label)
}
const handleDragLeave = (draggingNode, dropNode, ev) => {
    console.log('tree drag leave:', dropNode.label)
}
const handleDragOver = (draggingNode, dropNode, ev) => {
    console.log('tree drag over:', dropNode.label)
}
const handleDragEnd = (draggingNode, dropNode, dropType, ev) => {
    console.log('tree drag end:', dropNode && dropNode.label, dropType)
}
const handleDrop = (draggingNode, dropNode, dropType, ev) => {
    console.log('tree drop:', dropNode.label, dropType)
}
const allowDrop = (draggingNode, dropNode, type) => {
    if (dropNode.data.label === 'Level two 3-1') {
        return type !== 'inner'
    } else {
        return true
    }
}
const allowDrag = (draggingNode) => {
    return !draggingNode.data.label.includes('Level three 3-1-1')
}
</script>
<style scoped lang="scss">
.material-content{
    :deep(.el-tabs__header){
        margin: 0;
    }
}
.main-material-wrap{
    .left-menu{
        padding: 20px 0;
        width: 260px;
        border-right: 1px solid #eee;
    }
}
.right-mater{
    padding: 15px;
    .material-list{
        .edit-icon{
            margin-left: 8px;
            cursor: pointer;
            &:hover{
                color: var(--main-color);
            }
        }
        .material-table-item-name{
            display: flex;
            align-items: center;
            .folder{
                width: 40px;
                height: 40px;
                margin-right: 10px;
                font-size: 40px;
                line-height: 40px;
            }
            .material-img{
                width: 40px;
                height: 40px;
                margin-right: 10px;
                overflow: hidden;
                border-radius: 3px;
                img{
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
            }

        }
    }
}
.search-form{
    .search-form-item{
        display: flex;
        align-items: center;
        margin: 5px 0;
        padding-right: 20px;
        >span{
            white-space: nowrap;
            margin-right: 10px;
        }
    }
}
</style>
