<template>
    <div class="custom-uploader">
        <el-upload
            ref="uploadRef"
            :accept="accepts"
            :show-file-list="false"
            :multiple="type == 'img' && !needCrop && multiple"
            :http-request="(file) => uploadFiles(file)"
            :before-upload="beforeUpload">
            <i class="iconfont icon-jiahao1"></i>
        </el-upload>
        <el-dialog title="图片裁剪" v-model="cropperDialogShow" width="700px" center :show-close="false">
            <div class="cropper-wrap bg-fff" style="width: 660px;height: 660px;">
                <vue-cropper
                    ref="cropperRef"
                    :autoCrop="true"
                    mode="cover"
                    :autoCropWidth="cropSize"
                    :autoCropHeight="cropSize"
                    :fixedBox="true"
                    :img="currentFileCropBlob">
                </vue-cropper>
            </div>
            <div class="s-flex ai-ct jc-ct" style="padding-top: 15px;">
                <el-button type="primary" @click="cropImageConfirm">确定裁剪</el-button>
                <el-button type="default" @click="cropperDialogShow = false, currentFileCropBlob = null">取消
                </el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, getCurrentInstance } from 'vue';
import { VueCropper } from 'vue-cropper';
import 'vue-cropper/dist/index.css';
import { fileUpload } from '@/api/common';

const cns = getCurrentInstance().appContext.config.globalProperties;

const uploadRef = ref(null);
const acceptsImg = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
const acceptsVideo = ['video/mp4'];
const selectedFiles = ref([]); // 存储选择的文件

// 图片裁剪
const currentFileCropBlob = ref(null);
const cropperRef = ref(null);
const cropperDialogShow = ref(false);
const props = defineProps({
    type: {
        type: String,
        default: 'img'  // img, video
    },
    multiple: {
        type: Boolean,
        default: false
    },
    needCrop: {
        type: Boolean,
        default: false
    },
    cropSize: {
        type: Number,
        default: 500
    }
});
const emit = defineEmits(['success']);
const accepts = ref(props.type === 'img' ? 'image/jpeg,image/jpg,image/png,image/gif' : 'video/mp4');
const multipleTimer = ref(null);

const beforeUpload = (file) => {
    if (props.type === 'video') {
        if (acceptsVideo.indexOf(file.type) === -1) {
            cns.$message.error('仅支持mp4格式上传');
            return false;
        }
        const isLt100M = file.size / 1024 / 1024 <= 100;
        if (!isLt100M) {
            cns.$message.error('仅支持mp4格式上传，单个视频不得超过100M!');
            return false;
        }
        return true;
    } else if (props.type === 'img') {
        if (acceptsImg.indexOf(file.type) === -1) {
            cns.$message.error('仅支持.png .jpg .jpeg .gif格式上传');
            return false;
        }
        const isLt5M = file.size / 1024 / 1024 <= 5;
        if (!isLt5M) {
            cns.$message.error('支持 .png .jpg .jpeg .gif格式，单个图片不得超过5M!');
            return false;
        }
        return true;
    }
    return true;
};

const uploadFiles = async (file) => {
    if (props.type === 'img' && props.needCrop) {
        const dataUrl = await loadFile(file.file);
        currentFileCropBlob.value = dataUrl;
        cropperDialogShow.value = true;
    } else {
        // 如果是多选模式，先存储文件
        if (props.multiple && props.type === 'img' && !props.needCrop) {
            // 获取所有选择的文件
            const files = file.file instanceof FileList ? Array.from(file.file) : [file.file];
            selectedFiles.value = [...selectedFiles.value, ...files];

            // 使用 setTimeout 等待所有文件选择完成
            if(multipleTimer.value){
                clearTimeout(multipleTimer.value);
            }
            multipleTimer.value = setTimeout(async () => {
                if (selectedFiles.value.length > 0) {
                    try {
                        // 创建一个 Promise 数组来存储所有上传操作
                        const uploadPromises = selectedFiles.value.map(file => {
                            return new Promise((resolve, reject) => {
                                fileUpload({ file }).then(res => {
                                    if (res.code == 200) {
                                        resolve(res.data.url);
                                    } else {
                                        reject(new Error('上传失败'));
                                    }
                                }).catch(err => {
                                    reject(err);
                                });
                            });
                        });

                        // 等待所有文件上传完成
                        const urls = await Promise.all(uploadPromises);
                        // 所有文件上传完成后，一次性触发success事件
                        emit('success', urls);
                        // 清空已选择的文件
                        selectedFiles.value = [];
                    } catch (err) {
                        cns.$message.error('部分文件上传失败');
                        selectedFiles.value = [];
                    }
                }
            }, 100);
        } else {
            // 单选模式保持原有逻辑
            fileUpload(file).then((res) => {
                if (res.code == 200) {
                    emit('success', [res.data.url]);
                }
            }).catch((err) => {
                cns.$message.error('上传失败');
            });
        }
    }
};
const loadFile = (file) => {
    return new Promise((resolve, reject) => {
        // 创建一个 FileReader 对象
        const reader = new FileReader();
        // 当文件读取成功时触发
        reader.onload = (event) => {
            if (event.target && event.target.result) {
                // 解析为 DataURL 字符串
                resolve(event.target.result);
            } else {
                reject(new Error('文件读取失败'));
            }
        };
        // 当文件读取失败时触发
        reader.onerror = () => {
            reject(new Error('文件读取出错'));
        };
        // 以 DataURL 格式读取文件
        reader.readAsDataURL(file);
    });
};

const blobToFile = (blob) => {
    // 创建 File 对象
    return new File([blob], '裁剪图片.jpg');
};

const cropImageConfirm = () => {
    cropperRef.value.getCropBlob((data) => {
        fileUpload({ file: blobToFile(data) }).then((res) => {
            cropperDialogShow.value = false;
            currentFileCropBlob.value = null;
            if (res.code == 200) {
                emit('success', [res.data.url]);
            }
        }).catch((err) => {
            cns.$message.error('上传失败');
        });
    });
};
</script>

<style scoped lang="scss">
.custom-uploader {
    :deep(.el-upload) {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        background: #fff;
    }

    :deep(.el-upload):hover {
        border-color: #409EFF;
    }

    :deep(.icon-jiahao1) {
        font-size: 28px;
        color: #8c939d;
        width: 84px;
        height: 84px;
        line-height: 84px;
        text-align: center;
    }

    .el-upload__tip {
        font-size: 14px;
        font-weight: normal;
        line-height: 22px;
        color: #9E9E9E;
    }
}
</style>
