<link href="https://cdn.toodudu.com/mp3s/2024/05/20/admin-editor.css" rel="stylesheet">
<style>
    .flex-dir { flex-direction: column; }
    body .admin-editor { border: 1px solid #cccccc; font-size: 14px; display: flex; }
    body .admin-editor.full-screen { position: relative; z-index: 9999; }
    body .admin-editor .admin-editor-html { outline: none; }
    body .admin-editor .el-dialog { margin: 15vh auto 0 auto; }
    body .admin-editor *,
    .admin-editor .w-e-text-container [data-slate-editor] p { line-height: normal; margin: 0; }
    .admin-editor .w-e-text-container [data-slate-editor] p { white-space: pre-wrap; }
    .admin-editor .w-e-text-container { padding: 0; }
    .admin-editor .w-e-scroll { display: flex; padding: 12px 0; overflow-y: hidden !important; }
    .admin-editor .w-e-scroll > div { width: 100%; height: 100%; overflow-y: auto; }
    body .admin-editor .toolbar-menu { border-bottom: 1px solid #cccccc; }
    body .admin-editor h1,
    body .admin-editor h2,
    body .admin-editor h3,
    body .admin-editor h4,
    body .admin-editor h5 { margin: 0 !important; font-weight: bold !important; }
    body .admin-editor h1 { font-size: 2em !important; }
    body .admin-editor h2 { font-size: 1.5em !important; }
    body .admin-editor h3 { font-size: 1.17em !important; }
    body .admin-editor h4 { font-size: 1em !important; }
    body .admin-editor h5 { font-size: 12px !important; }
    body .admin-editor strong { font-weight: bold !important; }
    body .admin-editor em { font-style: italic !important; }
    body .admin-editor u { text-decoration: underline !important; }
    body .admin-editor s { text-decoration: line-through !important; }
    body .admin-editor sup { vertical-align: super !important; }
    body .admin-editor sub { vertical-align: sub !important; }
    body .admin-editor .w-e-text-container { line-height: normal !important; margin: 0 !important; }
    body .admin-editor .table-container { padding: 0 !important; margin-top: 0 !important; border: none !important; overflow-x: inherit !important; }

    /*上传图片样式*/
    .editor-upload-item { width: 100px; height: 100px; margin-right: 10px; flex-shrink: 0; border: 1px dashed #cccccc; background-color: #F2F2F2; border-radius: 4px; position: relative; }
    body .editor-upload-item em { font-style: normal !important; }
    body .editor-upload-item .editor-upload-add { font-size: 40px; color: #cccccc; }
    .editor-upload-item img { max-width: 100%; max-height: 100%; }
    .editor-upload-mark { display: none; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); position: absolute; left: 0; top: 0; }
    .editor-upload-icon { width: 30px; height: 30px; margin: 0 8px; background-color: rgba(0, 0, 0, 0.3); border-radius: 100%; }
    .editor-upload-item:hover .editor-upload-mark { display: flex; }
    .editor-upload-icon em { font-size: 14px; color: #ffffff; }
    .editor-upload-btn { padding: 5px 15px; margin-bottom: 20px !important; background-color: #278ff0; border-radius: 4px; font-size: 14px; color: #ffffff; cursor: pointer; }

    /*自定义菜单*/
    body .admin-editor button[data-menu-key="insert-file"] line,
    body .admin-editor button[data-menu-key="insert-file"] path { fill: none; stroke: #444444; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2; }

    .demo-uploadFileForm.el-form { display: block; }
    .demo-uploadFileForm.el-form .el-form-item__content { margin: 0 !important; position: static; }
</style>
<script type="text/x-template" id="quill-editor">
    <div class="admin-editor-parent">
        <div class="admin-editor" :style="{ width, height, minHeight }" :class="{ 'full-screen': is_full_screen }">
            <div style="display: flex; flex-direction: column;flex: 1;">
                <!-- 工具栏 -->
                <div :id="`editorToolbar${editor_index}`" class="toolbar-menu"></div>
                <slot />
                <!-- 编辑器 -->
                <div :id="`admin_editor${editor_index}`" class="admin_editor_list" style="flex: 1; overflow-y: auto;" v-loading="loading" element-loading-text="正在识别中，请稍等片刻" element-loading-spinner="el-icon-loading" element-loading-background="rgba(0, 0, 0, 0.8)"></div>
                <div class="toolbar-menu"></div>
                <!--<div class="admin-editor-html" contenteditable="true" style="flex: 1; overflow-y: auto;" @input="handleEditorHtmlChange">@{{content}}</div>-->
            </div>
        </div>
        <!--上传图片弹窗-->
        <input type="file" :accept="editor_upload_accept" @change="handleChangeUploadFile" :multiple="file_upload_type === 'image'" value="" style="display: none;" ref="upload_file" />
        <el-dialog
                :title="file_upload_type == 'image' ? '上传图片' : file_upload_type == 'spacing' ? '文本间距' : '上传文件'"
                :visible.sync="show_upload_dialog"
                :close-on-click-modal="false"
                :close-on-press-escape="false"
                :show-close="false"
                :width="file_upload_type === 'file' ? '400px' : '500px'"
                style="text-align: center;"
        >
            <div class="editor-upload-image" :class="{ 'jc-ct': file_upload_type === 'file' }">
                <el-form :model="uploadFileForm" ref="uploadFileForm" class="demo-uploadFileForm">
                    <template v-if="file_upload_type == 'spacing'">
                        <el-form-item prop="spacing" :rules="
                            [
                                { required: true, message: '请输入文本间距', trigger: 'blur' },
                                { validator: (rule, value, callback) => {
                                    if (value) {
                                        if (isNaN(value)) {
                                            callback(new Error('请输入数字'));
                                        } else if (value < 1) {
                                            callback(new Error('排序最小值是1'));
                                        } else { callback(); }
                                    } else { callback(); }
                                }, trigger: 'blur' },
                            ]
                        ">
                            <el-input size="small" v-model="uploadFileForm.spacing" style="width: 300px;"></el-input>
                        </el-form-item>
                    </template>
                    <template v-else>
                        <el-form-item prop="editor_files" :rules="[{ required: true, message: file_upload_type == 'image' ? '请上传图片' : '请上传文件', trigger: 'change' }]">
                            <div class="s-flex ai-ct flex-wrap">
                                <!--<div class="editor-upload-item image s-flex ai-ct jc-ct"
                                     v-for="(item, index) in uploadFileForm.editor_files"
                                     :style="{ width: file_upload_type === 'file' ? '300px' : '100px', height: file_upload_type === 'file' ? '150px' : '100px', margin: file_upload_type === 'file' ? '0 auto 20px auto' : '' }"
                                >
                                    <img :src="item" alt="" />
                                    <div class="editor-upload-mark s-flex ai-ct jc-ct">
                                        <div class="editor-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `uploadFileForm`, validate: file_upload_type === 'file' ? 3 : 1, target: 'editor_files', index })">
                                            <em class="iconfont">&#xe727;</em>
                                        </div>
                                        <div class="editor-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile({ index })">
                                            <em class="iconfont">&#xe738;</em>
                                        </div>
                                    </div>
                                </div>-->
                            </div>
                            <div class="editor-upload-item s-flex ai-ct jc-ct cursorp"
                                 :style="{ width: file_upload_type === 'file' ? '300px' : '100px', height: file_upload_type === 'file' ? '150px' : '100px', margin: file_upload_type === 'file' ? '0 auto 20px auto' : '' }"
                                 v-if="(file_upload_type === 'image' && uploadFileForm.editor_files.length < 10) || (file_upload_type === 'file' && !uploadFileForm.editor_files.length)" @click="handleClickUploadFile({ parent: 'uploadFileForm', validate: file_upload_type === 'file' ? 3 : 1, target: 'editor_files', type: '' })"
                            >
                                <em class="iconfont editor-upload-add">&#xe6aa;</em>
                            </div>
                            <el-input v-else-if="files_name" v-model="files_name" disabled="" style="width: 300px;"></el-input>
                        </el-form-item>
                        <el-form-item prop="name" style="width: 300px;" :rules="[{ required: true, message: '请输入名称', trigger: 'blur' }]" v-if="file_upload_type === 'file'">
                            <el-input v-model="uploadFileForm.name" style="width: 300px;"></el-input>
                        </el-form-item>
                    </template>
                </el-form>
            </div>
            <!--<p style="font-size: 12px; color: #999999;" v-if="file_upload_type === 'image'">每次最多可上传10张图片</p>-->
            <!--<el-dialog :visible.sync="show_photo_preview" custom-class="photo-preview">
                <img width="100%" :src="preview_photo" alt="">
            </el-dialog>-->
            <div slot="footer" class="dialog-footer s-flex ai-ct jc-ct">
                <el-button @click="handleClickCancleDialog">取 消</el-button>
                <el-button type="primary" @click="handleClickSubmitDialog('editor_files')" v-if="uploadFileForm.editor_files.length || file_upload_type == 'spacing'">确 定</el-button>
            </div>
        </el-dialog>
        <div class="import-table"></div>
    </div>
</script>
<script src="https://cdn.toodudu.com/mp3s/2024/05/20/admin-editor.js"></script>
<script src="https://cdn.toodudu.com/mp3s/2024/05/21/admin-snabbdom.js"></script>
<script src="https://cdn.toodudu.com/mp3s/2024/05/22/admin-editor-mammoth.js"></script>
<script src="https://unpkg.com/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>

    /**
     * name 富文本编辑器组件
     * @content 富文本编辑器内容
     * @editor_index    富文本编辑器索引(多个富文本时使用)，可以直接传递 :editor_index='0' :editor_index='1'
     * @placeholder 富文本编辑器默认占位
     * @is_html 是否展示html模式
     * **/
    Vue.component('quill-editor', {
        template: '#quill-editor',
        props: {
            content: {
                required: true,
                type: String,
                default: ''
            },
            editor_index: {
                type: String,
                default: 0
            },
            placeholder: {
                type: String,
                default: '请输入内容'
            },
            is_html: {
                type: Boolean,
                default: false
            },
            width: {
                type: String,
                default: '100%'
            },
            height: {
                type: String,
                default: '300px'
            },
            exclude_keys:{
                type:Array,
                default: ['code', 'blockquote', 'fontFamily', 'codeBlock' , 'fullScreen']
            },
            minHeight:{
                type:String,
                default: '800px'
            },
        },
        data() {
            const vm = this
            return {
                loading: false,
                editor_name: '',
                save_loading: false,
                dataTempalteForm: null,
                editor_content: '',
                quill: null,
                //  富文本编辑器工具栏配置
                toolbarOptions: null,

                uploadFileForm: {
                    editor_files: [],   //  图片列表
                    name: ''
                },
                show_upload_dialog: false,
                //  是否展示上传文件弹窗
                file_upload_type: 'image',
                //  弹窗类型 image: 上传图片、video: 上传视频、file: 上传文件
                editor_files: [],
                //  视频列表
                editor_video: [],
                //  音频列表
                editor_audio: [],
                //  视频文件
                editor_upload_accept: 'image/gif,image/jpeg,image/png,image/jpg,image/bmp',
                show_photo_preview: false,
                //  图片预览列表
                preview_photo: [],
                //  记录当前富文本光标对象
                range: null,
                show_img_setting: false, // 是否展示图片设置弹窗
                img_width: '', // 图片宽度
                img_height: '', // 图片高度
                img_alt: '', // 图片备注
                img_href: '', // 图片是否有超链接
                is_open_source: false,
                is_open_typeset: false, //  是否开启一键排版
                files_name: '',
                is_full_screen: false
            }
        },
        computed: {},
        methods: {
            /** 初始化富文本编辑器 */
            async initEditorInfo () {
                const that = this
                const { createEditor, createToolbar, Boot, SlateDescendant, SlateTransforms, SlateElement, DomEditor, IDomEditor } = window.wangEditor
                const { h, VNode } = window.snabbdom
                setTimeout(async () => {
                    this.handleCheckDestroy()
                    this.editor_content = this.content ? await this.handleFilterAudioAddAttribute() : ''

                        //  配置富文本
                    this.editorConfig = {
                        placeholder: this.placeholder,
                        pasteFilterStyle: false,    //  关闭粘贴样式的过滤
                        pasteIgnoreImg: false,  //  忽略粘贴内容中的图片
                        async onChange (editor) {
                            //  重置行内样式
                            const parent = await that.resetElementInlineStyle()
                            that.$emit('update:content', parent.innerHTML)
                            that.$emit('change', parent.innerHTML)
                        },
                        MENU_CONF: {
                            uploadImage: {
                                //  自定义上传图片
                                async customUpload(file, insertFn) {
                                    this.uploadValidateType = 1
                                    const is_skip = await that.beforeAvatarUpload(file)
                                    if (!is_skip) return false
                                    const uploaded = await that.handleAfterUpload(file)
                                    if (uploaded.status == 'success') {
                                        // 插入图片
                                        insertFn(uploaded.url, file.name, uploaded.url)
                                    }
                                }
                            },
                            uploadVideo: {
                                //  自定义上传视频
                                async customUpload(file, insertFn) {
                                    this.uploadValidateType = 2
                                    const is_skip = await that.beforeAvatarUpload(file)
                                    if (!is_skip) return false
                                    const uploaded = await that.handleAfterUpload(file)
                                    if (uploaded.status == 'success') {
                                        // 插入视频
                                        insertFn(uploaded.url)
                                    }
                                }
                            }
                        },
                        /** 自定义粘贴，拦截复制表格 */
                       customPaste (editor, event) {
                            const html = event.clipboardData.getData('text/html')
                            //  如果获取到的html有值，则拦截并过滤表格，将表格生成为图片，并将表格标签替换为图片
                            if (html) {
                                const parent = document.createElement('div')
                                parent.innerHTML = html
                                const table_view = document.querySelector('.import-table')
                                table_view.style.position = 'fixed'
                                table_view.style.left = '-100vw'
                                table_view.style.top = '-100vh'
                                that.loading = true
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
                                                    const img_url = await that.handleTableTransToImage(table_view)
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
                                                that.handleInsertHtmlToEditor(create.innerHTML, that.editor)
                                                that.loading = false
                                            }
                                        })
                                    } else {
                                        setTimeout(async () => {
                                            table_view.innerHTML = parent.innerHTML
                                            const img_url = await that.handleTableTransToImage(table_view)
                                            const img_html = `<img src='${img_url}' alt='' width='auto' />`
                                            that.handleInsertHtmlToEditor(img_html, that.editor)
                                            that.loading = false
                                        }, 200)
                                    }
                                    // 阻止默认的粘贴行为
                                    event.preventDefault();
                                    return false
                                } else {
                                    that.loading = false
                                }
                            }
                            setTimeout(() => {
                                that.scrollToCursor(editor);
                            },20)
                        }
                    }
                    this.editor_name = `admin_editor${this.editor_index}`

                    setTimeout(async () => {

                        this.editor.setHtml(this.editor_content)
                        this.editor.updateView()
                    }, 200)
                    this.editor = createEditor({
                        selector: `#admin_editor${this.editor_index}`,
                        html: '',
                        config: this.editorConfig,
                    })
                    this.editor.on('fullScreen', () => { this.is_full_screen = true })
                    this.editor.on('unFullScreen', () => { this.is_full_screen = false })


                    this.is_open_source = this.is_html
                    //  开启源码显示
                    let source = this.editor.getHtml()
                    if (this.is_open_source) {
                        this.is_open_source = false
                        source = source.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/ /g, "&nbsp;")
                        source = source.replace(/<p><br><\/p>/g, "")
                        this.editor.setHtml(source)
                    } else {
                        this.is_open_source = true
                        source = source.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&nbsp;/g, " ")
                        source = source.replace(/<p><br><\/p>/g, "")
                        this.editor.setHtml(source)
                    }

                    //  配置工具栏
                    const toolbarConfig = {
                        //  设置排除功能
                        excludeKeys: this.exclude_keys
                    }



                    await this.handleInsertFileButtonMenu(Boot, toolbarConfig, SlateTransforms)
                    await this.handleAudioCustomTagElement(Boot, DomEditor, IDomEditor, SlateDescendant, SlateElement, h, VNode)

                    this.toolbar = createToolbar({
                        editor: this.editor,
                        selector: `#editorToolbar${this.editor_index}`,
                        config: toolbarConfig,
                    })

                    //  获取所有工具栏
                    // console.log(this.toolbar.getConfig().toolbarKeys)
                },200)

            },
            scrollToCursor(editor){
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
            },
            /** 重置行内样式 */
            resetElementInlineStyle () {
                return new Promise(resolve => {
                    const html = this.editor.getHtml()
                    const parent = document.createElement('div')
                    const child = document.createElement('p')
                    // child.setAttribute('style', 'line-height: 1.42; padding: 12px 15px; font-size: 14px; word-wrap: break-word;')
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
            },
            /** 自定义插入文件菜单按钮 */
            handleInsertFileButtonMenu (Boot, toolbarConfig, SlateTransforms) {
                const that = this
                //  自定义插入文件按钮
                class InsertFileButtonMenu {

                    constructor() {
                        this.title = '插入文件'
                        this.iconSvg = '<svg class="insert-file" style="width: 18px; height: 18px;" viewBox="0 0 18 18"><line x1="7" x2="11" y1="7" y2="11"></line><path d="M8.9,4.577a3.476,3.476,0,0,1,.36,4.679A3.476,3.476,0,0,1,4.577,8.9C3.185,7.5,2.035,6.4,4.217,4.217S7.5,3.185,8.9,4.577Z"></path><path d="M13.423,9.1a3.476,3.476,0,0,0-4.679-.36,3.476,3.476,0,0,0,.36,4.679c1.392,1.392,2.5,2.542,4.679.36S14.815,10.5,13.423,9.1Z"></path></svg>' // 可选
                        this.tag = 'button'
                        this.showModal = true
                        this.modalWidth = 300
                    }

                    // 获取菜单执行时的 value ，用不到则返回空 字符串或 false
                    getValue() { return '' }

                    // 菜单是否需要激活（如选中加粗文本，“加粗”菜单会激活），用不到则返回 false
                    isActive(editor) { return false }

                    // 菜单是否需要禁用（如选中 H1 ，“引用”菜单被禁用），用不到则返回 false
                    isDisabled(editor) { return false }

                    // 点击菜单时触发的函数
                    exec(editor, value) {
                        if (this.isDisabled(editor)) return
                        /*that.editor_upload_accept = '*'
                        that.file_upload_type = 'file'
                        that.show_upload_dialog = true*/
                    }

                    getModalPositionNode(editor) { return null }

                    /** 使用富文本自带的弹窗时使用 */
                    getModalContentElem(editor) {

                        const $content = $('<div class="s-flex flex-dir ai-ct jc-ct"></div>')
                        let image_url = '', input_value = ''

                        this.input_file = $('<input type="file" accept="*" value="" style="display: none;" />')

                        this.upload_btn = $('<div class="editor-upload-item s-flex ai-ct jc-ct cursorp" style="width: 88%; height: 150px; margin: 0px auto 10px; cursor: pointer;"><em class="iconfont editor-upload-add"></em></div>')
                        this.input_value = $('<input class="el-input__inner" style="width: 88%; padding: 0 15px; margin-bottom: 10px;" value="" />')
                        this.input_btn = $('<div class="editor-upload-btn">确定</div>')
                        $content.append(this.input_file)
                        $content.append(this.upload_btn)
                        $content.append(this.input_value)
                        $content.append(this.input_btn)

                        this.input_file.on('change', async (event) => {
                            image_url = await that.handleChangeUploadFileAsync(event.target.files, 'custom_modal')
                            $('.editor-upload-add').remove()
                            this.upload_btn.append($('<em class="iconfont editor-upload-file" style="font-size: 36px;">&#xe834;</em>'))
                        })

                        this.input_value.on('input', async (event) => {
                            input_value = event.target.value
                        })
                        this.upload_btn.on('click', () => {
                            this.input_file.click()
                        })
                        this.input_btn.on('click', () => {
                            if (image_url === '') {
                                that.msg_error && that.msg_error.close()
                                that.msg_error = that.$message.error('请上传文件');
                            }
                            if (input_value === '') {
                                that.msg_error && that.msg_error.close()
                                that.msg_error = that.$message.error('请输入名称');
                            }

                            if (image_url && input_value) {
                                const html = `<a href='${image_url}' target="_blank" >${input_value}</a>`
                                that.handleInsertHtmlToEditor(html, editor)
                            }
                        })

                        return $content[0] // 返回 DOM Element 类型
                    }

                }
                this.InsertFileButtonMenu = InsertFileButtonMenu
                const insertFileConfig = {
                    key: 'insert-file', // 定义 menu key ：要保证唯一、不重复（重要）
                    factory () { return new InsertFileButtonMenu() },
                }

                //  自定义插入音频按钮
                class InsertAudioButtonMenu {

                    constructor() {
                        this.title = '插入音频'
                        this.iconSvg = '<svg style="width: 16px; height: 16px;" t="1716274882907" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="19256" width="200" height="200"><path d="M566.215111 899.811556v118.158222h-85.333333v-114.915556C294.4 888.718222 147.342222 735.573333 147.342222 562.688a42.666667 42.666667 0 0 1 85.333334 0c0 134.257778 123.790222 256.113778 276.764444 256.113778s276.707556-121.912889 276.707556-256.113778a42.666667 42.666667 0 1 1 85.333333 0c0 164.067556-132.380444 310.385778-305.265778 337.123556zM510.976 33.336889a170.666667 170.666667 0 0 1 170.666667 170.666667v341.333333a170.666667 170.666667 0 1 1-341.333334 0v-341.333333a170.666667 170.666667 0 0 1 170.666667-170.666667z" fill="#333333" p-id="19257"></path></svg>' // 可选
                        this.tag = 'button'
                    }

                    // 获取菜单执行时的 value ，用不到则返回空 字符串或 false
                    getValue() { return '' }

                    // 菜单是否需要激活（如选中加粗文本，“加粗”菜单会激活），用不到则返回 false
                    isActive(editor) { return false }

                    // 菜单是否需要禁用（如选中 H1 ，“引用”菜单被禁用），用不到则返回 false
                    isDisabled(editor) { return false }

                    // 点击菜单时触发的函数
                    exec(editor, value) {
                        if (this.isDisabled(editor)) return
                        that.file_upload_type = 'audio'
                        that.editor_upload_accept = 'audio/*'
                        that.handleClickUploadFile({
                            validate: 3,
                            target: 'editor_audio',
                            type: 'editor_audio',
                            editor
                        })
                    }

                }
                const insertAudioConfig = {
                    key: 'insert-audio',
                    factory () { return new InsertAudioButtonMenu() },
                }

                //  自定义导入Word文件按钮
                class ImportWordButtonMenu {

                    constructor() {
                        this.title = '导入Word'
                        this.iconSvg = '<svg style="width: 16px; height: 16px; t="1716340150130" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2696" width="200" height="200"><path d="M726.641778 170.666667h232.675555v682.666666h-232.675555V170.666667zM28.444444 853.333333V170.666667l651.662223-170.666667v1024L28.444444 853.333333zM207.644444 284.444444H112.241778l66.332444 393.671112h122.140445l52.337777-300.373334 50.062223 300.373334h123.392L593.92 284.444444H503.182222l-46.535111 323.128889L404.309333 284.444444h-100.124444l-57.002667 323.128889L207.644444 284.444444z" fill="#666666" p-id="2697"></path></svg>' // 可选
                        this.tag = 'button'
                    }

                    // 获取菜单执行时的 value ，用不到则返回空 字符串或 false
                    getValue() { return '' }

                    // 菜单是否需要激活（如选中加粗文本，“加粗”菜单会激活），用不到则返回 false
                    isActive(editor) { return false }

                    // 菜单是否需要禁用（如选中 H1 ，“引用”菜单被禁用），用不到则返回 false
                    isDisabled(editor) { return false }

                    // 点击菜单时触发的函数
                    exec(editor, value) {
                        if (this.isDisabled(editor)) return
                        that.$confirm('此功能不确保完全识别Word中的所有依赖样式, 同时会清除表格中的样式，表格可以通过截图或复制/粘贴的形式实现，是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            const input_file = $('<input type="file" accept=".doc,.docx,.xls,.xlsx,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" style="display: none;" />')
                            input_file.click()
                            input_file.on('change', (event) => {
                                that.loading = true
                                const [file] = event.target.files
                                const reader = new FileReader();
                                reader.onload = function(event) {
                                    console.log(event.target.result)
                                    mammoth.convertToHtml({arrayBuffer: event.target.result}).then(async (result) => {
                                        const parent = document.createElement('p')
                                        parent.innerHTML = result.value
                                        const images = parent.querySelectorAll('img')
                                        let uploadImages = []
                                        for (let element of parent.children) {
                                            if (element.tagName !== 'TABLE' && element.tagName !== 'IMG') {
                                                // element.style.textIndent = '2em'
                                                element.style.lineHeight = 2
                                            }
                                        }
                                        //  如果word文件中存在图片，则将图片转换为File进行上传获取图片链接后，再将处理好的html插入到富文本中
                                        if (images.length) {
                                            async function asyncForEach (array, callback) {
                                                for (let item of array) {
                                                    const img_url = await that.handleBase64ToFileUpload(item.src)
                                                    item.setAttribute('src', img_url)
                                                    uploadImages.push(item)
                                                    await callback(item)
                                                }
                                            }
                                            //  同步等待循环操作结束后，执行下一步操作
                                            asyncForEach(images, async () => {
                                                if (uploadImages.length === images.length) {
                                                    // editor.setHtml(parent.innerHTML)
                                                    that.loading = false
                                                    that.handleInsertHtmlToEditor(parent.innerHTML, that.editor)
                                                }
                                            })
                                        } else {
                                            that.loading = false
                                            that.handleInsertHtmlToEditor(parent.innerHTML, that.editor)
                                        }
                                    }).catch(function(err) {
                                        that.loading = false
                                        that.$message.error('导入失败，请重新导入')
                                    });
                                };
                                reader.readAsArrayBuffer(file);
                            })
                        }).catch(() => {});
                    }

                }
                const importWordConfig = {
                    key: 'insert-word',
                    factory () { return new ImportWordButtonMenu() },
                }

                //  自定义一键排版按钮
                class TypesetButtonMenu {

                    constructor() {
                        this.title = '一键排版'
                        this.iconSvg = '<svg style="width: 16px; height: 16px;" t="1716341051002" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3812" width="200" height="200"><path d="M1007.323429 241.517714a35.401143 35.401143 0 0 1-10.386286 26.112L258.633143 1013.467429a34.816 34.816 0 0 1-25.746286 10.532571 34.816 34.816 0 0 1-25.892571-10.532571l-113.590857-114.761143a35.547429 35.547429 0 0 1-10.386286-26.038857c0-10.605714 3.510857-19.236571 10.386286-26.112l738.304-746.057143a34.816 34.816 0 0 1 25.746285-10.459429c10.386286 0 19.017143 3.510857 25.819429 10.459429l113.664 114.834285a36.352 36.352 0 0 1 10.386286 26.185143zM171.081143 134.436571l60.708571 18.797715-60.708571 18.797714-18.651429 61.293714-18.651428-61.293714L73.142857 153.234286l60.708572-18.797715L152.356571 73.142857l18.651429 61.293714z m216.502857 101.302858l121.417143 37.522285-121.417143 37.376-37.083429 122.660572-37.083428-122.660572-121.344-37.376 121.344-37.522285 37.083428-122.587429 37.156572 122.587429z m179.492571-101.302858l60.708572 18.797715-60.708572 18.797714-18.578285 61.293714-18.651429-61.293714-60.635428-18.797714 60.708571-18.797715L548.425143 73.142857l18.578286 61.293714z m163.401143 302.08l181.394286-183.149714-66.340571-66.998857-181.321143 183.296 66.267428 66.925714z m232.886857 98.377143L1024 553.691429l-60.708571 18.651428-18.578286 61.293714-18.578286-61.293714-60.708571-18.797714 60.708571-18.797714 18.578286-61.293715 18.651428 61.44z" fill="#000000" p-id="3813"></path></svg>' // 可选
                        this.tag = 'button'
                    }

                    // 获取菜单执行时的 value ，用不到则返回空 字符串或 false
                    getValue() { return '' }

                    // 菜单是否需要激活（如选中加粗文本，“加粗”菜单会激活），用不到则返回 false
                    isActive(editor) { return false }

                    // 菜单是否需要禁用（如选中 H1 ，“引用”菜单被禁用），用不到则返回 false
                    isDisabled(editor) { return false }

                    // 点击菜单时触发的函数
                    exec(editor, value) {
                        /*if (this.isDisabled(editor)) return
                        const headers = editor.getElemsByTypePrefix('header')
                        console.log('headers',headers)
                        headers.map(item => {
                            if (item.type == 'header1') {
                                // item.textAlign = 'center'
                                SlateTransforms.setNodes(editor, { fontSize: '16px' })
                            }
                        })*/
                        /*const html = editor.getHtml()
                        const parent = document.createElement('div')
                        const child = document.createElement('p')
                        child.setAttribute('style', 'line-height: 1.42; padding: 12px 15px; font-size: 14px; word-wrap: break-word;')
                        child.innerHTML = html
                        //  重置标题1行内样式
                        const header1 = child.querySelectorAll('h1')
                        if (header1.length) {
                            header1.forEach(item => item.style.fontSize = '16px')
                        }
                        parent.appendChild(child)
                        editor.setHtml(parent.innerHTML)
                        console.log('parent',parent.innerHTML)
                        this.is_open_typeset = true*/

                        // editor.render()
                    }

                }
                const typesetConfig = {
                    key: 'typeset',
                    factory () { return new TypesetButtonMenu() },
                }

                //  自定义一键排版按钮
                class ShowSourceButtonMenu {

                    constructor() {
                        this.title = '显示源码'
                        this.iconSvg = '<svg style="width: 16px; height: 16px;" t="1716364042478" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="7568" width="200" height="200"><path d="M906.6 0c0 91.7 0 182.9 0 274.5-25.9 0-51.7 0-78 0 0.2 0.2-0.2 0-0.3-0.3-0.2-0.3-0.3-0.6-0.3-0.9 0-65.4 0-130.7 0-196.5-210.5 0-420.6 0-631.1 0 0.2-0.2-0.2 0-0.3 0.3-0.2 0.3-0.5 0.6-0.5 0.9 0 65.4 0 130.8 0 196.5-26.1 0-52 0-77.8 0-1.6-9.2-2.2-241.3-0.6-274.5C380.4 0 643.4 0 906.6 0z" p-id="7569"></path><path d="M906.5 1024c-263 0-525.7 0-788.9 0 0-91.5 0-183.2 0-274.8 26.1 0 51.9 0 78.1 0 0 65.7 0 131.4 0 197 11 2.2 565.4 3 632.2 1.1 0-65.9 0-131.9 0-198.1 26.6 0 52.5 0 78.6 0C906.5 840.4 906.5 931.7 906.5 1024z" p-id="7570"></path><path d="M512.3 354.1c25.9 0 51.4 0 78.5 0 12.7 16.6 26.1 33.9 39.6 51.4 13.2-17.1 26.6-34.5 39.6-51.4 26.6 0 52.5 0 78.6 0 0 105 0 209.9 0 315.1-25.8 0-51.9 0-78.8 0 0-60.6 0-121.3 0-183.3-13.5 17.7-26.1 34.2-39 51.3-13.7-16.5-26.1-33.2-39.5-51.3 0 62 0 122.4 0 182.6-0.6 0.6-0.9 0.9-1.2 1.1-0.3 0.2-0.6 0.3-0.9 0.3-25.5 0-51 0-76.9 0C512.3 564.6 512.3 459.9 512.3 354.1z" p-id="7571"></path><path d="M155.1 553.9c0 38.2 0 76.9 0 115.7-25.5 0-50.5 0-76.1 0 0-105 0-210.1 0-315.4 25.3 0 50.3 0 75.8 0 0 38.7 0 77.2 0 115.7 1.4 1.2 2.5 2.3 3.7 3.4 12.1 0 24.4 0 37.1 0 0-39.6 0-79.1 0-118.9 26.4 0 52.5 0 78.9 0 0 105 0 209.7 0 315.1-26.1 0-52.2 0-78.6 0 0-39.6 0-78.9 0-119.3-12.9 0.8-25.5-0.9-38.1 1.1C157.2 551.2 156.4 552.6 155.1 553.9z" p-id="7572"></path><path d="M354.3 432.8c-13.4 0-25.6 0-38.4 0 0-26.3 0-52 0-78.5 51.3 0 103 0 155.1 0 0 25.6 0 51.4 0 78.1-12.3 0-24.7 0-37.6 0 0 79.2 0 157.7 0 236.8-26.4 0-52.5 0-78.9 0C354.3 591 354.3 512.5 354.3 432.8z" p-id="7573"></path><path d="M789.6 669.5c0-105.2 0-209.9 0-315.1 25.2 0 50.3 0 75.8 0-0.2-0.2 0.2 0 0.3 0.3 0.2 0.3 0.3 0.6 0.3 0.9 0 78.1 0 156.3 0 234.8 26.1 0 51.9 0 77.4 0 0.8 0.6 1.1 0.9 1.2 1.1 0.2 0.3 0.3 0.6 0.3 0.9 0 25.5 0 51 0 76.9C893.4 669.5 842 669.5 789.6 669.5z" p-id="7574"></path></svg>' // 可选
                        this.tag = 'button'
                    }

                    clickHandler () {
                        // 做任何你想做的事情
                        // 可参考【常用 API】文档，来操作编辑器
                        alert('hello world')
                    }

                    // 获取菜单执行时的 value ，用不到则返回空 字符串或 false
                    getValue() { return '' }

                    // 菜单是否需要激活（如选中加粗文本，“加粗”菜单会激活），用不到则返回 false
                    isActive(editor) { return false }

                    // 菜单是否需要禁用（如选中 H1 ，“引用”菜单被禁用），用不到则返回 false
                    isDisabled(editor) { return false }

                    // 点击菜单时触发的函数
                    exec(editor, value) {
                        let source = editor.getHtml()
                        that.is_open_source = !that.is_open_source
                        if (that.is_open_source) {
                            source = source.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/ /g, "&nbsp;")
                            source = source.replace(/<p><br><\/p>/g, "")
                            editor.setHtml(source)
                        } else {
                            const parent = document.createElement('p')
                            source = source.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&nbsp;/g, " ")
                            source = source.replace(/<p><br><\/p>/g, "")
                            parent.innerHTML = source
                            editor.setHtml(parent.innerHTML)
                        }
                        editor.restoreSelection()
                    }

                }
                const showSourceConfig = {
                    key: 'show-source',
                    factory () { return new ShowSourceButtonMenu() },
                }

                const all_menu = this.editor.getAllMenuKeys()
                // if (!all_menu.includes('insert-file')) {
                //     Boot.registerMenu(insertFileConfig)
                // }
                // if (!all_menu.includes('insert-audio')) {
                //     Boot.registerMenu(insertAudioConfig)
                // }
                if (!all_menu.includes('insert-word')) {
                    Boot.registerMenu(importWordConfig)
                }
                // if (!all_menu.includes('typeset')) {
                //     Boot.registerMenu(typesetConfig)
                // }
                // if (!all_menu.includes('show-source')) {
                //     Boot.registerMenu(showSourceConfig)
                // }
                toolbarConfig.insertKeys = {
                    index: 24,  //  插入的位置，基于当前的 toolbarKeys
                    keys: [ 'insert-word']
                }
                return new Promise(resolve => {
                    resolve(true)
                })
            },
            /** 过滤audio元素，添加属性 */
            handleFilterAudioAddAttribute () {
                return new Promise(resolve => {
                    const parent = document.createElement('p')
                    parent.innerHTML = this.content
                    let audioList = [], audioContent = parent.querySelectorAll('audio')
                    if (audioContent.length) {
                        async function asyncForEach (array, callback) {
                            for (let item of array) {
                                if (item.tagName === 'AUDIO') {
                                    item.setAttribute('data-w-e-type', 'audio')
                                    audioList.push(1)
                                    await callback(item)
                                }
                            }
                        }

                        //  同步等待循环操作结束后，执行下一步操作
                        asyncForEach(parent.children, async () => {
                            if (audioList.length === audioContent.length) {
                                resolve(parent.innerHTML)
                            }
                        })

                    } else {
                        resolve(parent.innerHTML)
                    }
                })
            },
            /** 自定义Audio元素，以及渲染自定义Audio元素 */
            handleAudioCustomTagElement (Boot, DomEditor, IDomEditor, SlateDescendant, SlateElement, h, VNode) {
                const that = this
                return new Promise(resolve => {
                    let audio_src = ''
                    //  定义一个插件，重写 isInline 和 isVoid API
                    function withAttachment (editor) {                        // JS 语法
                        const { isInline, isVoid } = editor
                        const newEditor = editor

                        newEditor.isInline = elem => {
                            const type = DomEditor.getNodeType(elem)
                            if (type === 'audio') return false // 针对 type: audio ，设置为 inline
                            return isInline(elem)
                        }

                        newEditor.isVoid = elem => {
                            const type = DomEditor.getNodeType(elem)
                            if (type === 'audio') return true // 针对 type: audio ，设置为 void
                            return isVoid(elem)
                        }

                        return newEditor // 返回 newEditor ，重要！！！
                    }
                    //  把插件 withAttachment 注册到 wangEditor
                    Boot.registerPlugin(withAttachment)

                    //  定义 renderElem 函数
                    function renderAttachment(elem, children, editor) {                                                // JS 语法
                        if (!audio_src) audio_src = elem.src || elem.link
                        // 附件 icon 图标 vnode
                        const iconVnode = h(
                            // HTML tag
                            'audio',
                            // HTML 属性
                            {
                                props: { src: audio_src, controls: 'controls' }, // HTML 属性，驼峰式写法
                                style: { width: '300px' } // HTML style ，驼峰式写法
                            }
                        )

                        // 附件元素 vnode
                        return h(
                            // HTML tag
                            'p',
                            // HTML 属性、样式、事件
                            {
                                props: { contentEditable: false }, // HTML 属性，驼峰式写法
                                style: { display: 'inline-block', marginLeft: '3px', /* 其他... */ },
                            },
                            // 子节点
                            [iconVnode]
                        )
                    }
                    //  定义 renderElem 配置
                    const renderElemConf = {
                        type: 'audio', // 新元素 type ，重要！！！
                        renderElem: renderAttachment,
                    }
                    //  把 renderElemConf 注册到 wangEditor
                    Boot.registerRenderElem(renderElemConf)

                    /** 把新元素转换为 HTML */
                    //  定义 elemToHtml 函数
                    function attachmentToHtml(elem, childrenHtml) {                             // JS 语法
                        if (!audio_src) audio_src = elem.src || elem.link
                        // 生成 HTML 代码
                        return `<audio controls data-w-e-is-void data-w-e-type="audio" data-w-e-is-inline src="${audio_src}" data-fileName="${elem.fileName}"></audio>`
                    }
                    //  定义 elemToHtml 配置
                    const elemToHtmlConf = {
                        type: 'audio', // 新元素的 type ，重要！！！
                        elemToHtml: attachmentToHtml,
                    }
                    //  注册到 wangEditor
                    Boot.registerElemToHtml(elemToHtmlConf)
                    /** 解析新元素 HTML 到编辑器 */
                    //  定义 parseElemHtml 函数
                    function parseAttachmentHtml(domElem, children, editor) {                                                     // JS 语法

                        // 从 DOM element 中获取“附件”的信息
                        const link = domElem.getAttribute('src') || domElem.getAttribute('link') || ''
                        const fileName = domElem.getAttribute('data-fileName') || ''

                        // 生成“附件”元素（按照此前约定的数据结构）
                        const myResume = {
                            type: 'audio',
                            src: link,
                            fileName,
                        }

                        return myResume
                    }
                    const parseHtmlConf = {
                        selector: 'audio[data-w-e-type="audio"]', // CSS 选择器，匹配特定的 HTML 标签
                        parseElemHtml: parseAttachmentHtml,
                    }
                    Boot.registerParseElemHtml(parseHtmlConf)
                    resolve(true)
                })
            },
            /** 点击记录图片上传类型 **/
            handleClickUploadFile (data) {
                const {parent, validate, target, index, type, editor } = data
                setTimeout(()=>{
                    this.uploadParent = parent || ''
                    this.uploadValidateType = validate
                    this.uploadTarget = target || null
                    this.uploadIndex = !isNaN(index) ? index : 'null'
                    this.uploadType = type
                    this.editor = editor
                    this.$refs['upload_file'] && this.$refs['upload_file'].click()
                }, 300)
            },
            /** 执行上传图片操作 **/
            async handleChangeUploadFile (event, type) {
                const files = type && type == 'custom_modal' ? event : event.srcElement.files
                let is_skip = true, is_arr_skip = [], upload_error = []
                if (Object.prototype.toString.call(files).slice(8, -1) === 'FileList') {
                    //  使用同步机制，校验每个文件是否符合，不符合存入false，在执行上传时，过滤false只上传符合条件的文件
                    for (let key in files) {
                        if (Object.prototype.toString.call(files[key]).slice(8, -1) === 'File') {
                            is_arr_skip[key] = await this.beforeAvatarUpload(files[key])
                        }
                    }
                } else {
                    is_skip = await this.beforeAvatarUpload(files)
                }
                if (is_arr_skip.length && Object.prototype.toString.call(files).slice(8, -1) === 'FileList') {
                    //  使用同步机制，实现节流上传，从而使图片按照顺序上传
                    for (let index in files) {
                        if (is_arr_skip[index] && Object.prototype.toString.call(files[index]).slice(8, -1) === 'File') {
                            const uploaded = await this.handleAfterUpload(files[index])
                            if (uploaded.status == 'error') {
                                upload_error.push(uploaded.file)
                            } else {
                                if (!Array.isArray(this.uploadFileForm[this.uploadTarget]) || this.uploadFileForm[this.uploadTarget] == '') {
                                    this.uploadFileForm[this.uploadTarget] = []
                                }
                                this.uploadFileForm[this.uploadTarget].push(uploaded.url)
                                //  如果上传的图片个数超出最多可上传个数，则截取前面的数据
                                /*if (this.uploadFileForm[this.uploadTarget].length > 10) {
                                    this.uploadFileForm[this.uploadTarget] = this.uploadFileForm[this.uploadTarget].slice(0, 10)
                                }*/
                            }
                        }
                    }
                    setTimeout(() => {
                        if (this.uploadTarget === 'editor_audio') {
                            if (this.uploadFileForm[this.uploadTarget].length) {
                                const audio = {
                                    type: 'audio',
                                    fileName: Math.round(new Date() / 1000),
                                    src: this.uploadFileForm[this.uploadTarget][0],
                                    children: [{ text: '' }]  // void 元素必须有一个 children ，其中只有一个空字符串，重要！！！
                                }
                                this.editor.insertNode(audio);
                                this.editor.restoreSelection()
                            } else {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('请上传音频文件')
                            }
                        }
                    }, 300)
                } else {
                    //  如果传过来的 files 数据不是 FileList 类型，则说明是单张替换图片
                    if (!is_skip) return false
                    const uploaded = await this.handleAfterUpload(files)
                    if (uploaded.status === 'error') {
                        upload_error.push(uploaded.file)
                    } else {
                        if (this.uploadType && this.uploadType === 'editor_video') {
                            this.handleClickSubmitDialog(this.uploadType)
                        } else {
                            this.uploadFileForm[this.uploadTarget][this.uploadIndex] = uploaded.url
                        }
                    }
                }
                //  清空 [input='file'] 内容
                this.$refs['upload_file'] && (this.$refs['upload_file'].value = '')
            },
            /** 同步执行图片上传 */
            handleChangeUploadFileAsync (event, type) {
                return new Promise(async (resolve, reject) => {
                    const [file] = type && type == 'custom_modal' ? event : event.srcElement.files
                    let is_skip = true, is_arr_skip = [], upload_error = []
                    is_skip = await this.beforeAvatarUpload(file)
                    if (is_skip) {
                        const uploaded = await this.handleAfterUpload(file)
                        if (uploaded.status == 'error') {
                            reject(uploaded.file)
                        } else {
                            resolve(uploaded.url)
                        }
                    }
                })
                /*if (is_arr_skip.length && Object.prototype.toString.call(files).slice(8, -1) === 'FileList') {
                    //  使用同步机制，实现节流上传，从而使图片按照顺序上传
                    for (let index in files) {
                        if (is_arr_skip[index] && Object.prototype.toString.call(files[index]).slice(8, -1) === 'File') {
                        }
                    }
                } else {
                    //  如果传过来的 files 数据不是 FileList 类型，则说明是单张替换图片
                    if (!is_skip) return false
                    const uploaded = await this.handleAfterUpload(files)
                    if (uploaded.status === 'error') {
                        upload_error.push(uploaded.file)
                    } else {
                        if (this.uploadType && this.uploadType === 'editor_video') {
                            this.handleClickSubmitDialog(this.uploadType)
                        } else {
                            this.uploadFileForm[this.uploadTarget][this.uploadIndex] = uploaded.url
                        }
                    }
                }*/
                //  清空 [input='file'] 内容
                // this.$refs['upload_file'] && (this.$refs['upload_file'].value = '')
            },
            /** 校验图片格式 **/
            beforeAvatarUpload (file) {
                this.files_name = file.name
                return new Promise(resolve=>{
                    let result = true
                    if (file && file.type) {
                        const isPNG = file.type === 'image/png'
                        const isJPEG = file.type === 'image/jpeg'
                        const isJPG = file.type === 'image/jpg'
                        const isLt10M = file.size / 1024 / 1024 < 10
                        const isLt100M = file.size / 1024 / 1024 < 20
                        const isGIF = file.type === 'image/gif'
                        if (this.uploadValidateType === 1) {
                            if (!isPNG && !isJPEG && !isJPG && !isGIF) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('仅限上传jpg、jpeg、png、gif格式的图片');
                                result = false
                                resolve(false)
                                return false
                            }
                            if (!isLt10M) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('图片大小不能超过10M');
                                result = false
                            }
                        } else if (this.uploadValidateType === 2) {
                            var fileType = file.name.split(".")[1]
                            fileType = fileType.toLowerCase()
                            //rm，rmvb，mpeg1－4， mov， mtv， dat， wmv， avi， 3gp， amv， dmv， flv
                            const isMP4 = fileType === 'mp4'
                            if (!isMP4) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('请根据提示上传正确的视频格式!');
                                result = false
                            }
                            if (!isLt100M) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('上传视频的大小不能超过 20MB!');
                                result = false
                            }
                        } else if (this.uploadValidateType === 3) {
                            var fileType = file.name.split(".")[1]
                            fileType = fileType.toLowerCase()
                            //rm，rmvb，mpeg1－4， mov， mtv， dat， wmv， avi， 3gp， amv， dmv， flv
                            /*const isMP4 = fileType === 'mp4'
                            if (!isMP4) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('请根据提示上传正确的视频格式!');
                                result = false
                            }
                            if (!isLt100M) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('上传视频的大小不能超过 20MB!');
                                result = false
                            }*/
                        } else if (this.uploadValidateType === 4) {
                            var fileType = file.name.split(".")[1]
                            fileType = fileType.toLowerCase()
                            const isMpeg = file.type === 'audio/mpeg'
                            //rm，rmvb，mpeg1－4， mov， mtv， dat， wmv， avi， 3gp， amv， dmv， flv
                            const isMP3 = fileType === 'mp3'
                            if (!isMP3 && !isMpeg) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('请根据提示上传正确的音频格式!');
                                result = false
                            }
                            if (!isLt100M) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('上传音频的大小不能超过 20MB!');
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
            },
            /** 将图片上传到接口并返回图片链接地址 */
            handleAfterUpload (file) {
                return new Promise(resolve=>{
                    //  由于 FileList 类型数据最后的length 字段是一个数字类型，因此过滤只上传文件流
                    if (Object.prototype.toString.call(file).slice(8, -1) === 'File') {
                        let reader = new FileReader()
                        //转化为binary类型
                        reader.readAsArrayBuffer(file)
                        reader.onload = (e)=>{
                            const fromdata = new FormData()
                            fromdata.append('file', file)
                            this.doPost('{{ route('manage.common.upload')  }}', fromdata).then(res=>{
                                if (res.errcode == 0) {
                                    resolve({ status: 'success', url: res.data.file })
                                } else {
                                    resolve({ status: 'error' })
                                    this.msg_error && this.msg_error.close()
                                    this.msg_error = this.$message.error('图片上传失败');
                                }
                            })
                        }
                    }
                })
            },
            /** 点击弹窗将内容添加到富文本中 */
            handleClickSubmitDialog (name) {
                this.$refs['uploadFileForm'].validate((valid)=>{
                    if (valid) {
                        if (this.uploadFileForm[name].length) {
                            if (this.file_upload_type === 'file') {
                                const html = `<a href='${this.uploadFileForm[name][0]}' target="_blank" >${this.uploadFileForm.name}</a>`
                                this.handleInsertHtmlToEditor(html, this.editor)
                            } else {
                                const list = this.uploadFileForm[name].reverse()
                                list.forEach(item=>{
                                    // this.quill.insertEmbed(index, 'image', item)
                                    const img_html = `<img src='${item}' alt='' width='auto' />`
                                    this.handleInsertHtmlToEditor(img_html, this.editor)
                                })
                                this.uploadFileForm[name] = []
                            }
                            setTimeout(_=>this.handleClickCancleDialog(), 300)
                        } else {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error('请上传图片')
                        }
                    } else {
                        return false;
                    }
                });
            },
            /** 点击关闭弹窗 */
            handleClickCancleDialog () {
                this.show_upload_dialog = false
                this.uploadFileForm.editor_files = []
                this.$refs['uploadFileForm'].resetFields();
                this.$refs['uploadFileForm'].clearValidate();
            },
            /** 将 base64 转换为 File 类型，并上传获取图片链接 */
            handleBase64ToFileUpload (base64) {
                return new Promise(async resolve => {
                    let arr = base64.split(",");
                    //  获取文件类型
                    let [,file_type] = arr[0].split('data:');
                    [file_type] = file_type.split(';base64')
                    //  获取文件后缀
                    let [,file_prefix] = file_type.split('image/')
                    //  将 base64 字符串解码为二进制字符串, atob 仅支持新版浏览器
                    let bstr = atob(arr[1]);
                    let n = bstr.length;
                    //  使用 Uint8Array 构造函数将二进制字符串转换为字节数组，Uint8Array 仅支持新版浏览器
                    let u8arr = new Uint8Array(n);
                    while (n--) {
                        u8arr[n] = bstr.charCodeAt(n);
                    }
                    const file_name = `${Math.round(new Date() / 1000)}.${file_prefix}`
                    //  使用 Blob 对象构造函数创建一个 File 对象
                    const file = new File([u8arr], file_name, { type: file_type })
                    const upload_result = await this.handleAfterUpload(file)
                    if (upload_result.status === 'success') {
                        resolve(upload_result.url)
                    }
                })
            },
            /** 将table表格转换为图片，获取图片链接 */
            handleTableTransToImage (table_view) {
                return new Promise(resolve => {
                    const options = {
                        scale: window.devicePixelRatio, // 添加的scale 参数
                        logging: false, //日志开关，便于查看html2canvas的内部执行流程
                        useCORS: true, // 【重要】开启跨域配置
                        scrollY: 0
                    };
                    html2canvas(table_view, options).then(async (canvas) => {
                        const img_url = await this.handleBase64ToFileUpload(canvas.toDataURL())
                        resolve(img_url)
                    });
                })
            },
            /** 设置将HTML插入到富文本中 */
            handleInsertHtmlToEditor (html, editor) {
                console.log(html, editor)
                if (editor) {
                    editor.restoreSelection()
                    editor.dangerouslyInsertHtml(html)
                } else {
                    this.editor.restoreSelection()
                    this.editor.dangerouslyInsertHtml(html)
                }
            },
            /** 更新富文本内容 */
            updateEditor (html) {
                this.editor.setHtml(html)
            },
            /** 注销富文本 */
            handleCheckDestroy () {
                const editor = this.editor
                if (editor == null) return
                this.editor.destroy()
            },
        },
        mounted () {
            this.initEditorInfo()
        }
    })
</script>
