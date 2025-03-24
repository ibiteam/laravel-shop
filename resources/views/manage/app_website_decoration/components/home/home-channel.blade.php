<style>
    /* 频道广场 */
    .template-channel dl { width: 348px; min-height: 290px; padding: 30px 20px; margin-top: 10px; background-color: #ffffff; border-radius: 20px; box-sizing: border-box; }
    .template-channel dl:nth-child(2n) { margin-left: 10px; }
    .template-channel dt img { width: 27px; height: 27px; margin-right: 12px; }
    .template-channel dt h1 { font-weight: bold; font-size: 30px; color: #333333; }
    .template-channel dt p { margin: 20px 0; font-size: 24px; color: #f71111; }
    .template-channel dt .channel-tag { padding: 4px 10px; margin-left: 12px; background-color: #F71111; border-radius: 6px; font-size: 20px; color: #ffffff; }
    .template-channel dd .channel-title { margin: 20px 0; font-size: 26px; }
    .template-channel dd .channel-title,
    .template-channel dd .channel-data { width: 100%; }
    .template-channel dd .channel-data span { font-weight: bold; font-size: 28px; color: #333333; }
    /*.template-channel dd .channel-title span:nth-of-type(2),*/
    .template-channel dd .channel-title span.zhang { color: #f71111; }
    .template-channel dd .channel-title span.die { color: #00B578; }
    .template-channel dd .channel-data em { font-size: 28px; }
    .template-channel dd .channel-data img { width: 146px; height: 146px; border-radius: 6px; }
</style>

<script type="text/x-template" id="home-channel-template">
    <div class="temp-item" :class="{ 's-flex ai-ct jc-ct': temp_item.activity_info && !temp_item.activity_info.length, active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <public-mark-template :title="temp_item.name" :index="temp_index" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>
        <template>
            <template>
                <div class="temp-item-nodata s-flex flex-dir ai-ct jc-ct" v-if="!temp_item.data || computedDataType(temp_item.data) == 'Array' || !temp_item.data.items.length">
                    <p>@{{ temp_item.name }}</p>
                    <p>点击“设置”，配置组件内需要展示的内容吧</p>
                </div>
                <div class="template-channel s-flex flex-wrap" v-else>
                    <dl v-for="(item, index) in temp_item.data.items">
                        <dt>
                            <div class="s-flex ai-ct">
                                <img v-if="item.title_style == 2" :src="item.icon" alt="" />
                                <h1 class="ellipsis-1" :style="{ maxWidth: item.title_style == 3 ? '50%' : '' }">@{{ item.title }}</h1>
                                <div v-if="item.title_style == 3" class="channel-tag ellipsis-1" :style="{ maxWidth: item.title_style == 3 ? '50%' : '', backgroundColor: item.label_bg_color }">@{{ item.label }}</div>
                            </div>
                            <p class="ellipsis-1" v-if="item.type == 3" :style="{ color: item.type == 3 ? item.subtitle_font_color : '' }">@{{ item.subtitle }}</p>
                        </dt>
                        <dd v-if="item.type == 1">
                            <carousel-swiper
                                    width="308"
                                    height="200"
                                    :list="item.up_down_data"
                                    mode="vertical"
                                    :is_show_dots="false"
                                    :key="swiperKey">
                                <template slot="default" slot-scope="scope">
                                    <template v-for="child in scope.row">
                                        <div class="channel-title s-flex ai-ct jc-bt">
                                            <p class="ellipsis-1">@{{ child.produce_name }}</p>
                                        </div>
                                        <div class="channel-data s-flex ai-ct jc-bt">
                                            {{--<span>@{{ child.price }}</span>
                                            <span>@{{ child.change }}<em class="iconfont" :style="{ color: child.change == '--' || child.change == 0 ? '#333333' : child.change > 0 ? '' : '' }">&#xe7c4;</em></span>--}}
                                            <span style="color: #f71111;">登录可见</span>
                                        </div>
                                    </template>
                                </template>
                            </carousel-swiper>
                        </dd>
                        <dd v-if="item.type == 2 && item.price_data">
                            <carousel-swiper
                                    width="308"
                                    height="200"
                                    :list="item.price_data"
                                    mode="vertical"
                                    :is_show_dots="false"
                                    :key="parent.tabIndex">
                                <template slot="default" slot-scope="scope">
                                    {{--<div class="channel-title s-flex ai-ct jc-bt">
                                        <p>@{{ child.produce_name }}</p>
                                    </div>--}}
                                    <div class="channel-title s-flex ai-ct jc-bt" style="margin: 13px 0;">
                                        <span class="ellipsis-1">@{{ scope.row.produce_name }}</span>
                                        <span class="ellipsis-1" :class="{ zhang: scope.row.change > 0, die: scope.row.change < 0 }">@{{ scope.row.index_price }}</span>
                                    </div>
                                    <div class="channel-data s-flex ai-ct jc-bt" style="height: 100%;">
                                        <img src="https://cdn.toodudu.com/uploads/2023/11/06/index_zhang.png" style="width: 100%; height: 100%;" alt="" v-if="scope.row.change > 0" />
                                        <img src="https://cdn.toodudu.com/uploads/2023/11/06/index_die.png" style="width: 100%; height: 100%;" alt="" v-if="scope.row.change < 0" />
                                        <img src="https://cdn.toodudu.com/uploads/2023/11/06/index_ping.png" style="width: 100%; height: 100%;" alt="" v-if="scope.row.change == 0" />
                                    </div>
                                </template>
                            </carousel-swiper>
                        </dd>
                        <dd class="s-flex ai-ct jc-bt" v-if="item.type == 3">
                            <div class="channel-data s-flex ai-ct jc-bt flex-1" :style="{ marginTop: item.type == 3 && item.subtitle ? 0 : '64px' }">
                                <img v-for="child in item.custom_data" :src="child.image" alt="">
                            </div>
                        </dd>
                    </dl>
                </div>
            </template>
        </template>
        <!--设置弹窗-->
        <el-drawer  :with-header="false" :modal-append-to-body="false" :close-on-press-escape="false" :show-close="false" :wrapper-closable="false" size="1200px" :visible.sync="is_show_drawer" :direction="direction">
            <input type="file" @change="handleChangeUploadFile" value="" style="display: none;" ref="upload_file" />
            <el-form :model="templateSetForm" :rules="templateSetRule" ref="templateSetForm" class="drawer-content" v-load="set_save_loading">
                <div class="drawer-name s-flex ai-ct">
                    <label>设置板块：</label>
                    <span>@{{ templateSetFormName }}</span>
                </div>
                <div style="height: calc(100vh - 160px); overflow-y: auto;" ref="drawerBox" v-if="templateSetForm.items">
                    <div class="drawer-item" v-if="templateSetForm.items">
                        <div class="drawer-item-dt">
                            <div class="s-flex ai-ct jc-bt">
                                <div>
                                    <p>最多添加2~10个</p>
                                </div>
                                <div class="drawer-btn s-flex ai-ct jc-ct cursorp" :class="{ disabled: templateSetForm.items.length >= 10 }" @click="handleClickAddData('items', 10, '', publicListItem.channel)">
                                    <em class="iconfont">&#xe620;</em>
                                    <label>添加（@{{ templateSetForm.items.length }}/10）</label>
                                </div>
                            </div>
                        </div>
                        <div class="drawer-item-dd">
                            <drawer-slides :slide_active.sync="slideIndex" target="title" default_text="标题名称" :list="templateSetForm.items" @change="() => { this.templateSetForm.items[slideIndex].type == 2 && handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: this.templateSetForm.items[slideIndex].price_index.data_source, page_type: 1 }, parent: `items.${slideIndex}`, target: 'default_selection_data' }) }"></drawer-slides>
                            <div class="drawer-item-cate" v-for="(item, index) in templateSetForm.items" :key="slideIndex" v-if="slideIndex == index">
                                <div class="cate-title s-flex ai-ct jc-bt" style="height: 40px;">
                                    <h1>板块内容</h1>
                                    <div class="drawer-btn s-flex ai-ct jc-ct cursorp" v-if="templateSetForm.items.length" @click="handleClickDeleteData({ index: slideIndex, parent: 'items', confirm: '确定要删除吗？', min: 1 })">
                                        <label>删除</label>
                                    </div>
                                </div>
                                <el-form-item label="选择板块" label-width="110px" :prop="'items.' + index + '.type'" :rules="{ required: true, message: '请选择板块', trigger: 'change' }">
                                    <el-radio-group v-model="item.type" @change="(value) => { clearFormValidate(`templateSetForm`), value == 2 && handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: 1, page_type: 1 }, parent: `items.${index}`, target: 'default_selection_data' }) }">
                                        <el-radio :label="1" :disabled="templateSetForm.items.filter(check => check.type == 1).length">
                                            <span>商品涨跌</span>
                                            <el-tooltip
                                                    style="margin: 0 20px 0 0;"
                                                    effect="light"
                                                    placement="right"
                                                    width="100"
                                                    popper-class="drawer-preview-image"
                                                    style="min-width: 100px;"
                                                    trigger="hover">
                                                <div slot="content" class="drawer-upload-warning">
                                                    <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2023/10/26/title_icon.png', is_show_imageview = true">
                                                        <em class="iconfont cursorp">&#xe7c3;</em>
                                                    </div>
                                                    <el-image style="width: 100px; height: 100px" :src="'https://cdn.toodudu.com/uploads/2023/10/26/title_icon.png'"></el-image>
                                                </div>
                                                <em class="iconfont cursorp">&#xe72d;</em>
                                            </el-tooltip>
                                        </el-radio>
                                        <el-radio :label="2" :disabled="templateSetForm.items.filter(check => check.type == 2).length">
                                            <span>价格指数</span>
                                            <el-tooltip
                                                    style="margin: 0 20px 0 0;"
                                                    effect="light"
                                                    placement="right"
                                                    width="100"
                                                    popper-class="drawer-preview-image"
                                                    style="min-width: 100px;"
                                                    trigger="hover">
                                                <div slot="content" class="drawer-upload-warning">
                                                    <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2023/10/26/title.png', is_show_imageview = true">
                                                        <em class="iconfont cursorp">&#xe7c3;</em>
                                                    </div>
                                                    <el-image style="width: 100px; height: 100px" :src="'https://cdn.toodudu.com/uploads/2023/10/26/title.png'"></el-image>
                                                </div>
                                                <em class="iconfont cursorp">&#xe72d;</em>
                                            </el-tooltip>
                                        </el-radio>
                                        <el-radio :label="3">
                                            <span>自定义板块</span>
                                            <el-tooltip
                                                    style="margin: 0 20px 0 0;"
                                                    effect="light"
                                                    placement="right"
                                                    width="100"
                                                    popper-class="drawer-preview-image"
                                                    style="min-width: 100px;"
                                                    trigger="hover">
                                                <div slot="content" class="drawer-upload-warning">
                                                    <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2023/10/26/title_label.png', is_show_imageview = true">
                                                        <em class="iconfont cursorp">&#xe7c3;</em>
                                                    </div>
                                                    <el-image style="width: 100px; height: 100px" :src="'https://cdn.toodudu.com/uploads/2023/10/26/title_label.png'"></el-image>
                                                </div>
                                                <em class="iconfont cursorp">&#xe72d;</em>
                                            </el-tooltip>
                                        </el-radio>
                                    </el-radio-group>
                                </el-form-item>
                                <el-form-item label="标题样式" label-width="110px" :prop="'items.' + index + '.title_style'" :rules="{ required: true, message: '请选择板块', trigger: 'change' }">
                                    <el-radio-group v-model="item.title_style" style="height: 40px;" class="s-flex ai-ct" @change="clearFormValidate(`templateSetForm`), $forceUpdate()">
                                        <el-radio :label="1">
                                            <span>标题</span>
                                            <el-tooltip
                                                    style="margin: 0 20px 0 0;"
                                                    effect="light"
                                                    placement="right"
                                                    width="100"
                                                    popper-class="drawer-preview-image"
                                                    style="min-width: 100px;"
                                                    trigger="hover">
                                                <div slot="content" class="drawer-upload-warning">
                                                    <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = item.type != 3 ? 'https://cdn.toodudu.com/uploads/2023/10/26/title.png' : 'https://cdn.toodudu.com/uploads/2023/10/26/title_subtitle.png', is_show_imageview = true">
                                                        <em class="iconfont cursorp">&#xe7c3;</em>
                                                    </div>
                                                    <el-image style="width: 100px; height: 100px" :src="item.type != 3 ? 'https://cdn.toodudu.com/uploads/2023/10/26/title.png' : 'https://cdn.toodudu.com/uploads/2023/10/26/title_subtitle.png'"></el-image>
                                                </div>
                                                <em class="iconfont cursorp">&#xe72d;</em>
                                            </el-tooltip>
                                        </el-radio>
                                        <el-radio :label="2">
                                            <span>ICON+标题</span>
                                            <el-tooltip
                                                    style="margin: 0 20px 0 0;"
                                                    effect="light"
                                                    placement="right"
                                                    width="100"
                                                    popper-class="drawer-preview-image"
                                                    style="min-width: 100px;"
                                                    trigger="hover">
                                                <div slot="content" class="drawer-upload-warning">
                                                    <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2023/10/26/title_icon.png', is_show_imageview = true">
                                                        <em class="iconfont cursorp">&#xe7c3;</em>
                                                    </div>
                                                    <el-image style="width: 100px; height: 100px" src="https://cdn.toodudu.com/uploads/2023/10/26/title_icon.png"></el-image>
                                                </div>
                                                <em class="iconfont cursorp">&#xe72d;</em>
                                            </el-tooltip>
                                        </el-radio>
                                        <el-radio :label="3">
                                            <span>标题+标签</span>
                                            <el-tooltip
                                                    style="margin: 0 20px 0 0;"
                                                    effect="light"
                                                    placement="right"
                                                    width="100"
                                                    popper-class="drawer-preview-image"
                                                    style="min-width: 100px;"
                                                    trigger="hover">
                                                <div slot="content" class="drawer-upload-warning">
                                                    <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2023/10/26/title_label.png', is_show_imageview = true">
                                                        <em class="iconfont cursorp">&#xe7c3;</em>
                                                    </div>
                                                    <el-image style="width: 100px; height: 100px" src="https://cdn.toodudu.com/uploads/2023/10/26/title_label.png"></el-image>
                                                </div>
                                                <em class="iconfont cursorp">&#xe72d;</em>
                                            </el-tooltip>
                                        </el-radio>
                                    </el-radio-group>
                                </el-form-item>
                                <el-form-item label="ICON" label-width="110px" v-if="item.title_style == 2" :prop="'items.' + index + '.icon'" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                    <div class="s-flex">
                                        <div class="drawer-upload s-flex ai-ct jc-ct cursorp">
                                            <img v-if="item.icon" :src="item.icon" alt="">
                                            <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!item.icon" @click="handleClickUploadFile({ parent: `items.${index}`, target: 'icon', validate: 1})">
                                                <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                    <em class="iconfont">&#xe727;</em>
                                                </div>
                                                <p>未上传</p>
                                            </div>
                                            <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-if="item.icon" @click="handleClickUploadFile({ parent: `items.${index}`, target: 'icon', validate: 1})">
                                                <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0;">
                                                    <em class="iconfont">&#xe727;</em>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="drawer-upload-warning">
                                            <p>支持jpg、jpeg、png格式</p>
                                            <p>建议尺寸：100px*100px</p>
                                            <p>建议大小：2M内</p>
                                        </div>
                                    </div>
                                </el-form-item>
                                <el-form-item label="标题" label-width="110px" :prop="'items.' + index + '.title'" :rules="[
                                    { required: true, message: '请输入标题', trigger: 'blur' },
                                    { max: 6, message: '标题不能超过6个字符', trigger: 'blur' },
                                    { validator: (rule, value, callback) => {
                                        //  校验是否有相同的名称
                                        const same_name = templateSetForm.items.filter(data => value == data.title)
                                        if (same_name.length > 1) { callback(new Error('该标题已存在，请修改！')); } else { callback(); }
                                    }, trigger: 'blur' }
                                ]">
                                    <el-input v-model="item.title" style="width: 500px;"></el-input>
                                    <p class="warning-text">用于改变页面中展示的名称</p>
                                </el-form-item>
                                <el-form-item label="标签" label-width="110px" v-if="item.title_style == 3" :prop="'items.' + index + '.label'" :rules="[
                                    { required: true, message: '请输入标签', trigger: 'blur' },
                                    { max: 6, message: '标签不能超过6个字符', trigger: 'blur' },
                                    { validator: (rule, value, callback) => {
                                        //  校验是否有相同的名称
                                        const same_name = templateSetForm.items.filter(data => value == data.label)
                                        if (same_name.length > 1) { callback(new Error('该标签已存在，请修改！')); } else { callback(); }
                                    }, trigger: 'blur' }
                                ]">
                                    <div class="s-flex ai-ct">
                                        <el-input v-model="item.label" style="width: 500px;"></el-input>
                                        <el-color-picker style="margin-left: 20px;" v-model="item.label_bg_color"></el-color-picker>
                                    </div>
                                    <p class="warning-text">文字显示在标题右侧。样式：背景色块+文字，默认文字白色，背景色可修改</p>
                                </el-form-item>
                                <el-form-item label="副标题" label-width="110px" v-show="item.type == 3" :prop="'items.' + index + '.subtitle'" :rules="[
                                    { required: item.type == 3, message: '请输入副标题', trigger: 'blur' },
                                    { max: 10, message: '副标题不能超过10个字符', trigger: 'blur' }
                                ]">
                                    <div class="s-flex ai-ct">
                                        <el-input v-model="item.subtitle" style="width: 500px;"></el-input>
                                        <el-color-picker style="margin-left: 20px;" v-model="item.subtitle_font_color"></el-color-picker>
                                    </div>
                                    <p class="warning-text">文字显示在标题下方。样式：文字，文字颜色可修改</p>
                                </el-form-item>
                                <el-form-item label="URL链接" label-width="110px" v-if="item.url && item.type != 3">
                                    <div class="s-flex ai-ct">
                                        <el-form-item style="margin-bottom: 22px;">
                                            <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="item.url.alias" @change="item.url.value = '', item.url.default_selection_data = [], clearFormValidate(`items_url_value${index}`)" @clear="item.url.value = ''" placeholder="请选择" style="width: 150px;">
                                                <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                            </el-select>
                                        </el-form-item>
                                        <el-form-item :ref="`items_url_value${index}`" style="margin-bottom: 22px;" :prop="'items.' + index + '.url.value'" :rules="{ required: item.url.alias != '' && computedOptionsPlaceholder(item.url.alias), message: computedOptionsPlaceholder(item.url.alias), trigger: 'blur' }">
                                            <el-select v-model="item.url.value"
                                                   v-if="computedOptionsPlaceholder(item.url.alias) && computedOptionsCanRemote(item.url.alias)"
                                                   filterable
                                                   remote
                                                   reserve-keyword
                                                   clearable
                                                   :remote-method="(query) => {
                                                        handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: item.url.alias }, parent: `items.${index}.url`, target: 'default_selection_data' }),
                                                        handleRemoteSearchChose({ parent: `items.${index}.url.default_selection_data`, chose: item.url.value })
                                                   }"
                                                   @change="(value) => handleRemoteSearchChose({ parent: `items.${index}.url.default_selection_data`, chose: item.url.value, ref: `items_url_value${index}` })"
                                                   @focus="handleRemoteSearchChose({ parent: `items.${index}.url.default_selection_data`, chose: item.url.value })"
                                                   :placeholder="computedOptionsPlaceholder(item.url.alias)" style="width: 350px">
                                                    <el-option v-for="option in item.url.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                </el-select>
                                            <el-input class="radius-left-none__input" v-else v-model="item.url.value" :disabled="(!computedOptionsPlaceholder(item.url.alias) && item.url.alias != '' && item.url.alias != null) || (!item.url.alias && !item.url.value)" :placeholder="computedOptionsPlaceholder(item.url.alias) ? computedOptionsPlaceholder(item.url.alias) : !computedOptionsPlaceholder(item.url.alias) && item.url.alias != '' && item.url.alias != null ? '系统生成链接' : ''" style="width: 350px;"></el-input>
                                        </el-form-item>
                                    </div>
                                    <p class="warning-text">默认跳转至@{{ item.type == 1 ? '商品涨跌' : '多多指数' }}页面，设置URL链接后将跳转至指定页面</p>
                                </el-form-item>
                                <el-form-item label="排序" label-width="110px">
                                    <el-input v-model="item.sort" style="width: 500px;"></el-input>
                                    <p class="warning-text">板块从左到右，从上到下依次显示，数字越大越靠上</p>
                                </el-form-item>
                                {{--<el-form-item label="商品涨跌" label-width="110px" v-if="item.type == 1">
                                    <p class="warning-text" style="line-height: inherit; font-size: 14px;">获取“商品涨跌”列表中最新日期的所有数据，循环轮播展示。</p>
                                    <el-button type="text"><span class="cursorp" @click="parent.openTab('商品涨跌列表', '{!! route('manage.dd_produce_up_down.list') !!}')" style="color: #409EFF;">去设置产品 ></span></el-button>
                                </el-form-item>--}}
                                <template v-if="item.type == 1">
                                    <el-form-item label="数据规则" label-width="110px" :prop="'items.' + index + '.goods_up_down_data_type'" :rules="{ required: true, message: '请选择数据规则', trigger: 'change' }">
                                        {{-- @change="handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: item.price_index.data_source, page_type: 1 }, parent: `items.${index}`, target: 'default_selection_data' }), item.price_index.price_item = []"--}}
                                        <el-radio-group v-model="item.goods_up_down_data_type">
                                            <el-radio :label="1">按日期显示</el-radio>
                                            <el-radio :label="2">按数量显示</el-radio>
                                        </el-radio-group>
                                        <p class="warning-text">@{{ item.goods_up_down_data_type == 1 ? '获取“商品涨跌”列表中最新日期的所有数据，循环轮播展示。' : '获取“商品涨跌”列表中前xx条的数据（按ID倒序排列），循环轮播展示。' }}</p>
                                    </el-form-item>
                                    <el-form-item label="展示数量" v-if="item.goods_up_down_data_type == 2" label-width="110px" :prop="'items.' + index + '.goods_up_down_num'" :rules="
                                        [
                                            { required: true, message: '请输入展示数量', trigger: 'blur' },
                                            { validator: (rule, value, callback) => {
                                                if (value) {
                                                    if (isNaN(value)) {
                                                        callback(new Error('请输入数字'));
                                                    } else if (!Number.isInteger(value * 1)) {
                                                        callback(new Error('请输入整数'));
                                                    } else if (value < 1 || value > 100) {
                                                        callback(new Error('展示数量的填写范围是1~100'));
                                                    } else { callback(); }
                                                } else { callback(); }
                                            }, trigger: 'blur' },
                                        ]
                                    ">
                                        <el-input v-model="item.goods_up_down_num" style="width: 500px;"></el-input>
                                        <p class="warning-text">填写的数量范围：1~100。在数量范围内，重复的产品仅显示最新日期的一条数据。</p>
                                    </el-form-item>
                                    <el-form-item label="数据来源" label-width="110px">
                                        <div class="s-flex ai-ct">
                                            <p class="warning-text" style="line-height: inherit; font-size: 14px;">此处不支持修改，请前往<商品涨跌>菜单中配置。</p>
                                            <el-button type="text"><span class="cursorp" style="color: #409EFF;">去设置产品 ></span></el-button>
                                        </div>
                                    </el-form-item>
                                </template>
                                <template v-if="item.price_index" v-if="item.type == 2">
                                    <el-form-item label="数据来源" label-width="110px" :prop="'items.' + index + '.price_index.data_source'" :rules="{ required: true, message: '请选择数据来源', trigger: 'change' }">
                                        <el-radio-group v-model="item.price_index.data_source" v-model="item.price_index.data_source" @change="handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: item.price_index.data_source, page_type: 1 }, parent: `items.${index}`, target: 'default_selection_data' }), item.price_index.price_item = []">
                                            <el-radio :label="1">获取各产品的价格指数</el-radio>
                                            <el-radio :label="2">获取“多多指数”表中数据</el-radio>
                                        </el-radio-group>
                                    </el-form-item>
                                    <el-form-item label="推荐产品" label-width="110px">
                                        <div class="s-flex ai-ct">
                                            <el-select
                                                    v-model="item.price_index.product_ids"
                                                    filterable
                                                    placeholder="请输入产品名称搜索"
                                                    @focus="handleRemoteSearchChose({ parent: `items.${index}.default_selection_data`, chose: item.price_index.price_item, value: 'id', child_value: 'product_id' })"
                                                    style="width: 500px;">
                                                <el-option v-for="(option, optionIndex) in item.default_selection_data" :disabled="option.is_disabled" :key="option.value" :label="option.name" :value="option.value" v-if=""></el-option>
                                            </el-select>
                                            <div class="drawer-btn primary s-flex ai-ct jc-ct" style="margin-left: 20px;" :class="{ disabled: item.price_index.price_item.length >= 10 }"
                                                @click="() => {
                                                    const filter = item.default_selection_data.filter(selected => selected.value == item.price_index.product_ids)
                                                    filter.length && handleClickAddData(`items.${index}.price_index.price_item`, 10, '推荐产品', { product_id: filter[0].id, name: filter[0].name, value: filter[0].value, type: filter[0].type }, 'product_id'),
                                                    handleRemoteSearchChose({ parent: `items.${index}.default_selection_data`, chose: item.price_index.price_item, value: 'id', child_value: 'product_id' }),
                                                    item.price_index.product_ids = ''
                                                }"
                                            >添加（@{{ item.price_index.price_item.length }}/10）</div>
                                        </div>
                                    </el-form-item>
                                    <el-form-item label="已选择产品" label-width="110px" class="cate-choosed" :prop="'items.' + index + '.price_index.price_item'" :rules="
                                        [
                                            { required: true, message: '请推荐并添加产品', trigger: 'change' },
                                        ]
                                    ">
                                        <ul class="s-flex ai-ct flex-wrap" v-if="item.price_index.price_item.length">
                                            <li class="s-flex ai-ct cursorp" v-for="(child, childIndex) in item.price_index.price_item" v-if="child.name">
                                                <span>【@{{ child.product_id }}】@{{ child.name }}</span>
                                                <em class="iconfont" @click="handleClickDeleteData({ index: childIndex, parent: `items.${index}.price_index.price_item` })">&#xe634;</em>
                                            </li>
                                        </ul>
                                        <p class="warning-text" style="line-height: 40px; font-size: 14px;" v-else>请推荐并添加产品</p>
                                    </el-form-item>
                                </template>
                                <template v-if="item.type == 3">
                                    <dl>
                                        <dt>
                                            <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                                <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                                    <span>*</span>
                                                    <label>图片</label>
                                                    <el-tooltip
                                                            style="margin: 0 20px 0 0;"
                                                            effect="light"
                                                            placement="right"
                                                            width="200"
                                                            trigger="hover">
                                                        <div slot="content" class="drawer-upload-warning">
                                                            <p>支持jpg、jpeg、png、gif格式</p>
                                                            <p>建议尺寸：73px*73px</p>
                                                            <p>建议大小：2M内</p>
                                                        </div>
                                                        <em class="iconfont cursorp">&#xe72d;</em>
                                                    </el-tooltip>
                                                </li>
                                                <li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">URL链接</li>
                                                <li class="s-flex ai-ct jc-ct" style="width: 90px;">排序</li>
                                            </ul>
                                        </dt>
                                        <dd class="s-flex flex-dir ai-ct">
                                            <ul class="s-flex ai-ct" style="height: 90px; padding: 0 40px;" v-for="(child, childIndex) in item.image_items">
                                                <li style="width: 90px;">
                                                    <el-form-item :key="index" :prop="'items.' + index + '.image_items.' + childIndex + '.image'" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                                        <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                            <img v-if="child.image" :src="child.image" alt="">
                                                            <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!child.image" @click="handleClickUploadFile({ parent: `items.${index}.image_items.${childIndex}`, validate: 2, target: 'image'})">
                                                                <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                                    <em class="iconfont">&#xe727;</em>
                                                                </div>
                                                                <p>未上传</p>
                                                            </div>
                                                            <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                                <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `items.${index}.image_items.${childIndex}`, validate: 2, target: 'image'})">
                                                                    <em class="iconfont">&#xe727;</em>
                                                                </div>
                                                                <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile({ parent: `items.${index}.image_items.${childIndex}`, target: 'image'})">
                                                                    <em class="iconfont">&#xe738;</em>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </el-form-item>
                                                </li>
                                                <li class="s-flex ai-ct jc-ct flex-1">
                                                    <el-form-item :prop="'items.' + index + '.image_items.' + childIndex + '.url.alias'">
                                                        <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="child.url.alias" @change="child.url.value = '', child.url.default_selection_data = [], child.url.default_selection_data = [], child.url.alias == 'yi_qi_xiu' && handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: '', alias: child.url.alias }, parent: `items.${index}.image_items.${childIndex}.url`, target: 'default_selection_data' }), clearFormValidate(`image_items_url_value${index}`)" @clear="child.url.value = ''" placeholder="请选择" style="width: 200px;">
                                                            <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                                        </el-select>
                                                    </el-form-item>
                                                    <el-form-item :ref="`image_items_url_value${index}`" :prop="'items.' + index + '.image_items.' + childIndex + '.url.value'" :rules="{ required: child.url.alias != '' && computedOptionsPlaceholder(child.url.alias), message: computedOptionsPlaceholder(child.url.alias), trigger: 'blur' }">
                                                        <el-select v-model="child.url.value"
                                                           v-if="computedOptionsPlaceholder(child.url.alias) && computedOptionsCanRemote(child.url.alias)"
                                                           filterable
                                                           remote
                                                           reserve-keyword
                                                           clearable
                                                           :remote-method="(query) => {
                                                                handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: child.url.alias }, parent: `items.${index}.image_items.${childIndex}.url`, target: 'default_selection_data' }),
                                                                handleRemoteSearchChose({ parent: `items.${index}.image_items.${childIndex}.url.default_selection_data`, chose: child.url.value })
                                                           }"
                                                           @change="(value) => handleRemoteSearchChose({ parent: `items.${index}.image_items.${childIndex}.url.default_selection_data`, list: item.image_items, index: childIndex, chose: child.url.value, ref: `image_items_url_value${index}`, selected: child.url })"
                                                           @focus="handleRemoteSearchChose({ parent: `items.${index}.image_items.${childIndex}.url.default_selection_data`, chose: child.url.value })"
                                                           :placeholder="computedOptionsPlaceholder(child.url.alias)" style="width: 330px">
                                                            <el-option v-for="option in child.url.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                        </el-select>
                                                        <el-input class="radius-left-none__input" v-else v-model="child.url.value" :disabled="(!computedOptionsPlaceholder(child.url.alias) && child.url.alias != '' && child.url.alias != null) || (!child.url.alias && !child.url.value)" :placeholder="computedOptionsPlaceholder(child.url.alias) ? computedOptionsPlaceholder(child.url.alias) : !computedOptionsPlaceholder(child.url.alias) && child.url.alias != '' && child.url.alias != null ? '系统生成链接' : ''" style="width: 330px;"></el-input>
                                                    </el-form-item>
                                                </li>
                                                <li style="width: 90px;">
                                                    <el-form-item :prop="'items.' + index + '.image_items.' + childIndex + '.sort'" :rules="
                                                    [
                                                        { validator: (rule, value, callback) => {
                                                            if (value) {
                                                                if (isNaN(value)) {
                                                                    callback(new Error('请输入数字'));
                                                                } else if (!Number.isInteger(value * 1)) {
                                                                    callback(new Error('请输入整数'));
                                                                } else if (value < 1) {
                                                                    callback(new Error('排序最小值是1'));
                                                                } else if (value > 100) {
                                                                    callback(new Error('排序最大值是100'));
                                                                } else { callback(); }
                                                            } else { callback(); }
                                                        }, trigger: 'blur' },
                                                    ]
                                                ">
                                                        <el-input v-model="child.sort" placeholder="1~100" ></el-input>
                                                    </el-form-item>
                                                </li>
                                            </ul>
                                            <p class="warning-nodata" v-if="!item.image_items.length">暂无数据！</p>
                                        </dd>
                                    </dl>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <el-form-item style="margin: 0; box-shadow: 0 -0.2rem 0.2rem -0.22rem rgba(0, 0, 0, 0.2);">
                    <div class="s-flex ai-ct jc-ct" style="margin: 20px 0;">
                        <div style="width: 120px; margin: 0 15px;" class="drawer-btn primary s-flex ai-ct jc-ct" @click="handleClickSubmitSetForm('templateSetForm')">保存</div>
                        <div style="width: 120px; margin: 0 15px;" class="drawer-btn s-flex ai-ct jc-ct" @click.stop="handleClickCancelSetForm('templateSetForm')">取消</div>
                    </div>
                </el-form-item>
            </el-form>
        </el-drawer>
        <el-dialog
            title="提示"
            :visible.sync="is_show_imageview"
            width="600px">
            <div class="img-set s-flex ai-ct jc-ct">
                <img :src="image_view" alt="">
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
    </div>
</script>

<!--模块遮罩插件-->
@include('manage.app_website_decoration.components.template-mark-setting')
<!--广告轮播插件-->
@include('manage.app_website_decoration.components.carousel-swiper')
<!--slide tab按钮插件-->
@include('manage.app_website_decoration.components.drawer-slides')
<script>
    /**
     * name 装修模版--按钮切换组件
     * list Tab数据
     * width 父级盒子宽度
     * child_width 滑动元素宽度
     * offset Tab元素间隔：margin-right 值
     * custom_class 自定义类名
     * icon_top 图标上下偏移量
     * initial_index 指定选中项
     * **/
    Vue.component('home-channel-template', {
        props: {
            temp_index: {
                type: Number,
                default: 0
            },
            temp_item: {
                type: Object,
                default: () => { return {} }
            },
            temp_list: {
                type: Array,
                default: () => { return [] }
            },
            active_index: {
                type: Number,
                default: 0
            },
            options: {
                type: Array,
                default: () => { return [] }
            },
            mode: {
                type: String,
                default: '1'
            },
            upload_size_validate: {
                type: Number,
                default: 2
            },    //  上传大小限制，以兆为单位，默认2M
        },
        data () {
            return {
                direction: 'rtl',   //  打开方向
                set_save_loading: false,    //  保存加载动画
                is_show_drawer: false,
                templateSetFormName: '',    //  设置弹窗标题
                templateSetFormType: '',    //  设置组件别名
                templateSetFormIndex: '',    //  设置组件索引值
                templateSetForm: {},    //  设置表单数据
                templateSetRule: {},
                is_show_imageview: false,
                image_view: '',
                publicListItem: {
                    channel: {
                        goods_up_down_data_type: '',
                        goods_up_down_num: '',
                        'type': 3,
                        'title_style': 1,
                        'icon': "",
                        'title': "",
                        'label': "",
                        'label_bg_color': "#F71111",
                        'subtitle': "",
                        'subtitle_font_color': "#F71111",
                        'url': {
                            'alias': "https",
                            'value': ""
                        },
                        'sort': "1",
                        'price_index': {
                            'data_source': 1,
                            'price_item': []
                        },
                        'image_items': [
                            {
                                'image': "",
                                'url': {
                                    'alias': "https",
                                    'value': ""
                                },
                                'sort': "1"
                            },
                            {
                                'image': "",
                                'url': {
                                    'alias': "https",
                                    'value': ""
                                },
                                'sort': "1"
                            }
                        ]
                    }
                },
                slideIndex: 0,
                swiperKey: Math.round(new Date() / 1000),
                msg_error: null,
                is_show_material_dialog: false,
                material_dialog_model: 'single',
                material_dialog_max_count: 1,
                material_dialog_selected_count: 0,
                uploadValidateType: 2
            }
        },
        computed: {
            /** 获取公共下拉数据返回输入框占位 */
            computedOptionsPlaceholder () {
                return function(value) {
                    const filter = this.options.filter(item => item.alias == value)
                    return filter.length ? filter[0].desc : ''
                }
            },
            /** 获取公共URL链接配置是否可以进行下拉搜索 */
            computedOptionsCanRemote () {
                return function(value) {
                    const filter = this.options.filter(item => item.alias == value)
                    return filter.length ? filter[0].need_remote_search : false
                }
            },
            /** 获取字段类型 */
            computedDataType () {
                return function (value) {
                    const type = Object.prototype.toString.call(value).slice(8, -1)
                    return type
                }
            },
        },
        template: '#home-channel-template',
        methods: {
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting (data) {
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
                this.templateSetFormType = item.component_name
                if (this.templateSetForm.items && this.templateSetForm.items.length && this.templateSetForm.items[this.slideIndex].type == 2) {
                    this.handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: 1, page_type: 1 }, parent: `items.${this.slideIndex}`, target: 'default_selection_data' })
                }
                this.is_show_drawer = true
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
            /** 校验图片格式 **/
            beforeAvatarUpload(file) {
                return new Promise(resolve => {
                    let resovlt = true
                    if (file && file.type) {
                        const isPNG = file.type === 'image/png'
                        const isJPEG = file.type === 'image/jpeg'
                        const isJPG = file.type === 'image/jpg'
                        const isLt2M = file.size / 1024 / 1024 < this.upload_size_validate;
                        const isGIF = file.type === 'image/gif'
                        if (this.uploadValidateType >= 2) {
                            if (!isPNG && !isJPEG && !isJPG && !isGIF) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('仅限上传jpg、jpeg、png、gif格式的图片');
                                resolve(false)
                                return false
                            }
                        } else {
                            if (!isPNG && !isJPEG && !isJPG) {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error('仅限上传jpg、jpeg、png格式的图片');
                                resolve(false)
                                return false
                            }
                        }
                        if (!isLt2M) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`图片大小不能超过${this.upload_size_validate}M`);
                            resovlt = false
                        }
                    } else {
                        resovlt = false
                    }
                    resolve(resovlt)
                })
            },
            /** 执行上传图片操作 **/
            async handleChangeUploadFile (data) {
                let result;
                if (this.material_dialog_model === 'single') {
                    result = data[0].img
                } else {
                    result = []
                    data.forEach(item => {
                        this.uploadDataItem[this.uploadImageName] = item.img
                        result.push({ ...this.uploadDataItem })
                    })
                }
                if (this.uploadParent) {
                    let newValue = this.getNestedProperty(this.templateSetForm, this.uploadParent)
                    if (this.material_dialog_model === 'single') {
                        this.$set(newValue, this.uploadTarget, result)
                    } else {
                        newValue[this.uploadTarget] = newValue[this.uploadTarget].concat(result)
                        this.$set(newValue, this.uploadTarget, newValue[this.uploadTarget])
                    }
                } else {
                    this.templateSetForm[this.uploadTarget] = this.templateSetForm[this.uploadTarget].concat(result)
                    this.$set(this.templateSetForm, this.uploadTarget, this.templateSetForm[this.uploadTarget])
                }

                this.clearFormValidate(`templateSetForm`)
            },
            /** 删除图片操作 **/
            handleClickDeleteFile (data) {
                const { parent, target } = data
                if (parent) {
                    let newValue = this.getNestedProperty(this.templateSetForm, parent)
                    this.$set(newValue, target, '');
                } else {
                    this.$set(this.templateSetForm, target, '')
                }
            },
            /** 深度对象调用 */
            getNestedProperty (obj, propertyPath) {
                if (typeof propertyPath === 'string') {
                    propertyPath = propertyPath.split('.');
                }
                if (propertyPath.length === 1) {
                    return obj[propertyPath[0]];
                } else {
                    let nextObj = obj[propertyPath[0]];
                    let nextPath = propertyPath.slice(1);
                    return this.getNestedProperty(nextObj, nextPath);
                }
            },
            /** 远程搜索下拉数据 */
            handleRemoteSearchChange (data) {
                const { path, method, params, parent, target } = data
                const url = path
                const post_method = method == 'post' ? 'doGet' : 'doPost'
                let newList = null
                parent && (newList = this.getNestedProperty(this.templateSetForm, parent))
                let info = JSON.parse(JSON.stringify(params))
                this[post_method](url, info).then(res => {
                    if (res.code == 200) {
                        const list = res.data
                        if (newList) {
                            this.$set(newList, target, list)
                        } else {
                            this.$set(this.templateSetForm, target, list)
                        }
                    } else {
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.error(res.message)
                    }
                    this.$forceUpdate()
                })
            },
            /** 点击添加组件数据 */
            async handleClickAddData (dom, len, warning_text, data, validate_name) {
                let newValue = this.getNestedProperty(this.templateSetForm, dom)
                /*const is_has_goods = newValue.some(one => one.type == 1)
                const is_has_index = newValue.some(one => one.type == 2)
                if (is_has_goods) data.type = 2
                if (is_has_index) data.type = 3*/
                const item = await JSON.parse(JSON.stringify(data))
                if (newValue.length >= len) {
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.error(`最多可添加${len}个${warning_text}`)
                    return false
                }
                if (validate_name) {
                    const diff = newValue.filter(label => label[validate_name] == item[validate_name])
                    if (diff.length) {
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.error('该选项已存在')
                        return false
                    }
                }
                newValue.push(item)
            },
            /** 点击批量添加图片组件数据 */
            handleClickAddImageData (data) {
                let { type, target, image_name = 'image', max, length, parent, validate = 2, item } = data
                if (max > -1 && length >= max) return false;
                this.uploadType = type
                this.uploadTarget = target
                this.uploadParent = parent
                this.uploadValidateType = validate
                this.uploadDataItem = item
                this.uploadImageName = image_name
                // this.$refs['upload_file'].click()
                this.is_show_material_dialog = true
                this.material_dialog_model = 'multiple'
                this.material_dialog_max_count = max
                this.material_dialog_selected_count = length
            },
            /** 点击删除组件数据 */
            async handleClickDeleteData (data) {
                const { index, parent, confirm, min } = data
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
                if (newValue.length <= min) {
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.error('最少填写一个板块数据')
                    return false
                }
                if (confirm) {
                    this.$confirm(confirm, '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning',
                        center: true
                    }).then(() => {
                        newValue.splice(index, 1)
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.success('删除成功！')
                        this.slideIndex = 0
                    }).catch(() => {});
                } else {
                    newValue.splice(index, 1)
                    this.$message.success('删除成功！')
                }
            },
            /** 下拉选中禁用 */
            handleRemoteSearchChose (data) {
                setTimeout(() => {
                    const { parent, list, index, chose, value = 'value', child_value, ref, selected } = data
                    const choseType = Object.prototype.toString.call(chose).slice(8, -1)
                    const newList = this.getNestedProperty(this.templateSetForm, parent)
                    if (newList && newList.length) {
                        if (choseType == 'Array') {
                            newList.map(item => chose.some(child => item.is_disabled = item[value] == child[child_value]))
                        } else {
                            newList.map(item => item.is_disabled = item[value] == chose)
                        }
                    }
                    //  针对易企秀下拉选择更换广告图片
                    /*if (selected && selected.alias == 'yi_qi_xiu') {
                        const find_data = selected.default_selection_data.find(option => option.value == selected.value)
                        if (find_data) list[index].image = find_data.image
                    }*/
                    ref && this.clearFormValidate(ref)
                    this.$forceUpdate()
                }, 200)
            },
            /** 点击提交设置弹窗数据 */
            handleClickSubmitSetForm (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        let is_valid = this.templateSetForm.items.findIndex(item => {
                            return (item.title_style == 1 && item.title == '') ||
                                (item.title_style == 2 && (item.title == '' || item.icon == '')) ||
                                (item.title_style == 3 && (item.title == '' || item.label == '')) ||
                                (item.type == 3 && item.title_style == 1 && (item.title == '' || item.subtitle == '')) ||
                                (item.type == 3 && item.title_style == 2 && (item.title == '' || item.icon == '' || item.subtitle == '')) ||
                                (item.type == 3 && item.title_style == 3 && (item.title == '' || item.label == '' || item.subtitle == ''))
                        })
                        if (is_valid > -1) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请将频道广场第【${is_valid + 1}】选项表单完善`)
                            return false
                        }
                        is_valid = this.templateSetForm.items.findIndex(item => {
                            return (item.type == 2 && !item.price_index.price_item.length)
                        })
                        if (is_valid > -1) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请选择第【${is_valid + 1}】选项价格指数推荐产品`)
                            return false
                        }


                        const newData = JSON.parse(JSON.stringify(this.temp_item))
                        newData.content = this.templateSetForm
                        if (newData.data) delete newData.data
                        if (isNaN(newData.id)) newData.id = ''

                        this.set_save_loading = true
                        const info = { component_name: this.templateSetFormType, component_data: newData, name: this.templateSetFormName }
                        this.doPost('{!! route('manage.app_web_decoration.get_content_data_by_alias') !!}', info).then(res => {
                            if (res.code == 200) {
                                this.is_show_drawer = false
                                this.is_setting_save = true
                                this.set_save_loading = false
                                this.$set(this.temp_item, 'data', res.data)
                                this.$set(this.temp_item, 'content', this.templateSetForm)
                                this.$emit('save', { content: this.templateSetForm, data: res.data, index: this.temp_index })
                                this.swiperKey = Math.round(new Date() / 1000)
                            } else {
                                this.is_setting_save = true
                                this.set_save_loading = false
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error(res.message)
                            }
                        })
                    } else {
                        return false;
                    }
                });
            },
            /** 点击取消提交设置弹窗数据 */
            handleClickCancelSetForm (formName) {
                this.$refs[formName].clearValidate();
                if (this.$refs.drawerBox) {
                    this.$refs.drawerBox.scrollTop = 0;
                }
                this.is_show_drawer = false
                this.slideIndex = 0
                // this.$emit('cancel')
            },
            /** 点击查看图片 */
            handleClickOpenPreviewImage (list, index, name) {
                this.imagePreviewList = list
                this.preview_index = index || 0
                this.preview_name = name || 'image'
                setTimeout(() => this.is_show_dialog = true, 400)
            },
        },
        mounted () {
        }
    })
</script>
