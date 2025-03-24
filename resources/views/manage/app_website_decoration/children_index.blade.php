@extends('admin.layout')

@section('content')
    <div class="box-header">
        <h3 class="box-title"><span style="font-weight: bold;cursor: pointer"
                                    @click="parent.openTab('移动端装修','{!! route('manage.app_web_decoration.index') !!}')">移动端装修</span>>><span
                    style="font-weight: bold;">@{{ parent_name }}</span></h3>
    </div>
    <el-header id="content-header" style="display: flex;align-items: center;justify-content: space-between;">
        <div>
            <el-button type="primary" @click="openUpdateInfoVisible([],form.alias)"
                       style="width: 120px;background: #409EFF;">创建页面
            </el-button>
        </div>
        <div>
            <el-input v-model="form.keywords" placeholder="请输入页面名称/页面ID" clearable
                      @keyup.enter.native="getData()" style="width: 280px;" @clear="getData()"></el-input>
            <el-button type="danger" @click="getData()" style="width: 90px;">查询</el-button>
        </div>
    </el-header>
    <el-main>
        <dl class="decoration-list">
            <dt>
                <ul class="s-flex ai-ct">
                    <li class="s-flex ai-ct">ID/编号</li>
                    <li class="s-flex ai-ct jc-fs">页面</li>
                    <li class="s-flex ai-ct jc-ct">端口</li>
                    <li class="s-flex ai-ct jc-ct">
                        <el-dropdown @command="handleCommand" trigger="click">
                    <span class="el-dropdown-link">
                        <template>
                           <span v-if="range === '1'">底部菜单</span>
                           <span v-else-if="range === '2'">一级页面</span>
                           <span v-else-if="range === '3'">二级页面</span>
                           <span v-else>页面类型</span>
                        </template>
                       <i class="el-icon-arrow-down el-icon--right"></i>
                    </span>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item command="" :class="range===''?'select-item':''">页面类型<i
                                            class="el-icon-check" v-if="range===''"></i></el-dropdown-item>
                                <el-dropdown-item command="1" :class="range==='1'?'select-item':''">底部菜单<i
                                            class="el-icon-check" v-if="range==='1'"></i></el-dropdown-item>
                                <el-dropdown-item command="2" :class="range==='2'?'select-item':''">一级页面<i
                                            class="el-icon-check" v-if="range==='2'"></i></el-dropdown-item>
                                <el-dropdown-item command="3" :class="range==='3'?'select-item':''">二级页面<i
                                            class="el-icon-check" v-if="range==='3'"></i></el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </li>
                    <li class="s-flex ai-ct jc-ct">所属文件夹</li>
                    <li class="s-flex ai-ct jc-ct">APP端</li>
                    <li class="s-flex ai-ct jc-ct">H5端</li>
                    <li class="s-flex ai-ct jc-ct">小程序端</li>
                    <li class="s-flex ai-ct jc-ct">是否展示</li>
                    <li class="s-flex ai-ct jc-ct">装修人</li>
                    <li class="s-flex ai-ct jc-ct">更新时间</li>
                    <li class="s-flex ai-ct jc-fe flex-1">操作</li>
                </ul>
            </dt>
            <dd>
                <ul class="s-flex ai-ct" v-for="(item,index) in tableData">
                    <li>@{{ item.id }}</li>
                    <li style="text-align: left">
                        <div class="s-flex ai-ct jc-fs">
                            <div style="margin-right: 10px;">
                                {{--<svg v-if="!item.is_dir" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path
                                            d="M11.8 15.5H6.2c-.568 0-.964 0-1.273-.026-.302-.024-.476-.07-.608-.138a1.5 1.5 0 0 1-.656-.655c-.067-.132-.113-.305-.137-.608-.026-.309-.026-.705-.026-1.273V3.2c0-.568 0-.964.026-1.273.024-.302.07-.476.137-.608A1.5 1.5 0 0 1 4.32.663c.132-.067.306-.113.608-.137C5.236.5 5.632.5 6.2.5h3.788c.572 0 .767.004.942.05a1.5 1.5 0 0 1 .47.218c.149.103.278.25.648.686l1.811 2.136c.295.348.394.468.463.597a1.5 1.5 0 0 1 .145.395l.489-.105-.49.105c.031.144.034.299.034.755V12.8c0 .568 0 .964-.026 1.273-.024.303-.07.476-.138.608a1.5 1.5 0 0 1-.655.655c-.132.068-.305.114-.608.138-.309.026-.705.026-1.273.026Z"
                                            fill="#fff" stroke="#B5C1D3"></path>
                                    <rect x="1" y="4" width="10" height="8.969" rx="2" fill="#fff"></rect>
                                    <rect x="1" y="4" width="10" height="8.969" rx="2" fill="#428CFD"></rect>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M3 6.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5ZM3 8.484a.5.5 0 0 1 .5-.5h5a.5.5 0 1 1 0 1h-5a.5.5 0 0 1-.5-.5Z"
                                          fill="#fff"></path>
                                    <path opacity="0.7" fill-rule="evenodd" clip-rule="evenodd"
                                          d="M3 10.469c0-.276.256-.5.571-.5H6.43c.315 0 .571.224.571.5s-.256.5-.571.5H3.57c-.315 0-.571-.224-.571-.5Z"
                                          fill="#fff"></path>
                                </svg>
                                <svg v-else width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path opacity="0.01" fill="#C4C4C4" d="M0 0h16v16H0z"></path>
                                    <path
                                            d="M6.006 1c.367 0 .55 0 .723.041.153.037.3.098.433.18.152.093.282.223.54.482l.595.594c.26.26.39.39.54.482a1.5 1.5 0 0 0 .434.18C9.444 3 9.627 3 9.994 3H13.6c.84 0 1.26 0 1.581.163a1.5 1.5 0 0 1 .655.656c.164.32.164.74.164 1.581v7.2c0 .84 0 1.26-.164 1.581a1.5 1.5 0 0 1-.655.655c-.32.164-.74.164-1.581.164H2.4c-.84 0-1.26 0-1.581-.164a1.5 1.5 0 0 1-.656-.655C0 13.861 0 13.441 0 12.6V3.4c0-.84 0-1.26.163-1.581a1.5 1.5 0 0 1 .656-.656C1.139 1 1.559 1 2.4 1h3.606Z"
                                            fill="#6AB9FF"></path>
                                </svg>--}}
                                <em v-if="!item.is_dir" class="iconfont" style="font-size: 20px;">&#xe612;</em>
                                <em v-else class="iconfont">&#xe7dd;</em>
                            </div>
                            <div style="">
                                <p>@{{ item.name }}</p>
                                <p v-if="item.is_dir" style="font-size: 12px;color: #ccc">共@{{ item.children_count
                                    }}个页面</p>
                            </div>
                        </div>

                    </li>
                    <li class="s-flex ai-ct jc-ct">移动端</li>
                    <li class="s-flex ai-ct jc-ct">@{{ item.type_message }}</li>
                    <li class="s-flex ai-ct jc-ct">@{{ item.parent_name }}</li>
                    <li class="s-flex ai-ct jc-ct">@{{ item.app_url }}</li>
                    <li class="s-flex ai-ct jc-ct">
                        <div class="s-flex flex-dir ai-ct" v-if="item.h5_url">
                            <el-link :underline="false" type="primary" :href="item.h5_url" target="_blank">预览
                            </el-link>
                            <el-button type="text" @click="copyData(item.h5_url,'复制地址成功')">复制地址</el-button>
                        </div>
                        <div v-else>--</div>

                    </li>
                    <li class="s-flex ai-ct jc-ct">@{{ item.mini_url }}</li>
                    <li class="s-flex ai-ct jc-ct">
                        <el-switch
                                @change="changeItemIsShow(item)"
                                :value="item.is_show"
                                active-color="#13ce66"
                                active-value="1"
                                inactive-color="#ff4949"
                                inactive-value="0">
                        </el-switch>
                    </li>
                    <li class="s-flex ai-ct jc-ct">@{{ item.admin_user_name }}</li>
                    <li class="s-flex ai-ct jc-ct">@{{ item.updated_at }}</li>
                    <li class="s-flex ai-ct jc-fe flex-1">
                        <el-button type="success" size="small"
                                   @click="openUpdateInfoVisible(item.type,'update',item.id)">页面设置
                        </el-button>
                        <el-button type="primary" size="small" @click="openDesignVisible(item)">页面装修</el-button>
                        <el-button type="warning" size="small" @click="copyAppWebsiteDecoration(item)">生成副本
                        </el-button>
                        <el-button type="danger" size="small" v-if="item.is_show_destroy"
                                   @click="destroyAppWebsiteDecoration(item)">删除
                        </el-button>
                    </li>
                </ul>
            </dd>
        </dl>
        <div v-if="!tableData.length&&!loading">
            <div style="text-align: center;padding-bottom: 50px;">
                <div style="padding: 50px 0 20px;">
                    <img src="https://cdn.toodudu.com/uploads/2022/01/04/order-no.png" alt="" style="width: 221px;"
                         v-if="!is_search">
                    <img src="https://cdn.toodudu.com/uploads/2022/01/04/order-search-no.png" alt=""
                         style="width: 221px;"
                         v-else>
                </div>
                <p style="font-size: 14px;color: #333;" v-if="!is_search">暂无数据！</p>
                <p style="font-size: 14px;color: #333;" v-else>没有搜索到符合条件的数据！</p>
            </div>
        </div>
    </el-main>
    @include('admin.components.footer')
    <el-dialog
            title="创建页面"
            :visible.sync="dialogVisible"
            width="30%"
            :close-on-click-modal="false"
            :before-close="cancelUpdateInfoVisible"
            :show-close="false"
            custom-class="set-dialog"
            :close-on-press-escape="false">
        <div slot="title">@{{ dialog_title }}</div>
        <div class="set-main">
            <div class="set-base">
                <div class="set-title">基础设置</div>
                <div>
                    <el-form label-width="90px" class="ruleForm">
                        <el-form-item label="网页名称" required style="margin-bottom: 20px;">
                            <el-input v-model="formSet.name" :disabled="formSet.name_is_disabled == true" placeholder=""
                                      style="width: 500px;" show-word-limit maxlength="8"></el-input>
                        </el-form-item>
                        <el-form-item v-if="is_show_form_set_show" label="是否显示" required
                                      style="margin-bottom: 20px;">
                            <el-switch
                                    v-model="formSet.is_show"
                                    active-color="#13ce66"
                                    active-value="1"
                                    active-text="开启"
                                    inactive-color="#ff4949"
                                    inactive-text="关闭"
                                    inactive-value="0">
                            </el-switch>
                        </el-form-item>
                        <el-form-item label="页面类型" required style="margin-bottom: 20px;">
                            <el-checkbox-group v-model="formSet.type" disabled="true">
                                <el-checkbox v-for="check_item in check_data" :label="check_item.value">@{{
                                    check_item.label }}
                                </el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="小程序入口封面图" required style="margin-bottom: 20px;">
                            <div style="display: flex;">
                                <div class="signature-images">
                                    <template v-if="formSet.image_url">
                                        <div class="image-item">
                                            <el-image :src="formSet.image_url" disabled
                                                      v-if="formSet.image_url"></el-image>
                                            <div class="image-icon upload" ref="imageUpload" id="btn"
                                                 @click="handleClickUploadFile({ target: 'image_url', validate: 3 })">
                                                <i class="iconfont">&#xe6eb;</i>
                                            </div>
                                            <div class="image-icon last delete" @click="removeSmallPhoto">
                                                <i class="el-icon-delete"></i>
                                            </div>
                                            <!--<input type="file" ref="uploadFile" accept=".png"-->
                                            <!--       @change="function(file) { beforeAvatarUpload(file) }">-->
                                        </div>
                                    </template>
                                    <!--<el-upload
                                            style="margin-right: 10px;"
                                            v-else
                                            ref="signatureUpload"
                                            accept=".png"
                                            action=""
                                            :show-file-list="false"
                                            name="licenseUrl"
                                            :before-upload="beforeAvatarUpload">
                                        <i class="el-icon-plus"></i>
                                    </el-upload>-->
                                    <div class="el-upload el-upload--text" v-else @click="handleClickUploadFile({ target: 'image_url', validate: 3 })"></div>
                                </div>
                                <dl>
                                    <dd class="contract_elwarr">支持png格式</dd>
                                    <dd class="contract_elwarr">建议尺寸：300px*240px</dd>
                                    <dd class="contract_elwarr">建议大小：128KB</dd>
                                </dl>
                            </div>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="set-img">
                <div class="set-title">TDK设置</div>
                <div>
                    <el-form label-width="100px" class="ruleForm">
                        <el-form-item label="网页标题">
                            <el-input v-model="formSet.title" placeholder="" style="width: 500px;" show-word-limit
                                      maxlength="80"></el-input>
                            <p class="tip-p">建议填写页面主题定位，用于在浏览器的标签文字中显示</p>
                        </el-form-item>
                        <el-form-item label="网页关键词">
                            <el-input v-model="formSet.keywords" placeholder="" style="width: 500px;" show-word-limit
                                      maxlength="100"></el-input>
                            <p class="tip-p">建议填写页面主题定位相关的词语，多个关键词请使用逗号隔开。</p>
                        </el-form-item>
                        <el-form-item label="网页描述">
                            <el-input v-model="formSet.description" type="textarea" placeholder="" style="width: 500px;"
                                      show-word-limit maxlength="200"></el-input>
                            <p class="tip-p">建议填写页面主题定位的详细描述，能给用户带来什么价值，提供什么样的内容等。</p>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
        <div slot="footer" style="display: flex;justify-content: center;padding-top: 5px;">
            <el-button type="primary" :loading="storeDataLoading" style="width: 120px;margin-right: 20px;"
                       @click="storeData()">保存
            </el-button>
            <el-button style="width: 120px;" @click="cancelUpdateInfoVisible()">取消</el-button>
        </div>
    </el-dialog>
    <material-lib
            :is_show="is_show_material_dialog"
            :model="material_dialog_model"
            :selected_count="material_dialog_selected_count"
            :max_count="material_dialog_max_count"
            :upload_validate="uploadValidateType"
            @close="is_show_material_dialog = false"
            @update="handleChangeUploadFile" />
@endsection

@section('js')
    <script>
        var vm = new Vue({
            el: '#app',
            data: {
                is_search: false,
                tableData: [],  //  表数据信息
                loading: true,
                dialogVisible: false,   //  设置弹窗
                pageInfo: {},
                dialog_title: '创建页面',
                formSet: {
                    id: 0,
                    name: '',
                    title: '',
                    keywords: '',
                    description: '',
                    name_is_disabled: false,
                    type: [],
                    is_show: '1',
                    image_url: '',
                    alias: '',
                    parent_id: '{{ $parent_id }}',
                },
                is_show_form_set_show: false,
                form: {
                    page: 1,
                    type: '',
                    status: '',
                    keywords: '',
                    alias: '{{ $alias }}'
                },
                check_data: @json( $check_data ),
                range: '',
                parent_name: "{{ $parent_name }}",
                storeDataLoading: false,
                upload_size: 2,
                is_show_material_dialog: false,
                material_dialog_model: 'single',
                material_dialog_max_count: 1,
                material_dialog_selected_count: 0,
                uploadValidateType: 3
            },
            methods: {
                getData(page = 1) {
                    this.form.page = page;
                    this.loading = true;
                    let url = ''
                    if (this.form.alias == '{{ \App\Models\AppWebsiteDecoration::ALIAS_ZIXUN_CATEGORY_PAGE }}') {
                        url = '{!! route('manage.zixun_app_web_decoration.index') !!}' + '?alias=' + this.form.alias
                    } else {
                        url = '{!! route('manage.app_web_decoration.children_index') !!}'
                    }
                    this.doGet(url, this.form).then(res => {
                        if (res.code == 200) {
                            this.tableData = res.data.data;
                            this.setPageInfo(res.data);
                            this.is_search = !!this.form.type
                        }
                        this.loading = false;
                    });
                },
                /** 打开 页面设置 弹窗 */
                openUpdateInfoVisible(type, identification = "update", app_website_decoration_id = null) {
                    if (identification !== "update") { // 数据新增
                        this.dialog_title = '创建页面';
                        this.dialogVisible = true;
                        setTimeout(() => {
                            $('.el-dialog__body').scrollTop(0)
                        }, 100)
                        if (identification === '{{ \App\Models\AppWebsiteDecoration::ALIAS_INDUSTRIAL }}') {
                            this.check_data = [{label: "一级页面", value: 2}];
                            this.formSet.type = [2]
                        } else if (identification === '{{ \App\Models\AppWebsiteDecoration::ALIAS_CATEGORY_LIST }}') {
                            this.check_data = [{label: "二级页面", value: 3}];
                            this.formSet.type = [3]
                        } else if (identification === '{{ \App\Models\AppWebsiteDecoration::ALIAS_PUBLIC }}') {
                            this.check_data = [{label: "底部菜单", value: 1}, {label: "二级页面", value: 3}];
                            this.formSet.type = [1, 3]
                        } else if (identification === '{{ \App\Models\AppWebsiteDecoration::ALIAS_ZIXUN_CATEGORY_PAGE }}') {
                            this.check_data = [{label: "二级页面", value: 3}];
                            this.formSet.type = [3]
                        }
                        this.formSet.alias = identification
                        this.is_show_form_set_show = true;
                    } else { // 数据编辑
                        this.dialog_title = '页面设置';
                        let url = ''
                        if (this.form.alias == '{{ \App\Models\AppWebsiteDecoration::ALIAS_ZIXUN_CATEGORY_PAGE }}') {
                            url = '{!! route('manage.zixun_app_web_decoration.edit') !!}' + '?alias=' + this.form.alias
                        } else {
                            url = '{!! route('manage.app_web_decoration.edit') !!}'
                        }
                        this.doPost(url, {id: app_website_decoration_id}).then(res => {
                            this.formSet = res.data.info;
                            this.is_show_form_set_show = res.data.info.parent_id > 0;
                            this.dialogVisible = true;
                            this.check_data = res.data.type_map_data;
                            this.formSet.name_is_disabled = res.data.info.parent_id == 0;
                            setTimeout(() => {
                                $('.el-dialog__body').scrollTop(0)
                            }, 100)
                        }).catch(error => {
                            this.$message.error('获取详情失败');
                        });
                    }

                },
                /** 关闭弹窗 */
                cancelUpdateInfoVisible() {
                    this.dialogVisible = false;
                    setTimeout(() => {
                        this.formSet.id = 0;
                        this.formSet.is_show = '1';
                        this.formSet.image_url = '';
                        this.formSet.name = '';
                        this.formSet.title = '';
                        this.formSet.keywords = '';
                        this.formSet.description = '';
                        this.formSet.name_is_disabled = false;
                        this.formSet.type = [];
                    }, 200)
                },
                /** 保存数据 */
                storeData() {
                    if (!this.formSet.name) {
                        this.$message.error('请输入页面名称');
                        return;
                    }
                    if (this.formSet.name.length > 30) {
                        this.$message.error('页面名称不能超过30个字符');
                        return;
                    }
                    if (!this.formSet.title) {
                        this.formSet.title = this.formSet.name;
                    }
                    if (this.formSet.title.length > 80) {
                        this.$message.error('网页标题不能超过80个字符');
                        return;
                    }
                    if (!this.formSet.keywords) {
                        this.formSet.keywords = this.formSet.name;
                    }
                    if (this.formSet.keywords.length > 100) {
                        this.$message.error('网页关键词不能超过100个字符');
                        return;
                    }
                    if (!this.formSet.description) {
                        this.formSet.description = this.formSet.name;
                    }
                    if (this.formSet.description.length > 200) {
                        this.$message.error('网页描述不能超过200个字符');
                        return;
                    }
                    this.storeDataLoading = true;
                    let url = ''
                    if (this.form.alias == '{{ \App\Models\AppWebsiteDecoration::ALIAS_ZIXUN_CATEGORY_PAGE }}') {
                        url = '{!! route('manage.zixun_app_web_decoration.store') !!}'
                    } else {
                        url = '{!! route('manage.app_web_decoration.store') !!}'
                    }
                    this.doPost(url, this.formSet).then(res => {
                        this.storeDataLoading = false;
                        // 关闭当前页面 并重新获取数据
                        if (res.code == 200) {
                            this.$message.success('设置成功');
                            this.cancelUpdateInfoVisible()
                            this.getData();
                        } else {
                            this.$message.error(res.message);
                        }
                    }).catch(error => {
                        this.$message.error('请求失败')
                    })
                },
                /**
                 * 设置分页数据
                 * @param pageInfo
                 */
                @include('admin.components.footer-js')
                /**
                 * 列表  范围处理
                 * @param command
                 */
                handleCommand(command) {
                    this.form.type = this.range = command;
                    this.getData()
                },
                /**
                 * 复制别名 act
                 * @param data
                 * @param success_message
                 */
                copyData(data, success_message) {
                    let fakeElem = document.createElement('textarea')
                    fakeElem.value = data
                    document.body.appendChild(fakeElem)
                    fakeElem.select()
                    if (document.execCommand('Copy')) {
                        vm.$message({
                            message: success_message,
                            type: 'success'
                        });
                    } else {
                        vm.$message({
                            message: "复制失败! ",
                            type: 'error'
                        });
                    }
                    if (fakeElem) {
                        document.body.removeChild(fakeElem)
                        fakeElem = null
                    }
                },
                /**
                 * 打开装修页面
                 * @param item
                 */
                openDesignVisible(item) {
                    let url = ''
                    if (this.form.alias == '{{ \App\Models\AppWebsiteDecoration::ALIAS_ZIXUN_CATEGORY_PAGE }}') {
                        url = '{!! route('manage.zixun_app_web_decoration.decoration') !!}' + "?id=" + item.id;
                    } else {
                        url = '{!! route('manage.app_web_decoration.decoration') !!}' + "?id=" + item.id;
                    }
                    parent.openTab('页面装修-' + item.name + '-' + item.id, url)

                },
                /**
                 * 生成副本
                 * @param item
                 */
                copyAppWebsiteDecoration(item) {
                    this.$confirm('【' + item.name + '】确定要生成副本吗？', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning',
                        center: true
                    }).then(() => {
                        let url = ''
                        if (this.form.alias == '{{ \App\Models\AppWebsiteDecoration::ALIAS_ZIXUN_CATEGORY_PAGE }}') {
                            url = '{!! route('manage.zixun_app_web_decoration.copy') !!}'
                        } else {
                            url = '{!! route('manage.app_web_decoration.copy') !!}'
                        }
                        this.doPost(url, {id: item.id}).then(res => {
                            if (res.code == 200) {
                                this.$message.success('生成副本成功');
                                this.getData()
                            } else {
                                this.$message.error("生成副本失败:" + res.message + "请刷新页面后重试")
                            }
                        })
                    });
                },
                /**
                 * 删除页面
                 * @param item
                 */
                destroyAppWebsiteDecoration(item) {
                    this.$confirm('确定要删除【' + item.name + '】页面吗?', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning',
                        center: true
                    }).then(() => {
                        let url = ''
                        if (this.form.alias == '{{ \App\Models\AppWebsiteDecoration::ALIAS_ZIXUN_CATEGORY_PAGE }}') {
                            url = '{!! route('manage.zixun_app_web_decoration.destroy') !!}'
                        } else {
                            url = '{!! route('manage.app_web_decoration.destroy') !!}'
                        }
                        this.doPost(url, {id: item.id}).then(res => {
                            if (res.code == 200) {
                                this.$message.success('删除成功');
                                this.getData()
                            } else {
                                this.$alert(res.message, '提示', {
                                    confirmButtonText: '确定',
                                    type: 'warning',
                                    center: true
                                });
                            }
                        })
                    })
                },
                /**
                 * 修改图片
                 * @param item
                 * @param index
                 */
                updateSmallPhoto(item, index) {
                    this.$refs['uploadFile'].click()
                },
                /**
                 * 删除图片
                 */
                removeSmallPhoto() {
                    this.formSet.image_url = '';
                },
                /**
                 * 上传图片
                 * @param e
                 */
                beforeAvatarUpload(e) {
                    var js_file = ''
                    if (e.target) {
                        js_file = e.target.files[0];
                    } else {
                        js_file = e
                    }
                    var isPNG = js_file.type && js_file.type === 'image/png';
                    if (!isPNG) {
                        this.$message.error('仅支持png格式的图片!');
                        return false;
                    }
                    var isLt129k = js_file.size / 1024 / 1024 < this.upload_size;
                    if (!isLt129k) {
                        this.$message.error(`仅限上传大小在${this.upload_size}M内的图片!`);
                        return false;
                    }
                    var isSize = this.validateImageSize(js_file)
                    if (!isSize) {
                        this.$message.error('仅限上传300px*240px的图片');
                        return false;
                    }

                    if (isPNG && isSize && isLt129k) {
                        var fromdata = new FormData();
                        fromdata.append('file', js_file);
                        this.doPost('{!! route('manage.common.upload') !!}', fromdata).then(res => {
                            if (res.code == 200) {
                                this.formSet.image_url = res.data.file;
                            } else {
                                this.$message.error('上传文件失败')
                            }
                        }).catch(error => {
                            this.$message.error('上传失败');
                        });
                    }
                },
                validateImageSize(file) {
                    return new Promise((resolve, reject) => {
                        let reader = new FileReader();
                        setTimeout(() => {
                            reader.readAsDataURL(file); // 必须用file.raw
                            reader.onload = () => { // 让页面中的img标签的src指向读取的路径
                                let img = new Image();
                                img.src = reader.result;
                                if (img.complete) { // 如果存在浏览器缓存中
                                    if (img.width > 300 || img.height > 240) {
                                        resolve(false)
                                    } else {
                                        resolve(true)
                                    }
                                } else {
                                    img.onload = () => {
                                        if (img.width > 300 || img.height > 240) {
                                            resolve(false)
                                        } else {
                                            resolve(true)
                                        }
                                    }
                                }
                            }
                        }, 150)
                    })
                },
                changeItemIsShow(item) {
                    this.$confirm(item.is_show == 0 ? '' : '关闭后，页面装修时将不能选择此页面。', '确定要' + (item.is_show == 0 ? '开启' : '关闭') + '【' + item.name + '】页面吗？', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        center: true
                    }).then(() => {
                        let url = ''
                        if (this.form.alias == '{{ \App\Models\AppWebsiteDecoration::ALIAS_ZIXUN_CATEGORY_PAGE }}') {
                            url = '{!! route('manage.zixun_app_web_decoration.change_is_show') !!}'
                        } else {
                            url = '{!! route('manage.app_web_decoration.change_is_show') !!}'
                        }
                        this.doPost(url, {id: item.id}).then(res => {
                            if (res.code == 200) {
                                this.$message.success('变更成功');
                                this.getData()
                            } else {
                                this.$alert(res.message, '提示', {
                                    confirmButtonText: '确定',
                                    type: 'warning',
                                    center: true
                                });
                            }
                        })
                    })
                },
                /** 点击记录图片上传类型 **/
                handleClickUploadFile (data) {
                    const { parent, validate, target, index, size } = data
                    this.uploadParent = parent || ''
                    this.uploadValidateType = validate
                    this.uploadTarget = target || null
                    this.uploadIndex = !isNaN(index) ? index : 'null'
                    // this.$refs['upload_file'].click()
                    this.is_show_material_dialog = true
                    this.material_dialog_model = 'single'
                    this.material_dialog_max_count = 1
                    this.material_dialog_selected_count = 0
                },
                /** 执行上传图片操作 **/
                handleChangeUploadFile (data) {
                    let result = data[0].img;
                    this.$set(this.formSet, this.uploadTarget, result)
                },
            },
            mounted() {
                this.getData();
            }
        });
    </script>

@endsection
@section('css')
    <style>
        li {
            list-style: none;
        }

        p, ul, li {
            margin: 0;
            padding: 0;
        }

        /*flex布局*/
        .s-flex {
            display: -webkit-box;
            display: -moz-box;
            display: -webkit-flex;
            display: -moz-flex;
            display: -ms-flexbox;
            display: flex;
        }

        .flex-1 {
            -prefix-box-flex: 1;
            -webkit-box-flex: 1;
            -webkit-flex: 1;
            -moz-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
        }

        .flex-dir {
            flex-direction: column;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        .flex-no-wrap {
            flex-wrap: nowrap;
        }

        .jc-ct {
            justify-content: center;
        }

        .ai-ct {
            align-items: center;
        }

        .ai-bl {
            align-items: baseline;
        }

        .jc-bt {
            justify-content: space-between;
        }

        .jc-ad {
            justify-content: space-around;
        }

        .jc-fe {
            justify-content: flex-end;
        }

        .jc-fs {
            justify-content: flex-start;
        }

        .ai-fe {
            align-items: flex-end;
        }

        .ai-fs {
            align-items: flex-start;
        }

        .cursorp {
            cursor: pointer;
        }

        .decoration-list {
        }

        .decoration-list dt ul {
            background-color: #F4F8FB;
        }

        .decoration-list dt ul li {
            height: 50px;
            font-weight: bold;
            color: #333333;
        }

        .decoration-list dd ul {
            padding: 20px 0;
        }

        .decoration-list dd ul:not(:last-child) {
            border-bottom: 1px solid #f2f2f2;
        }

        .decoration-list ul li {
            display: flex;
            justify-content: center;
            font-size: 14px;
        }

        .decoration-list ul li:nth-of-type(1) {
            width: 5%;
        }

        .decoration-list ul li:nth-of-type(2) {
            width: 8%;
        }

        .decoration-list ul li:nth-of-type(3) {
            width: 5%;
        }

        .decoration-list ul li:nth-of-type(4) {
            width: 10%;
        }

        .decoration-list ul li:nth-of-type(5) {
            width: 10%;
        }

        .decoration-list ul li:nth-of-type(6) {
            width: 10%;
            text-align: center;
        }

        .decoration-list ul li:nth-of-type(7) {
            width: 5%;
        }

        .decoration-list ul li:nth-of-type(8) {
            width: 5%;
        }

        .decoration-list ul li:nth-of-type(9) {
            width: 5%;
        }

        .decoration-list ul li:nth-of-type(10) {
            width: 5%;
        }

        .decoration-list ul li:nth-of-type(11) {
            width: 10%;
        }

        .decoration-list ul li:last-child {
            padding-right: 20px;
            justify-content: flex-end;
            box-sizing: border-box;
        }

        .set-dialog {
            width: 700px !important;
            height: 100vh;
            margin-top: 0 !important;
            position: absolute;
            right: 0;
            border-radius: 0;
            overflow: auto;
        }

        .set-dialog .el-dialog__header {
            padding: 0 20px;
            height: 70px;
            line-height: 70px;
            background: #F4F8FB;
        }

        .set-dialog .el-dialog__header div {
            color: #333333;
            font-size: 16px;
            font-weight: bold;
        }

        .set-dialog .el-dialog__body {
            padding: 0;
        }

        .set-dialog .set-main > div {
            border-bottom: 1px solid #EFEFEF;
            padding: 20px 20px 15px;
        }

        .set-dialog .set-main > div:last-of-type {
            border-bottom: none;
        }

        .el-dialog__footer {
            border-top: 1px solid #EFEFEF;
        }

        .set-dialog .set-main .set-title {
            font-size: 14px;
            font-weight: bold;
            color: #333333;
            line-height: 20px;
            padding: 5px 0 20px;
        }

        .set-dialog .set-main .tip-p {
            font-size: 12px;
            color: #999;
            line-height: 26px;
        }

        .set-dialog .set-main .tip-h5 {
            font-size: 14px;
            color: #333;
            line-height: 28px;
            padding: 5px 0;
        }

        .set-dialog .set-main .el-form-item {
            margin-bottom: 5px;
        }

        .set-dialog .signature-images {
            display: flex;
            flex-wrap: wrap;
        }

        .set-dialog .signature-images input {
            display: none;
        }

        .set-dialog .signature-images .el-image {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .set-dialog .signature-images .el-image img {
            width: 100%;
            height: 100%;
        }

        .set-dialog .signature-images .image-item {
            width: 100px;
            height: 100px;
            background: #F2F2F2;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid #d9d9d9;
            border-radius: 0;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .set-dialog .signature-images .image-item .image-icon {
            display: block;
            width: 50px;
            height: 50px;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.5);
            position: absolute;
        }

        .set-dialog .signature-images .image-item .image-icon.upload {
            width: 30px;
            height: 30px;
            left: 12px;
            border-radius: 50%;
            top: 35px;
            display: flex;
        }

        .set-dialog .signature-images .image-item .image-icon.delete {
            width: 30px;
            height: 30px;
            right: 12px;
            border-radius: 50%;
            top: 35px;
            display: flex;
        }

        .set-dialog .signature-images .image-item:hover .image-icon {
            opacity: 1
        }

        .set-dialog .signature-images .image-item .image-icon.text.active {
            display: flex;
        }

        .set-dialog .signature-images .image-item .image-icon i {
            font-size: 16px;
            color: #ffffff;
        }

        .set-dialog .el-upload .el-icon-plus {
            width: 100px;
            height: 100px;
            line-height: 100px;
            border: 1px dashed #d9d9d9;
            border-radius: 0;
        }

        .set-dialog .el-upload-list--picture-card .el-upload-list__item {
            width: 100px;
            height: 100px;
        }

        .set-dialog .el-upload--text {
            width: 100px;
            height: 100px;
            background: #F8F8F8 url("https://cdn.toodudu.com/uploads/2022/01/05/upload-btn.png") no-repeat center;
            background-size: 45px 45px;
            border: 1px dashed #CFCFCF;
        }

        .set-dialog .el-upload--text i {
            display: none;
        }

        .set-dialog .contract_elwarr {
            height: 22px;
            line-height: 22px;
            font-size: 12px;
            color: #999999;
        }

        .set-dialog .contract_elwarr span {
            color: #999;
        }

        .auto-label .el-form-item__label {
            line-height: 20px;
        }

        .set-dialog .el-textarea {
            position: relative;
        }

        .set-dialog .el-dialog__body {
            padding: 0;
            height: calc(100vh - 145px);
            overflow: auto;
        }

        .set-dialog .el-textarea .el-input__count {
            position: absolute;
            right: 5px;
            bottom: -5px;
            color: #c0c4cc;
        }
    </style>
@endsection
