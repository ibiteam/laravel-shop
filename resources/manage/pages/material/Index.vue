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
                            :data="folderData"
                            draggable
                            default-expand-all
                            node-key="id"
                            @node-drag-start="handleDragStart"
                            @node-drag-enter="handleDragEnter"
                            @node-drag-leave="handleDragLeave"
                            @node-drag-over="handleDragOver"
                            @node-drag-end="handleDragEnd"
                            @node-drop="handleDrop"
                            :expand-on-click-node="false"
                        >
                            <template #default="{ node, data }">
                                <div class="custom-tree-node" @click="checkDir(data.id, data.type)">
                                    <i class="iconfont" style="color: var(--main-color);margin-right: 5px;">&#xe600;</i>
                                    <span>{{ data.name }}</span>
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
                                <el-input v-model="searchForm.admin_name" placeholder="请输入添加人"></el-input>
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
                                <el-date-picker
                                    v-model="searchForm.time"
                                    type="daterange"
                                    style="width: 300px;"
                                    start-placeholder="开始日期"
                                    value-format="YYYY-MM-DD"
                                    end-placeholder="结束日期"
                                />
                            </div>
                            <div class="search-form-item">
                                <el-button type="primary" @click="searchMaterial" style="margin-right: 10px;">查询</el-button>
                                <el-upload
                                    class="logo-uploader"
                                    :accept="tabValue === '1' ? 'image/jpeg,image/jpg,image/png' : 'video/mp4,video/ogg,video/flv,video/avi,video/wmv,video/rmvb'"
                                    action=""
                                    :show-file-list="false"
                                    :http-request="(request) => uploadFile(request, 'shop_logo')"
                                    :with-credentials="true"
                                >
                                    <el-button type="danger">上传文件</el-button>
                                </el-upload>
                                <el-button type="default" @click="openAddFolder">新建文件夹</el-button>
                            </div>
                        </div>
                        <div class="material-ctrl" style="padding: 10px 0;">
                            <el-button type="default" size="small" @click="handleBatchDelete">批量删除</el-button>
                            <el-button type="default" size="small" @click="batchMoveTo">批量移动</el-button>
                            <el-select placeholder="排序方式" v-model="searchForm.sort" style="width: 150px;margin-left: 10px;" size="small">
                                <el-option label="默认排序" value="0"></el-option>
                                <el-option label="最新上传在前" value="1"></el-option>
                                <el-option label="最新上传在后" value="2"></el-option>
                                <el-option label="最新更新在前" value="3"></el-option>
                                <el-option label="最新更新在后" value="4"></el-option>
                                <el-option label="按文件名降序" value="5"></el-option>
                                <el-option label="按文件名升序" value="6"></el-option>
                            </el-select>
                        </div>
                        <div class="material-list">
                            <el-table
                                :data="tableList"
                                @selection-change="handleSelectionChange"
                                style="width: 100%"
                                v-loading="tableListLoading"
                                row-key="id"
                                ref="materialTableRef"
                            >
                                <el-table-column type="selection" width="55" />
                                <el-table-column prop="name" label="素材名称" width="220">
                                    <template #default="scope">
                                        <div class="s-flex ai-ct">
                                            <div @click="checkDir(scope.row.id, scope.row.type)" :class="scope.row.type === 1 ? 'pointer' : ''">
                                                <div v-if="scope.row.type == 1">
                                                    <div class="material-table-item-name">
                                                        <i class="iconfont folder" style="color: var(--main-color);margin-right: 5px;">&#xe600;</i>
                                                        {{scope.row.name}}
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <div class="material-table-item-name">
                                                        <div class="material-img">
                                                            <img :src="scope.row.file_path" alt="" />
                                                        </div>
                                                        {{scope.row.name}}
                                                    </div>
                                                </div>
                                            </div>
                                            <i class="iconfont edit-icon" @click="editMaterial(scope.row)">&#xe79a;</i>
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column label="添加人" width="150">
                                    <template #default="scope">
                                        <div class="s-flex ai-ct" v-if="scope.row.admin_user">
                                            {{scope.row.admin_user.user_name}}
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="px" label="尺寸" width="120">
                                    <template #default="scope">
                                        {{scope.row.type == 2 && scope.row.dir_type == 1 ? scope.row.width+'*'+scope.row.height : '--'}}
                                    </template>
                                </el-table-column>
                                <el-table-column prop="size" label="大小" width="100">
                                    <template #default="scope">
                                        {{scope.row.type == 1?'--':scope.row.size}}
                                    </template>
                                </el-table-column>
                                <el-table-column prop="updated_at" label="更新时间"/>
                                <el-table-column label="操作">
                                    <template #default="scope">
                                        <el-button type="text" size="small" @click="moveTo(scope.row)">移动至</el-button>
                                        <el-button type="text" size="small" @click="handleDelete(scope.row.id)">删除</el-button>
                                    </template>
                                </el-table-column>
                            </el-table>

                        </div>
                        <!-- 添加分页组件 -->
                        <div class="pagination-container" v-if="pageInfo.total > 0" style="padding-top:20px;">
                            <el-pagination
                                @size-change="handleSizeChange"
                                @current-change="handleCurrentChange"
                                :current-page="pageInfo.current_page"
                                :page-sizes="[10, 15, 30, 50, 100]"
                                :page-size="pageInfo.per_page"
                                layout="total, sizes, prev, pager, next, jumper"
                                :total="pageInfo.total">
                            </el-pagination>
                        </div>
                    </div>
                </div>
            </div>
            <el-dialog v-model="editMaterialVisible" :title="editMaterialTitle" width="500" center :close-on-click-modal="false" :close-on-press-escape="false">
                <div class="edit-material-form" style="padding-top: 30px;">
                    <el-form :model="currentCtrlMaterial" ref="ctrlMaterialRef" label-width="120px" :rules="ctrlMaterialRules">
                        <el-form-item label="上级文件夹" prop="parent_id" v-if="editMaterialType == 'move' || editMaterialType == 'new-folder' || editMaterialType == 'batch_move'">
                            <el-tree-select
                                v-model="currentCtrlMaterial.parent_id"
                                :data="folderData"
                                :props="{label: 'name',value: 'id'}"
                                :check-strictly="true"
                                placeholder="请选择上一级文件夹"
                                :render-after-expand="false"
                                style="width: 240px">
                                <template #default="{ node, data }">
                                    <span>{{ data.name }}</span>
                                </template>
                            </el-tree-select>
                        </el-form-item>
                        <el-form-item label="名称" prop="name" v-if="editMaterialType == 'new-folder'|| editMaterialType == 'only-name'">
                            <el-input v-model="currentCtrlMaterial.name" placeholder="请输入文件/文件夹名称" style="width: 240px"/>
                        </el-form-item>
                    </el-form>
                </div>
                <div class="s-flex ai-ct jc-ct">
                    <el-button v-if="editMaterialType == 'new-folder'" type="primary" @click="handleEditMaterial">确定</el-button>
                    <el-button v-else-if="editMaterialType == 'only-name'" type="primary" @click="handleRenameMaterial">确定</el-button>
                    <el-button v-else-if="editMaterialType == 'move'" type="primary" @click="handleMove">确定</el-button>
                    <el-button v-else type="primary" @click="handleBatchMove">确定</el-button>
                    <el-button type="default" @click="closeMaterialDialog">取消</el-button>
                </div>
            </el-dialog>
        </div>
    </div>
</template>
<script setup>
import { ref, reactive, getCurrentInstance, watch, onMounted } from 'vue';
const cns = getCurrentInstance().appContext.config.globalProperties
import { folderList, materialIndex, rename, newFolder, destory, batchDestory, batchMove, move, materialUpload } from '@/api/material.js';

const tabValue = ref('1');
const multipleSelection = ref([]);
const multipleSelectionId = ref('');
const searchForm = ref({
    name: '', // 素材名称
    admin_name:'', // 添加人
    type: '0', // 素材类型 1、文件夹 2、文件
    time: '',
    page: 1,
    sort: '0', // 排序字段
    dir_type: 1, // 文件夹类型 1、图片 2、视频
    parent_id: 0, // 文件夹id
})
const tableList = ref([])
const tableListLoading = ref(true)
const folderData = ref([])
const currentCtrlMaterial = ref({
    'parent_id' : 0,
    'name' : '',
    'dir_type' : 1,
})
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

// 添加分页相关状态
const pageInfo = reactive({
    total: 0,
    per_page: 10,
    current_page: 1
});


// 页码改变
const handleCurrentChange = (val) => {
    getMaterialData(val);
}

// 每页条数改变
const handleSizeChange = (val) => {
    searchForm.number = val;
    pageInfo.per_page = val;
    getMaterialData(1);
}

// 设置分页数据
const setPageInfo = (meta) => {
    pageInfo.total = meta.total;
    pageInfo.per_page = Number(meta.per_page);
    pageInfo.current_page = meta.current_page;
}

onMounted( () => {
    getFolderData()
});

const uploadFile = async (request, type) => {
    const info = {
        file: request.file,
        parent_id: searchForm.value.parent_id,
        dir_type: searchForm.value.dir_type
    }
    try {
        const res = await materialUpload(info);
        if (res.code === 200) {
            getFolderData()
        } else {
            cns.$message.error(res.message)
        }
    } catch (error) {
        console.error('Failed:', error);
    }
};

const handleSelectionChange = (val) => {
    multipleSelection.value = []
    // 筛选出 id 字段并存入数组
    multipleSelection.value = val.map(item => item.id);
}

const searchMaterial = () => {
    getMaterialData()
}

const getMaterialData = (page = 1) => {
    // 更新当前页码
    searchForm.value.page = page;
    tableListLoading.value = true;
    materialIndex(searchForm.value).then(res => {
        if (res.code === 200) {
            tableList.value = res.data.list;
            // 更新分页信息
            setPageInfo(res.data.meta);
        } else {
            cns.$message.error(res.message)
        }
        tableListLoading.value = false;
    }).catch(() => {})
}

const getFolderData = () => {
    folderList({dir_type: tabValue.value}).then(res => {
        if (res.code === 200) {
            folderData.value = res.data;
            searchForm.value.dir_type = tabValue.value;
            getMaterialData()
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {})
}


const editMaterial = (item) => {
    currentCtrlMaterial.value = item;
    editMaterialTitle.value = item.type == 1 ? '修改文件夹名':'修改文件名';
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
const closeMaterialDialog = () => {
    getFolderData()
    editMaterialVisible.value = false;
    editMaterialTitle.value = '';
    editMaterialType.value = '';
}
/**
 * 公共方法：更新 folderData 并禁用目标节点及其子节点
 */
const updateFolderData = (ids) => {
    // 复制 folderData 以避免直接修改原始数据
    const updatedFolderData = JSON.parse(JSON.stringify(folderData.value));

    // 禁用目标节点及其所有子节点
    disableFolderAndChildren(updatedFolderData, ids);

    // 更新 folderData
    folderData.value = updatedFolderData;
    console.log(folderData.value);
};

/**
 * 移动单个文件或文件夹
 */
const moveTo = (item) => {
    multipleSelectionId.value = item.id; // 记录当前选中的单个节点 ID
    editMaterialTitle.value = '移动至';
    editMaterialVisible.value = true;
    editMaterialType.value = 'move';

    // 调用公共方法更新 folderData
    updateFolderData([multipleSelectionId.value]);
};

/**
 * 批量移动多个文件或文件夹
 */
const batchMoveTo = () => {
    editMaterialTitle.value = '移动至';
    editMaterialVisible.value = true;
    editMaterialType.value = 'batch_move';

    // 调用公共方法更新 folderData
    updateFolderData(multipleSelection.value);
};

/**
 * 递归查找并设置禁用状态（包括所有子节点）
 */
const disableFolderAndChildren = (data, ids) => {
    for (let d = 0; d < ids.length; d++) {
        for (let i = 0; i < data.length; i++) {
            if (data[i].id === ids[d]) {
                // 禁用当前节点
                data[i].disabled = true;

                // 如果有子节点，递归禁用所有子节点
                if (data[i].children && data[i].children.length > 0) {
                    disableAllChildren(data[i].children);
                }
            }

            // 如果当前节点有子节点，继续递归查找
            if (data[i].children && data[i].children.length > 0) {
                disableFolderAndChildren(data[i].children, ids);
            }
        }
    }
};

/**
 * 递归禁用所有子节点
 */
const disableAllChildren = (children) => {
    for (let i = 0; i < children.length; i++) {
        children[i].disabled = true;

        // 如果子节点还有子节点，继续递归禁用
        if (children[i].children && children[i].children.length > 0) {
            disableAllChildren(children[i].children);
        }
    }
};

const handleRenameMaterial = () => {
    ctrlMaterialRef.value.validate((valid) => {
        if (valid) {
            rename(currentCtrlMaterial.value).then(res => {
                if (res.code === 200) {
                    cns.$message.success('保存成功')
                    getFolderData()
                    editMaterialVisible.value = false;
                } else {
                    cns.$message.error(res.message)
                }
            }).catch(() => {})
        } else {
            return false;
        }
    })
}
const handleEditMaterial = () => {
    currentCtrlMaterial.value.dir_type = tabValue.value
    ctrlMaterialRef.value.validate((valid) => {
        if (valid) {
            newFolder(currentCtrlMaterial.value).then(res => {
                if (res.code === 200) {
                    cns.$message.success('保存成功')
                    getFolderData()
                    editMaterialVisible.value = false;
                } else {
                    cns.$message.error(res.message)
                }
            }).catch(() => {})
        } else {
            return false;
        }
    })
}
const handleDelete = (id) => {
    cns.$dialog.confirm({ message:'此操作将删除本文件和关联所有子文件, 是否继续?', title:'提示' }).then(() => {
        destory({id: id}).then(res => {
            if (res.code === 200) {
                cns.$message.success('删除成功')
                getFolderData()
            } else {
                cns.$message.error(res.message)
            }
        }).catch(() => {
            cns.$message.error('删除失败')
        })
    }).catch(() => {
        cns.$message.error('取消删除')
    })
}
const handleBatchDelete = () => {
    cns.$dialog.confirm({ message:'此操作将删除选中文件和关联所有子文件, 是否继续?', title:'提示' }).then(() => {
        batchDestory({ids: multipleSelection.value}).then(res => {
            if (res.code === 200) {
                cns.$message.success('删除成功')
                getFolderData()
            } else {
                cns.$message.error(res.message)
            }
        }).catch(() => {
            cns.$message.error('删除失败')
        })
    }).catch(() => {
        cns.$message.error('取消删除')
    })
}
const handleMove = () => {
    const info = {
        id: multipleSelectionId.value,
        target_directory_id: currentCtrlMaterial.value.parent_id
    }
    move(info).then(res => {
        if (res.code === 200) {
            editMaterialVisible.value = false
            cns.$message.success('保存成功')
            getFolderData()
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        cns.$message.error('取消移动')
    })
}
const handleBatchMove = () => {
    const info = {
        ids: multipleSelection.value,
        target_directory_id: currentCtrlMaterial.value.parent_id
    }
    console.log(info);
    batchMove(info).then(res => {
        if (res.code === 200) {
            editMaterialVisible.value = false
            cns.$message.success('保存成功')
            getFolderData()
        } else {
            cns.$message.error(res.message)
        }
    }).catch(() => {
        cns.$message.error('取消移动')
    })
}
const checkDir = (id, type) => {
    if (type === 1 || id === 0) {
        searchForm.value.parent_id = id
        getMaterialData()
    }
}
const changTab = () => {
    getFolderData()
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
    if (dropNode.data.name === 'Level two 3-1') {
        return type !== 'inner'
    } else {
        return true
    }
}
const allowDrag = (draggingNode) => {
    return !draggingNode.data.name.includes('Level three 3-1-1')
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
        .logo-uploader{
            margin-right: 10px;
        }
        :deep(.logo-uploader .el-upload){
            border:none;
            &:hover{
                border:none;
            }
        }
    }
}
.pointer {
    cursor: pointer;
}
</style>
