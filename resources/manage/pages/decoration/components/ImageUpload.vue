<template>
    <div class="uploader s-flex ai-ct jc-ct" :style="{width: width +'px', height: height + 'px', backgroundColor}" v-loading="uploading">
        <el-image v-if="src" :src="src" :fit="fit" :style="{width: width +'px', height: height + 'px'}">
        </el-image>
        <em class="iconfont icon-jiahao1" v-else></em>
        <div class="upload-modal s-flex ai-ct jc-ct">
            <em class="iconfont icon-shangchuan1 icon-btn" @click="emit('material')" title="从素材库选择"></em>
            <em class="iconfont icon-shangchuan icon-btn" @click="handleLocalUpload" title="从本地上传图片"></em>
            <em class="iconfont icon-shanchu icon-btn" v-if="src" @click="emit('remove')" title="删除图片"></em>
        </div>
        <input type="file" ref="uploadImageRef" style="display: none" @change="beforeUpload" accept="image/jpeg,image/jpg,image/png,image/gif">
    </div>
</template>

<script setup>
import { getCurrentInstance, ref } from 'vue';
import { fileUpload } from '@/api/common';

const cns = getCurrentInstance().appContext.config.globalProperties
const props = defineProps({
    width: {
        type: [String, Number],
        default: 64
    },
    height: {
        type: [String, Number],
        default: 64
    },
    backgroundColor: {
        type: String,
        default: '#fff'
    },
    src: {
        type: String,
        default: '',
    },
    fit: {
        type: String,
        default: 'fill', // cover, contain, fill, none, scale-down
    }
})

const uploadImageRef = ref(null)
const uploading = ref(false)

const emit = defineEmits(['material', 'remove', 'local'])

const beforeUpload = (event) => {
    const file = event.target.files[0]
    if (uploading.value) return
    uploading.value = true
    let validateType = false;
    if (file.type != "image/jpg" && file.type != "image/jpeg" && file.type != "image/png" && file.type != "image/gif") {
        if (file.type) {
            validateType = true
        }
    }
    const isLt2M = file.size / 1024 / 1024 <= 5;
    if (validateType || !isLt2M) {
        cns.$message.error("支持 .png .jpg .jpeg格式，单个附件不得超过5M!");
        uploading.value = false
        return false;
    }else {
        uploadImage(file)
        return true
    }
}

const uploadImage = async (file) => {
    fileUpload({file}).then((res) => {
        uploading.value = false
        if (cns.$successCode(res.code)) {
            emit('local', res.data?.url)
        }
    }).catch((err)=>{
        cns.$message.error("上传失败");
        uploading.value = false
    })
}

const handleLocalUpload = () => {
    uploadImageRef.value.click()
}

</script>

<style lang='scss' scoped>
.uploader  {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    &:hover {
        border-color: var(--main-color);
        .upload-modal {
            display: flex;
        }
    }

    .upload-modal {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        background: rgba(0, 0, 0, 0.5);
        cursor: default;
        display: none;
        justify-content: space-around;
        border-radius: 0 0 6px 6px;
        z-index: 2;
        align-items: center;
        em{
            cursor: pointer;
            color: #fff;
            font-size: 16px;

        }
    }
}
// .avatar-uploader  {
//     border: 1px dashed #d9d9d9;
//     border-radius: 6px;
//     cursor: pointer;
//     position: relative;
//     overflow: hidden;
//     :deep(.el-upload) {
//         width: inherit;
//         height: inherit;
//         &:hover {
//             border-color: var(--main-color);
//             .masking {
//                 display: flex;
//             }
//         }
//     }

//     .masking {
//         position: absolute;
//         width: 100%;
//         height: 100%;
//         left: 0;
//         top: 0;
//         background: rgba(0, 0, 0, 0.5);
//         cursor: default;
//         display: none;
//         justify-content: space-around;
//         border-radius: 0 0 6px 6px;
//         z-index: 2;
//         align-items: center;
//         em{
//             cursor: pointer;
//             color: #fff;
//             font-size: 16px;
//         }
//     }
// }

</style>
