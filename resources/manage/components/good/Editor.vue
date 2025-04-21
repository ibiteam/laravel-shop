<template>
    <div style="border: 1px solid #ccc;max-width:100%;position: relative;" class="editor-wrap" ref="editorDomRef">
        <Toolbar
            ref="toolbarRef"
            style="border-bottom: 1px solid #ccc"
            :editor="editorRef"
            :defaultConfig="toolbarConfig"
            :mode="mode"
        />
        <Editor
            class="resizable-editor"
            :value="modelValue"
            :defaultConfig="editorConfig"
            :mode="mode"
            @customPaste="customPasteSet"
            @onChange="handleChange"
            @onCreated="handleCreated"
        >
        </Editor>
        <div ref="resizeHandle" class="resize-handle"></div>
        <input type="file" :accept="editor_upload_accept" @change="handleChangeUploadFile" :multiple="file_upload_type === 'image'" value="" style="display: none;" ref="uploadFileRef" />
        <el-dialog
            title="上传图片"
            v-model="showUploadDialog"
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            :show-close="false"
            width="500px"
            style="text-align: center;"
        >
            <div class="editor-upload-image">
                <el-form :model="uploadFileForm" ref="uploadFileFormRef" class="demo-upload-file-form">
                    <el-form-item prop="editor_files" :rules="[{ required: true, message: '请上传图片', trigger: 'change' }]">
                        <div class="s-flex ai-ct flex-wrap"></div>
                        <div class="editor-upload-item s-flex ai-ct jc-ct cursorp" :style="{ width: '100px', height: '100px' }" v-if="file_upload_type === 'image' && uploadFileForm.editor_files.length < 10" @click="handleClickUploadFile({ parent: 'uploadFileForm', validate: 1, target: 'editor_files', type: '' })">
                            <em class="iconfont editor-upload-add">&#xe6aa;</em>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
            <div slot="footer" class="dialog-footer s-flex ai-ct jc-ct">
                <el-button @click="handleClickCancleDialog">取 消</el-button>
                <el-button type="primary" @click="handleClickSubmitDialog('editor_files')" v-if="uploadFileForm.editor_files.length">确 定</el-button>
            </div>
        </el-dialog>
        <div class="import-table"></div>
        <MaterialCenterDialog v-bind="{show: materialDialogShow, dir_type: materialSelectType, multiple: true}" @close="materialDialogShow = false" @confirm="confirmSelectMaterial"/>
    </div>
</template>

<script setup>
import '@wangeditor/editor/dist/css/style.css' // 引入 css
import { onBeforeUnmount, ref, shallowRef, onMounted, reactive, getCurrentInstance, watch } from 'vue'
import { Boot, SlateTransforms } from '@wangeditor/editor'
import { Editor, Toolbar } from '@wangeditor/editor-for-vue'
import html2canvas from 'html2canvas-pro';
import { fileUpload } from '@/api/common'
import MaterialCenterDialog from '@/components/MaterialCenter/Dialog.vue';

const cns = getCurrentInstance().appContext.config.globalProperties
const editorRef = shallowRef()

const emit = defineEmits(['update:modelValue', 'change'])

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: '请输入商品详细信息'
    }
})
const uploadFileFormRef = ref(null);
const uploadFileRef = ref(null);

const materialDialogShow = ref(false);
const materialSelectType = ref(1);

class MyMaterialButtonMenu {
    constructor() {
        this.title = '从素材库选择' // 自定义菜单标题
        this.tag = 'button'
    }

    // 获取菜单执行时的 value ，用不到则返回空 字符串或 false
    getValue(editor) {
        return false
    }

    // 菜单是否需要激活，用不到则返回 false
    isActive(editor) {
        return false
    }

    // 菜单是否需要禁用，用不到则返回 false
    isDisabled(editor) {
        return false
    }

    // 点击菜单时触发的函数
    exec(editor, value) {
        if (this.isDisabled(editor)) return
        materialDialogShow.value = true
    }
}

const materialConf = {
    key: 'material', // 定义 menu key ：要保证唯一、不重复（重要）
    factory() {
        return new MyMaterialButtonMenu()
    },
}

try {
    Boot.registerMenu(materialConf)
} catch (err){}


const uploadFileForm = reactive({
    editor_files: [],   //  图片列表
    editor_video: [],
    name: ''
});
const showUploadDialog = ref(false);
const uploadTarget = ref(null);
const uploadIndex = ref(0);
const uploadType = ref('');
//  是否展示上传文件弹窗
const file_upload_type = ref('image');
const uploadValidateType  = ref('1');
//  视频文件
const editor_upload_accept = ref('image/gif,image/jpeg,image/png,image/jpg,image/bmp')
//  记录当前富文本光标对象
const img_width = ref(''); // 图片宽度
const exclude_keys = ['code', 'blockquote', 'fontFamily', 'codeBlock']
const mode = ref('default')
const msgErr = ref(null)

const toolbarConfig = ref({
    excludeKeys: exclude_keys,
    insertKeys:{
        index: 99,
        keys:['material']
    }
})
const editorConfig = {
    placeholder: props.placeholder,
    pasteFilterStyle: false,    //  关闭粘贴样式的过滤
    pasteIgnoreImg: false,  //  忽略粘贴内容中的图片
    MENU_CONF: {
        uploadImage: {
            //  自定义上传图片
            async customUpload(file, insertFn) {
                uploadValidateType.value = 1
                const is_skip = await beforeAvatarUpload(file)
                if (!is_skip) return false
                const uploaded = await handleAfterUpload(file)
                if (uploaded.status == 'success') {
                    // 插入图片
                    insertFn(uploaded.url, file.name, uploaded.url)
                }
            }
        },
        uploadVideo: {
            //  自定义上传视频
            async customUpload(file, insertFn) {
                uploadValidateType.value = 2
                const is_skip = await beforeAvatarUpload(file)
                if (!is_skip) return false
                const uploaded = await handleAfterUpload(file)
                if (uploaded.status == 'success') {
                    // 插入视频
                    insertFn(uploaded.url)
                }
            }
        }
    }
}
// 选择素材确认回调
const confirmSelectMaterial = (data) => {
    materialDialogShow.value = false
    if (!data.length) return
    let imagesNodes = []
    data.length && data.forEach(item => {
        imagesNodes.push({
            type: 'image',
            src: item.file_path,
            style:{
                width: '100%'
            },
            children: [
                {text:''}
            ]
        })
    })
    SlateTransforms.insertNodes(editorRef.value, imagesNodes)
}
const customPasteSet = (editor, event)=> {
    const html = event.clipboardData.getData('text/html')
    //  如果获取到的html有值，则拦截并过滤表格，将表格生成为图片，并将表格标签替换为图片
    if (html) {
        const parent = document.createElement('div')
        parent.innerHTML = html
        const table_view = document.querySelector('.import-table')
        table_view.style.position = 'fixed'
        table_view.style.left = '-100vw'
        table_view.style.top = '-100vh'
        let uploadImages = []
        let tableList = parent.querySelectorAll('table')
        const pElement = parent.querySelectorAll('p')

        if (tableList.length) {
            if (pElement.length) {
                //  将 HTMLCollection 数据转换为数组
                let arrayChildren = Array.from(parent.children)
                async function asyncForEach (array, callback) {
                    for (let index in array) {
                        if (array[index].tagName === 'TABLE') {
                            table_view.appendChild(array[index])
                            const img_url = await handleTableTransToImage(table_view)
                            table_view.removeChild(table_view.children[0])
                            const image = document.createElement('img')
                            image.setAttribute('width', 'auto')
                            image.setAttribute('data-type', 'table-image')
                            image.setAttribute('alt', '')
                            image.setAttribute('src', img_url)
                            array.splice(index, 1, image)
                            uploadImages.push(1)
                            await callback(array[index])
                        }
                    }
                }

                //  同步等待循环操作结束后，执行下一步操作
                asyncForEach(arrayChildren, async () => {
                    if (uploadImages.length === tableList.length) {
                        //  将数组转换为 HTMLCollection 数据
                        const create = document.createElement('div')
                        for (let item of arrayChildren) {
                            create.appendChild(item)
                        }
                        for (let item of create.children) {
                            if (item.tagName === 'P') {
                                item.style.lineHeight = 2
                            }
                        }
                        handleInsertHtmlToEditor(create.innerHTML, editor)
                    }
                })
            } else {
                setTimeout(async () => {
                    table_view.innerHTML = parent.innerHTML
                    const img_url = await handleTableTransToImage(table_view)
                    const img_html = `<img src='${img_url}' alt='' width='auto' />`
                    handleInsertHtmlToEditor(img_html, editor)
                }, 200)
            }
            // 阻止默认的粘贴行为
            event.preventDefault();
            return false
        } else {
        }
    }
    setTimeout(() => {
        scrollToCursor(editor);
    },20)
}
const resetElementInlineStyle = (editor)=> {
    return new Promise(resolve => {
        const html = editor.getHtml()
        const parent = document.createElement('div')
        const child = document.createElement('p')
        child.innerHTML = html
        //  重置p标签行内样式
        const paragraph = child.querySelectorAll('p')
        if (paragraph.length) {
            paragraph.forEach(item => {
                item.style.color = '#333333'
                if (!item.style.lineHeight) item.style.lineHeight = 2
            })
        }
        //  重置标题1行内样式
        const header1 = child.querySelectorAll('h1')
        if (header1.length) {
            header1.forEach(item => item.style.fontSize = '2em')
        }
        //  重置标题2行内样式
        const header2 = child.querySelectorAll('h2')
        if (header2.length) {
            header2.forEach(item => item.style.fontSize = '1.5em')
        }
        //  重置标题3行内样式
        const header3 = child.querySelectorAll('h3')
        if (header3.length) {
            header3.forEach(item => item.style.fontSize = '1.17em')
        }
        //  重置标题4行内样式
        const header4 = child.querySelectorAll('h4')
        if (header4.length) {
            header4.forEach(item => item.style.fontSize = '1em')
        }
        //  重置标题5行内样式
        const header5 = child.querySelectorAll('h5')
        if (header5.length) {
            header5.forEach(item => item.style.fontSize = '12px')
        }
        //  重置加粗行内样式
        const strong = child.querySelectorAll('strong')
        if (strong.length) {
            strong.forEach(item => item.style.fontWeight = 'bold')
        }
        //  重置斜体行内样式
        const emElement = child.querySelectorAll('em')
        if (emElement.length) {
            emElement.forEach(item => item.style.fontStyle = 'italic')
        }
        //  重置下划线行内样式
        const underlineElement = child.querySelectorAll('u')
        if (underlineElement.length) {
            underlineElement.forEach(item => item.style.textDecoration = 'underline')
        }
        //  重置删除线行内样式
        const lineThroughElement = child.querySelectorAll('s')
        if (lineThroughElement.length) {
            lineThroughElement.forEach(item => item.style.textDecoration = 'line-through')
        }
        //  重置上标行内样式
        const superElement = child.querySelectorAll('sup')
        if (superElement.length) {
            superElement.forEach(item => item.style.verticalAlign = 'super')
        }
        //  重置下标行内样式
        const subElement = child.querySelectorAll('sub')
        if (subElement.length) {
            subElement.forEach(item => item.style.verticalAlign = 'sub')
        }
        //  重置表格行内样式
        const tableThElement = child.querySelectorAll('th')
        if (tableThElement.length) {
            tableThElement.forEach(item => {
                item.style.lineHeight = '1.5'
                item.style.padding = '2px 5px'
                item.style.backgroundColor = '#f5f2f0'
                item.style.border = '1px solid #333333'
            })
        }
        //  重置表格行内样式
        const tableTdElement = child.querySelectorAll('td')
        if (tableTdElement.length) {
            tableTdElement.forEach(item => {
                item.style.lineHeight = '1.5'
                item.style.border = '1px solid #333333'
                item.style.padding = '2px 5px'
            })
        }
        //  重置有序列表行内样式
        const orderedElement = child.querySelectorAll('ol')
        if (orderedElement.length) {
            orderedElement.forEach(item => {
                item.style.paddingLeft = '1.5em'
                if (item.children.length) {
                    for (let index in item.children) {
                        if (Object.prototype.toString.call(item.children[index]).slice(8, -1) === 'HTMLLIElement') {
                            item.children[index].style.listStyle = 'decimal'
                        }
                    }
                }
            })
        }
        //  重置无序列表行内样式
        const bulletElement = child.querySelectorAll('ul')
        if (bulletElement.length) {
            bulletElement.forEach(item => {
                item.style.paddingLeft = '1.5em'
                if (item.children.length) {
                    for (let index in item.children) {
                        if (Object.prototype.toString.call(item.children[index]).slice(8, -1) === 'HTMLLIElement') {
                            item.children[index].style.listStyle = 'outside'
                        }
                    }
                }
            })
        }
        parent.appendChild(child)
        resolve(child)
    })
}
// 组件销毁时，也及时销毁编辑器
onBeforeUnmount(() => {
    const editor = editorRef.value
    if (editor == null) return
    editor.destroy()
    if (resizeHandle.value) {
        resizeHandle.value.removeEventListener('mousedown', (e) => {});
    }
})
const toolbarRef = ref(null);
const editorDomRef = ref(null);
const resizeHandle = ref(null);

let isDragging = false;
let startX;
let startY;
let startWidth;
let startHeight;
onMounted(()=>{
    const handleMouseDown = (e) => {
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        startWidth = editorDomRef.value.offsetWidth;
        startHeight = editorDomRef.value.offsetHeight;
        document.addEventListener('mousemove', handleMouseMove);
        document.addEventListener('mouseup', handleMouseUp);
    };

    const handleMouseMove = (e) => {
        if (isDragging) {
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            editorDomRef.value.style.width = `${startWidth + dx}px`;
            editorDomRef.value.style.height = `${startHeight + dy}px`;
        }
    };

    const handleMouseUp = () => {
        isDragging = false;
        document.removeEventListener('mousemove', handleMouseMove);
        document.removeEventListener('mouseup', handleMouseUp);
    };

    resizeHandle.value.addEventListener('mousedown', handleMouseDown);
})
/** 点击记录图片上传类型 **/
const handleClickUploadFile = (data)=> {
    const {validate, target, index, type } = data
    setTimeout(()=>{
        uploadValidateType.value = validate
        uploadTarget.value= target || null
        uploadIndex.value = !isNaN(index) ? index : 'null'
        uploadType.value = type
        uploadFileRef.value.click()
    }, 300)
}

const handleCreated = (editor) => {
    editorRef.value = editor // 记录 editor 实例，重要！
    editorRef.value.setHtml(props.modelValue)
}

watch(() => props.modelValue, (newVal) => {
    if (editorRef.value && newVal !== editorRef.value.getHtml() && newVal) {
        editorRef.value.setHtml(newVal)
    }
})

const handleChange = async (editor) => {
    const parent = await resetElementInlineStyle(editor)
    emit('update:modelValue', parent.innerHTML)
    emit('change', parent.innerHTML)
}
const scrollToCursor = (editor) => {
    const editorContent = editor.getEditableContainer(); // 获取编辑器内容区域
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0); // 获取当前的 range 对象
        const rect = range.getBoundingClientRect(); // 获取光标位置的矩形区域

        const editorPosition = editorContent.getBoundingClientRect(); // 获取编辑器容器的矩形区域
        const offset = rect.top - editorPosition.top + editorContent.scrollTop;

        document.querySelector(".article-form-scroll") && document.querySelector(".article-form-scroll").scrollTo({
            top: offset,
            behavior: "smooth"
        });

    }
}

// /** 过滤audio元素，添加属性 */
// const handleFilterAudioAddAttribute = (content) => {
//     return new Promise(resolve => {
//         const parent = document.createElement('p')
//         parent.innerHTML = content
//         let audioList = [], audioContent = parent.querySelectorAll('audio')
//         if (audioContent.length) {
//             async function asyncForEach (array, callback) {
//                 for (let item of array) {
//                     if (item.tagName === 'AUDIO') {
//                         item.setAttribute('data-w-e-type', 'audio')
//                         audioList.push(1)
//                         await callback(item)
//                     }
//                 }
//             }
//
//             asyncForEach(parent.children, async () => {
//                 if (audioList.length === audioContent.length) {
//                     resolve(parent.innerHTML)
//                 }
//             })
//
//         } else {
//             resolve(parent.innerHTML)
//         }
//     })
// }

/** 执行上传图片操作 **/
const handleChangeUploadFile = async (event, type) => {
    const files = type && type == 'custom_modal' ? event : event.srcElement.files
    let is_skip = true, is_arr_skip = [], upload_error = []
    if (Object.prototype.toString.call(files).slice(8, -1) === 'FileList') {
        for (let key in files) {
            if (Object.prototype.toString.call(files[key]).slice(8, -1) === 'File') {
                is_arr_skip[key] = await beforeAvatarUpload(files[key])
            }
        }
    } else {
        is_skip = await beforeAvatarUpload(files)
    }
    if (is_arr_skip.length && Object.prototype.toString.call(files).slice(8, -1) === 'FileList') {
        for (let index in files) {
            if (is_arr_skip[index] && Object.prototype.toString.call(files[index]).slice(8, -1) === 'File') {
                const uploaded = await handleAfterUpload(files[index])
                if (uploaded.status == 'error') {
                    upload_error.push(uploaded.file)
                } else {
                    if (!Array.isArray(uploadFileForm[uploadTarget.value]) || uploadFileForm[uploadTarget.value] == '') {
                        uploadFileForm[uploadTarget.value] = []
                    }
                    uploadFileForm[uploadTarget.value].push(uploaded.url)
                }
            }
        }
    } else {
        if (!is_skip) return false
        const uploaded = await handleAfterUpload(files)
        if (uploaded.status === 'error') {
            upload_error.push(uploaded.file)
        } else {
            if (uploadType.value && uploadType.value === 'editor_video') {
                handleClickSubmitDialog(uploadType.value)
            } else {
                uploadFileForm[uploadTarget.value][uploadIndex.value] = uploaded.url
            }
        }
    }
}

/** 校验图片格式 **/
const beforeAvatarUpload = (file) => {
    return new Promise(resolve=>{
        let result = true
        if (file && file.type) {
            const isPNG = file.type === 'image/png'
            const isJPEG = file.type === 'image/jpeg'
            const isJPG = file.type === 'image/jpg'
            const isLt10M = file.size / 1024 / 1024 < 10
            const isLt100M = file.size / 1024 / 1024 < 20
            const isGIF = file.type === 'image/gif'
            if (uploadValidateType.value === 1) {
                if (!isPNG && !isJPEG && !isJPG && !isGIF) {
                    msgErr.value && msgErr.value.close()
                    msgErr.value = cns.$message.error('仅限上传jpg、jpeg、png、gif格式的图片')
                    resolve(false)
                    return false
                }
                if (!isLt10M) {
                    msgErr.value && msgErr.value.close()
                    msgErr.value = cns.$message.error('图片大小不能超过10M')
                    result = false
                }
            } else if (uploadValidateType.value === 2) {
                var fileType = file.name.split(".")[1]
                fileType = fileType.toLowerCase()
                const isMP4 = fileType === 'mp4'
                if (!isMP4) {
                    msgErr.value && msgErr.value.close()
                    msgErr.value = cns.$message.error('请根据提示上传正确的视频格式!');
                    result = false
                }
                if (!isLt100M) {
                    msgErr.value && msgErr.value.close()
                    msgErr.value = cns.$message.error('上传视频的大小不能超过 20MB!');
                    result = false
                }
            } else {
                result = true
            }

        } else {
            result = false
        }
        resolve(result)
    })
}

const handleAfterUpload = (file) => {
    return new Promise((resolve) => {
        if (Object.prototype.toString.call(file).slice(8, -1) === 'File') {
            let reader = new FileReader();
            reader.readAsArrayBuffer(file);
            reader.onload = () => {
                fileUpload({ file }).then((res) => {
                    if (res.code == 200) {
                        resolve({ status: 'success', url: res.data.url });
                    } else {
                        resolve({ status: 'error' });
                        if (msgErr.value) msgErr.value.close();
                        msgErr.value = cns.$message.error('图片上传失败');
                    }
                });
            };
        }
    });
};

const handleClickSubmitDialog = (name) => {
    uploadFileFormRef.value.validate((valid) => {
        if (valid) {
            if (uploadFileForm[name].length) {
                const list = uploadFileForm[name].reverse();
                list.forEach((item) => {
                    const img_html = `<img src='${item}' alt='' width='auto' />`;
                    handleInsertHtmlToEditor(img_html, editorRef.value);
                });
                uploadFileForm[name] = [];
                setTimeout(() => handleClickCancleDialog(), 300);
            } else {
                if (msgErr.value) msgErr.value.close();
                msgErr.value = cns.$message.error('请上传图片');
            }
        } else {
            return false;
        }
    });
};

const handleClickCancleDialog = () => {
    showUploadDialog.value = false;
    uploadFileForm.editor_files = [];
    uploadFileFormRef.value.resetFields();
    uploadFileFormRef.value.clearValidate();
};

const handleBase64ToFileUpload = (base64) => {
    return new Promise(async (resolve) => {
        let arr = base64.split(',');
        let [, file_type] = arr[0].split('data:');
        [file_type] = file_type.split(';base64');
        let [, file_prefix] = file_type.split('image/');
        let bstr = atob(arr[1]);
        let n = bstr.length;
        let u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        const file_name = `${Math.round(new Date() / 1000)}.${file_prefix}`;
        const file = new File([u8arr], file_name, { type: file_type });
        const upload_result = await handleAfterUpload(file);
        if (upload_result.status === 'success') {
            resolve(upload_result.url);
        }
    });
};

const handleTableTransToImage = (table_view) => {
    return new Promise((resolve) => {
        const options = {
            scale: window.devicePixelRatio,
            logging: false,
            useCORS: true,
            scrollY: 0,
        };
        html2canvas(table_view, options).then(async (canvas) => {
            const img_url = await handleBase64ToFileUpload(canvas.toDataURL());
            resolve(img_url);
        });
    });
};

const handleInsertHtmlToEditor = (html, editorInstance) => {
    if (editorInstance) {
        editorInstance.restoreSelection();
        editorInstance.dangerouslyInsertHtml(html);
    } else {
        editorRef.value.restoreSelection();
        editorRef.value.dangerouslyInsertHtml(html);
    }
};
</script>

<style scoped lang="scss">
    .w-e-full-screen-container{
        z-index: 9999;
        position: fixed!important;
    }
    .resizable-editor{
        height: max-content!important;
    }
    .resize-handle {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        background-color: #666;
        cursor: nwse-resize;
    }
    .editor-wrap{
        :deep(.w-e-text-placeholder){
            font-style: normal;
            top: 12px;
            line-height: 1.5;
        }
        :deep(.w-e-text-container){
            padding: 0;
            [data-slate-editor] p{
                line-height: 1.5!important; margin: 0; white-space: pre-wrap;
            }
            line-height: normal !important; margin: 0 !important;
        }
        :deep(.w-e-scroll){
            display: flex; padding: 12px 0; overflow-y: hidden !important;
            >div{
                width: 100%; height: 100%; overflow-y: auto;
            }
        }
        :deep(.toolbar-menu){
            border-bottom: 1px solid #cccccc;
        }
        .table-container { padding: 0 !important; margin-top: 0 !important; border: none !important; overflow-x: inherit !important; }
        .toolbar-menu { border-bottom: 1px solid #cccccc; }
        h1, h2, h3, h4, h5 { margin: 0 !important; font-weight: bold !important; }
        h1 { font-size: 2em !important; }
        h2 { font-size: 1.5em !important; }
        h3 { font-size: 1.17em !important; }
        h4 { font-size: 1em !important; }
        h5 { font-size: 12px !important; }
        strong { font-weight: bold !important; }
        em { font-style: italic !important; }
        u { text-decoration: underline !important; }
        s { text-decoration: line-through !important; }
        sup { vertical-align: super !important; }
        sub { vertical-align: sub !important; }
        /*上传图片样式*/
        .editor-upload-item { width: 100px; height: 100px; margin-right: 10px; flex-shrink: 0; border: 1px dashed #cccccc; background-color: #F2F2F2; border-radius: 4px; position: relative; }
        .editor-upload-item em { font-style: normal !important; }
        .editor-upload-item .editor-upload-add { font-size: 40px; color: #cccccc; }
        .editor-upload-item img { max-width: 100%; max-height: 100%; }
        .editor-upload-mark { display: none; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); position: absolute; left: 0; top: 0; }
        .editor-upload-icon { width: 30px; height: 30px; margin: 0 8px; background-color: rgba(0, 0, 0, 0.3); border-radius: 100%; }
        .editor-upload-item:hover .editor-upload-mark { display: flex; }
        .editor-upload-icon em { font-size: 14px; color: #ffffff; }
        .editor-upload-btn { padding: 5px 15px; margin-bottom: 20px !important; background-color: #278ff0; border-radius: 4px; font-size: 14px; color: #ffffff; cursor: pointer; }
        button[data-menu-key="insert-file"] line,
        button[data-menu-key="insert-file"] path { fill: none; stroke: #444444; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2; }
        .demo-upload-file-form.el-form { display: block; }
        .demo-upload-file-form.el-form .el-form-item__content { margin: 0 !important; position: static; }
    }

</style>
