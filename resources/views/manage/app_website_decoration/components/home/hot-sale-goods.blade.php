<style>
    /* 推荐商家 */
    .hot-sale-goods {  }
    .hot-sale-goods .public_text img { width: 32px; height: 32px; line-height: 1; margin-right: 16px; }
    .hot-sale-goods .goods-cate-tabs { /*padding-left: 30px;*/ margin: 10px 0; overflow: hidden; }
    .hot-sale-goods .goods-cate-tabs li { width: 150px; height: 50px; padding: 0 20px; margin-right: 30px; font-size: 24px; background-color: #FFFFFF; border-radius: 30px; flex-shrink: 0; }
    .hot-sale-goods .goods-cate-tabs li.active { background-color: #FFF1F1; color: #F71111; }
    .hot-sale-goods .recommend-seller-list { padding: 0 0 0 20px; margin: 20px 0 30px 0; overflow-x: auto; overflow-y: hidden; }
    .hot-sale-goods .recommend-seller-list li { width: 200px; height: 260px; margin: 0 10px; padding-bottom: 12px; border-radius: 10px; background-color: #FAFAFA; flex-shrink: 0; }
    .hot-sale-goods .recommend-seller-list li:last-child { margin: 0 20px 0 10px; }
    .hot-sale-goods .recommend-seller-list .goods-image { width: 100%; height: 130px; border-radius: 10px 10px 0 0; overflow: hidden; }
    .hot-sale-goods .recommend-seller-list .shop-logo { width: 92px; height: 92px; margin: -46px auto 0 auto; border-radius: 100%; border: 1px solid #E5E5E5; overflow: hidden; position: relative; }
    /*.hot-sale-goods .recommend-seller-list .shop-logo img { border: 1px solid #E5E5E5; }*/
    .hot-sale-goods .recommend-seller-list h1 { padding: 0 10px; margin: 6px 0 12px 0; text-align: center; font-size: 28px; }
    .hot-sale-goods .recommend-seller-list p { line-height: 30px; padding: 0 10px; text-align: center; font-size: 18px; color: #999999; }
    .hot-sale-goods .recommend-seller-list p.goods-tag { max-width: 160px; padding: 0 6px; margin: 0 auto; border: 1px solid #f71111; border-radius: 4px; color: #f71111; }

    .hot-sale-goods .sale-goods-list { /*padding: 0 15px;*/ }
    .hot-sale-goods .sale-goods-list .is-no-data { width: 610px; height: 588px; margin: 0 auto; }
    .hot-sale-goods .sale-goods-list .is-no-data img {
        width: 610px;
        height: 273px;
    }

    .hot-sale-goods .sale-goods-list .is-no-data p {
        text-align: center;
        color: #999999;
        font-size: 28px;
        margin-top: 42px;
    }
    .hot-sale-goods .sale-goods-list ul li { width: 343px; margin: 10px 5px 0 5px; background-color: #ffffff; border-radius: 20px; overflow: hidden; position: relative; }
    .hot-sale-goods .sale-goods-list ul li .recommend-ad-name { width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); position: absolute; left: 0; top: 0; z-index: 1; }
    .hot-sale-goods .sale-goods-list ul li .recommend-ad-name p { font-size: 28px; color: #ffffff; }
    .hot-sale-goods .sale-goods-list ul li .carousel-swiper { border-radius: 20px; }
    .hot-sale-goods .sale-goods-list ul li .carousel-swiper .carousel-item,
    .hot-sale-goods .sale-goods-list ul li .carousel-swiper .carousel-item img { width: 100%; height: 100%; }
    .hot-sale-goods .sale-goods-list ul li .carousel-indicators { bottom: 0; }
    .hot-sale-goods .sale-goods-list ul li .carousel-indicators li { width: 10px; height: 10px; border-radius: 10px; background-color: #E2E2E2; }
    .hot-sale-goods .sale-goods-list ul li .carousel-indicators li.active { width: 26px; background-color: #ffffff; }

    .hot-sale-goods .sale-goods-list ul li .goods-info { padding: 0 20px 24px 20px; }
    .hot-sale-goods .sale-goods-list ul li .goods-image { width: 100%; height: 350px; }
    .hot-sale-goods .sale-goods-list ul li .goods-name { margin-top: 15px; line-height: 1.4; font-size: 26px; }
    .hot-sale-goods .sale-goods-list ul li .goods-name .goods-tag { padding: 2px 12px; text-align: center; background: linear-gradient(to right, #F71111, #FA5F5F); border-radius: 6px; font-size: 20px; color: #ffffff; }
    .hot-sale-goods .sale-goods-list ul li .goods-name .goods-tag.is-zhiying { background: linear-gradient(to right, #5436D5, #735CFF); }
    .hot-sale-goods .sale-goods-list ul li .goods-type { margin-top: 15px; font-size: 22px; color: #f71111; }
    .hot-sale-goods .sale-goods-list ul li .goods-activity { margin-top: 10px; }
    .hot-sale-goods .sale-goods-list ul li .goods-activity .activity-tag { width: 80px; height: 30px; line-height: 30px; text-align: center; margin-right: 20px; background: #ffffff; border: 1px solid #f71111; border-radius: 4px; font-size: 20px; color: #f71111; }
    .hot-sale-goods .sale-goods-list ul li .goods-activity .activity-tag.is-group { background: linear-gradient(to right, #FF7B1A, #FFA841); border: none; color: #ffffff; }
    .hot-sale-goods .sale-goods-list ul li .goods-activity .activity-tag.activity-seckill { width: 100%; padding: 2px 10px; margin: 0; background-color: #FDEFEF; border-color: #FDEFEF; border-radius: 4px; }
    .hot-sale-goods .sale-goods-list ul li .goods-activity .activity-tag.activity-seckill em { margin-right: 10px; font-size: 20px; }
    .hot-sale-goods .sale-goods-list ul li .goods-activity p { font-size: 20px; color: #f71111; }
    .hot-sale-goods .sale-goods-list ul li .goods-shop { margin-top: 15px; font-size: 22px; color: #FFAA00; }
    .hot-sale-goods .sale-goods-list ul li .goods-shop h1 { margin-right: 30px; }
    .hot-sale-goods .sale-goods-list ul li .goods-shop em { font-size: 12px; color: #333333; }
    .hot-sale-goods .sale-goods-list ul li .goods-price { margin-top: 10px; }
    .hot-sale-goods .sale-goods-list ul li .goods-price p { padding: 5px 10px; background-color: rgba(255,195,0,0.12); border-radius: 4px; font-size: 20px; color: #FF8F1F; }
    .hot-sale-goods .sale-goods-list ul li .goods-price p.is-now { background-color: rgba(247, 17, 17, 0.04); color: #f71111; }
    .hot-sale-goods .sale-goods-list ul li .goods-hot { padding: 5px 10px; margin-top: 16px; background-color: rgba(255,195,0,0.12); border-radius: 4px; font-size: 20px; color: #FF8F1F; }
</style>

<script type="text/x-template" id="hot-sale-goods">
    <div class="temp-item"  :class="{ 's-flex ai-ct jc-ct': temp_item.activity_info && !temp_item.activity_info.length, active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <public-mark-template :title="temp_item.name" box_height="50px" :index="temp_index" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>
        <template>
            <template>
                <div class="temp-item-nodata s-flex flex-dir ai-ct jc-ct" v-if="!temp_item.data || !temp_item.data.hot_sale_items || computedDataType(temp_item.data.hot_sale_items) == 'Array'">
                    <p>@{{ temp_item.name }}</p>
                    <p>点击“设置”，配置组件内需要展示的内容吧</p>
                </div>
                <div class="hot-sale-goods" v-else>
                    <template>
                        <div class="public-title" style="padding: 0; background-color: transparent;" v-if="temp_item.data.hot_sale_items.title_is_show == 1">
                            <div class="public_text s-flex ai-ct">
                                <img v-if="temp_item.data.hot_sale_items.icon" :src="temp_item.data.hot_sale_items.icon" />
                                <p>@{{ temp_item.data.hot_sale_items.title }}</p>
                                {{--<p>热销商品</p>--}}
                            </div>
                            <div class="look_more s-flex ai-ct" v-if="temp_item.data.hot_sale_items.url">
                                <div class="look_more_view">更多 <em class="iconfont">&#xe772;</em></div>
                            </div>
                        </div>
                        <ul class="goods-cate-tabs s-flex ai-ct" v-if="temp_item.data.hot_sale_items.category_list.length && temp_item.data.hot_sale_items.category_list.length >= 2">
                            <li class="cursorp s-flex ai-ct jc-ct" :class="{ active: tabIndex == index }" @click="handleClickCheckData({ item, index, target: 'tabIndex', path: '{!! route('manage.app_web_decoration.get_hot_sale_goods_by_cat_id') !!}', params: { content: temp_item.content, cat_id: item.cat_id } })" v-for="(item, index) in temp_item.data.hot_sale_items.category_list">
                                <p class="ellipsis-1">@{{ item.cat_name }}</p>
                            </li>
                        </ul>
                    </template>
                    <div class="sale-goods-list s-flex">
                        <template v-if="temp_item.data.hot_sale_items.goods.length">
                            <ul class="s-flex flex-wrap jc-bt">
                                <li v-for="item in temp_item.data.hot_sale_items.goods">
                                    <div class="goods-image img-set-full">
                                        <img :src="item.goods_thumb ? item.goods_thumb : 'https://cdn.toodudu.com/uploads/2021/02/20/app_nopic.png'" alt="">
                                    </div>
                                    <div class="goods-info">
                                        <div class="goods-name ellipsis-2">
                                            <span class="goods-tag" :class="{ 'is-zhiying': item.is_ziying == 0 }" v-if="item.sign">@{{ item.sign }}</span> @{{ item.goods_name }}
                                        </div>
                                        <div class="goods-type ellipsis-1" v-if="item.sign_type == 4">@{{ item.goods_subtitle }}</div>
                                        <div class="goods-price s-flex ai-ct jc-bt">
                                            <form-price class="ellipsis-1 flex-1" :price="item.shop_price" weight="bolder" int_size="34" df_size="24" sign_size="22" :unit="item.unit" unit_size="24" color="#F71111"></form-price>
                                            <p v-if="item.goods_type" :class="{ 'is-now': item.goods_type == '现货' }">@{{ item.goods_type }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </template>
                        <template v-else>
                            <div class="is-no-data">
                                <img src="https://cdn.toodudu.com/uploads/2020/07/09/nodata.png" style="width: 400px;" alt="">
                                <p style="margin-top:30px;" class="fs12 co_666">暂无数据</p>
                            </div>
                        </template>
                    </div>
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
                <div style="height: calc(100vh - 160px); overflow-y: auto;" ref="drawerBox">
                    <div class="drawer-item">
                        <div class="drawer-item-dt">
                            <h1>基础设置</h1>
                        </div>
                        <div class="drawer-item-dd">
                            <el-form-item label="标题" label-width="110px" :prop="'title_is_show'" :rules="{ required: true, message: '请选择展现样式', trigger: 'change' }">
                                <el-radio-group v-model="templateSetForm.title_is_show">
                                    <el-radio :label="1">显示</el-radio>
                                    <el-radio :label="0">不显示</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <template v-if="templateSetForm.title_is_show == 1">
                                <el-form-item label="ICON" label-width="110px" :prop="'icon'" :rules="{ required: false, message: '请上传图片', trigger: 'blur' }">
                                    <div class="s-flex">
                                        <div class="drawer-upload s-flex ai-ct jc-ct cursorp">
                                            <img v-if="templateSetForm.icon" :src="templateSetForm.icon" alt="">
                                            <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!templateSetForm.icon" @click="handleClickUploadFile({ target: 'icon', validate: 1})">
                                                <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                    <em class="iconfont">&#xe727;</em>
                                                </div>
                                                <p>未上传</p>
                                            </div>
                                            <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-if="templateSetForm.icon" @click="handleClickUploadFile({ target: 'icon', validate: 1})">
                                                <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0;">
                                                    <em class="iconfont">&#xe727;</em>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="drawer-upload-warning">
                                            <p>未上传则不显示此图标</p>
                                            <p>支持jpg、jpeg、png格式</p>
                                            <p>建议尺寸：100px*100px</p>
                                            <p>建议大小：2M内</p>
                                            <div class="s-flex ai-ct">
                                                <el-tooltip
                                                        style="margin: 0 20px 0 0;"
                                                        effect="light"
                                                        placement="right"
                                                        width="100"
                                                        popper-class="drawer-preview-image"
                                                        trigger="hover">
                                                    <div slot="content" class="drawer-upload-warning">
                                                        <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2024/01/10/hot_sale_goods_view.png', is_show_imageview = true">
                                                            <em class="iconfont cursorp">&#xe7c3;</em>
                                                        </div>
                                                        <el-image style="width: 100px; height: 100px" src="https://cdn.toodudu.com/uploads/2024/01/10/hot_sale_goods_view.png"></el-image>
                                                    </div>
                                                    <p class="iconfont cursorp" style="margin-right: 20px; color: #278ff0;">示例效果</p>
                                                </el-tooltip>
                                            </div>
                                        </div>
                                    </div>
                                </el-form-item>
                                <el-form-item label="标题" label-width="110px" :prop="'title'" :rules="
                                    [
                                        { required: true, message: '请输入标题', trigger: 'blur' },
                                        { max: 6, message: '标题不能超过6个字符', trigger: 'blur' },
                                        { validator: (rule, value, callback) => {
                                            //  校验是否有相同的名称
                                            const same_name = temp_list.filter(data => data.content.title && data.content.title == templateSetForm.title && data.id != temp_item.id)
                                            if (same_name.length) { callback(new Error('该标题已存在，请修改！')); } else { callback(); }
                                        }, trigger: 'blur' },
                                    ]
                                ">
                                    <el-input v-model="templateSetForm.title" style="width: 500px;"></el-input>
                                    <p class="warning-text">用于改变页面中展示的板块名称</p>
                                </el-form-item>
                                <el-form-item label="URL链接" label-width="110px" v-if="templateSetForm.url">
                                    <div class="s-flex ai-ct">
                                        <el-form-item style="margin-bottom: 22px;">
                                            <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="templateSetForm.url.alias" @change="templateSetForm.url.value = '', templateSetForm.default_selection_data = [], clearFormValidate(`form_url_value`)" @clear="templateSetForm.url.value = ''" placeholder="请选择" clearable style="width: 150px;">
                                                <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                            </el-select>
                                        </el-form-item>
                                        <el-form-item :ref="`form_url_value`" style="margin-bottom: 22px;" :prop="'url.value'" :rules="{ required: templateSetForm.url.alias != '' && computedOptionsPlaceholder(templateSetForm.url.alias), message: computedOptionsPlaceholder(templateSetForm.url.alias), trigger: 'blur' }">
                                            <el-select v-model="templateSetForm.url.value"
                                               v-if="computedOptionsPlaceholder(templateSetForm.url.alias) && computedOptionsCanRemote(templateSetForm.url.alias)"
                                               filterable
                                               remote
                                               reserve-keyword
                                               clearable
                                               :remote-method="(query) => {
                                                    handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: templateSetForm.url.alias }, target: 'default_selection_data' }),
                                                    handleRemoteSearchChose({ parent: `default_selection_data`, chose: templateSetForm.url.value })
                                               }"
                                               @change="(value) => handleRemoteSearchChose({ parent: `default_selection_data`, chose: templateSetForm.url.value, ref: 'form_url_value' })"
                                               @focus="handleRemoteSearchChose({ parent: `default_selection_data`, chose: templateSetForm.url.value })"
                                               :placeholder="computedOptionsPlaceholder(templateSetForm.url.alias)" style="width: 350px">
                                                <el-option v-for="option in templateSetForm.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                            </el-select>
                                            <el-input class="radius-left-none__input" v-else v-model="templateSetForm.url.value" :disabled="(!computedOptionsPlaceholder(templateSetForm.url.alias) && templateSetForm.url.alias != '' && templateSetForm.url.alias != null) || (!templateSetForm.url.alias && !templateSetForm.url.value)" :placeholder="computedOptionsPlaceholder(templateSetForm.url.alias) ? computedOptionsPlaceholder(templateSetForm.url.alias) : !computedOptionsPlaceholder(templateSetForm.url.alias) && templateSetForm.url.alias != '' && templateSetForm.url.alias != null ? '系统生成链接' : ''" style="width: 350px;"></el-input>
                                        </el-form-item>
                                    </div>
                                    <p class="warning-text">控制是否在标题右侧显示更多，配置链接后将跳转至指定页面。</p>
                                </el-form-item>
                            </template>
                        </div>
                    </div>
                    <div class="drawer-item" v-if="templateSetForm.items">
                        <div class="drawer-item-dt">
                            <h1>推荐商品</h1>
                            <div class="s-flex ai-ct jc-bt">
                                <div>
                                    <p>推荐分类1~10个，推荐店铺2~10个</p>
                                    <p>页面按照分类显示商品，若设置的分类＜2，则页面不显示分类栏，只显示商品。</p>
                                </div>
                                <div class="drawer-btn s-flex ai-ct jc-ct cursorp" :class="{ disabled: templateSetForm.items.length >= 10 }" @click="handleClickAddData('items', 10, '分类', publicListItem.items)">
                                    <em class="iconfont">&#xe620;</em>
                                    <label>添加（@{{ templateSetForm.items.length }}/10）</label>
                                </div>
                            </div>
                        </div>
                        <div class="drawer-item-dd">
                            <drawer-slides :width="1085" :slide_active.sync="slideIndex" target="alias" default_text="推荐分类" :list="templateSetForm.items"></drawer-slides>
                            <div class="drawer-item-cate" v-for="(item, index) in templateSetForm.items" :key="slideIndex" v-if="slideIndex == index">
                                <div class="cate-title s-flex ai-ct jc-bt" style="height: 40px;">
                                    <h1>推荐商品</h1>
                                    <div class="drawer-btn s-flex ai-ct jc-ct cursorp" v-if="templateSetForm.items.length > 1" @click="handleClickDeleteData({ index: slideIndex, parent: 'items', confirm: '确定要删除分类吗？', min: 1, min_text: '最少填写一个推荐分类数据' })">
                                        <label>删除</label>
                                    </div>
                                </div>
                                <el-form-item label="推荐分类" label-width="100px" :prop="'items.' + index + '.cat_id'" :rules="{ required: true, message: '请选择推荐分类', trigger: 'change' }">
                                    <el-cascader
                                            v-model="item.cat_id"
                                            @focus="handleRemoteSearchChoseCascader({ parent: `categories`, chose: templateSetForm.items, value: 'value', child_value: 'cat_id' })"
                                            @change="(value) => { value.length && (item.cat_id = value[value.length - 1]), item.goods_items = [], item.default_selection_data = [], item.is_ziying = '' }"
                                            placeholder="请输入分类ID或名称"
                                            :props="{ checkStrictly: true , emitPath: true}"
                                            :options="templateSetForm.categories"
                                            :key="cascader_key"
                                            filterable style="width: 500px;"></el-cascader>
                                </el-form-item>
                                <el-form-item label="分类别名" label-width="100px" :prop="'items.' + index + '.alias'" :rules="
                                    [
                                        { required: true, message: '请输入分类别名', trigger: 'blur' },
                                        { max: 10, message: '分类别名不能超过10个字符', trigger: 'blur' },
                                        { validator: (rule, value, callback) => {
                                            //  校验是否有相同的名称
                                            const same_name = templateSetForm.items.filter(data => data.cat_name == value)
                                            if (same_name.length > 1) { callback(new Error('该分类别名已存在，请修改！')); } else { callback(); }
                                        }, trigger: 'blur' },
                                    ]
                                ">
                                    <el-input v-model="item.alias" style="width: 500px;"></el-input>
                                </el-form-item>
                                <el-form-item label="分类排序" label-width="100px" :prop="'items.' + index + '.sort'" :rules="
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
                                    <el-input v-model="item.sort" style="width: 500px;"></el-input>
                                    <p class="warning-text">分类从左到右依次显示，数字越大越靠左</p>
                                </el-form-item>
                                <el-form-item label="关联商品" label-width="100px" required>
                                    <div class="s-flex ai-ct">
                                        <el-select v-model="item.is_ziying" @change="item.default_selection_data = [], item.goods_id = '', $forceUpdate()" placeholder="产品类型" style="width: 150px;">
                                            <el-option key="-1" label="全部" :value="-1"></el-option>
                                            <el-option key="1" label="自营产品" :value="1"></el-option>
                                            <el-option key="2" label="非自营产品" :value="2"></el-option>
                                        </el-select>
                                        <el-select v-model="item.goods_id"
                                                   @change="(value) => { item.goods_id = value, $forceUpdate() }"
                                                   filterable
                                                   remote
                                                   reserve-keyword
                                                   placeholder="请输入商品 ID/ 名称/货号查询"
                                                   :remote-method="(query) => {
                                                        handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { page_type: 13, keywords: query, is_ziying: item.is_ziying, cat_id: item.cat_id }, parent: `items.${index}`, target: 'default_selection_data', chose: item.goods_items, chose_value: 'goods_id' })
                                                   }"
                                                   @focus="handleRemoteSearchChose({ parent: `items.${index}.default_selection_data`, chose: item.goods_items, child_value: 'goods_id' })"
                                                   style="width: 340px; margin-left: 10px;">
                                            <el-option v-for="good in item.default_selection_data" :disabled="good.is_disabled" :key="good.value" :label="good.label" :value="good.value"></el-option>
                                        </el-select>
                                        <div class="drawer-btn primary s-flex ai-ct jc-ct" :class="{ disabled: item.goods_items.length >= 10 }" style="margin-left: 20px;" @click="handleClickAddChildData({ parent: `items.${index}.goods_items`, list: item.default_selection_data, value: item.goods_id, child_target: 'value', length: 10, select_name: 'default_selection_data', cat_id: item.cat_id }), item.goods_id = ''">添加商品（@{{item.goods_items.length}}/10）</div>
                                    </div>
                                </el-form-item>
                                <dl>
                                    <dt>
                                        <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                            <li class="s-flex ai-ct jc-ct" style="width: 500px; margin-right: 150px;"><span>*</span>商品名称</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 100px; margin-right: 190px;">排序</li>
                                            <li class="s-flex ai-ct jc-ct" style="width: 80px;">操作</li>
                                        </ul>
                                    </dt>
                                    <dd class="s-flex flex-dir ai-ct">
                                        <ul class="s-flex ai-ct" style="padding: 0 40px;" v-for="(goods, goodsIndex) in item.goods_items">
                                            <li style="width: 500px; margin-right: 150px;">
                                                <el-form-item :key="goodsIndex" :prop="'items.' + index + '.goods_items.' + goodsIndex + '.goods_id'" :rules="{ required: true, message: '请选择推荐商品', trigger: 'blur' }">
                                                    <el-select v-model="goods.goods_id" filterable placeholder="请选择" style="width: 500px;">
                                                        <el-option v-for="option in goods.default_selection_data" :key="option.value" :label="option.label" :value="option.value">
                                                        </el-option>
                                                    </el-select>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 100px; margin-right: 190px;">
                                                <el-form-item :prop="'items.' + index + '.goods_items.' + goodsIndex + '.sort'" :rules="
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
                                                    <el-input v-model="goods.sort" placeholder="1~100"></el-input>
                                                </el-form-item>
                                            </li>
                                            <li style="width: 80px;">
                                                <div class="drawer-btn cursorp s-flex ai-ct jc-ct" @click="handleClickDeleteData({ index: goodsIndex, parent: `items.${index}.goods_items` })">删除</div>
                                            </li>
                                        </ul>
                                        <p class="warning-nodata" v-if="!item.goods_items.length">还没添加推荐商品哦</p>
                                    </dd>
                                </dl>
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
    Vue.component('hot-sale-goods', {
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
                publicListItem: {
                    items: { //items
                        cat_id: '', //分类id
                        alias: '', //分类别名
                        sort: '', //排序
                        default_categories: [], //默认分类数据
                        goods_items: [{ //商品数据
                            goods_id: '', //商品id
                            sort: '', //排序
                            default_selection_data: []
                        }]
                    },
                    goods: { //商品数据
                        goods_id: '', //商品id
                        sort: '', //排序
                        default_selection_data: []
                    }
                },
                tabIndex: 0,
                slideIndex: 0,
                msg_error: null,
                is_show_imageview: false,
                image_view: '',
                is_can_add_data: true,  //  是否可以添加商品
                cascader_key: Math.round(new Date() / 1000),
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
            /** 针对分类选择值转为单个字符串 */
            computedCascaderSelectedToString () {
                return function (value) {
                    let id = null
                    if (Object.prototype.toString.call(value).slice(8, -1) == 'Array') {
                        id = value[value.length - 1]
                    } else {
                        id = value
                    }
                    return id
                }
            },
        },
        template: '#hot-sale-goods',
        methods: {
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting (data) {
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
                this.templateSetForm.slideIndex = 0
                this.templateSetFormType = item.component_name
                this.is_show_drawer = true
                setTimeout(() => {
                    if (!this.templateSetForm.categories) {
                        this.handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { page_type: 9 }, target: 'categories' })
                    }
                }, 200)
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
                    if (this.material_dialog_model === 'single') {
                        this.$set(this.templateSetForm, this.uploadTarget, result)
                    } else {
                        this.templateSetForm[this.uploadTarget] = this.templateSetForm[this.uploadTarget].concat(result)
                        this.$set(this.templateSetForm, this.uploadTarget, this.templateSetForm[this.uploadTarget])
                    }
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
            /** 点击切换热销商品分类 */
            handleClickCheckData (data) {
                const { item, index, target, path, params } = data
                const post_path = path
                this.doPost(post_path, params).then(res => {
                    if (res.code == 200) {
                        res.data && (this.temp_item.data.hot_sale_items.goods = res.data)
                    } else {
                        this.$message.error(res.message)
                    }
                })
                this.$set(this, target, index)
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
                const { path, method, params, parent, target, index, childIndex, chose, chose_value } = data
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
                    if (params.page_type == 13) {
                        this.handleRemoteSearchChose({ parent: `${parent}.${target}`, chose, value: 'value', child_value: chose_value })
                    }
                })
            },
            /** 下拉选中禁用 */
            handleRemoteSearchChose (data) {
                setTimeout(() => {
                    const { parent, chose, value = 'value', child_value, ref } = data
                    const choseType = Object.prototype.toString.call(chose).slice(8, -1)
                    const newList = this.getNestedProperty(this.templateSetForm, parent)
                    if (newList && newList.length) {
                        if (choseType == 'Array') {
                            newList.map(item => chose.some(child => item.is_disabled = item[value] == child[child_value]))
                        } else {
                            newList.map(item => item.is_disabled = item[value] == chose)
                        }
                    }
                    ref && this.clearFormValidate(ref)
                    this.$forceUpdate()
                }, 200)
                // (newList && newList.length) && (newList.map(item => item.is_disabled = item[value] == chose))
            },
            /** 下拉选中禁用 */
            handleRemoteSearchChoseShop (data) {
                setTimeout(() => {
                    const { parent, chose, value = 'value', child_value, ref } = data
                    const choseType = Object.prototype.toString.call(chose).slice(8, -1)
                    const newList = this.getNestedProperty(this.templateSetForm, parent)
                    if (newList && newList.length) {
                        if (choseType == 'Array') {
                            newList.map(item => chose.some(child => item.is_disabled = child[child_value].some(shop => item[value] == shop['seller_id'])))
                        } else {
                            newList.map(item => item.is_disabled = item[value] == chose)
                        }
                    }
                    ref && this.clearFormValidate(ref)
                    this.$forceUpdate()
                }, 200)
                // (newList && newList.length) && (newList.map(item => item.is_disabled = item[value] == chose))
            },
            /** 级联选中禁用 */
            handleRemoteSearchChoseCascader (data) {
                const { parent, chose, value = 'value', child_value, ref } = data
                const choseType = Object.prototype.toString.call(chose).slice(8, -1)
                const newList = this.getNestedProperty(this.templateSetForm, parent)
                if (newList && newList.length) {
                    this.handleGetSelectedName({ list: newList, chose, value, child_value })
                }
            },
            /** 使用递归实现级联选中禁用 */
            handleGetSelectedName (data) {
                const { list, chose, value, child_value } = data
                for (let i = 0; i < list.length; i++) {
                    for (let j = 0; j < chose.length; j++) {
                        if (list[i][value] == chose[j][child_value]) {
                            list[i]['disabled'] = true
                        }
                        if (list[i][value] != chose[j][child_value]) {
                            if (list[i].children && list[i].children.length) {
                                this.handleGetSelectedName({ list: list[i].children, chose, value, child_value })
                            }
                        }
                    }
                }
            },
            /** 点击添加组件数据 */
            async handleClickAddData (dom, len, warning_text, data) {
                const item = JSON.parse(JSON.stringify(data))
                let newValue = this.getNestedProperty(this.templateSetForm, dom)
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
            async handleClickDeleteData (data) {
                const { index, parent, confirm, min = -1, min_text } = data
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
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
                    }).catch(() => {});
                } else {
                    if (min > -1 && newValue.length <= min) {
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.success(min_text)
                        return false
                    }
                    newValue.splice(index, 1)
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.success('删除成功！')
                }
                if (parent == 'items') {
                    setTimeout(() => this.slideIndex = 0, 200)
                }
            },
            /** 点击添加多个数据 */
            async handleClickAddChildData (data) {
                const { parent, list, value, child_target, length, select_name, cat_id } = data
                if (!this.is_can_add_data) return false
                if (!cat_id) {
                    this.$message.error('请先选择推荐分类');
                    this.is_can_add_data = false
                    setTimeout(() => this.is_can_add_data = true, 3000)
                    return
                }
                if (!list || !value) {
                    this.$message.error('请选择商品');
                    this.is_can_add_data = false
                    setTimeout(() => this.is_can_add_data = true, 3000)
                    return
                }
                let result = list.filter(one => one[child_target] == value)
                if (!result.length) return false
                let newList = this.getNestedProperty(this.templateSetForm, parent)
                if (length > -1 && newList.length >= length) {
                    this.$message.error(`最多可设置${length}个产品`);
                    this.is_can_add_data = false
                    setTimeout(() => this.is_can_add_data = true, 3000)
                    return false
                }
                newList.push({
                    goods_id: result[0].value,
                    [select_name]: result,
                    sort: ''
                })
                //  禁用已选中下拉数据
                if (newList.length) {
                    list.map(item => newList.some(child => item.is_check = child.goods_id == item.value))
                } else {
                    list.map(item => item.is_check = false)
                }
                this.$forceUpdate()
                /*const filter = newList.filter(item => result.some(child => child.value == item.good_id))
                if (!filter.length) {
                    newList.push({
                        good_id: result[0].value,
                        [select_name]: result,
                        sort: ''
                    })
                    this.$forceUpdate()
                } else {
                    this.$message.error('该商品已存在');
                }*/
            },
            /** 点击提交设置弹窗数据 */
            handleClickSubmitSetForm (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        /*const cat_name_null = this.templateSetForm.items.filter(item => item.alias == '')
                        const data_null = this.templateSetForm.items.findIndex(item => !item.shop.length || item.shop.length < 3)
                        const sort_null = this.templateSetForm.items.filter(item => item.shop.some(child => child.seller_id == ''))
                        if (cat_name_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请输入分类别名`)
                            return false
                        }
                        if (data_null > -1) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`推荐店铺第【${data_null}】项至少添加3个店铺`)
                            return false
                        }
                        if (sort_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请选择店铺`)
                            return false
                        }*/
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
                this.templateSetForm.categories = []
                this.cascader_key = Math.round(new Date() / 1000)
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
        mounted () {
        }
    })
</script>
