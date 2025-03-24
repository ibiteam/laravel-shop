<style>
    /* 菜单金刚区 */
    .home-menu .home-menu-content { padding: 10px 20px 20px 20px; background-color: #ffffff; border-radius: 20px; }
    .home-menu .home-menu-content .carousel-swiper,
    .home-menu .home-menu-content .carousel-swiper-menu { /*padding-bottom: 20px;*/ }
    .home-menu .home-menu-content .carousel-swiper { flex-wrap: nowrap; }
    .home-menu .home-menu-content.home-menu-scroll { /*overflow-x: auto; overflow-y: hidden;*/ overflow: hidden; }
    .home-menu .home-menu-content.home-menu-scroll ul { width: 100%; }
    .home-menu .home-menu-content.home-menu-scroll li { flex-shrink: 0; }
    .home-menu .home-menu-content .home-menu-child { margin-top: 10px; position: relative; }
    .home-menu .home-menu-content .home-menu-child img { width: 90px; height: 90px; }
    .home-menu .home-menu-content .home-menu-child p { line-height: 44px; text-align: center; }
    .home-menu .home-menu-content .home-menu-gif { width: 60px !important; height: 40px !important; position: absolute; right: -10px; top: -20px; }
    .home-menu .home-menu-content .carousel-indicators { bottom: 10px; }
    .home-menu .home-menu-content .carousel-indicators li { width: 8px; height: 8px; border-radius: 10px; background: #E2E2E2; }
    .home-menu .home-menu-content .carousel-indicators li.active { width: 26px; background: linear-gradient(to bottom, #FA5F5F, #F71111); }
    .home-menu .home-menu-content .home-menu-dos { width: 60px; height: 8px; background: #F4F4F4; border-radius: 6px; position: absolute; bottom: 10px; left: 50%; transform: translate(-50%); }
    .home-menu .home-menu-content .home-menu-dos .home-menu-nprogress { width: 30px; height: 100%; background: linear-gradient(to bottom, #FA5F5F, #F71111); border-radius: 6px; }
    .home-menu .home-menu-content .home-menu-dos.home-menu-carousel { width: auto; height: auto; background: transparent; border-radius: 0; }
    .home-menu .home-menu-content .home-menu-dos.home-menu-carousel .home-menu-item { width: 8px; height: 8px; margin-left: 8px; border-radius: 10px; background: #E2E2E2; }
    .home-menu .home-menu-content .home-menu-dos.home-menu-carousel .home-menu-item.active { width: 16px; background: linear-gradient(to bottom, #FA5F5F, #F71111); }
</style>

<script type="text/x-template" id="home-menu-template">
    <div class="temp-item home-menu" style="min-height: 130px;" :class="{ 's-flex ai-ct jc-ct': temp_item.activity_info && !temp_item.activity_info.length, active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <public-mark-template :title="temp_item.name" :index="temp_index" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>
        <template>
            <template>
                <div class="temp-item-nodata s-flex flex-dir ai-ct jc-ct" v-if="!temp_item.data || computedDataType(temp_item.data) == 'Array' || !temp_item.data.items.length">
                    <p>@{{ temp_item.name }}</p>
                    <p>点击“设置”，配置组件内需要展示的内容吧</p>
                </div>
                <template v-else>
                    {{-- :style="{ height: temp_item.data.style == 1 ? ((computedItemBasicHeight(temp_item.data)) * computedChildLength(temp_item.data)) + 'px' : 'auto' }"--}}
                    <div style="overflow: hidden;" v-if="temp_item.data.style == 1 && temp_item.data.height != 0">
                        {{-- :style="{ height: ((computedItemBasicHeight(temp_item.data)) * computedChildLength(temp_item.data)) - 25 + 'px' }"--}}
                        <div class="home-menu-content home-menu-scroll s-flex ai-ct">
                            <ul class="s-flex ai-ct flex-wrap flex-shrink" v-for="(item, index) in temp_item.data.list">
                                <li v-for="(child, childIndex) in item" :style="{ width: 100 / temp_item.data.number + '%' }">
                                    <div class="home-menu-dir s-flex flex-dir ai-ct jc-ct flex-1">
                                        <div class="home-menu-child">
                                            <img class="home-menu-gif" :src="child.gif" v-if="child.gif" alt="">
                                            <img :src="child.image" alt="">
                                            <p>@{{ child.title }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="home-menu-dos">
                                <div class="home-menu-nprogress"></div>
                            </div>
                        </div>
                    </div>
                    <div class="home-menu-content home-menu-scroll s-flex ai-ct" v-if="temp_item.data.style == 2 && temp_item.data.height != 0">
                        {{--轮播代码暂时注释，防止后期产品需要设置金刚区可以轮播或滑动--}}
                        {{--<carousel-swiper
                                :width="710"
                                height="auto"
                                :list="temp_item.data.list"
                                :is_autoplay="false"
                                :key="swiperKey">
                            <template slot="default" slot-scope="scope">
                                --}}{{--<div class="home-menu-dir s-flex flex-dir ai-ct jc-ct flex-1" v-for="(child, childIndex) in scope.row">
                                    <div class="home-menu-child">
                                        <img :src="child.image" alt="">
                                        <p>@{{ child.title }}</p>
                                    </div>
                                </div>--}}{{--
                                <div class="home-menu-dir s-flex flex-dir ai-ct jc-ct" v-for="(item, childIndex) in scope.row" :style="{ width: 100 / temp_item.data.number + '%' }">
                                    <div class="home-menu-child">
                                        <img class="home-menu-gif" :src="item.gif" v-if="item.gif" alt="">
                                        <img :src="item.image" alt="">
                                        <p>@{{ item.title }}</p>
                                    </div>
                                </div>
                            </template>
                        </carousel-swiper>--}}
                        <ul class="s-flex ai-ct flex-wrap flex-shrink" v-for="(item, index) in temp_item.data.list">
                            <li v-for="(child, childIndex) in item" :style="{ width: 100 / temp_item.data.number + '%' }">
                                <div class="home-menu-dir s-flex flex-dir ai-ct jc-ct flex-1">
                                    <div class="home-menu-child">
                                        <img class="home-menu-gif" :src="child.gif" v-if="child.gif" alt="">
                                        <img :src="child.image" alt="">
                                        <p>@{{ child.title }}</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="home-menu-dos home-menu-carousel s-flex ai-ct" v-if="temp_item.data.list.length > 1">
                            <div class="home-menu-item" :class="{ active: index == 0 }" v-for="(item, index) in temp_item.data.list"></div>
                        </div>
                    </div>
                    <div class="home-menu-content s-flex ai-ct" v-if="temp_item.data.height == 0">
                        <ul class="s-flex ai-ct flex-wrap" style="width: 710px;">
                            <li class="s-flex flex-dir ai-ct jc-ct" v-for="(item, index) in temp_item.data.list" :style="{ width: 100 / temp_item.data.number + '%' }">
                                <div class="home-menu-child">
                                    <img class="home-menu-gif" :src="item.gif" v-if="item.gif" alt="">
                                    <img :src="item.image" alt="">
                                    <p>@{{ item.title }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </template>
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
                <div style="height: calc(100vh - 160px); overflow-y: auto;" ref="drawerBox">
                    <!--设置板块：Banner轮播图-->
                    <template v-if="templateSetForm.items">
                        <div class="drawer-item">
                            <div class="drawer-item-dd" style="padding-top: 30px;">
                                <el-form-item label="轮播样式" label-width="100px" prop="style">
                                    <el-radio-group v-model="templateSetForm.style">
                                        <el-radio label="1">横向滚动</el-radio>
                                        <el-radio label="2">整屏翻页</el-radio>
                                    </el-radio-group>
                                    <p class="warning-text s-flex flex-dir">
                                        <span>控制金刚区超出一屏数据时的数据查看方式</span>
                                        <span>横向滚动：≤1屏数据，从左到右从上到下显示；＞1屏数据，从上到下从左到右显示</span>
                                        <span>整屏翻页：每屏数据按照从左到右从上到下固定显示</span>
                                    </p>
                                </el-form-item>
                                <el-form-item label="板块高度" label-width="100px" prop="height">
                                    <el-select v-model="templateSetForm.height" placeholder="请选择">
                                        <el-option key="1" label="1行" value="1"></el-option>
                                        <el-option key="2" label="2行" value="2"></el-option>
                                        <el-option key="3" label="3行" value="3"></el-option>
                                        <el-option key="0" label="不限制" value="0"></el-option>
                                    </el-select>
                                    <p class="warning-text s-flex flex-dir">
                                        <span>菜单个数=板块高度*每行个数</span>
                                        <span>1行/2行/3行代表板块的总高度（单个菜单的高度是固定），超出的菜单需要左右滑动查看</span>
                                        <span>不限制，按照每行个数向下展示数据，不做隐藏或滑动处理</span>
                                    </p>
                                </el-form-item>
                                <el-form-item label="每行个数" label-width="100px" prop="number">
                                    <el-select v-model="templateSetForm.number" placeholder="请选择">
                                        <el-option key="1" label="3个" value="1"></el-option>
                                        <el-option key="2" label="4个" value="2"></el-option>
                                        <el-option key="3" label="5个" value="3"></el-option>
                                    </el-select>
                                    <p class="warning-text">控制每行显示的菜单个数，超出此个数折行或翻页处理</p>
                                </el-form-item>
                                <dl>
                                    <dt>
                                        <ul class="s-flex ai-ct" style="padding: 0 20px;">
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                                <span>*</span>
                                                <label>图片</label>
                                                <el-tooltip
                                                        style="margin: 0 20px 0 0;"
                                                        effect="light"
                                                        placement="top"
                                                        width="200"
                                                        trigger="hover">
                                                    <div slot="content" class="drawer-upload-warning">
                                                        <p>支持jpg、jpeg、png、gif格式</p>
                                                        <p>建议尺寸：90px*90px</p>
                                                        <p>建议大小：2M内</p>
                                                    </div>
                                                    <em class="iconfont cursorp">&#xe72d;</em>
                                                </el-tooltip>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                                {{--<span>*</span>--}}
                                                <label>动图</label>
                                                <el-tooltip
                                                        style="margin: 0 20px 0 0;"
                                                        effect="light"
                                                        placement="top"
                                                        width="200"
                                                        trigger="hover">
                                                    <div slot="content" class="drawer-upload-warning">
                                                        <p>支持jpg、jpeg、png、gif格式</p>
                                                        <p>建议尺寸：60px*40px</p>
                                                        <p>建议大小：2M内</p>
                                                    </div>
                                                    <em class="iconfont cursorp">&#xe72d;</em>
                                                </el-tooltip>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                                <span>*</span>
                                                <label>名称</label>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">URL链接</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 75px;">排序</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 84px; text-align: center;">显示在APP端</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 75px; text-align: center;">显示在H5端</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 84px; text-align: center;">显示在小程序</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 50px; margin: 0;">操作</li>
                                        </ul>
                                    </dt>
                                    <dd class="s-flex flex-dir ai-ct">
                                        <ul class="s-flex ai-ct" style="height: 90px; padding: 0 20px;" v-for="(item, index) in templateSetForm.items">
                                            <li style="width: 90px;">
                                                <el-form-item :key="index" :prop="'items.' + index + '.image'" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                                    <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                        <img v-if="item.image" :src="item.image" alt="">
                                                        <div  class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!item.image" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'image'})">
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <p>未上传</p>
                                                        </div>
                                                        <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'image'})">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile({ parent: `items.${index}`, target: 'image'})">
                                                                <em class="iconfont">&#xe738;</em>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 90px;">
                                                <el-form-item :key="index" :prop="'items.' + index + '.gif'">
                                                    <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                        <img v-if="item.gif" :src="item.gif" alt="">
                                                        <div  class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!item.gif" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'gif'})">
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <p>未上传</p>
                                                        </div>
                                                        <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `items.${index}`, validate: 2, target: 'gif'})">
                                                                <em class="iconfont">&#xe727;</em>
                                                            </div>
                                                            <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile({ parent: `items.${index}`, target: 'gif'})">
                                                                <em class="iconfont">&#xe738;</em>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 90px;">
                                                <el-form-item :prop="'items.' + index + '.title'" :rules="
                                                    [
                                                        { required: true, message: '请输入名称', trigger: 'blur' },
                                                        { max: 5, message: '名称不能超过5个字符', trigger: 'blur' },
                                                        { validator: (rule, value, callback) => {
                                                            //  校验是否有相同的名称
                                                            const same_name = templateSetForm.items.filter(data => value == data.title)
                                                            if (same_name.length > 1) { callback(new Error('该名称已存在，请修改！')); } else { callback(); }
                                                        }, trigger: 'blur' },
                                                    ]
                                                ">
                                                    <el-input v-model="item.title" placeholder="请输入名称" ></el-input>
                                                </el-form-item>
                                            </li>
                                            <li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">
                                                <el-form-item>
                                                    <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="item.alias" @change="item.value = '', item.url.default_selection_data = [], item.alias == 'yi_qi_xiu' && handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: '', alias: item.alias }, parent: `items.${index}.url`, target: 'default_selection_data' }), clearFormValidate(`items_url_value${index}`), $forceUpdate()" @clear="item.value = ''" placeholder="请选择" style="width: 120px;">
                                                        <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                                    </el-select>
                                                </el-form-item>
                                                <el-form-item :ref="`items_url_value${index}`" :prop="'items.' + index + '.value'" :rules="{ required: item.alias != '' && computedOptionsPlaceholder(item.alias), message: computedOptionsPlaceholder(item.alias), trigger: 'blur' }">
                                                    <el-select v-model="item.value"
                                                       v-if="computedOptionsPlaceholder(item.alias) && computedOptionsCanRemote(item.alias)"
                                                       filterable
                                                       remote
                                                       reserve-keyword
                                                       clearable
                                                       :remote-method="(query) => {
                                                            handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: item.alias }, parent: `items.${index}.url`, target: 'default_selection_data' }),
                                                            handleRemoteSearchChose({ parent: `items.${index}.url.default_selection_data`, chose: item.value })
                                                       }"
                                                       @change="(value) => handleRemoteSearchChose({ parent: `items.${index}.url.default_selection_data`, list: templateSetForm.items, index, chose: item.value, ref: `items_url_value${index}`, selected: item.url })"
                                                       @focus="handleRemoteSearchChose({ parent: `items.${index}.url.default_selection_data`, chose: item.value })"
                                                       :placeholder="computedOptionsPlaceholder(item.alias)" style="width: 200px">
                                                        <el-option v-for="option in item.url.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                    </el-select>
                                                    <el-input class="radius-left-none__input" v-else v-model="item.value" :disabled="(!computedOptionsPlaceholder(item.alias) && item.alias != '' && item.alias != null) || (!item.alias && !item.value)" :placeholder="computedOptionsPlaceholder(item.alias) ? computedOptionsPlaceholder(item.alias) : ! computedOptionsPlaceholder(item.alias) && item.alias != '' && item.alias != null ? '系统生成链接' : ''" style="width: 200px;"></el-input>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 75px;">
                                                <el-form-item :prop="'items.' + index + '.sort'" :rules="
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
                                                    <el-input v-model="item.sort" placeholder="1~100" ></el-input>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 84px; text-align: center;">
                                                <el-switch v-model="item.is_app" :active-value="1" :inactive-value="0" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                            </li>
                                            <li style="width: 75px; text-align: center;">
                                                <el-switch v-model="item.is_h5" :active-value="1" :inactive-value="0" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                            </li>
                                            <li style="width: 84px; text-align: center;">
                                                <el-switch v-model="item.is_mini" :active-value="1" :inactive-value="0" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                            </li>
                                            <li style="width: 50px; margin: 0;">
                                                <div class="drawer-btn cursorp s-flex ai-ct jc-ct small" style="padding: 0;" @click="handleClickDeleteData(index, `items`)">删除</div>
                                            </li>
                                        </ul>
                                        <p class="warning-nodata" v-if="!templateSetForm.items.length">暂无数据！</p>
                                        <div class="drawer-btn primary s-flex ai-ct jc-ct" :class="{ disabled: templateSetForm.items.length >= 30 }" @click="handleClickAddImageData({ target: 'items', max: 30, length: templateSetForm.items.length, item: { image: '', gif: '', title: '', alias: 'https', value: '', url: { alias: 'https', value: '' }, sort: '1', is_app: 1, is_h5: 1, is_mini: 1, } })">添加（@{{templateSetForm.items.length}}/30）</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </template>
                </div>
                <el-form-item style="margin: 0; box-shadow: 0 -0.2rem 0.2rem -0.22rem rgba(0, 0, 0, 0.2);">
                    <div class="s-flex ai-ct jc-ct" style="margin: 20px 0;">
                        <div style="width: 120px; margin: 0 15px;" class="drawer-btn primary s-flex ai-ct jc-ct" @click="handleClickSubmitSetForm('templateSetForm')">保存</div>
                        <div style="width: 120px; margin: 0 15px;" class="drawer-btn s-flex ai-ct jc-ct" @click.stop="handleClickCancelSetForm('templateSetForm')">取消</div>
                    </div>
                </el-form-item>
            </el-form>
            {{--<el-dialog
                title="提示"
                :visible.sync="is_show_imageview"
                width="900px">

        </el-dialog>--}}
        </el-drawer>
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

<!--模块遮罩组件-->
@include('manage.app_website_decoration.components.template-mark-setting')
<!--广告轮播组件-->
@include('manage.app_website_decoration.components.carousel-swiper')
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
    Vue.component('home-menu-template', {
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
            /** 根据数据中有无GIF值返回菜单基数高度值 */
            computedItemBasicHeight () {
                return function (data) {
                    const diff = data.list.filter(item => item.gif != '')
                    return diff.length ? 156 : 146
                }
            },
            /** 返回子级数据长度 */
            computedChildLength () {
                return function (data) {
                    let len = 0
                    if (data.height == 1) {
                        len = 1
                    } else {
                        len = data.list[0].length <= data.number ? data.list.length : data.list[0].length >= ((data.number * data.height) / 2) ? data.height : (data.number * data.height) / data.list[0].length
                    }
                    return len
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
        template: '#home-menu-template',
        methods: {
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting (data) {
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
                this.templateSetForm.items.map(oneitem => {
                    if (oneitem.url) {
                        oneitem.alias = oneitem.url.alias
                        oneitem.value = oneitem.url.value
                    }
                })
                this.templateSetFormType = item.component_name
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
            /** 初始化对象字段值 */
            clearFormObjectFields (item) {
                return new Promise(resolve => {
                    Object.keys(item).forEach(key => {
                        if (isNaN(item[key]) && key != 'type') {
                            //  检验数据类型
                            const getType = (value) => {
                                return Object.prototype.toString.call(value).slice(8, -1)
                            }
                            switch (getType(item[key])) {
                                case 'String':
                                    item[key] = ''
                                    break
                                case 'Array':
                                    item[key] = []
                                    break
                                case 'Object':
                                    item[key] = {}
                                    break
                            }
                        }
                    })
                    resolve(item)
                })
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
                        this.$forceUpdate()
                    } else {
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.error(res.message)
                    }
                })
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
                // (newList && newList.length) && (newList.map(item => item.is_disabled = item[value] == chose))
            },
            /** 点击添加组件数据 */
            async handleClickAddData (dom, len, warning_text, data) {
                const item = await JSON.parse(JSON.stringify(data))
                let newValue = this.getNestedProperty(this.templateSetForm, dom)
                /*const is_null = newValue.filter(item => item.image == '' || item.gif == '' || item.title == '')
                if (is_null.length) {
                    this.msg_error && this.msg_error.close()
                this.msg_error = this.$message.error('请先完善数据再进行添加')
                    return false
                }*/
                if (newValue.length >= len) {
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.error(`最多可添加${len}个${warning_text}`)
                    return false
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
            async handleClickDeleteData (index, parent) {
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
                newValue.splice(index, 1)
                this.msg_error && this.msg_error.close()
                this.msg_error = this.$message.success('删除成功！')
            },
            /** 点击提交设置弹窗数据 */
            handleClickSubmitSetForm (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        if (!this.templateSetForm.items.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error('至少添加1个广告图')
                            return false
                        }

                        this.templateSetForm.items.map(oneitem => {
                            if (oneitem.url) {
                                oneitem.url.alias = oneitem.alias
                                oneitem.url.value = oneitem.value
                            }
                        })

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
                                // 总数据长度
                                let total = this.temp_item.data.items.length
                                // 一屏数据个数
                                let slide = this.temp_item.data.number * this.temp_item.data.height
                                if (res.data.height != 0) {
                                    //  根据总数据按照一屏数据个数对数据进行格式化
                                    if (!this.temp_item.data.list) this.temp_item.data.list = []
                                    for (let i = 0; i < total; i += slide) {
                                        this.temp_item.data.list.push(this.temp_item.data.items.slice(i, i + slide));
                                    }
                                } else {
                                    this.temp_item.data.list = res.data.items
                                }
                                this.$set(this.temp_item, 'content', this.templateSetForm)
                                this.$emit('save', { content: this.templateSetForm, data: this.temp_item.data, index: this.temp_index })
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
        mounted () {}
    })
</script>
