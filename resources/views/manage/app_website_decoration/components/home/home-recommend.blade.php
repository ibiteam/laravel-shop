<style>
    /* 为您推荐 */
    .temp-item.template-recommend { padding: 0; }
    .template-recommend-con .recommend-title { display: table; margin: 50px auto; font-size: 34px; font-weight: bold; position: relative; }
    .template-recommend-con .recommend-title::before { content: ''; width: 98px; height: 30px; background: url('https://cdn.toodudu.com/uploads/2023/10/20/title_left.png') center no-repeat; background-size: 100%; position: absolute; left: -108px; top: 2px; }
    .template-recommend-con .recommend-title::after { content: ''; width: 98px; height: 30px; background: url('https://cdn.toodudu.com/uploads/2023/10/20/title_right.png') center no-repeat; background-size: 100%; position: absolute; right: -108px; top: 2px; }
    .template-recommend-con .recommend-tabs { padding: 0 20px; }
    .template-recommend-con .recommend-tabs h1 { text-align: center; font-size: 28px; font-weight: bold; }
    .template-recommend-con .recommend-tabs p { width: 120px; height: 40px; margin: 20px 0; line-height: 40px; text-align: center; border: 1px solid transparent; border-radius: 8px; }
    .template-recommend-con .recommend-tabs .tab-item.active h1 { text-align: center; color: #f71111; }
    .template-recommend-con .recommend-tabs .tab-item.active p { border-color: #f71111; color: #f71111; }
    .template-recommend-con .recommend-list { padding: 0 15px; }
    .template-recommend-con .recommend-list .is-no-data { width: 610px; height: 588px; margin: 0 auto; }
    .template-recommend-con .recommend-list .is-no-data img {
        width: 610px;
        height: 273px;
    }

    .template-recommend-con .recommend-list .is-no-data p {
        text-align: center;
        color: #999999;
        font-size: 28px;
        margin-top: 42px;
    }
    .template-recommend-con ul li { width: 348px; margin: 0 5px 10px 5px; background-color: #ffffff; border-radius: 20px; overflow: hidden; position: relative; }
    .template-recommend-con ul li .recommend-ad-name { width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); position: absolute; left: 0; top: 0; z-index: 1; }
    .template-recommend-con ul li .recommend-ad-name p { font-size: 28px; color: #ffffff; }
    .template-recommend-con ul li .carousel-swiper { border-radius: 20px; }
    .template-recommend-con ul li .carousel-swiper .carousel-item,
    .template-recommend-con ul li .carousel-swiper .carousel-item img { width: 100%; height: 100%; }
    .template-recommend-con ul li .carousel-indicators { bottom: 0; }
    .template-recommend-con ul li .carousel-indicators li { width: 10px; height: 10px; border-radius: 10px; background-color: #E2E2E2; }
    .template-recommend-con ul li .carousel-indicators li.active { width: 26px; background-color: #ffffff; }

    .template-recommend-con ul li .goods-info { padding: 0 20px 24px 20px; }
    .template-recommend-con ul li .goods-image { width: 100%; height: 350px; }
    .template-recommend-con ul li .goods-name { margin-top: 15px; line-height: 1.4; font-size: 26px; }
    .template-recommend-con ul li .goods-name .goods-tag { padding: 2px 12px; text-align: center; background: linear-gradient(to right, #F71111, #FA5F5F); border-radius: 6px; font-size: 20px; color: #ffffff; }
    .template-recommend-con ul li .goods-name .goods-tag.is-zhiying { background: linear-gradient(to right, #5436D5, #735CFF); }
    .template-recommend-con ul li .goods-type { margin-top: 15px; font-size: 22px; color: #f71111; }
    .template-recommend-con ul li .goods-activity { margin-top: 10px; }
    .template-recommend-con ul li .goods-activity .activity-tag { width: 80px; height: 30px; line-height: 30px; text-align: center; margin-right: 20px; background: #ffffff; border: 1px solid #f71111; border-radius: 4px; font-size: 20px; color: #f71111; }
    .template-recommend-con ul li .goods-activity .activity-tag.is-group { background: linear-gradient(to right, #FF7B1A, #FFA841); border: none; color: #ffffff; }
    .template-recommend-con ul li .goods-activity .activity-tag.activity-seckill { width: 100%; padding: 2px 10px; margin: 0; background-color: #FDEFEF; border-color: #FDEFEF; border-radius: 4px; }
    .template-recommend-con ul li .goods-activity .activity-tag.activity-seckill em { margin-right: 10px; font-size: 20px; }
    .template-recommend-con ul li .goods-activity p { font-size: 20px; color: #f71111; }
    .template-recommend-con ul li .goods-shop { margin-top: 15px; font-size: 22px; color: #FFAA00; }
    .template-recommend-con ul li .goods-shop h1 { margin-right: 30px; }
    .template-recommend-con ul li .goods-shop em { font-size: 12px; color: #333333; }
    .template-recommend-con ul li .goods-price { margin-top: 10px; }
    .template-recommend-con ul li .goods-price p { padding: 5px 10px; background-color: rgba(255,195,0,0.12); border-radius: 4px; font-size: 20px; color: #FF8F1F; }
    .template-recommend-con ul li .goods-price p.is-now { background-color: rgba(247, 17, 17, 0.04); color: #f71111; }
    .template-recommend-con ul li .goods-hot { padding: 5px 10px; margin-top: 16px; background-color: rgba(255,195,0,0.12); border-radius: 4px; font-size: 20px; color: #FF8F1F; }


    .drawer-content-recommend .drawer-slide-btn { top: 0 !important; }

</style>

<script type="text/x-template" id="home-recommend-template">
    <div class="temp-item template-recommend" :class="{ 's-flex ai-ct jc-ct': temp_item.activity_info && !temp_item.activity_info.length, active: active_index == temp_item.id, disabled: !temp_item.is_show }">
        <public-mark-template :title="temp_item.name" :index="temp_index" box_height="50px" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>
        <template>
            <div class="temp-item-nodata s-flex flex-dir ai-ct jc-ct" v-if="!temp_item.data || computedDataType(temp_item.data) == 'Array'">
                <p>@{{ temp_item.name }}</p>
                <p>点击“设置”，配置组件内需要展示的内容吧</p>
            </div>
            <template v-else>
                <div class="template-recommend-con">
                    <div class="recommend-title" v-if="temp_item.data.name">@{{ temp_item.data.name }}</div>
                    <div class="recommend-tabs s-flex ai-ct jc-bt" v-if="temp_item.data.data.recommend_good_category && temp_item.data.data.recommend_good_category">
                        <div class="tab-item cursorp" :class="{ active: index == tabIndex }" v-for="(item, index) in temp_item.data.data.recommend_good_category" @click="handleClickRecommendThemeTab(item, index)">
                            <h1 class="cursorp">@{{ item.title }}</h1>
                            <p class="cursorp">@{{ item.subtitle }}</p>
                        </div>
                    </div>
                    <div class="recommend-list s-flex">
                        <template v-if="temp_item.data.data.recommend_good_data.length">
                            <ul class="s-flex flex-dir" v-if="temp_item.data.recommend_good_data_left && temp_item.data.recommend_good_data_left.length">
                                <li v-for="item in temp_item.data.recommend_good_data_left">
                                    <template v-if="item.type == 1">
                                        <div class="goods-image img-set-full">
                                            <img :src="item.goods_thumb ? item.goods_thumb : 'https://cdn.toodudu.com/uploads/2021/02/20/app_nopic.png'" alt="">
                                        </div>
                                        <div class="goods-info">
                                            <div class="goods-name ellipsis-2">
                                                <span class="goods-tag" :class="{ 'is-zhiying': item.is_ziying == 0 }" v-if="item.ziying_sign">@{{ item.ziying_sign }}</span> @{{ item.goods_name }}
                                            </div>
                                            <div class="goods-type ellipsis-1" v-if="item.sign_type == 4">@{{ item.goods_subtitle }}</div>
                                            <!-- 优惠券 -->
                                            <div class="goods-activity s-flex ai-ct" v-if="item.sign_type == 2">
                                                <div class="activity-tag activity-seckill s-flex ai-ct">
                                                    <em class="iconfont">&#xe7c7;</em>
                                                    <p class="ellipsis-1">@{{ item.coupon }}</p>
                                                </div>
                                            </div>
                                            <!-- 拼团 -->
                                            <div class="goods-activity s-flex ai-ct" v-if="item.activity_info && item.sign_type == 1 && item.is_group">
                                                <div class="activity-tag is-group">@{{ item.activity_info.type_sign }}</div>
                                                <p class="ellipsis-1 flex-1">@{{ item.activity_info.activity_desc }}</p>
                                            </div>
                                            <!-- 秒杀\特卖\满减 -->
                                            <div class="goods-activity s-flex ai-ct" v-if="item.activity_info && item.sign_type == 1 && !item.is_group">
                                                <div class="activity-tag">@{{ item.activity_info.act_type == 1 ? '秒杀中' : item.activity_info.act_type == 2 ? '特卖' : item.activity_info.act_type == 6 ? '满减' : '' }}</div>
                                                <p class="ellipsis-1 flex-1">@{{ item.activity_info.activity_desc }}</p>
                                            </div>
                                            <div class="goods-shop s-flex ai-ct" v-if="item.sign_type == 3 && item.frequent_visits">
                                                <h1 class="ellipsis-1">常买的店</h1>
                                                <p>进店 <em class="iconfont">&#xe772;</em></p>
                                            </div>
                                            <div class="goods-price s-flex ai-ct jc-bt">
                                                <form-price class="ellipsis-1 flex-1" :price="item.shop_price" weight="bolder" int_size="34" df_size="24" sign_size="22" :unit="item.unit" unit_size="24" color="#F71111"></form-price>
                                                <p v-if="item.goods_type" :class="{ 'is-now': item.goods_type == '现货' }">@{{ item.goods_type }}</p>
                                            </div>
                                            <div class="goods-hot s-flex ai-ct" v-if="item.hot_heat">
                                                <p class="ellipsis-1 flex-1">@{{ item.hot_heat.act_name }}排行榜TOP@{{ item.hot_heat.top }}</p>
                                                <em class="iconfont">&#xe772;</em>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-if="item.type == 2 && item.img_data && item.img_data.length">
                                        <div class="recommend-ad-name s-flex ai-ct jc-ct">
                                            <p>@{{ item.ad_name }}</p>
                                        </div>
                                        <carousel-swiper
                                                width="360"
                                                height="460"
                                                :list="item.img_data">
                                            <template slot="default" slot-scope="scope">
                                                <img :src="scope.row.img_url" alt="">
                                            </template>
                                        </carousel-swiper>
                                    </template>
                                </li>
                            </ul>
                            <ul class="s-flex flex-dir" v-if="temp_item.data.recommend_good_data_right && temp_item.data.recommend_good_data_right.length">
                                <li v-for="item in temp_item.data.recommend_good_data_right" v-if="(item.type == 2 && item.img_data && item.img_data.length) || item.type == 1">
                                    <template v-if="item.type == 1">
                                        <div class="goods-image img-set-full">
                                            <img :src="item.goods_thumb ? item.goods_thumb : 'https://cdn.toodudu.com/uploads/2021/02/20/app_nopic.png'" alt="">
                                        </div>
                                        <div class="goods-info">
                                            <div class="goods-name ellipsis-2">
                                                <span class="goods-tag" :class="{ 'is-zhiying': item.is_ziying == 0 }" v-if="item.ziying_sign">@{{ item.ziying_sign }}</span> @{{ item.goods_name }}
                                            </div>
                                            <div class="goods-type ellipsis-1" v-if="item.sign_type == 4">@{{ item.goods_subtitle }}</div>
                                            <!-- 优惠券 -->
                                            <div class="goods-activity s-flex ai-ct" v-if="item.sign_type == 2">
                                                <div class="activity-tag activity-seckill s-flex ai-ct">
                                                    <em class="iconfont">&#xe7c7;</em>
                                                    <p class="ellipsis-1">@{{ item.coupon }}</p>
                                                </div>
                                            </div>
                                            <!-- 拼团 -->
                                            <div class="goods-activity s-flex ai-ct" v-if="item.activity_info && item.sign_type == 1 && item.is_group">
                                                <div class="activity-tag is-group">@{{ item.activity_info.type_sign }}</div>
                                                <p class="ellipsis-1 flex-1">@{{ item.activity_info.activity_desc }}</p>
                                            </div>
                                            <!-- 秒杀\特卖\满减 -->
                                            <div class="goods-activity s-flex ai-ct" v-if="item.activity_info && item.sign_type == 1 && !item.is_group">
                                                <div class="activity-tag">@{{ item.activity_info.act_type == 1 ? '秒杀中' : item.activity_info.act_type == 2 ? '特卖' : item.activity_info.act_type == 6 ? '满减' : '' }}</div>
                                                <p class="ellipsis-1 flex-1">@{{ item.activity_info.activity_desc }}</p>
                                            </div>
                                            <div class="goods-shop s-flex ai-ct" v-if="item.sign_type == 3 && item.frequent_visits">
                                                <h1 class="ellipsis-1">常买的店</h1>
                                                <p>进店 <em class="iconfont">&#xe772;</em></p>
                                            </div>
                                            <div class="goods-price s-flex ai-ct jc-bt">
                                                <form-price class="ellipsis-1 flex-1" :price="item.shop_price" weight="bolder" int_size="34" df_size="24" sign_size="22" :unit="item.unit" unit_size="24" color="#F71111"></form-price>
                                                <p v-if="item.goods_type" :class="{ 'is-now': item.goods_type == '现货' }">@{{ item.goods_type }}</p>
                                            </div>
                                            <div class="goods-hot s-flex ai-ct" v-if="item.hot_heat">
                                                <p class="ellipsis-1 flex-1">@{{ item.hot_heat.act_name }}排行榜TOP@{{ item.hot_heat.top }}</p>
                                                <em class="iconfont">&#xe772;</em>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-if="item.type == 2 && item.img_data && item.img_data.length">
                                        <div class="recommend-ad-name s-flex ai-ct jc-ct">
                                            <p>@{{ item.ad_name }}</p>
                                        </div>
                                        <carousel-swiper
                                                width="360"
                                                height="460"
                                                :list="item.img_data">
                                            <template slot="default" slot-scope="scope">
                                                <img :src="scope.row.img_url" alt="">
                                            </template>
                                        </carousel-swiper>
                                    </template>
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
            <el-form :model="templateSetForm" :rules="templateSetRule" ref="templateSetForm" class="drawer-content drawer-content-recommend" v-load="set_save_loading">
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
                            <el-form-item label="板块名称" label-width="110px" :prop="'name'" :rules="
                                [
                                    { max: 10, message: '板块名称不能超过10个字符', trigger: 'blur' },
                                    { validator: (rule, value, callback) => {
                                        //  校验是否有相同的名称
                                        const same_name = temp_list.filter(data => data.content.name && data.content.name == templateSetForm.name && data.id != temp_item.id)
                                        if (same_name.length) { callback(new Error('该板块名称已存在，请修改！')); } else { callback(); }
                                    }, trigger: 'blur' },
                                ]
                            ">
                                <el-input v-model="templateSetForm.name" style="width: 500px;"></el-input>
                                <p class="warning-text">用于改变页面中展示的板块名称</p>
                            </el-form-item>
                            <el-form-item label="展示样式" label-width="110px">
                                <el-radio-group :value="1" style="height: 40px;" class="s-flex ai-ct">
                                    <el-radio :label="1">
                                        <span>标题+主题</span>
                                        <el-tooltip
                                                style="margin: 0 20px 0 0;"
                                                effect="light"
                                                placement="right"
                                                width="100"
                                                popper-class="drawer-preview-image"
                                                style="min-width: 100px;"
                                                trigger="hover">
                                            <div slot="content" class="drawer-upload-warning">
                                                <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2023/10/30/recommend_goods.png', is_show_imageview = true">
                                                    <em class="iconfont cursorp">&#xe7c3;</em>
                                                </div>
                                                <el-image style="width: 100px; height: 100px" :src="'https://cdn.toodudu.com/uploads/2023/10/30/recommend_goods.png'"></el-image>
                                            </div>
                                            <em class="iconfont cursorp">&#xe72d;</em>
                                        </el-tooltip>
                                    </el-radio>
                                </el-radio-group>
                            </el-form-item>
                        </div>
                    </div>
                    <div class="drawer-item" v-if="templateSetForm.theme">
                        <div class="drawer-item-dt">
                            <h1>推荐主题</h1>
                            <p class="warning-text" style="margin-left: 0;">支持添加3个或4个主题，也可以不添加主题。无主题，移动端将按照商品个数显示。</p>
                        </div>
                        <div class="drawer-item-dd">
                            <dl>
                                <dt>
                                    <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                        <li class="s-flex ai-ct jc-ct" style="width: 300px;">
                                            <span>*</span>
                                            <label>主标题</label>
                                        </li>
                                        <li class="s-flex ai-ct jc-ct" style="width: 300px;">
                                            <span>*</span>
                                            <label>副标题</label>
                                        </li>
                                        <li class="s-flex ai-ct jc-ct" style="width: 200px; text-align: center;">
                                            <span>*</span>
                                            <label>关联分类</label>
                                        </li>
                                        <li class="s-flex ai-ct jc-ct" style="width: 90px;">排序</li>
                                        <li class="s-flex ai-ct jc-ct" style="width: 100px; margin: 0;">操作</li>
                                    </ul>
                                </dt>
                                <dd class="s-flex flex-dir ai-ct">
                                    <ul class="s-flex ai-ct" style="height: 60px; padding: 0 40px;" v-for="(item, index) in templateSetForm.theme">
                                        <li style="width: 300px;">
                                            <el-form-item :key="index" :prop="'theme.' + index + '.title'" :rules="[
                                                { required: true, message: '请输入主标题', trigger: 'blur' },
                                                { max: 4, message: '主标题不能超过4个字符', trigger: 'blur' },
                                                { validator: (rule, value, callback) => {
                                                    //  校验是否有相同的名称
                                                    const same_name = templateSetForm.theme.filter(data => data.title == value)
                                                    if (same_name.length > 1) { callback(new Error('该主标题已存在，请修改！')); } else { callback(); }
                                                }, trigger: 'blur' }
                                            ]">
                                                <el-input v-model="item.title" placeholder="请输入主标题" ></el-input>
                                            </el-form-item>
                                        </li>
                                        <li class="" style="width: 300px;">
                                            <el-form-item :key="index" :prop="'theme.' + index + '.subtitle'" :rules="[
                                                { required: true, message: '请输入副标题', trigger: 'blur' },
                                                { max: 4, message: '副标题不能超过4个字符', trigger: 'blur' },
                                                { validator: (rule, value, callback) => {
                                                    //  校验是否有相同的名称
                                                    const same_name = templateSetForm.theme.filter(data => data.subtitle == value)
                                                    if (same_name.length > 1) { callback(new Error('该副标题已存在，请修改！')); } else { callback(); }
                                                }, trigger: 'blur' }
                                            ]">
                                                <el-input v-model="item.subtitle" placeholder="请输入副标题" ></el-input>
                                            </el-form-item>
                                        </li>
                                        <li class="s-flex ai-ct jc-ct flex-1" style="width: 200px;">
                                            <el-form-item :prop="'theme.' + index + '.cat_id'" :rules="{ required: true, message: '请关联主题分类', trigger: 'blur' }" style="width: 200px;">
                                                <el-select v-model="item.cat_id"
                                                           filterable
                                                           remote
                                                           reserve-keyword
                                                           clearable
                                                           :remote-method="(query) => handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_cat_list') !!}', method: 'post', params: { keywords: query }, parent: `theme.${index}`, target: 'default_selection_data', index })"
                                                           @change="(value) => {
                                                                const filter = item.default_selection_data.filter(selected => selected.value == item.cat_id)
                                                                filter.length && (item.cat_name = filter[0].cat_name),
                                                                handleRemoteSearchChose({ parent: `theme.${index}.default_selection_data`, chose: templateSetForm.theme, child_value: 'cat_id' })
                                                           }"
                                                           @focus="handleRemoteSearchChose({ parent: `theme.${index}.default_selection_data`, chose: templateSetForm.theme, child_value: 'cat_id' })"
                                                           placeholder="请选择分类" style="width: 100%;">
                                                    <el-option v-for="option in item.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                </el-select>
                                            </el-form-item>
                                        </li>
                                        <li style="width: 90px;">
                                            <el-form-item :prop="'theme.' + index + '.sort'" :rules="
                                                [
                                                    { validator: (rule, value, callback) => {
                                                        if (value != '') {
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
                                        <li style="width: 100px; margin: 0;">
                                            <div class="drawer-btn cursorp s-flex ai-ct jc-ct" @click="handleClickDeleteData({ index, parent: `theme`})">删除</div>
                                        </li>
                                    </ul>
                                    <p class="warning-nodata" v-if="!templateSetForm.theme.length">暂无数据！</p>
                                    <div class="drawer-btn primary s-flex ai-ct jc-ct" :class="{ disabled: templateSetForm.theme.length >= 4 }" @click="handleClickAddData('theme', 4, '推荐主题', { title: '', subtitle: '', cat_id: '', sort: '1' })">添加主题（@{{templateSetForm.theme.length}}/4）</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="drawer-item">
                        <div class="drawer-item-dt">
                            <h1>推荐商品</h1>
                        </div>
                        <div class="drawer-item-dd">
                            <el-form-item label="商品数量" label-width="110px">
                                <el-select class="radius-right-none__input border-right-none__input" v-model="templateSetForm.number" placeholder="请选择商品数量" style="width: 500px;">
                                    <el-option label="20个" :value="20"></el-option>
                                    <el-option label="40个" :value="40"></el-option>
                                    <el-option label="60个" :value="60"></el-option>
                                </el-select>
                            </el-form-item>
                            <div class="el-form-item">
                                <label for="name" class="el-form-item__label" style="width: 110px; line-height: 2; padding: 8px 12px 0 42px;">是否显示自营标识</label>
                                <div class="el-form-item__content" style="margin-left: 75px;">
                                    <el-switch v-model="templateSetForm.is_show_ziying" :active-value="1" :inactive-value="0" active-text="显示" inactive-text="隐藏" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                    <p class="warning-text">开启后，商品名称前将展示自营、非自营的标识文字</p>
                                </div>
                            </div>
                            <el-form-item label="推荐形式" label-width="110px">
                                <p class="warning-text" style="line-height: 40px; font-size: 14px;">系统推荐</p>
                            </el-form-item>
                            <el-form-item label="推荐规则" label-width="110px">
                                <p class="warning-text" style="line-height: 40px; font-size: 14px;">推荐优先级如下：</p>
                                <p class="warning-text">①分类商品</p>
                                <p class="warning-text">②浏览优先</p>
                                <p class="warning-text">③人为推荐商品</p>
                                <p class="warning-text">④其他</p>
                            </el-form-item>
                        </div>
                    </div>
                    <div class="drawer-item" v-if="templateSetForm.ad_space && this.templateSetFormType == 'recommend_theme'">
                        <div class="drawer-item-dt">
                            <h1>中插广告</h1>
                        </div>
                        <div class="drawer-item-dd">
                            <el-form-item label="中插广告" label-width="110px">
                                <el-switch v-model="templateSetForm.is_show_ad" :active-value="1" :inactive-value="0" active-text="开启" inactive-text="关闭" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                <p class="warning-text">开启后，仅支持在第一个主题或者无主题情况下显示，其他主题不做处理</p>
                            </el-form-item>
                            <template v-if="templateSetForm.is_show_ad">
                                <el-form-item label="" label-width="50px">
                                    <div class="s-flex ai-ct jc-bt">
                                        <div>
                                            <p>广告位</p>
                                        </div>
                                        <div class="drawer-btn s-flex ai-ct jc-ct cursorp" :class="{ disabled: templateSetForm.ad_space.length >= 10 }" @click="handleClickAddData('ad_space', 10, '广告位', publicListItem.ad_space)">
                                            <em class="iconfont" style="height: auto;">&#xe620;</em>
                                            <label>添加（@{{ templateSetForm.ad_space.length }}/10）</label>
                                        </div>
                                    </div>
                                    <drawer-slides :width="1085" :slide_active.sync="slideIndex" target="ad_name" default_text="广告位" :list="templateSetForm.ad_space"></drawer-slides>
                                    <div class="drawer-item-cate" v-for="(item, index) in templateSetForm.ad_space" :key="slideIndex" v-if="slideIndex == index">
                                        <div class="cate-title s-flex ai-ct jc-bt" style="height: 40px;">
                                            <h1>广告位</h1>
                                            <div class="drawer-btn s-flex ai-ct jc-ct cursorp" v-if="templateSetForm.ad_space.length > 1" @click="handleClickDeleteData({ index: slideIndex, parent: 'ad_space', confirm: '确定要删除吗？' })">
                                                <label>删除</label>
                                            </div>
                                        </div>
                                        <el-form-item style="margin-bottom: 20px;" label="广告位名称" label-width="100px" :prop="'ad_space.' + index + '.ad_name'" :rules="[
                                            { required: true, message: '请输入广告位名称', trigger: 'blur' }
                                        ]">
                                            <el-input v-model="item.ad_name" maxlength="10" style="width: 500px;"></el-input>
                                            <p class="warning-text">仅用于在装修时快速查找广告位置使用</p>
                                        </el-form-item>
                                        <el-form-item style="margin-bottom: 20px;" label="指定位置" label-width="100px" :prop="'ad_space.' + index + '.space'" :rules="
                                            [
                                                { required: true, message: '请输入广告位指定位置', trigger: 'blur' },
                                                { validator: (rule, value, callback) => {
                                                    if (value) {
                                                        if (isNaN(value)) {
                                                            callback(new Error('请输入数字'));
                                                        } else if (!Number.isInteger(value * 1)) {
                                                            callback(new Error('请输入整数'));
                                                        } else if (value < 1) {
                                                            callback(new Error('排序最小值是1'));
                                                        } else if (value > templateSetForm.number) {
                                                            callback(new Error(`商品数量${templateSetForm.number}个，只能填写${templateSetForm.number}范围内的数字`));
                                                        } else { callback(); }
                                                    } else { callback(); }
                                                }, trigger: 'blur' },
                                            ]
                                        ">
                                            <el-input v-model="item.space" style="width: 500px;"></el-input>
                                            <p class="warning-text">输入数字范围：1~@{{ templateSetForm.number || '20' }}，系统将按照输入的数字位置将广告插入对应位置的商品前方。</p>
                                        </el-form-item>
                                        <el-form-item label="图片数据" label-width="100px">
                                            <p class="warning-text" style="line-height: 40px; font-size: 14px;">支持一个广告位上传多张图片，移动端展示样式是轮播广告</p>
                                        </el-form-item>
                                        <dl v-if="item.img_data">
                                            <dt>
                                                <ul class="s-flex ai-ct" style="padding: 0 40px;">
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
                                                                <p>建议尺寸：120px*62px</p>
                                                                <p>建议大小：2M内</p>
                                                            </div>
                                                            <em slot="reference" class="iconfont cursorp">&#xe72d;</em>
                                                        </el-tooltip>
                                                    </li>
                                                    <li class="s-flex ai-ct jc-ct flex-1" style="width: 460px;">URL链接</li>
                                                    <li class="s-flex ai-ct jc-ct" style="width: 100px;">
                                                        <label>排序</label>
                                                        <el-tooltip
                                                                style="margin: 0 20px 0 0;"
                                                                effect="light"
                                                                placement="top"
                                                                width="200"
                                                                trigger="hover">
                                                            <div slot="content" class="drawer-upload-warning">
                                                                <p>正序排列，系统将按照填写的排序值将广告插入商品数据中指定位置。</p>
                                                            </div>
                                                            <em slot="reference" class="iconfont cursorp">&#xe72d;</em>
                                                        </el-tooltip>
                                                    </li>
                                                    <li class="s-flex ai-ct jc-ct" style="width: 80px;">操作</li>
                                                </ul>
                                            </dt>
                                            <dd class="s-flex flex-dir ai-ct">
                                                <ul class="s-flex ai-ct" style="height: 90px; padding: 0 40px;" v-for="(child, childIndex) in item.img_data">
                                                    <li style="width: 90px;">
                                                        <el-form-item :key="index" :prop="'ad_space.' + index + '.img_data.' + childIndex + '.img_url'" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
                                                            <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                                <img v-if="child.img_url" :src="child.img_url" alt="">
                                                                <div  class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!child.img_url" @click="handleClickUploadFile({ parent: `ad_space.${index}.img_data.${childIndex}`, validate: 2, target: 'img_url' })">
                                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                                        <em class="iconfont">&#xe727;</em>
                                                                    </div>
                                                                    <p>未上传</p>
                                                                </div>
                                                                <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `ad_space.${index}.img_data.${childIndex}`, validate: 2, target: 'img_url' })">
                                                                        <em class="iconfont">&#xe727;</em>
                                                                    </div>
                                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile({ parent: `ad_space.${index}.img_data.${childIndex}`, target: 'img_url'})">
                                                                        <em class="iconfont">&#xe738;</em>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </el-form-item>
                                                    </li>
                                                    <li class="s-flex ai-ct jc-ct flex-1" style="width: 400px;">
                                                        <el-form-item :prop="'ad_space.' + index + '.img_data.' + childIndex + '.alias'">
                                                            <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="child.alias" @change="child.value = '', child.default_selection_data = [], child.alias == 'yi_qi_xiu' && handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: '', alias: child.alias }, parent: `ad_space.${index}.img_data.${childIndex}`, target: 'default_selection_data' }), clearFormValidate(`form_url_value${index}_${childIndex}`)" @clear="child.value = ''" placeholder="请选择" style="width: 200px;">
                                                                <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                                            </el-select>
                                                        </el-form-item>
                                                        <el-form-item :ref="`form_url_value${index}_${childIndex}`" :prop="'ad_space.' + index + '.img_data.' + childIndex + '.value'" :rules="{ required: child.alias != '' && computedOptionsPlaceholder(child.alias), message: computedOptionsPlaceholder(child.alias), trigger: 'blur' }">
                                                            <el-select v-model="child.value"
                                                               v-if="computedOptionsPlaceholder(child.alias) && computedOptionsCanRemote(child.alias)"
                                                               filterable
                                                               remote
                                                               reserve-keyword
                                                               clearable
                                                               :remote-method="(query) => {
                                                                    handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: child.alias }, parent: `ad_space.${index}.img_data.${childIndex}`, target: 'default_selection_data' }),
                                                                    handleRemoteSearchChose({ parent: `ad_space.${index}.img_data.${childIndex}.default_selection_data`, chose: child.value, ref: `form_url_value${index}_${childIndex}` })
                                                               }"
                                                               @change="(value) => handleRemoteSearchChose({ parent: `ad_space.${index}.img_data.${childIndex}.default_selection_data`, list: item.img_data, index: childIndex, chose: child.value, selected: child })"
                                                               @focus="handleRemoteSearchChose({ parent: `ad_space.${index}.img_data.${childIndex}.default_selection_data`, chose: child.value })"
                                                               :placeholder="computedOptionsPlaceholder(child.alias)" style="width: 330px">
                                                                <el-option v-for="option in child.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                            </el-select>
                                                            <el-input class="radius-left-none__input" v-else v-model="child.value" :disabled="(!computedOptionsPlaceholder(child.alias) && child.alias != '' && child.alias != null) || (!child.alias && !child.value)" :placeholder="computedOptionsPlaceholder(child.alias) ? computedOptionsPlaceholder(child.alias) : !computedOptionsPlaceholder(child.alias) && child.alias != '' && child.alias != null ? '系统生成链接' : ''" style="width: 330px;"></el-input>
                                                        </el-form-item>
                                                    </li>
                                                    <li style="width: 100px;">
                                                        <el-form-item :prop="'ad_space.' + index + '.img_data.' + childIndex + '.sort'" :rules="
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
                                                            <el-input v-model="child.sort" placeholder="1~100"></el-input>
                                                        </el-form-item>
                                                    </li>
                                                    <li style="width: 80px;">
                                                        <div class="drawer-btn cursorp s-flex ai-ct jc-ct" @click="handleClickDeleteData({ index: childIndex, parent: `ad_space.${index}.img_data`})">删除</div>
                                                    </li>
                                                </ul>
                                                <p class="warning-nodata" v-if="!item.img_data.length">暂无数据！</p>
                                                <div class="drawer-btn primary s-flex ai-ct jc-ct" :class="{ disabled: item.img_data.length >= 5 }" @click="handleClickAddImageData({ parent: `ad_space.${index}`, target: 'img_data', max: 5, length: item.img_data.length, item: { img_url: '', alias: 'https', value: '', sort: '1' }, image_name: 'img_url' })">添加（@{{item.img_data.length}}/5）</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </el-form-item>
                            </template>
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
    Vue.component('home-recommend-template', {
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
                    ad_space: {
                        ad_name: '',
                        space: '',
                        img_data: [
                            {
                                alias: 'https',
                                value: '',
                                sort: '1',
                            }
                        ],
                    }
                },
                is_show_imageview: false,
                image_view: '',
                slideIndex: 0,
                recommend_good_data_left: [],
                recommend_good_data_right: [],
                tabIndex: 0,
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
        template: '#home-recommend-template',
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
            /** 点击切换 */
            handleClickRecommendThemeTab (item, index) {
                const info = { component_name: this.temp_item.component_name, component_data: this.temp_item, name: this.temp_item.name, cat_id: item.cat_id }
                this.doPost('{!! route('manage.app_web_decoration.get_content_data_by_alias') !!}', info).then(res => {
                    if (res.code == 200) {
                        this.is_show_drawer = false
                        this.is_setting_save = true
                        this.set_save_loading = false
                        this.temp_item.data = res.data
                        this.temp_item.data.recommend_good_data_left = []
                        this.temp_item.data.recommend_good_data_right = []
                        if (res.data.data.recommend_good_data && res.data.data.recommend_good_data.length) {
                            res.data.data.recommend_good_data.some((child, childIndex) => {
                                if ((childIndex + 1) % 2 == 0) {
                                    this.temp_item.data.recommend_good_data_right.push(child)
                                } else {
                                    this.temp_item.data.recommend_good_data_left.push(child)
                                }
                            })
                        } else {
                            this.temp_item.data.data.recommend_good_data = []
                        }
                        this.tabIndex = index
                    } else {
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.error(res.message)
                    }
                    this.$forceUpdate()
                })
            },
            /** 远程搜索下拉数据 */
            handleRemoteSearchChange (data) {
                const { path, method, params, parent, target, index = '' } = data
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
                    if (path == 'get_cat_list') {
                        this.handleRemoteSearchChose({ parent: `theme.${index}.default_selection_data`, chose: this.templateSetForm.theme, child_value: 'cat_id' })
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
                        if (find_data) list[index].img_url = find_data.image
                    }*/
                    ref && this.clearFormValidate(ref)
                    this.$forceUpdate()
                }, 200)
                // (newList && newList.length) && (newList.map(item => item.is_disabled = item[value] == chose))
            },
            /** 递归循环对比数据 */
            handleGetSelectedMap (data) {
                const { list, value, selected, child } = data
                list.some(item => {
                    if (item[value] == selected) {
                        item.disabled = item[value] == selected
                        child.cat_name = item.label
                    }
                    if (item.children && item.children.length) {
                        this.handleGetSelectedMap({ list: item.children, value, selected, child })
                    }
                })
            },
            /** 点击添加组件数据 */
            async handleClickAddData (dom, len, warning_text, data) {
                const item = await JSON.parse(JSON.stringify(data))
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
                const { index, parent, confirm, min = -1 } = data
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
                if (confirm) {
                    this.$confirm(confirm, '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning',
                        center: true
                    }).then(() => {
                        newValue.splice(index, 1)
                        this.slideIndex = 0
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.success('删除成功！')
                    }).catch(() => {});
                } else {
                    if (min > -1 && newValue.length <= min) {
                        this.msg_error && this.msg_error.close()
                        this.msg_error = this.$message.success('至少添加一个广告位，不能删除！')
                        return false
                    }
                    newValue.splice(index, 1)
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.success('删除成功！')
                }
            },
            /** 点击提交设置弹窗数据 */
            handleClickSubmitSetForm (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        if (this.templateSetForm.theme.length > 0 && this.templateSetForm.theme.length < 3) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error('最少添加3个主题')
                            return false
                        }
                        if (this.templateSetForm.is_show_ad) {
                            const is_null_full = this.templateSetForm.ad_space.findIndex(item => {
                                return item.ad_name == '' ||
                                    item.space == ''
                                    // || item.img_data.some(child => child.img_url == '' || (child.alias != '' && child.value == ''))
                            })
                            if (is_null_full > -1) {
                                this.$message.error(`请完善广告位第【${is_null_full}】项表单数据`)
                                return false
                            }
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
                                if (this.temp_item.data && this.temp_item.data.component_name == 'recommend_theme') {
                                    this.temp_item.data.recommend_good_data_left = []
                                    this.temp_item.data.recommend_good_data_right = []
                                    if (this.temp_item.data.data.recommend_good_data && this.temp_item.data.data.recommend_good_data.length) {
                                        this.temp_item.data.data.recommend_good_data.some((child, childIndex) => {
                                            if ((childIndex + 1) % 2 == 0) {
                                                this.temp_item.data.recommend_good_data_right.push(child)
                                            } else {
                                                this.temp_item.data.recommend_good_data_left.push(child)
                                            }
                                        })
                                    }
                                }
                                this.$emit('save', { content: this.templateSetForm, data: this.temp_item.data, index: this.temp_index })
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
        mounted () {
        }
    })
</script>
