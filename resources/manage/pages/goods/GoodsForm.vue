<template>
    <div class="s-flex jc-bt" ref="wrapRef" v-loading="pageDataLoading">
        <div class="manage-public-wrap update-box flex-1">
            <el-form ref="updateFormRef" :model="updateForm" :rules="updateFormRules" label-width="150px">
                <div class="manage-public-cont good-form" ref="baseRef">
                    <div class="manage-public-title">基础信息</div>
                    <div>
                        <el-form-item label="分类" prop="category_id">
                            <el-cascader v-model="updateForm.category_id" style="width: 360px;" :options="category" @change="selectedCategory" placeholder="请选择分类" :props="{value:'id',label:'name'}"/>
                        </el-form-item>
                        <el-form-item label="商品名称" prop="name">
                            <el-input v-model="updateForm.name" placeholder="请输入商品名称" style="width: 450px;" maxlength="60" show-word-limit></el-input>
                        </el-form-item>
                        <el-form-item label="商品标签" prop="label">
                            <el-input v-model="updateForm.label" :maxlength="6" show-word-limit placeholder="可填写热卖，推荐等" style="width: 450px;"></el-input>
                            <el-popover
                                placement="right"
                                title=""
                                width="auto"
                                trigger="hover"
                                content="用于在商品名称前加一个标记">
                                <template #reference>
                                    <em class="iconfont ml-10" style="cursor: pointer;">&#xe72d;</em>
                                </template>
                            </el-popover>
                        </el-form-item>
                        <el-form-item label="商品副标题" prop="sub_name">
                            <el-input v-model="updateForm.sub_name" style="width: 450px;" placeholder="请输入商品副标题" maxlength="60" show-word-limit></el-input>
                            <el-popover
                                placement="right"
                                title=""
                                width="auto"
                                trigger="hover"
                                content="用于在商品名称下面的二级小标题，一般起说明商品的作用">
                                <template #reference>
                                    <em class="iconfont ml-10" style="cursor: pointer;">&#xe72d;</em>
                                </template>
                            </el-popover>
                        </el-form-item>
                        <el-form-item label="产品参数">
                            <div class="s-flex">
                                <div style="min-width: 500px;">
                                    <div class="prop-wrap" v-if="updateForm.parameters.length">
                                        <div class="input-li s-flex ai-ct" v-for="(item,index) in updateForm.parameters" :key="index" style="margin: 5px 0;">
                                            <div style="width: 380px;" class="s-flex jc-bt ai-ct">
                                                <el-input v-model="item.name" placeholder="请输入属性名" style="width: 185px;" :maxlength="6" show-word-limit></el-input>
                                                <el-input v-model="item.value" placeholder="请输入属性值" style="width: 185px;" :maxlength="20"></el-input>
                                            </div>
                                            <div class="dels" style="margin-left: 10px;" @click="delGoodsAttr(index)">
                                                <span class="cu-p">删除</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="width: 100%;">
                                        <div>
                                            <el-button type="danger" @click="addGoodsAttr">添加</el-button>
                                            <el-button @click="ctrlParameterTemplate('save')" v-if="updateForm.parameters.length">存为模板</el-button>
                                            <el-button @click="ctrlParameterTemplate('update')" v-if="currentParameterTemplate&&updateForm.parameters.length">更新模板</el-button>
                                        </div>

                                        <div class="tips" v-if="!updateForm.parameters.length">
                                            <span class="co-999 fs14">可设置自定义属性，如内存：8G</span>
                                        </div>
                                    </div>
                                </div>
                                <div style="width: 200px;" ref="selectParameterTemplateRef" class="attr-select-template">
                                    <el-select placeholder="属性模板" v-model="currentParameterTemplate" :append-to="selectParameterTemplateRef" @change="selectedParameterTemplate">
                                        <el-option v-for="(templateItem,i) in parameterTemplateArr" :key="templateItem.id" :value="templateItem.id" :label="templateItem.name">
                                            <div class="attr-custom-item">
                                                <div class="s-flex ai-ct">{{ templateItem.name }} <i class="iconfont icon-bianji" @click.prevent.stop="updateParameterTemplate(templateItem,i,'edit')"></i> <i class="iconfont icon-shanchu" @click.prevent.stop="updateParameterTemplate(templateItem,i,'delete')"></i></div>
                                                <p class="co-999 fs14">更新时间: {{ templateItem.updated_at }}</p>
                                            </div>
                                        </el-option>
                                    </el-select>
                                </div>
                            </div>
                        </el-form-item>
                    </div>
                </div>
                <div class="manage-public-cont good-form" ref="imgRef">
                    <div class="manage-public-title">图文信息</div>
                    <div>
                        <el-form-item label="图片" prop="images">
                            <div class="s-flex">
                                <VueDraggable class="good-picture s-flex" v-model="updateForm.images" filter=".masking">
                                    <div class="good-picture-list" v-for="(img,imgIndex) in updateForm.images" :key="imgIndex">
                                        <div class="good-picture-li">
                                            <el-image
                                                style="width: 84px; height: 84px"
                                                :src="img"
                                                :zoom-rate="1.2"
                                                :max-scale="7"
                                                :min-scale="0.2"
                                                :preview-src-list="updateForm.images"
                                                show-progress
                                                :initial-index="4"
                                                fit="cover"
                                            />
                                            <div class="masking">
                                                <i class="iconfont icon-shangchuan" @click.stop.prevent="changeImages(imgIndex, 'http')" title="从本地上传图片"></i>
                                                <i class="iconfont icon-shangchuan1" @click="changeImages(imgIndex, 'material')" title="从素材库选择"></i>
                                                <i class="iconfont icon-shanchu" v-if="imgIndex" @click="updateForm.images.splice(imgIndex,1)" title="删除当前图片"></i>
                                            </div>
                                        </div>
                                        <div class="main" :class="{'main-img' : !imgIndex}" @click="setMain(imgIndex)">
                                            <span v-if="!imgIndex">主图</span>
                                            <span v-else>设为主图</span>
                                        </div>
                                    </div>
                                </VueDraggable>
                                <el-upload
                                    v-if="updateForm.images.length < 6"
                                    class="avatar-uploader"
                                    ref="uploadImageRef"
                                    accept="image/jpeg,image/jpg,image/png"
                                    :show-file-list="false"
                                    :http-request="(file) => uploadImage(file, 'images')"
                                    :before-upload="beforeUpload">
                                    <i class="iconfont icon-jiahao1"></i>
                                </el-upload>
                            </div>
                            <div class="tips" style="width: 100%;flex: none;">
                                <span>建议尺寸500*500px，最多6张</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="主图视频" prop="video">
                            <div class="s-flex">
                                <div class="good-picture s-flex" v-if="updateForm.video">
                                    <div class="good-picture-list">
                                        <div class="good-picture-li" style="cursor: default;">
                                            <video :src="updateForm.video"></video>
                                            <div class="video-play">
                                                <i class="iconfont icon-bofang1" @click="playVideo"></i>
                                            </div>
                                            <div class="masking">
                                                <i class="iconfont icon-shangchuan" @click="changeVideo('http')" title="从本地上传图片"></i>
                                                <i class="iconfont icon-shangchuan1" @click="changeVideo('material')" title="从素材库选择"></i>
                                                <i class="iconfont icon-shanchu" @click="updateForm.video = ''" title="删除当前主图视频"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <el-upload
                                    v-if="!updateForm.video"
                                    class="avatar-uploader"
                                    ref="uploadVideoRef"
                                    accept="video/mp4"
                                    :show-file-list="false"
                                    :http-request="(file) => uploadImage(file, 'video')"
                                    :before-upload="beforeUploadVideo">
                                    <i class="iconfont icon-jiahao1"></i>
                                </el-upload>
                            </div>
                            <div class="tips" style="width: 100%;flex: none;">
                                <span>仅支持mp4格式上传，大小100M内</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="商品详情" prop="content">
                            <div style="width: 100%;height: 500px;background: #f2f2f2;" v-if="pageDataLoading"></div>
                            <Editor v-model="updateForm.content" @change="handleChangeGoodsDetail" height="500px" min-height="500px" v-else/>
                        </el-form-item>
                    </div>
                </div>
                <div class="manage-public-cont good-form" ref="priceRef">
                    <div class="manage-public-title">价格库存</div>
                    <div>
                        <el-form-item label="单位" prop="unit">
                            <el-input v-model="updateForm.unit" placeholder="请输入商品单位" style="width: 160px;"></el-input>
                            <div class="tips" style="width: 100%;flex: none;">
                                <span>选填，可输入件，公斤等，多单位可在下面商品规格名中标注</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="商品规格">
                            <div class="specifications s-flex">
                                <div style="min-width: 500px;">
                                    <div class="specifications-box" v-if="goodsSkuTemplate.values.length">
                                        <el-form ref="templateFormRef" :model="goodsSkuTemplate" :rules="templateRules">
                                            <el-card class="specifications-list" style="padding-top: 0;width: 600px;" v-for="(item,index) in goodsSkuTemplate.values" :key="index">
                                                <div class="s-flex jc-fe" style="height: 20px;">
                                                    <div style="cursor: pointer;" @click="delGoodsSpecs(index)">
                                                        <em class="iconfont" style="font-size: 14px;">&#xe79b;</em>
                                                    </div>
                                                </div>
                                                <div class="specifications-content s-flex jc-bt">
                                                    <div class="left">
                                                        <div class="label">
                                                            <span>名称</span>
                                                        </div>
                                                        <el-form-item :prop="'values.' + index + '.name'" style="margin: 3px 0;">
                                                            <el-input v-model="item.name" placeholder="请输入规格名" maxlength="4" style="width: 120px;margin-right: 10px;"></el-input>
                                                        </el-form-item>
                                                        <div class="tips" style="padding-top: 8px;">
                                                            <span class="fs12" style="line-height: 16px;">名称如颜色、尺码等，最长4个字</span>
                                                        </div>
                                                    </div>
                                                    <div class="right">
                                                        <div class="label">
                                                            <span>名称</span>
                                                        </div>
                                                        <div class="specifications-input">
                                                            <template v-for="(its,ids) in item.values">
                                                                <el-form-item :prop="'values.' + index + '.values.' + ids + '.name'" style="margin: 3px 0;">
                                                                    <el-input v-model="its.name"
                                                                              placeholder="请输入规格值"
                                                                              maxlength="6"
                                                                              style="width: 150px;margin-right: 10px;"
                                                                              :key="ids">
                                                                        <template #suffix>
                                                                            <i style="font-size: 12px;cursor: pointer;" class="iconfont" @click="delSpecs(index,ids)">&#xe79b;</i>
                                                                        </template>
                                                                    </el-input>
                                                                </el-form-item>
                                                            </template>
                                                            <template v-if="goodsSkuTemplate.values[index].values.length < 6">
                                                                <el-popover
                                                                    placement="right"
                                                                    title=""
                                                                    width="auto"
                                                                    trigger="hover"
                                                                    :disabled="computedSpecs(index)"
                                                                    content="请填写完当前规格项">
                                                                    <template #reference>
                                                                        <el-link type="primary" :underline="false" style="margin: 3px 0;" :disabled="!computedSpecs(index)" @click="addSpecs(index)"><i class="iconfont">&#xe727;</i>新增规格项</el-link>
                                                                    </template>
                                                                </el-popover>
                                                            </template>
                                                        </div>
                                                        <div class="tips" style="padding-top: 8px;">
                                                            <span class="fs12" style="line-height: 16px;">规格项最长为6个字，最多可添加6个规格项。</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </el-card>
                                        </el-form>
                                    </div>
                                    <div class="specifications-btn">
                                        <el-button type="danger" @click="addGoodsSpecs()" v-if="goodsSkuTemplate.values.length < 3">添加</el-button>
                                        <template v-if="goodsSkuTemplate.values.length">
                                            <el-button type="primary" @click="ctrlSkuTemplate('save')" v-if="goodsSkuTemplate.values.length">保存模板</el-button>
                                            <el-button type="primary" @click="ctrlSkuTemplate('update')" v-if="currentSkuTemplate && goodsSkuTemplate.values.length">更新模板</el-button>
                                        </template>
                                    </div>
                                </div>
                                <div class="specifications-select s-flex jc-fe attr-select-template" ref="selectedSkuTemplateRef">
                                    <el-select placeholder="请选择模板" v-model="currentSkuTemplate" style="width: 160px;position: relative;height: 40px;left: -160px;" :append-to="selectedSkuTemplateRef" @change="changeSkuTemplate">
                                        <el-option v-for="(item,index) in specificationsArr" :key="item.id" :value="item.id" :label="item.name">
                                            <div class="attr-custom-item">
                                                <div class="s-flex ai-ct">{{ item.name }} <i class="iconfont icon-bianji" @click.prevent.stop="updateSkuTemplate(item,index,'update')"></i> <i class="iconfont icon-shanchu" @click.prevent.stop="updateSkuTemplate(item,index,'destroy')"></i></div>
                                                <p class="co-999 fs14">更新时间: {{ item.updated_at }}</p>
                                            </div>
                                        </el-option>
                                    </el-select>
                                </div>
                            </div>
                            <div class="tips" style="width: 100%;">
                                <span>可添加多个规格属性的商品</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="价格与库存" v-if="goodsSkuTemplate.values.length" class="is-required">
                            <template #label>
                                <label class="el-form-item__label">价格与库存</label>
                            </template>
                            <div class="more-input s-flex jc-fe" style="width: 100%;">
                                <div class="more-li" v-if="settings.is_open_integral">
                                    <label>
                                        <span>积分</span>
                                    </label>
                                    <el-input v-model="moreInput.integral" style="width: 80px;" size="small" placeholder="" @input="moreInput.integral = formatInput(moreInput.integral)"></el-input>
                                </div>
                                <div class="more-li">
                                    <label>
                                        <span>价格</span>
                                    </label>
                                    <el-input v-model="moreInput.price" style="width: 80px;" size="small" placeholder="" @input="moreInput.price = formatInput(moreInput.price)"></el-input>
                                </div>
                                <div class="more-li">
                                    <label>
                                        <span>库存</span>
                                    </label>
                                    <el-input v-model="moreInput.number" style="width: 80px;" placeholder="" size="small"  @input="moreInput.number = formatInput(moreInput.number)"></el-input>
                                </div>
                                <el-button @click="filling()">批量填充</el-button>
                            </div>
                            <el-table
                                :data="updateForm.sku_data"
                                :span-method="objectSpanMethod"
                                border
                                style="width: 100%; margin-top: 20px">
                                <el-table-column
                                    v-for="(its,ids) in goodsSkuTemplate.values"
                                    :prop="`template_${ids + 1}`"
                                    :label="its.name?its.name:'--'"
                                    align="center"
                                    :width="100">
                                    <template #default="scope">
                                        <span>{{ scope.row[`template_${ids + 1}`]?scope.row[`template_${ids + 1}`]:'--' }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="thumb"
                                    label="颜色图片"
                                    align="center"
                                    :width="120">
                                    <template #default="scope">
                                        <el-upload
                                            action=""
                                            class="table-upload"
                                            :show-file-list="false"
                                            accept="image/jpeg,image/jpg,image/png"
                                            v-if="!scope.row.thumb"
                                            :http-request="(file) => uploadImage(file, 'thumb', scope.row.template_1)"
                                            :before-upload="beforeUpload">
                                            <i class="iconfont icon-jiahao1 avatar-uploader-icon"></i>
                                        </el-upload>
                                        <div class="s-flex ai-ct jc-ct" v-else style="width: 100%;">
                                            <div class="thumb">
                                                <img :src="scope.row.thumb" alt="">
                                                <span class="el-upload-list__item-actions">
                                                    <i class="iconfont icon-shanchu" @click="handleTableRemove(scope.$index)"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column v-if="settings.is_open_integral" label="积分" align="center" min-width="120">
                                    <template #default="scope">
                                        <el-form-item :prop="'sku_data.' + scope.$index + '.integral'" :rules="moreIntegralPrice(scope.$index)">
                                            <el-input v-model="scope.row.integral" placeholder="" @input="scope.row.integral = formatInput(scope.row.integral)"></el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>
                                <el-table-column label="价格" align="center" min-width="120">
                                    <template #default="scope">
                                        <el-form-item :prop="'sku_data.' + scope.$index + '.price'" :rules="moreIntegralPrice(scope.$index)">
                                            <el-input v-model="scope.row.price" placeholder="" @input="scope.row.price = formatInput(scope.row.price)"></el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    label="库存"
                                    align="center"
                                    min-width="120">
                                    <template #default="scope">
                                        <el-form-item :prop="'sku_data.' + scope.$index + '.number'" :rules="updateFormRules.number">
                                            <el-input v-model="scope.row.number" placeholder="" @input="scope.row.number = formatInput(scope.row.number)"></el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>
                                <el-table-column label="是否显示" :width="100" align="center">
                                    <template #default="scope">
                                        <el-switch v-model="scope.row.is_show" :active-value="1" :inactive-value="0" active-color="#13ce66" inactive-color="#ff4949"></el-switch>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <div class="tips" v-if="goodsSkuTemplate.values.length">
                                <span>给第一组规格设置图片，用户选择不同规格会看到对应规格图片，建议尺寸：80 x 80 px</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="价格" v-else>
                            <div class="s-flex ai-ct">
                                <el-form-item prop="integral" v-if="settings.is_open_integral">
                                    <el-checkbox v-model="integralMoneyShow" @change="(val) => setCheck(val,'integral')">积分</el-checkbox>
                                    <el-input style="width: 100px;margin:0 10px;" @input="updateForm.integral = formatInput(updateForm.integral)" v-model="updateForm.integral" placeholder=""></el-input>
                                </el-form-item>
                                <el-form-item prop="price">
                                    <el-checkbox v-if="settings.is_open_integral" v-model="shopPriceShow" @change="(val) => setCheck(val,'price')">现金</el-checkbox>
                                    <el-input style="width: 100px;" v-model="updateForm.price" @input="updateForm.price = formatInput(updateForm.price)" placeholder=""><template #suffix>元</template></el-input>
                                </el-form-item>
                            </div>
                        </el-form-item>
                        <el-form-item label="库存" prop="total">
                            <el-input-number v-model="updateForm.total" :disabled="!!updateForm.sku_data.length" :min="1" style="width: 160px;"></el-input-number>
                            <div class="tips" v-if="goodsSkuTemplate.values.length" style="width: 100%;flex: none;">
                                <span>多规格商品库存为所有SKU的库存总和</span>
                            </div>
                        </el-form-item>
                        <el-form-item label="订单库存" prop="type">
                            <el-radio-group v-model="updateForm.type">
                                <el-radio :value="1">下单减库存</el-radio>
                                <el-radio :value="2">付款减库存</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </div>
                </div>
                <div class="manage-public-cont good-form" ref="serviceRef">
                    <div class="manage-public-title">服务售后</div>
                    <div>
                        <el-form-item label="上架" prop="status">
                            <el-radio-group v-model="updateForm.status">
                                <el-radio :value="1">立即上架</el-radio>
                                <el-radio :value="0">放入仓库</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="限购" prop="can_quota" class="limit-number">
                            <div>
                                <el-radio-group v-model="updateForm.can_quota" @change="changeCanQuota">
                                    <el-radio :value="0">不限购</el-radio>
                                    <el-radio :value="1">限购</el-radio>
                                </el-radio-group>
                                <div v-if="updateForm.can_quota">
                                    <el-input-number v-model="updateForm.quota_number" :min="1"></el-input-number>
                                    <div class="tips" style="width: 100%;flex: none;">
                                        <span>用户总购买数量不超过{{ updateForm.quota_number }} {{ updateForm.unit }}</span>
                                    </div>
                                </div>
                            </div>
                        </el-form-item>
                    </div>
                </div>
            </el-form>
            <div class="goods-footer-btn s-flex jc-ct ai-ct">
                <el-button type="danger" style="width: 140px;height: 50px;font-size: 20px;" @click="submitGoods" :loading="loading">发布商品</el-button>
            </div>
        </div>
        <div style="width: 380px;flex: none;">
            <div class="right-sidebar">
                <div class="right-header">
                    <i class="iconfont icon-mulu mr-10 co-fff"></i>
                    <span class="co-fff fs18 fw-b">商品信息</span>
                </div>
                <div class="right-menu">
                    <div class="right-menu-item" :class="{active: activeContType === 'baseRef'}" @click="scrollToCont('baseRef')">基础信息</div>
                    <div class="right-menu-item" :class="{active: activeContType === 'imgRef'}" @click="scrollToCont('imgRef')">图文信息</div>
                    <div class="right-menu-item" :class="{active: activeContType === 'priceRef'}" @click="scrollToCont('priceRef')">价格库存</div>
                    <div class="right-menu-item" :class="{active: activeContType === 'serviceRef'}" @click="scrollToCont('serviceRef')">服务售后</div>
                </div>
            </div>
        </div>
        <el-dialog v-model="videoDialogShow" title="视频预览" width="600px" center>
            <video :src="updateForm.video" controls ref="videoRef" width="100%"></video>
        </el-dialog>
        <el-dialog title="图片裁剪" v-model="cropperDialogShow" width="700px" center :show-close="false">
            <div class="cropper-wrap bg-fff" style="width: 660px;height: 660px;">
                <vue-cropper
                    ref="cropperRef"
                    :autoCrop="true"
                    mode="cover"
                    :autoCropWidth="500"
                    :autoCropHeight="500"
                    :fixedBox="true"
                    :img="currentFileCropBlob">
                </vue-cropper>
            </div>
            <div class="s-flex ai-ct jc-ct" style="padding-top: 15px;">
                <el-button type="primary" @click="cropImageConfirm">确定裁剪</el-button>
                <el-button type="default" @click="cropperDialogShow = false, currentFileCropBlob = null">取消</el-button>
            </div>
        </el-dialog>
        <input type="file" ref="fileImgRef" style="display: none" @change="(event) => {handleFileChange(event, 'image')}" accept="image/*">
        <input type="file" ref="fileVideoRef" style="display: none" @change="(event) => {handleFileChange(event, 'video')}" accept="video/*">
    </div>

</template>

<script setup>
import Editor from '@/components/good/Editor.vue'
import { ref, getCurrentInstance, onMounted, computed, watch, onBeforeUnmount, nextTick } from 'vue'
import {
    goodsDetailInit,
    getGoodsSkuTemplate,
    getGoodsParameterTemplate,
    goodsParameterTemplateStore,
    goodsParameterTemplateUpdate,
    goodsParameterTemplateDestroy,
    goodsSkuTemplateStore,
    goodsSkuTemplateUpdate,
    goodsSkuTemplateDestroy,
    goodsUpdate
} from '@/api/goods';
import { fileUpload } from '@/api/common'
import _ from 'lodash'
import { VueCropper }  from "vue-cropper";
import 'vue-cropper/dist/index.css'
import { VueDraggable } from 'vue-draggable-plus'
import { useRoute,useRouter } from 'vue-router';
const router = useRouter()
const route = useRoute();

const cns = getCurrentInstance().appContext.config.globalProperties
const pageDataLoading = ref(true)

/* 校验模板名称 */
const validatorTemplateName = (rule, value, callback) => {
    if (value == '' || value == null) {
        callback(new Error('模板名称不能为空'));
    }else if(value.length > 20){
        callback(new Error('模板名称不能超过10个字符'));
    }else{
        callback();
    }
}

/* 商品分类 */
const category = ref([]);
/* 商品分类选择触发函数 */
const selectedCategory = (item) => {
    if (item == undefined) {
        updateForm.value.category_id = '';
    } else {
        updateForm.value.category_id = item[parseInt(item.length) - 1]
    }
}

/* 产品参数模板 */
const selectParameterTemplateRef = ref(null)
const currentParameterTemplate = ref('')
const parameterTemplateArr = ref([])
const updateParameterTemplate = (item,i,type) => {
    if(type == 'edit'){
        cns.$dialog.prompt({message: '修改属性模板',inputValue: item.name,inputPlaceholder: '请输入模板名称',inputValidator: validatorTemplateName}).then(({ value }) => {
            goodsParameterTemplateUpdate({id: item.id, name: value}).then(res => {
                if (res.code === 200) {
                    cns.$message.success('修改产品参数模板成功')
                    getParameterTemplate()
                } else {
                    cns.$message.error(res.message)
                }
            })
        })
    }else {
        cns.$dialog.confirm({message: '确定删除该模板？'}).then(() => {
            parameterTemplateArr.value.splice(i, 1);
            goodsParameterTemplateDestroy({id: item.id}).then(res => {
                if (res.code === 200) {
                    cns.$message.success('删除产品参数模板成功')
                    getParameterTemplate()
                } else {
                    cns.$message.error(res.message)
                }
            })
        }).catch(() => {
        });
    }
}
const selectedParameterTemplate = (value) => {
    if(value){
        parameterTemplateArr.value.forEach((item,i) => {
            if(item.id == value){
                updateForm.value.parameters = item.values
            }
        })
    }
}
const ctrlParameterTemplate = (type) => {
    if(type == 'save'){
        cns.$dialog.prompt({message: '存为新属性模板',inputValue: '',inputPlaceholder: '请输入模板名称',inputValidator: validatorTemplateName}).then(({ value }) => {
            goodsParameterTemplateStore({ name: value, values: updateForm.value.parameters }).then(res => {
                if (res.code === 200) {
                    getParameterTemplate(res.data.id)
                    cns.$message.success('保存产品参数模板成功')
                } else {
                    cns.$message.error(res.message)
                }
            })
        })
    }else if(type == 'update'){
        goodsParameterTemplateUpdate({ id: currentParameterTemplate.value, values: updateForm.value.parameters }).then(res => {
            if (res.code === 200) {
                cns.$message.success('更新产品参数模板成功')
            } else {
                cns.$message.error(res.message)
            }
        })
    }
}
const getParameterTemplate = (id) => {
    getGoodsParameterTemplate().then(res => {
        if (res.code === 200) {
            parameterTemplateArr.value = [...res.data]
            currentParameterTemplate.value = id
        }
    })
}

/* 商品规格模板 */


// 图片裁剪
const currentFileCropBlob = ref(null);
const cropperRef = ref(null);
const cropperDialogShow = ref(false);

const activeContType = ref('baseRef') // 基础信息：baseRef;图文信息：imgRef;价格库存：priceRef;服务售后：serviceRef
const wrapRef = ref(null);
const baseRef = ref(null);
const imgRef = ref(null);
const priceRef = ref(null);
const serviceRef = ref(null);
const scrollToCont = (type) => {
    const element = {
        baseRef: baseRef,
        imgRef: imgRef,
        priceRef: priceRef,
        serviceRef: serviceRef
    }[type];
    activeContType.value = type;
    if (element && element.value) {
        element.value.scrollIntoView({ behavior: 'smooth' });
    }
}
const handleScroll = () => {
    const baseTop = baseRef.value.getBoundingClientRect().top;
    const imgTop = imgRef.value.getBoundingClientRect().top;
    const priceTop = priceRef.value.getBoundingClientRect().top;
    const serviceTop = serviceRef.value.getBoundingClientRect().top;

    if (baseTop < window.innerHeight && baseTop >= 0) {
        activeContType.value = 'baseRef';
    } else if (imgTop < window.innerHeight && imgTop >= 0) {
        activeContType.value = 'imgRef';
    } else if (priceTop < window.innerHeight && priceTop >= 0) {
        activeContType.value = 'priceRef';
    } else if (serviceTop < window.innerHeight && serviceTop >= 0) {
        activeContType.value = 'serviceRef';
    }
};

const validateFile = (rule, value, callback) => {
    if (!updateForm.value.images.length) {
        callback(new Error('请上传商品图片'));
    } else {
        callback();
    }
}

const validatePrice = (rule, value, callback, type) => {
    if (!updateForm.value.sku_data.length && updateForm.value[type === 'price' ? 'integral' : 'price'] < 0 && Number(value) < 0) {
        callback(new Error('积分和价格不能同时小于0'));
    } else if (updateForm.value.sku_data.length) {
        const checkPrices = (data) => {
            return data.filter(item => {
                const shopPrice = parseFloat(item.price) || 0;
                const integralMoney = parseFloat(item.integral) || 0;
                return shopPrice < 0 && integralMoney < 0;
            });
        };
        const invalidItems = checkPrices(updateForm.value.sku_data);
        if (invalidItems.length) {
            callback(new Error('积分和价格不能同时小于0'));
        } else {
            callback();
        }
    } else {
        callback();
    }
}

const validateContent = (rule, value, callback) => {
    if (updateForm.value.content == '' || updateForm.value.content == '<p style="color: rgb(51, 51, 51); line-height: 2;"><br></p>' || updateForm.value.content == '<p style="line-height: 2; color: rgb(51, 51, 51);"><br></p>') {
        callback(new Error('商品详情不能为空'));
    } else {
        callback();
    }
}

const specValue = (rule, value, callback, index, id) => {
    let arr = goodsSkuTemplate.value.values[index].values
    const hasDuplicates = arr.reduce((acc, current) => {
        if (acc.names[current.name]) {
            acc.hasDuplicates = true;
        } else {
            acc.names[current.name] = true;
        }
        return acc;
    }, {names: {}, hasDuplicates: false}).hasDuplicates;
    if (hasDuplicates) {
        callback(new Error('规格项重复'));
    } else {
        callback();
    }
}

const updateForm = ref({
    id: 0,
    category_id: null,
    name: '',
    label: '',
    sub_name: '',
    parameters: [],
    images: [],
    content: '',
    video: '',
    video_duration: 0,
    unit: '',
    price: 0,
    integral:0, //积分价格
    total: 0,
    type: 1,
    status: 1,
    can_quota: 0,
    quota_number: 0,
    sku_data: [],
    spec_data: [],
});

// 图文相关
const isInitContent = ref(true);
const uploadImageRef = ref(null);
const fileImgRef = ref(null);
const fileVideoRef = ref(null);
const uploadVideoRef = ref(null);
const videoDialogShow = ref(false);
const videoRef = ref(null);
const currentChangeImageIndex = ref(-1);

const updateFormRef = ref(null);
const templateFormRef = ref(null);
const selectedSkuTemplateRef = ref(null);
const updateFormRules = ref({
    category_id: [
        { required: true, message: '请选择商品分类', trigger: 'change' },
    ],
    name: [
        {required: true, message: '请输入商品名称', trigger: 'blur'},
    ],
    images: [
        {required: true, message: '请上传商品图片', trigger: 'change'},
        {validator: validateFile, trigger: 'change'}
    ],
    total: [
        { required: true, message: '请输入商品库存', trigger: 'blur' },
    ],
    content: [
        { required: true, message: '请输入商品详情', trigger: 'blur' },
        {validator: validateContent, trigger: 'blur'}
    ],
    status: [
        { required: true, message: '请选择是否上架', trigger: 'change' },
    ],
    can_quota: [
        { required: true, message: '请选择是否限购', trigger: 'blur' },
    ],
    quota_number: [
        { required: true, message: '请输入限购数量', trigger: 'blur' },
    ],
    price: [
        { required: false, message: '请输入价格', trigger: 'blur' },
        { validator: (rule, value, callback) => validatePrice(rule, value, callback, 'price'), trigger: 'blur' }
    ],
    integral: [
        { required: false, message: '请输入积分价格', trigger: 'blur' },
        { validator: (rule, value, callback) => validatePrice(rule, value, callback, 'integral'), trigger: 'blur' }
    ],
    number: [
        { required: true, message: '请输入商品库存', trigger: 'blur' },
    ]
});
/* 设置信息 */
const settings = ref({
    is_open_integral: false
})
// 价格/规格 相关
const shopPriceShow = ref(false);
const integralMoneyShow = ref(false);
const specificationsArr = ref([]);
const goodsSkuTemplate = ref({ name: '', template_id: null, values: [], });
const moreInput = ref({ price: '', integral: '', number: '' });
const loading = ref(false);

const changeCanQuota = () => {
  if (updateForm.value.can_quota == 0) {
      updateForm.value.quota_number = 1
  }
}

const templateRules = computed(() => {
    const rules = {};
    goodsSkuTemplate.value.values.forEach((field, index) => {
        rules[`values.${index}.name`] = [
            { required: true, message: '请输入规格名称', trigger: 'blur' }
        ];
        field.values.forEach((fd, id) => {
            rules[`values.${index}.values.${id}.name`] = [
                { required: true, message: '请输入规格项', trigger: 'blur' },
                { validator: (rule, value, callback) => specValue(rule, value, callback, index, id), trigger: 'blur' }
            ];
        })
    });
    return rules;
});

watch(() => goodsSkuTemplate.value.values, _.debounce((newVal, oldVal) => {
    if (newVal.length) {
        toTableArray(newVal)
    } else {
        updateForm.value.sku_data = []
        updateForm.value.spec_data = []
    }
}, 500), { deep: true, immediate: false });

watch(() => updateForm.value.price, (val) => {
    if (val && val > 0) {
        shopPriceShow.value = true
    } else {
        shopPriceShow.value = false
    }
});

watch(() => updateForm.value.integral, (val) => {
    if (val && val > 0) {
        integralMoneyShow.value = true
    } else {
        integralMoneyShow.value = false
    }
});

watch(() => updateForm.value.sku_data, (val) => {
    if (val.length) {
        let numberArr = Array.from(val, a => a.number)
        let sum = numberArr.reduce((accumulator, currentValue) => Number(accumulator) + Number(currentValue), 0);
        updateForm.value.total = sum
    }
}, { deep: true, immediate: false });

const moreIntegralPrice = (index) => {
    let skus = updateForm.value.sku_data[index]
    const prop1 = `sku_data.${index}.integral`;
    const prop2 = `sku_data.${index}.price`;
    return [
        {
            validator: (rule, value, callback) => {
                if (Number(skus.price) < 0 && Number(skus.integral) < 0) {
                    callback(new Error(`价格和积分必须大于0`));
                } else {
                    updateFormRef.value.clearValidate(prop1);
                    updateFormRef.value.clearValidate(prop2);
                    callback();
                }
            },
            trigger: 'blur',
        },
    ]
}
/* 获取规格模板 */
const getSkuTemplate = () => {
    getGoodsSkuTemplate().then(res => {
        if (res.code === 200) {
            specificationsArr.value = [...res.data]
        }
    })
}

const handleChangeGoodsDetail = () => {
    if(!isInitContent.value){
        updateFormRef.value.validateField('content')
    }else {
        isInitContent.value = false
    }
}

const addGoodsAttr = () => {
    updateForm.value.parameters.push({ name: '', value: '' })
}

const delGoodsAttr = (index) => {
    updateForm.value.parameters.splice(index, 1)
}

const delGoodsSpecs = (index) => {
    goodsSkuTemplate.value.values.splice(index, 1)
}

const addGoodsSpecs = () => {
    goodsSkuTemplate.value.values.push({ name: '', id: '', values: [{ name: '', id: '' }] })
}

const computedSpecs = (index) => {
    return goodsSkuTemplate.value.values[index].values.every(a => a.name)
}

const addSpecs = (index) => {
    if (computedSpecs(index) && goodsSkuTemplate.value.values[index].values.length < 6) {
        goodsSkuTemplate.value.values[index].values.push({ name: '', id: '' })
    }
}

const delSpecs = (index, index2) => {
    if (goodsSkuTemplate.value.values[index].values.length === 1) {
        goodsSkuTemplate.value.values[index].values[index2].name = ''
    } else {
        goodsSkuTemplate.value.values[index].values.splice(index2, 1)
    }
}

const objectSpanMethod = ({row, column, rowIndex, columnIndex}) => {
    const field = column.property;
    if (field && field.startsWith('template')) {
        let spanCount = 1;
        for (let i = rowIndex + 1; i < updateForm.value.sku_data.length; i++) {
            if (updateForm.value.sku_data[i][field] === row[field]) {
                spanCount++;
            } else {
                break;
            }
        }
        if (rowIndex === 0 || updateForm.value.sku_data[rowIndex - 1][field] !== row[field]) {
            return {rowspan: spanCount, colspan: 1};
        } else {
            return {rowspan: 0, colspan: 0};
        }
    }
    return {rowspan: 1, colspan: 1};
}

const toTableArray = (specs) => {
    const result = [];
    const existingSkus = updateForm.value.sku_data || [];

    function helper(current, index) {
        if (index === specs.length) {
            // 检查是否存在匹配的现有SKU
            const existingSku = existingSkus.find(sku => {
                // 检查所有template字段是否匹配
                return specs.every((spec, idx) => {
                    const templateKey = `template_${idx + 1}`;
                    return sku[templateKey] === current[templateKey];
                });
            });

            // 如果找到匹配的SKU，使用其值
            if (existingSku) {
                result.push({
                    ...current,
                    id: existingSku.id,
                    goods_id: existingSku.goods_id,
                    thumb: existingSku.thumb,
                    price: existingSku.price,
                    integral: existingSku.integral,
                    number: existingSku.number,
                    is_show: existingSku.is_show
                });
            } else {
                // 如果没有找到匹配的SKU，使用默认值
                result.push({
                    ...current,
                    id: null,
                    goods_id: null,
                    thumb: '',
                    price: '',
                    integral: '',
                    number: '',
                    is_show: 1
                });
            }
            return;
        }

        const spec = specs[index];
        const templateName = `template_${index + 1}`;
        spec.values.forEach(value => {
            const next = {...current};
            next[templateName] = value.name;
            helper(next, index + 1);
        });
    }

    helper({}, 0);
    updateForm.value.sku_data = [...result];
    updateForm.value.spec_data = [...specs];
}

const formatInput = (value) => {
    let match = value.match(/^-?\d*\.?\d{0,9}/);
    return match ? match[0] : '';
}

const handleTableRemove = (index) => {
    updateForm.value.sku_data[index].thumb = ''
}

const filling = () => {
    let newArray = updateForm.value.sku_data.map(item => {
        return {
            ...item,
            integral: moreInput.value.integral === '' ? item.integral : moreInput.value.integral,
            price: moreInput.value.price === '' ? item.price : moreInput.value.price,
            number: moreInput.value.number === '' ? item.number : moreInput.value.number
        }
    })
    updateForm.value.sku_data = [...newArray]
    Object.keys(moreInput.value).map(its => {
        if(moreInput.value[its]){
            for(let i = 0; i < updateForm.value.sku_data.length; i++){
                updateFormRef.value.clearValidate('sku_data.' + i + '.' + its);
            }
        }
    })
    moreInput.value = { integral: '', price: '', number: '' }
}

const setMain = (index) => {
    if (index) {
        let picture = updateForm.value.images.splice(index, 1)
        updateForm.value.images.unshift(picture[0])
    }
}
const beforeUpload = (file) => {
    let validateType = false;
    if (file.type != "image/jpg" && file.type != "image/jpeg" && file.type != "image/png") {
        if (file.type) {
            validateType = true
        }
    }
    const isLt2M = file.size / 1024 / 1024 <= 5;
    if (validateType || !isLt2M) {
        cns.$message.error("支持 .png .jpg .jpeg格式，单个附件不得超过5M!");
        return false;
    }else {
        return true
    }
}

const beforeUploadVideo = (file) => {
    let validateType = false;

    if (file.type != "video/mp4") {
        if (file.type) {
            validateType = true
        }
    }
    const isLt100M = file.size / 1024 / 1024 <= 100;
    if (validateType || !isLt100M) {
        cns.$message.error("仅支持mp4格式上传，大小100M内");
        return false;
    }
}
const changeImages = (i , type) => {
    currentChangeImageIndex.value = i
    if(type == 'http'){
        nextTick(() => {
            fileImgRef.value.click()
        })
    }else{
        // 调用素材
    }
}

const handleFileChange = (event, type)=> {
    const files = event.target.files;
    if (files.length > 0) {
        if(type == 'image'){
            if(beforeUpload(files[0])){
                uploadImage({file:files[0]}, 'images')
            }
        }else if(type == 'video'){
            if(beforeUploadVideo(files[0])){
                uploadImage({file:files[0]}, 'video')
            }
        }

    }
}

const changeVideo = (type) => {
    if(type == 'http'){
        fileVideoRef.value.click()
    }else{
        // 调用素材
    }
}

const playVideo = () => {
    videoDialogShow.value = true
    nextTick(() => {
        videoRef.value.play()
    })
}
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

const blobToFile = (blob)=> {
    // 创建 File 对象
    return new File([blob],'裁剪图片.jpg');
}

const cropImageConfirm = () => {
    cropperRef.value.getCropBlob((data)=>{
        uploadImage({file:blobToFile(data)}, 'images-cropper')
    })
}
const uploadImage = async (file, type, name) => {
    if(type === 'images'){
        const dataUrl = await loadFile(file.file);
        currentFileCropBlob.value = dataUrl
        cropperDialogShow.value  = true
    }else {
        fileUpload(file).then((res) => {
            if (res.code == 200) {
                if (type === 'thumb') {
                    let sku_data = JSON.parse(JSON.stringify(updateForm.value.sku_data))
                    sku_data.map(a => {
                        if (a.template_1 === name) {
                            a.thumb = res.data.url
                        }
                    })
                    updateForm.value.sku_data = [...sku_data]
                } else if(type === 'video'){
                    updateForm.value.video = res.data.url
                }else {
                    cropperDialogShow.value  = false
                    if(currentChangeImageIndex.value>=0){
                        updateForm.value.images[currentChangeImageIndex.value] = res.data.url
                    }else {
                        updateForm.value.images.push(res.data.url)
                    }
                    currentChangeImageIndex.value = -1
                }
            }
        }).catch((err)=>{
            cns.$message.error("上传失败");
        })
    }
}

const setCheck = (val, type) => {
    updateForm.value[type] = val ? 1 : 0
}
/* 保存商品规格或更新商品规格模板 */
const ctrlSkuTemplate = (type) => {
    templateFormRef.value.validate((valid) => {
        if (valid) {
            if (!goodsSkuTemplate.value.name) {
                cns.$dialog.prompt({
                    message: '',
                    title: '请输入模板名称',
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /\S+/,
                    inputErrorMessage: '请输入模板名称'
                }).then(({value}) => {
                    goodsSkuTemplate.value.name = value
                    ctrlSkuTemplate(type)
                })
            } else {
                if (type === 'save') {
                    // 保存SKU模板
                    goodsSkuTemplateStore(goodsSkuTemplate.value).then(res => {
                        if (res.code === 200) {
                            cns.$message.success('保存模板成功')
                        } else {
                            cns.$message.error(res.message)
                        }
                        goodsSkuTemplate.value.name = ''
                        getSkuTemplate()
                    });
                } else if (type === 'update') {
                    // 更新SKU模板
                    goodsSkuTemplateUpdate(goodsSkuTemplate.value).then(res => {
                        if (res.code === 200) {
                            cns.$message.success('更新模板成功')
                        } else {
                            cns.$message.error(res.message)
                        }
                        goodsSkuTemplate.value.name = ''
                        getSkuTemplate()
                    });
                }
            }
        } else {
            cns.$message.error("请完善销售规格");
            return false
        }
    })
}
/* 修改模板名称或删除模板 */
const updateSkuTemplate = (item,i,type) => {
    if(type == 'update'){
        cns.$dialog.prompt({message: '修改商品规格模板',inputValue: item.name,inputPlaceholder: '请输入模板名称',inputValidator: validatorTemplateName}).then(({ value }) => {
            goodsSkuTemplateUpdate({id: item.id, name: value}).then(res => {
                if (res.code === 200) {
                    cns.$message.success('修改商品规格模板成功')
                    getSkuTemplate()
                } else {
                    cns.$message.error(res.message)
                }
            })
        })
    }else {
        cns.$dialog.confirm({message: '确定删除该模板？'}).then(() => {
            parameterTemplateArr.value.splice(i, 1);
            goodsSkuTemplateDestroy({id: item.id}).then(res => {
                if (res.code === 200) {
                    cns.$message.success('删除商品规格模板成功')
                    getSkuTemplate()
                } else {
                    cns.$message.error(res.message)
                }
            })
        }).catch(() => {
        });
    }
}

const currentSkuTemplate = ref('')

const changeSkuTemplate = (value) => {
    if(value){
        specificationsArr.value.forEach((item,i) => {
            if(item.id == value){
                goodsSkuTemplate.value.values = item.values
            }
        })
    }
}

const submitGoods = () => {
    loading.value = true
    updateFormRef.value.validate((valid) => {
        if (valid) {
            goodsUpdate(updateForm.value).then(res => {
                if (res.code === 200) {
                    cns.$message.success('保存商品成功')
                    loading.value = false
                    router.push({name: 'goods'})
                } else {
                    cns.$message.error(res.message)
                    loading.value = false
                }
            })
        } else {
            cns.$message.error("请完善商品信息");
            loading.value = false
            return false
        }
    })
}

onMounted(() => {
    goodsDetailInit({id:route.params.id}).then(res => {
        if (res.code === 200) {
            updateForm.value = {...res.data.info}
            category.value = res.data.category
            settings.value = res.data.settings
            pageDataLoading.value = false
            if(updateForm.value.spec_data&&updateForm.value.spec_data.length){
                goodsSkuTemplate.value.values = [...updateForm.value.spec_data]
            }
        } else {
            cns.$message.error(res.message)
            router.push({name: 'goods'})
        }
    })
    getSkuTemplate()
    getParameterTemplate()
    wrapRef.value.parentElement.addEventListener('scroll', handleScroll);
})

onBeforeUnmount(() => {
    wrapRef.value.parentElement.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped lang="scss">
:deep(.cropper-box){
    width: 660px;
    height: 660px;
    box-sizing: border-box; overflow: hidden;
}

.right-sidebar{
    width: 360px;
    background: #fff;
    height: fit-content;
    border-radius: 10px 10px 0 0 ;
    position: fixed;
    right: 20px;
    .right-header{
        background: linear-gradient(153deg, #1477F0 17%, rgba(90, 23, 176, 0) 131%);
        border-radius: 10px 10px 0 0 ;
        height: 90px;
        line-height: 80px;
        padding-left: 25px;
        i{
            font-size: 18px;
        }
    }
    .right-menu{
        position: relative;
        top:-10px;
        background: #fff;
        border-radius: 10px 10px 0 0 ;
        padding: 30px 0 10px;
        .right-menu-item{
            padding: 0 30px;
            height: 44px;
            line-height: 44px;
            font-size: 15px;
            color: var(--color-text);
            cursor: pointer;
            font-weight: 600;
            &:hover, &.active{
                background: var(--main-color-20);
                color: var(--main-color);
            }
        }
    }
}

.update-box {
    position: relative;
    padding-bottom: 80px;
    .goods-footer-btn{
        width: 100%;
        height: 80px;
        box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.3);
        position: fixed;
        right: 0;
        bottom: 0;
        background: #fff;
        z-index: 99;
        :deep(.el-button span){
            font-size: 18px;
            font-weight: 700;
        }
    }
    .good-form{
        border-radius: 10px;
        .prop-wrap{
            background: #f2f2f2;
            padding: 5px 10px;
            margin-bottom: 10px;
            width: 450px;
            .dels span {
                font-size: 12px;
                font-weight: normal;
                line-height: 22px;
                color: #333;
                &:hover{
                    color: var(--red-color);
                }
                cursor: pointer;
            }
        }
        .attr-select-template{
            :deep(.el-select-dropdown__item){
                height: 56px;
                line-height: 25px;
                padding: 3px 32px 3px 20px;
                &:hover{
                    background: var(--main-color-20);
                    i{
                        display: block;
                    }
                }
            }
            :deep(.el-select-dropdown__item.is-selected) p{
                color: var(--main-color);
            }
            :deep(.attr-custom-item){
                font-size: 14px;
                i{
                    margin-left: 5px;
                    font-size: 14px;
                    &:hover{
                        color: var(--red-color);
                    }
                    display: none;
                }
            }
        }

        :deep(.el-form-item__label) {
            color: #333;
        }

        padding-bottom: 20px;
        :deep(.avatar-uploader .el-upload) {
            border: 1px dashed #d9d9d9;
            border-radius: 6px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        :deep(.avatar-uploader .el-upload):hover {
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

        .tips span {
            font-size: 12px;
            font-weight: normal;
            line-height: 24px;
            color: #9E9E9E;
        }

        .limit-number .el-form-item__content {
            display: flex;
            align-items: baseline;
        }

        .table-upload :deep(.el-upload) {
            border: 1px dashed #d9d9d9;
            border-radius: 6px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .table-upload .avatar-uploader-icon {
            font-size: 28px;
            color: #8c939d;
            width: 48px;
            height: 48px;
            line-height: 48px;
            border-radius: 6px;
            text-align: center;
        }
    }
}
.update-box::-webkit-scrollbar {
    display: none; /* 针对 Chrome, Safari 和 Opera */
}


.specifications .specifications-box {
    margin-top: 40px;
    margin-bottom: 30px;
}

.specifications-box .specifications-list {
    width: 100%;
    background: #FFFFFF;
    margin-bottom: 10px;
    :deep(.el-card__body){
        padding-top: 5px;
    }
}

.specifications-box .specifications-list .specifications-content .left {
    width: 35%;
    padding-right: 15px;
}

.specifications-box .specifications-list .specifications-content .right {
    width: 65%;
    padding-left: 15px;
    position: relative;
}

.specifications-box .specifications-list .specifications-content .right::before {
    position: absolute;
    content: '';
    height: 100%;
    width: 1px;
    left: 0;
    border-left: solid 1px #eee;
}

.specifications-box .specifications-list .specifications-content .label span {
    font-size: 12px;
    font-weight: normal;
    line-height: 24px;
    color: #3D3D3D;
}

.specifications-input {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
}

.thumb {
    width: 48px;
    height: 48px;
    position: relative;
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    overflow: hidden;
}

.thumb img {
    width: 100%;
    height: 100%;
}

.thumb .el-upload-list__item-actions {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    i{
        color: #fff;
        cursor: pointer;
    }
}

.thumb:hover .el-upload-list__item-actions {
    display: flex;
    justify-content: center;
    align-items: center;
}

.thumb .el-icon-delete {
    line-height: 48px;
    text-align: center;
    width: 48px;
    color: #fff;
    cursor: pointer;
}

.more-input .more-li {
    width: 140px;
    display: flex;
}

.more-input .more-li label {
    font-size: 12px;
    color: #333;
    margin-right: 5px;
}
.good-picture .good-picture-list{
    margin-right: 10px;
}

.good-picture .good-picture-li {
    width: 84px;
    height: 84px;
    position: relative;
    cursor: move;
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
}

.good-picture .good-picture-li .el-image, .good-picture .good-picture-li video{
    width: 100%;
    height: 100%;
    border-radius: 6px;
}

.good-picture .good-picture-li .masking {
    position: absolute;
    width: 100%;
    height: 24px;
    left: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    cursor: default;
    display: none;
    justify-content: space-around;
    border-radius: 0 0 6px 6px;
    z-index: 2;
}
.good-picture .good-picture-li .video-play{
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    left: 0;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    z-index: 1;
    i{
        color: #fff;
        font-size: 24px;
        cursor: pointer;
    }
}
.good-picture .good-picture-li:hover .masking {
    display: flex;
}

.good-picture .good-picture-li .masking .iconfont {
    font-size: 14px;
    color: #fff;
    line-height: 24px;
    cursor: pointer;
}

.good-picture .main {
    border-radius: 15px;
    height: 25px;
    line-height: 23px;
    text-align: center;
    cursor: pointer;
    margin: 5px auto 0;
}

.good-picture .main span {
    color: var(--red-color);
    font-size: 12px;
}

</style>
