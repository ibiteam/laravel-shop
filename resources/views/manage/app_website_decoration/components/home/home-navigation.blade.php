<style>
    .home-header-box { /*height: 390px;*/ /*height: 226px;*/ /*background: linear-gradient(to bottom, #FCD4D4, #FDF3F2, #ffffff);*/ background-size: 750px 100%; background-position: top center; background-repeat: no-repeat; box-sizing: border-box; /*position: fixed; top: 60px; left: 50%; transform: translate(-50%); margin-left: -377px;*/ z-index: 99; }
    .home-header-box .home-header-content { /*height: 226px;*/ padding: 10px 30px; box-sizing: border-box; position: relative; }
    .home-header-box .home-logo img { width: 136px; height: 50px; }
    .home-header-box .home-logo-icons .icon-item { margin-left: 24px; }
    .home-header-box .home-logo-icons .icon-image { width: 32px; height: 32px; position: relative; }
    .home-header-box .home-logo-icons p { padding: 4px; border-radius: 100%; background-color: #F43B38; box-sizing: border-box; font-size: 16px; color: #ffffff; position: absolute; right: -10px; top: -10px; }
    .home-header-box .home-logo-icons span { margin-left: 12px; font-size: 24px; color: #333333; }
    .home-search { height: 66px; padding: 5px 5px 5px 20px; margin-top: 20px; background-color: #ffffff; border-radius: 30px; box-sizing: border-box; }
    .home-search em { margin-right: 24px; font-size: 34px; position: relative; }
    .home-search em::before { content: ''; width: 2px; height: 34px; background: linear-gradient(to bottom, #ffffff, #cccccc, #ffffff); opacity: 1; position: absolute; right: -20px; top: 1px; }
    .home-search p { padding: 0 10px; font-size: 28px; color: #c1c1c1; }
    .home-search .home-search-btn { width: 120px; height: 56px; line-height: 56px; text-align: center; border-radius: 50px; background-color: #F71111; font-size: 26px; color: #ffffff; }
    .home-header-tabs { height: 72px; }
    .home-header-tabs h1 { margin-right: 18px; font-size: 32px; }
    .home-header-tabs em,
    .home-header-tabs p { font-size: 26px; }
    .home-header-tabs p { margin-left: 12px; }
    .home-header-tabs__more { padding-left: 26px; margin-left: 26px; border-left: 2px solid #E9DFDE; }
    .home-header-tabs__more img { width: 26px; height: 26px; }
</style>

<script type="text/x-template" id="home-navigation">
    <div>
        <div class="home-header" v-if="temp_item.data">
            {{--<div class="home-header-null" :style="{ height: temp_item.data.is_show_nav_data ? 226 + 'px' : 158 + 'px' }"></div>--}}
            <div class="home-header-box public-width" v-if="temp_item.data" :style="{ backgroundImage: `url(${temp_item.data.base_data.top_bg_image})` }">
                <div class="home-header-content">
                    <public-mark-template :title="warning_text || temp_item.name" height="100%" :index="temp_index" :is_show_set="is_show_setting" alias="home" :show_alone="false" :is_show_switch="true" :list="temp_list" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>
                    <div class="home-header-logo s-flex ai-ct jc-bt">
                        <div class="home-logo">
                            <img :src="temp_item.data.base_data.logo" alt="">
                        </div>
                        <div class="home-logo-icons s-flex ai-ct">
                            <div class="icon-item s-flex ai-ct" v-for="item in temp_item.data.base_data.items">
                                <div class="icon-image img-set-full">
                                    <img :src="item.icon" alt="">
                                </div>
                                <span :style="{ color: item.font_color }">@{{ item.text }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="home-search s-flex ai-ct">
                        <em class="iconfont">&#xe7c0;</em>
                        <carousel-swiper
                                height="28"
                                width="511"
                                :list="temp_item.data.search_data.items"
                                mode="vertical"
                                :is_show_dots="false"
                                :trans_autoplay="5000"
                                :key="swiperKey">
                            <template slot="default" slot-scope="scope">
                                <p >@{{ scope.row.keywords }}</p>
                            </template>
                        </carousel-swiper>
                        <div class="home-search-btn" :style="{ backgroundColor: temp_item.data.search_data.button_color, color: temp_item.data.search_data.search_font_color }">搜索</div>
                    </div>
                    <div class="home-header-tabs s-flex ai-ct" v-if="temp_item.data.is_show_nav_data">
                        <h1 :style="{ color: temp_item.data.nav_data.font_selection_color }">@{{ temp_item.data.nav_data_left.title }}</h1>
                        <basic-tabs-line class="flex-1" :style="{ width: 'auto', height: '72px', '--font-color': temp_item.data.nav_data.font_default_color || '#333333' }" child_width="auto" child_height="76px" :list="temp_item.data.nav_data_tabs" target="title"></basic-tabs-line>
                        <div class="home-header-tabs__more s-flex ai-ct">
                            <img :src="temp_item.data.nav_data_right.icon" alt="">
                            <p :style="{ color: temp_item.data.nav_data.font_default_color }">@{{ temp_item.data.nav_data_right.title }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--设置弹窗-->
        <el-drawer  :with-header="false" :modal-append-to-body="false" :close-on-press-escape="false" :show-close="false" :wrapper-closable="false" size="1200px" :visible.sync="is_show_drawer" :direction="direction">
            <input type="file" @change="handleChangeUploadFile" value="" style="display: none;" ref="upload_file" />
            <el-form :model="templateSetForm" :rules="templateSetRule" ref="templateSetForm" class="drawer-content" v-load="set_save_loading">
                <div class="drawer-name s-flex ai-ct">
                    <label>设置板块：</label>
                    <span>@{{ templateSetFormName }}</span>
                </div>
                <div ref="drawerBox">
                    <drawer-tabs :width="1085" :tab_active.sync="slideIndex" default_text="推荐分类" :list="['基本信息','全站搜索','导航页面']"></drawer-tabs>
                    <div style="height: calc(100vh - 160px - 50px); overflow-y: auto;">
                        <template v-if="templateSetForm.base_data && slideIndex == 0">
                            <div class="drawer-item" v-if="templateSetForm.base_data">
                                <div class="drawer-item-dt">
                                    <h1>网站LOGO</h1>
                                </div>
                                <div class="drawer-item-dd" style="padding: 0 20px;">
                                    <el-form-item label="网站LOGO" label-width="110px" :prop="'base_data.logo'" :rules="{ required: true, message: '请上传LOGO', trigger: 'blur' }">
                                        <div class="s-flex">
                                            <div class="drawer-upload s-flex ai-ct jc-ct cursorp">
                                                <img v-if="templateSetForm.base_data.logo" :src="templateSetForm.base_data.logo" alt="">
                                                <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!templateSetForm.base_data.logo" @click="handleClickUploadFile({ parent: 'base_data', validate: 2, target: 'logo'})">
                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                        <em class="iconfont">&#xe727;</em>
                                                    </div>
                                                    <p>未上传</p>
                                                </div>
                                                <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-if="templateSetForm.base_data.logo" @click="handleClickUploadFile({ parent: 'base_data', validate: 2, target: 'logo'})">
                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0;">
                                                        <em class="iconfont">&#xe727;</em>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="drawer-upload-warning">
                                                <p>支持jpg、jpeg、png、gif格式</p>
                                                <p>建议尺寸：136px*50px</p>
                                                <p>建议大小：2M内</p>
                                            </div>
                                        </div>
                                    </el-form-item>
                                </div>
                            </div>
                            <div class="drawer-item">
                                <div class="drawer-item-dt">
                                    <h1>背景图</h1>
                                </div>
                                <div class="drawer-item-dd">
                                    <el-form-item label="导航背景图" label-width="110px" :prop="'base_data.top_bg_image'" :rules="{ required: true, message: '请上传导航背景图', trigger: 'blur' }">
                                        <div class="s-flex">
                                            <div class="drawer-upload s-flex ai-ct jc-ct cursorp">
                                                <img v-if="templateSetForm.base_data.top_bg_image" :src="templateSetForm.base_data.top_bg_image" alt="">
                                                <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!templateSetForm.base_data.top_bg_image" @click="handleClickUploadFile({ parent: 'base_data', validate: 1, target: 'top_bg_image', size: 2})">
                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                        <em class="iconfont">&#xe727;</em>
                                                    </div>
                                                    <p>未上传</p>
                                                </div>
                                                <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-if="templateSetForm.base_data.top_bg_image" @click="handleClickUploadFile({ parent: 'base_data', validate: 1, target: 'top_bg_image', size: 2})">
                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0;">
                                                        <em class="iconfont">&#xe727;</em>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="drawer-upload-warning">
                                                <p>仅限改变首页的导航栏的背景样式</p>
                                                <p>支持jpg、jpeg、png格式</p>
                                                <p>建议尺寸：750px*268px</p>
                                                <p>建议大小：@{{ this.upload_size_validate }}M内</p>
                                                <div class="s-flex ai-ct">
                                                    <el-tooltip
                                                            style="margin: 0 20px 0 0;"
                                                            effect="light"
                                                            placement="right"
                                                            width="100"
                                                            popper-class="drawer-preview-image"
                                                            trigger="hover">
                                                        <div slot="content" class="drawer-upload-warning">
                                                            <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = templateSetForm.base_data.top_default_bg_image, is_show_imageview = true">
                                                                <em class="iconfont cursorp">&#xe7c3;</em>
                                                            </div>
                                                            <el-image style="width: 100px; height: 100px" :src="templateSetForm.base_data.top_default_bg_image"></el-image>
                                                        </div>
                                                        <p class="iconfont cursorp" style="margin-right: 20px; color: #278ff0;">查看示例</p>
                                                    </el-tooltip>
                                                    <p slot="reference" class="iconfont cursorp" style="color: #278ff0;" @click="templateSetForm.base_data.top_bg_image = templateSetForm.base_data.top_default_bg_image">还原为默认图</p>
                                                </div>
                                            </div>
                                        </div>
                                    </el-form-item>
                                    <el-form-item label="首页背景图" label-width="110px" :prop="'base_data.home_bg_image'" :rules="{ required: true, message: '请上传首页背景图', trigger: 'blur' }">
                                        <div class="s-flex">
                                            <div class="drawer-upload s-flex ai-ct jc-ct cursorp">
                                                <img v-if="templateSetForm.base_data.home_bg_image" :src="templateSetForm.base_data.home_bg_image" alt="">
                                                <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!templateSetForm.base_data.home_bg_image" @click="handleClickUploadFile({ parent: 'base_data', validate: 1, target: 'home_bg_image', size: 2 })">
                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                        <em class="iconfont">&#xe727;</em>
                                                    </div>
                                                    <p>未上传</p>
                                                </div>
                                                <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-if="templateSetForm.base_data.home_bg_image" @click="handleClickUploadFile({ parent: 'base_data', validate: 1, target: 'home_bg_image', size: 2 })">
                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0;">
                                                        <em class="iconfont">&#xe727;</em>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="drawer-upload-warning">
                                                <p>仅限改变首页的背景样式，此样式会随着页面内容滚动。作图时需要和导航背景图上下衔接准确</p>
                                                <p>支持jpg、jpeg、png格式</p>
                                                <p>建议尺寸：宽-750px，高不限制</p>
                                                <p>建议大小：@{{ this.upload_size_validate }}M内</p>
                                                <div class="s-flex ai-ct">
                                                    <el-tooltip
                                                            style="margin: 0 20px 0 0;"
                                                            effect="light"
                                                            placement="right"
                                                            width="100"
                                                            popper-class="drawer-preview-image"
                                                            trigger="hover">
                                                        <div slot="content" class="drawer-upload-warning">
                                                            <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = templateSetForm.base_data.home_default_bg_image, is_show_imageview = true">
                                                                <em class="iconfont cursorp">&#xe7c3;</em>
                                                            </div>
                                                            <el-image style="width: 100px; height: 100px" :src="templateSetForm.base_data.home_default_bg_image"></el-image>
                                                        </div>
                                                        <p class="iconfont cursorp" style="margin-right: 20px; color: #278ff0;">查看示例</p>
                                                    </el-tooltip>
                                                    <p slot="reference" class="iconfont cursorp" style="color: #278ff0;" @click="templateSetForm.base_data.home_bg_image = templateSetForm.base_data.home_default_bg_image">还原为默认图</p>
                                                </div>
                                            </div>
                                        </div>
                                    </el-form-item>
                                </div>
                            </div>
                            <div class="drawer-item">
                                <div class="drawer-item-dt">
                                    <h1>签到</h1>
                                </div>
                                <div class="drawer-item-dd">
                                    <el-form-item label="签到" label-width="110px">
                                        <el-switch v-model="templateSetForm.base_data.sign_in.is_show" :active-value="1" :inactive-value="0" active-text="显示" inactive-text="隐藏" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                        <p class="warning-text">开启后，首页将显示此入口</p>
                                    </el-form-item>
                                    <template v-if="templateSetForm.base_data.sign_in.is_show">
                                        <el-form-item label="签到ICON" label-width="110px" :prop="'base_data.sign_in.icon'" :rules="{ required: true, message: '请上传签到ICON', trigger: 'blur' }">
                                            <div class="s-flex">
                                                <div class="drawer-upload s-flex ai-ct jc-ct cursorp">
                                                    <img v-if="templateSetForm.base_data.sign_in.icon" :src="templateSetForm.base_data.sign_in.icon" alt="">
                                                    <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!templateSetForm.base_data.sign_in.icon" @click="handleClickUploadFile({ parent: 'base_data.sign_in',  validate: 2, target: 'icon' })">
                                                        <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                            <em class="iconfont">&#xe727;</em>
                                                        </div>
                                                        <p>未上传</p>
                                                    </div>
                                                    <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-if="templateSetForm.base_data.sign_in.icon" @click="handleClickUploadFile({ parent: 'base_data.sign_in',  validate: 2, target: 'icon' })">
                                                        <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0;">
                                                            <em class="iconfont">&#xe727;</em>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="drawer-upload-warning">
                                                    <p>支持jpg、jpeg、png、gif格式</p>
                                                    <p>建议尺寸：32px*32px</p>
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
                                                                <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2023/11/08/home_sgin.png', is_show_imageview = true">
                                                                    <em class="iconfont cursorp">&#xe7c3;</em>
                                                                </div>
                                                                <el-image style="width: 100px; height: 100px" src="https://cdn.toodudu.com/uploads/2023/11/08/home_sgin.png"></el-image>
                                                            </div>
                                                            <p class="iconfont cursorp" style="margin-right: 20px; color: #278ff0;">查看示例</p>
                                                        </el-tooltip>
                                                        <p slot="reference" class="iconfont cursorp" style="color: #278ff0;" @click="templateSetForm.base_data.sign_in.icon = templateSetForm.base_data.sign_in.default_icon">还原为默认图</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </el-form-item>
                                        <el-form-item label="文字颜色" label-width="110px" :prop="'base_data.sign_in.font_color'" :rules="{ required: true, message: '请设置文字颜色', trigger: 'blur' }">
                                            <el-color-picker v-model="templateSetForm.base_data.sign_in.font_color"></el-color-picker>
                                        </el-form-item>
                                    </template>
                                </div>
                            </div>
                            <div class="drawer-item">
                                <div class="drawer-item-dt">
                                    <h1>消息</h1>
                                </div>
                                <div class="drawer-item-dd">
                                    <el-form-item label="消息" label-width="110px">
                                        <el-switch v-model="templateSetForm.base_data.notice.is_show" :active-value="1" :inactive-value="0" active-text="显示" inactive-text="隐藏" active-color="#409EFF" inactive-color="#CBCBCB"></el-switch>
                                        <p class="warning-text">开启后，APP端首页将显示此入口，H5端和小程序隐藏此入口</p>
                                    </el-form-item>
                                    <template v-if="templateSetForm.base_data.notice.is_show">
                                        <el-form-item label="消息ICON" label-width="110px" :prop="'base_data.notice.icon'" :rules="{ required: true, message: '请上传消息ICON', trigger: 'blur' }">
                                            <div class="s-flex">
                                                <div class="drawer-upload s-flex ai-ct jc-ct cursorp">
                                                    <img v-if="templateSetForm.base_data.notice.icon" :src="templateSetForm.base_data.notice.icon" alt="">
                                                    <div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!templateSetForm.base_data.notice.icon" @click="handleClickUploadFile({ parent: 'base_data.notice', validate: 2, target: 'icon' })">
                                                        <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                            <em class="iconfont">&#xe727;</em>
                                                        </div>
                                                        <p>未上传</p>
                                                    </div>
                                                    <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-if="templateSetForm.base_data.notice.icon" @click="handleClickUploadFile({ parent: 'base_data.notice', validate: 2, target: 'icon' })">
                                                        <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0;">
                                                            <em class="iconfont">&#xe727;</em>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="drawer-upload-warning">
                                                    <p>支持jpg、jpeg、png、gif格式</p>
                                                    <p>建议尺寸：32px*32px</p>
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
                                                                <div class="drawer-preview-icon s-flex ai-ct jc-ct" @click="image_view = 'https://cdn.toodudu.com/uploads/2023/11/08/home_message.png', is_show_imageview = true">
                                                                    <em class="iconfont cursorp">&#xe7c3;</em>
                                                                </div>
                                                                <el-image style="width: 100px; height: 100px" src="https://cdn.toodudu.com/uploads/2023/11/08/home_message.png"></el-image>
                                                            </div>
                                                            <p class="iconfont cursorp" style="margin-right: 20px; color: #278ff0;">查看示例</p>
                                                        </el-tooltip>
                                                        <p slot="reference" class="iconfont cursorp" style="color: #278ff0;" @click="templateSetForm.base_data.notice.icon = templateSetForm.base_data.notice.default_icon">还原为默认图</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </el-form-item>
                                        <el-form-item label="文字颜色" label-width="110px" label-width="110px" :prop="'base_data.notice.font_color'" :rules="{ required: true, message: '请设置文字颜色', trigger: 'blur' }">
                                            <el-color-picker v-model="templateSetForm.base_data.notice.font_color"></el-color-picker>
                                        </el-form-item>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template v-if="templateSetForm.search_data && slideIndex == 1">
                            <div class="drawer-item" v-if="templateSetForm.search_data">
                                <div class="drawer-item-dt"></div>
                                <div class="drawer-item-dd">
                                    <el-form-item label="搜索按钮色值" label-width="110px" :prop="'search_data.button_color'" :rules="{ required: true, message: '请选择搜索按钮色值', trigger: 'change' }">
                                        <el-color-picker v-model="templateSetForm.search_data.button_color"></el-color-picker>
                                    </el-form-item>
                                    <el-form-item label="搜索文字色值" label-width="110px" :prop="'search_data.search_font_color'" :rules="{ required: true, message: '请选择搜索文字色值', trigger: 'change' }">
                                        <el-color-picker v-model="templateSetForm.search_data.search_font_color"></el-color-picker>
                                    </el-form-item>
                                    <el-form-item label="热搜词"></el-form-item>
                                    <dl>
                                        <dt>
                                            <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                                <li class="s-flex ai-ct jc-ct" style="width: 300px;">
                                                    <span>*</span>
                                                    <label>关键词</label>
                                                </li>
                                                <li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">
                                                    <span>*</span>
                                                    <label>URL链接</label>
                                                </li>
                                                <li class="s-flex ai-ct jc-ct" style="width: 90px;">排序</li>
                                                <li class="s-flex ai-ct jc-ct" style="width: 100px; margin: 0;">操作</li>
                                            </ul>
                                        </dt>
                                        <dd class="s-flex flex-dir ai-ct">
                                            <ul class="s-flex ai-ct" style="height: 50px; padding: 0 40px;" v-for="(item, index) in templateSetForm.search_data.items">
                                                <li style="width: 300px;">
                                                    <el-form-item :key="index" :prop="'search_data.items.' + index + '.keywords'" :rules="
                                                    [
                                                        { required: true, message: '请输入关键词', trigger: 'blur' },
                                                        { max: 15, message: '关键词不能超过15个字符', trigger: 'blur' },
                                                        { validator: (rule, value, callback) => {
                                                            //  校验是否有相同的名称
                                                            const same_name = templateSetForm.search_data.items.filter(data => value == data.keywords)
                                                            if (same_name.length > 1) { callback(new Error('该关键词已存在，请修改！')); } else { callback(); }
                                                        }, trigger: 'blur' },
                                                    ]
                                                ">
                                                        <el-input v-model="item.keywords" placeholder="请输入关键词" ></el-input>
                                                    </el-form-item>
                                                </li>
                                                <li class="s-flex ai-ct jc-ct flex-1" style="width: 400px;">
                                                    <el-form-item :ref="`search_data_alias${index}`" :prop="'search_data.items.' + index + '.type'" :rules="{ required: true, message: '请选择链接类型', trigger: 'blur' }">
                                                        <el-select class="radius-right-none__input border-right-none__input" filterable clearable v-model="item.type" placeholder="请选择" @change="item.value = '', item.default_selection_data = [], clearFormValidate(`search_data_alias${index}`), clearFormValidate(`search_data_value${index}`)" @clear="item.value = ''" style="width: 200px;">
                                                            <el-option v-for="option in options" :key="option.alias" :label="option.name" :value="option.alias"></el-option>
                                                        </el-select>
                                                    </el-form-item>
                                                    <el-form-item :ref="`search_data_value${index}`" :prop="'search_data.items.' + index + '.value'" :rules="{ required: computedOptionsPlaceholder(item.type) != '', message: computedOptionsPlaceholder(item.type) || '请选择链接类型', trigger: 'blur' }">
                                                        <el-select v-model="item.value"
                                                           v-if="computedOptionsPlaceholder(item.type) && computedOptionsCanRemote(item.type)"
                                                           filterable
                                                           remote
                                                           reserve-keyword
                                                           clearable
                                                           :remote-method="(query) => handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.router_search') !!}', method: 'post', params: { keywords: query, alias: item.type }, parent: `search_data.items.${index}`, target: 'default_selection_data', index })"
                                                           @change="(value) => handleRemoteSearchChose({ parent: `search_data.items.${index}.default_selection_data`, chose: item.value, ref: `search_data_value${index}` })"
                                                           @focus="handleRemoteSearchChose({ parent: `search_data.items.${index}.default_selection_data`, chose: item.value })"
                                                           :placeholder="computedOptionsPlaceholder(item.type)" style="width: 330px">
                                                            <el-option v-for="option in item.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                        </el-select>
                                                        <el-input class="radius-left-none__input" v-else v-model="item.value" clearable :disabled="(!computedOptionsPlaceholder(item.type) && item.type != '' && item.type != null) || (!item.type && !item.value)" :placeholder="computedOptionsPlaceholder(item.type) ? computedOptionsPlaceholder(item.type) : !computedOptionsPlaceholder(item.type) && item.type != '' && item.type != null ? '系统生成链接' : ''" style="width: 330px;"></el-input>
                                                    </el-form-item>
                                                </li>
                                                <li style="width: 90px;">
                                                    <el-form-item :prop="'search_data.items.' + index + '.sort'" :rules="
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
                                                <li style="width: 100px; margin: 0;">
                                                    <div class="drawer-btn cursorp s-flex ai-ct jc-ct" @click="handleClickDeleteData(index, `search_data.items`)">删除</div>
                                                </li>
                                            </ul>
                                            <p class="warning-nodata" v-if="!templateSetForm.search_data.items.length">暂无数据！</p>
                                            <div class="drawer-btn primary s-flex ai-ct jc-ct" :class="{ disabled: templateSetForm.search_data.items.length >= 10 }" @click="handleClickAddData('search_data.items', 10, '热搜词', { 'keywords': '', sort: '1', 'value': '', 'type': 'search' })">添加（@{{templateSetForm.search_data.items.length}}/10）</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </template>
                        <template v-if="templateSetForm.nav_data && slideIndex == 2">
                            <div class="drawer-item" v-if="templateSetForm.nav_data">
                                <div class="drawer-item-dd" style="padding: 20px 0 0;">
                                    <el-form-item label="文字默认颜色" label-width="110px" :prop="'nav_data.font_default_color'" :rules="{ required: true, message: '请选择文字默认颜色', trigger: 'change' }">
                                        <el-color-picker v-model="templateSetForm.nav_data.font_default_color"></el-color-picker>
                                        <p class="warning-text">用于改变导航专题中文字的默认颜色</p>
                                    </el-form-item>
                                    <el-form-item label="文字选中颜色" label-width="110px" :prop="'nav_data.font_selection_color'" :rules="{ required: true, message: '请选择文字选中颜色', trigger: 'change' }">
                                        <el-color-picker v-model="templateSetForm.nav_data.font_selection_color"></el-color-picker>
                                        <p class="warning-text">用于改变标签栏中文字的选中颜色</p>
                                    </el-form-item>
                                </div>
                                <div class="drawer-item-dt">
                                    <div class="s-flex ai-ct jc-bt">
                                        <div>
                                            <p>最多添加10个导航专题</p>
                                            <p>页面按照导航页面显示，若设置的导航＜3，则页面不显示导航栏，只显示首页（推荐）</p>
                                            <p>关联页面：前往<a @click="parent.openTab('产业链专题合集', '{!! route('manage.app_web_decoration.children_index') !!}' + '?alias=industrial')" style="color: #278ff0; cursor: pointer;">产业链专题合集</a>中创建页面。</p>
                                        </div>
                                        <div class="drawer-btn s-flex ai-ct jc-ct cursorp" :class="{ disabled: templateSetForm.nav_data.items.length >= 10 }" @click="handleClickAddData('nav_data.items', 10, '导航', { icon: '', title: '', sort: '1', value: '', fixed_position: '' })">
                                            <em class="iconfont">&#xe620;</em>
                                            <label>添加（@{{ templateSetForm.nav_data.items.length }}/10）</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="drawer-item-dd">
                                    <dl>
                                        <dt>
                                            <ul class="s-flex ai-ct" style="padding: 0 40px;">
                                                <li class="s-flex ai-ct jc-ct" style="width: 90px;">
                                                    <span>*</span>
                                                    <label>ICON</label>
                                                    <el-tooltip
                                                            style="margin: 0 20px 0 0;"
                                                            effect="light"
                                                            placement="right"
                                                            width="200"
                                                            trigger="hover">
                                                        <div slot="content" class="drawer-upload-warning">
                                                            <p>支持jpg、jpeg、png、gif格式</p>
                                                            <p>建议尺寸：26px*26px</p>
                                                            <p>建议大小：2M内</p>
                                                        </div>
                                                        <em class="iconfont cursorp">&#xe72d;</em>
                                                    </el-tooltip>
                                                </li>
                                                <li class="s-flex ai-ct jc-ct" style="width: 300px;">
                                                    <span>*</span>
                                                    <label>页面名称</label>
                                                </li>
                                                <li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">
                                                    <span>*</span>
                                                    <label>关联页面</label>
                                                </li>
                                                <li class="s-flex ai-ct jc-ct" style="width: 90px;">排序</li>
                                                <li class="s-flex ai-ct jc-ct" style="width: 100px; margin: 0;">操作</li>
                                            </ul>
                                        </dt>
                                        <dd class="s-flex flex-dir ai-ct">
                                            <ul class="s-flex ai-ct" style="height: 90px; padding: 0 40px;" v-for="(item, index) in templateSetForm.nav_data.items">
                                                <li style="width: 90px;">
                                                    <el-form-item :key="index" :prop="'nav_data.items.' + index + '.icon'" :rules="{ required: index == (templateSetForm.nav_data.items.length - 1), message: '请上传图片', trigger: 'blur' }">
                                                        <div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
                                                            <template v-if="index == (templateSetForm.nav_data.items.length - 1)">
                                                                <img v-if="item.icon" :src="item.icon" alt="">
                                                                <div  class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!item.icon" @click="handleClickUploadFile({ parent: `nav_data.items.${index}`, validate: 2, target: 'icon' })">
                                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
                                                                        <em class="iconfont">&#xe727;</em>
                                                                    </div>
                                                                    <p>未上传</p>
                                                                </div>
                                                                <div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
                                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `nav_data.items.${index}`, validate: 2, target: 'icon' })">
                                                                        <em class="iconfont">&#xe727;</em>
                                                                    </div>
                                                                    <div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile(`nav_data.items.${index}`, 'icon')">
                                                                        <em class="iconfont">&#xe738;</em>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                            <p v-else>不支持</p>
                                                        </div>
                                                    </el-form-item>
                                                </li>
                                                <li style="width: 300px;">
                                                    <el-form-item :key="index" :prop="'nav_data.items.' + index + '.title'" :rules="
                                                    [
                                                        { required: true, message: '请输入页面名称', trigger: 'blur' },
                                                        { max: 5, message: '页面名称不能超过5个字符', trigger: 'blur' },
                                                        { validator: (rule, value, callback) => {
                                                            //  校验是否有相同的名称
                                                            const same_name = templateSetForm.nav_data.items.filter(data => value == data.title)
                                                            if (same_name.length > 1) { callback(new Error('该页面名称已存在，请修改！')); } else { callback(); }
                                                        }, trigger: 'blur' },
                                                    ]
                                                ">
                                                        <el-input :disabled="index == 0 || index == templateSetForm.nav_data.items.length - 1" v-model="item.title" placeholder="请输入页面名称" ></el-input>
                                                    </el-form-item>
                                                </li>
                                                <li class="s-flex ai-ct jc-ct flex-1">
                                                    <el-form-item :key="index" :ref="`nav_data${index}`" style="width: 100%;" :prop="'nav_data.items.' + index + '.value'" :rules="[{ required: true, message: '请关联页面', trigger: 'blur' }]">
                                                        {{-- multiple filterable remote reserve-keyword :remote-method="remoteMethod"--}}
                                                        <el-select v-model="item.value"
                                                                   filterable
                                                                   remote
                                                                   reserve-keyword
                                                                   clearable
                                                                   :disabled="index == 0 || index == (templateSetForm.nav_data.items.length - 1)"
                                                                   :remote-method="(query) => handleRemoteSearchChange({ path: '{!! route('manage.app_web_decoration.get_options') !!}', method: 'post', params: { keywords: query, page_type: 6 }, parent: `nav_data.items.${index}`, target: 'default_selection_data', index })"
                                                                   @change="(value) => handleRemoteSearchChose({ parent: `nav_data.items.${index}.default_selection_data`, list: templateSetForm.nav_data.items, index, chose: templateSetForm.nav_data.items, child_value: 'value', ref: `nav_data${index}`, selected: item.url })"
                                                                   @focus="handleRemoteSearchChose({ parent: `nav_data.items.${index}.default_selection_data`, chose: templateSetForm.nav_data.items, child_value: 'value' })"
                                                                   placeholder="请关联页面" style="width: 100%;">
                                                            <el-option v-for="option in item.default_selection_data" :key="option.value" :disabled="option.is_disabled" :label="option.label" :value="option.value"></el-option>
                                                        </el-select>
                                                    </el-form-item>
                                                </li>
                                                <li style="width: 90px;">
                                                    <el-form-item :prop="'nav_data.items.' + index + '.sort'" v-if="item.fixed_position != 'left' && item.fixed_position != 'right'" :rules="
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
                                                <li style="width: 100px; margin: 0;">
                                                    <div class="drawer-btn cursorp s-flex ai-ct jc-ct" v-if="item.fixed_position != 'left' && item.fixed_position != 'right'" @click="handleClickDeleteData(index, `nav_data.items`)">删除</div>
                                                </li>
                                            </ul>
                                            <p class="warning-nodata" v-if="!templateSetForm.nav_data.items.length">暂无数据！</p>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </template>
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

<!--模块遮罩组件-->
@include('manage.app_website_decoration.components.template-mark-setting')
<!--广告轮播组件-->
@include('manage.app_website_decoration.components.carousel-swiper')
<!--tabs tab按钮插件-->
@include('manage.app_website_decoration.components.drawer-tabs')
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
    Vue.component('home-navigation', {
        props: {
            is_show_setting: {
                type: Boolean,
                default: true
            },
            temp_index: [Number, String],
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
            warning_text: {
                type: String,
                default: ''
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
                is_show_load: false,
                templateSetFormName: '',    //  设置弹窗标题
                templateSetFormType: '',    //  设置组件别名
                templateSetFormIndex: '',    //  设置组件索引值
                templateSetForm: {},    //  设置表单数据
                templateSetRule: {},
                slideIndex: 0,
                is_show_imageview: false,
                image_view: '',
                nav_data_tabs: [],  //  导航中间tab
                nav_data_left: {},  //  导航左侧数据
                nav_data_right: {},  //  导航右侧数据
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
        template: '#home-navigation',
        methods: {
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting (data) {
                const { name, item, index } = data
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
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
            handleClickDeleteFile (parent, target) {
                let newValue = this.getNestedProperty(this.templateSetForm, parent)
                this.$set(newValue, target, '');
            },
            /** 校验图片格式 **/
            beforeAvatarUpload(file) {
                return new Promise(resolve => {
                    let resovlt = true
                    if (file && file.type) {
                        const isPNG = file.type === 'image/png'
                        const isJPEG = file.type === 'image/jpeg'
                        const isJPG = file.type === 'image/jpg'
                        const isLt2M = file.size / 1024 / 1024 < this.uploadSize;
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
                            this.msg_error = this.$message.error(`图片大小不能超过${this.uploadSize}M`);
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
                const { path, method, params, parent, target, index } = data
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
                    if (path == 'router_search') {
                        newList && this.handleRemoteSearchChose({ parent: `search_data.items.${index}.default_selection_data`, chose: newList })
                    } else {
                        this.handleRemoteSearchChose({ parent: `nav_data.items.${index}.default_selection_data`, chose: this.templateSetForm.nav_data.items, child_value: 'value' })
                    }
                    setTimeout(() => this.$forceUpdate(), 200)
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
                const item = JSON.parse(JSON.stringify(data))
                let newValue = this.getNestedProperty(this.templateSetForm, dom)
                /*const is_null = newValue.filter(item => item.keywords == '' || item.type == '' || item.value == '')
                if (is_null.length) {
                    this.$message.error('请先完善数据再进行添加')
                    return false
                }*/
                if (newValue.length >= len) {
                    this.msg_error && this.msg_error.close()
                    this.msg_error = this.$message.error(`最多可添加${len}个${warning_text}`)
                    return false
                }
                if (dom == 'nav_data.items') {
                    const firstData = newValue.filter(item => item.fixed_position == 'left')
                    const centerData = newValue.filter(item => item.fixed_position != 'left' && item.fixed_position != 'right')
                    const lastData = newValue.filter(item => item.fixed_position == 'right')
                    centerData.push(item)
                    newValue = [...firstData, ...centerData, ...lastData]
                    this.$set(this.templateSetForm.nav_data, 'items', newValue)
                } else {
                    newValue.push(item)
                }
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
                        if (this.templateSetForm.base_data.logo == '') {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请上传LOGO`)
                            return false
                        }
                        if (this.templateSetForm.base_data.top_bg_image == '') {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请上传导航背景图`)
                            return false
                        }
                        if (this.templateSetForm.base_data.home_bg_image == '') {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请上传首页背景图`)
                            return false
                        }
                        if (this.templateSetForm.base_data.sign_in.is_show) {
                            if (this.templateSetForm.base_data.sign_in.icon == '') {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error(`请上传签到ICON`)
                                return false
                            }
                            if (this.templateSetForm.base_data.sign_in.font_color == '') {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error(`请选择签到文字颜色`)
                                return false
                            }
                        }
                        if (this.templateSetForm.base_data.notice.is_show) {
                            if (this.templateSetForm.base_data.notice.icon == '') {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error(`请上传消息ICON`)
                                return false
                            }
                            if (this.templateSetForm.base_data.notice.font_color == '') {
                                this.msg_error && this.msg_error.close()
                                this.msg_error = this.$message.error(`请选择消息文字颜色`)
                                return false
                            }
                        }

                        if (!this.templateSetForm.search_data.items.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error('至少添加1个热搜词')
                            return false
                        }
                        const search_keyword_null = this.templateSetForm.search_data.items.filter(item => item.keywords == '')
                        const search_type_null = this.templateSetForm.search_data.items.filter(item => item.type == '')
                        // const search_value_null = this.templateSetForm.search_data.items.filter(item => item.value == '')
                        if (this.templateSetForm.search_data.button_color == '') {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请选择搜索按钮颜色`)
                            return false
                        }
                        if (this.templateSetForm.search_data.search_font_color == '') {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请选择搜索文字颜色`)
                            return false
                        }
                        if (search_keyword_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请选择搜索文字颜色`)
                            return false
                        }
                        if (search_type_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请选择链接类型`)
                            return false
                        }
                        /*if (search_value_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请输入链接地址`)
                            return false
                        }*/

                        const nav_icon_null = this.templateSetForm.nav_data.items.filter((item, index) => index == (this.templateSetForm.nav_data.items.length - 1) && item.icon == '')
                        const nav_title_null = this.templateSetForm.nav_data.items.filter(item => item.title == '')
                        const same_name = this.templateSetForm.nav_data.items.filter((item, index) => {
                            if (this.templateSetForm.nav_data.items[index + 1]) {
                                return this.templateSetForm.nav_data.items[index + 1].title == item.title
                            }
                        })
                        const nav_value_null = this.templateSetForm.nav_data.items.filter(item => item.value == '')
                        const nav_sort_error = this.templateSetForm.nav_data.items.filter((item, index) => index != 0 && index != (this.templateSetForm.nav_data.items.length - 1) && item.sort != '' && (!Number.isInteger(item.sort * 1) || item.sort < 1 || item.sort > 100))
                        if (this.templateSetForm.nav_data.font_default_color == '') {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请选择文字默认颜色`)
                            return false
                        }
                        if (this.templateSetForm.nav_data.font_selection_color == '') {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请选择文字选中颜色`)
                            return false
                        }
                        if (nav_icon_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请上传导航ICON图片`)
                            return false
                        }
                        if (nav_title_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请输入页面名称`)
                            return false
                        }
                        if (same_name.length > 1) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`页面导航名称已存在，请修改！`)
                            return false
                        }
                        if (nav_value_null.length) {
                            this.msg_error && this.msg_error.close()
                            this.msg_error = this.$message.error(`请关联导航页面`)
                            return false
                        }
                        /*if (nav_sort_error.length) {
                            this.$message.error(`页面导航排序填写有误`)
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
                                if (this.temp_item && this.temp_item.data && this.temp_item.data.is_show_nav_data) {
                                    const left_data = this.temp_item.data.nav_data.items.filter(item => item.fixed_position == 'left')
                                    const right_data = this.temp_item.data.nav_data.items.filter(item => item.fixed_position == 'right')
                                    if (left_data.length) this.temp_item.data.nav_data_left = left_data[0]
                                    if (right_data.length) this.temp_item.data.nav_data_right = right_data[0]
                                    this.temp_item.data.nav_data_tabs = this.temp_item.data.nav_data.items.filter(item => !item.fixed_position)
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
        },
        mounted () {}
    })
</script>
