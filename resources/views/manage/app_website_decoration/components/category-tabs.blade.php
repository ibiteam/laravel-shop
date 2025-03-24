@include('manage.app_website_decoration.style.dialog-setting')
<style>
    .head-nav{
        padding: 20px 0;
        display: flex;
        justify-content: space-between;
        position: relative;
    }
    .head-nav .head-nav-box{
        width: 450px;
    }
    .head-nav .head-nav-more .mores{
        width: 58px;
        height: 58px;
        background: #FFFFFF;
        border-radius: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .head-nav .head-nav-more .mores i{
        font-size: 50px;
    }

	.tips{
		font-size: 12px;
		color: #ccc;
		line-height: 2;
	}
	.tips-popover{
		display: flex;
	}
	.tips-el{
		font-size: 14px;
		color: #ccc;
	}
    .dangers{
        background: #FFFFFF;
        border: 1px solid #409EFF;
    }
    .dangers span{
        color: #409EFF;
    }
</style>

<script type="text/x-template" id="category-tabs-template">
	<div class="head-nav s_flex jc_bt">
		{{--<public-mark-template title="分类数据" height="100%" alias="page_nav" @open="() => handleClickOpenTemplateSetting({ name: temp_item.name, item: temp_item, index: temp_index })"></public-mark-template>--}}
		<div class="head-nav-box">
			<div class="head-nav-ul s_flex">
				<div class="head-nav-li" v-for="(its,ids) in temp_item.data.items[left_indexs].names" :key="ids" :class="{'active' : ids === 0}">
					<span>@{{ its }}</span>
				</div>
			</div>
		</div>
		<div class="head-nav-more">
			<div class="mores s_flex jc_ct ai_ct" v-if="temp_item.data.items[left_indexs].names.length">
				<i class="iconfont" style="transform: rotateX(180deg);">&#xe6b2;</i>
			</div>
		</div>
		<el-drawer custom-class="category-drawer" :with-header="false" :modal-append-to-body="false" :close-on-press-escape="false" :show-close="false" :wrapper-closable="false" size="1200px" :visible.sync="show_category_drawer" direction="rtl">
			<input type="file" @change="handleChangeUploadFile" value="" style="display: none;" ref="upload_file" />
			<el-form :model="templateSetForm" :rules="templateSetRule" ref="templateSetForm" class="drawer-content" v-load="set_save_loading">
				<div class="drawer-name s-flex ai-ct">
					<label>设置板块：</label>
					<span>@{{ templateSetFormName }}</span>
				</div>
				<div style="height: calc(100vh - 150px); overflow-y: auto;" ref="drawerBox" v-if="templateSetForm.items">
					<div class="drawer-item" v-if="templateSetForm.items">
						<div class="drawer-item-dt">
							<div class="s-flex ai-ct jc-bt">
								<div>
									<p>推荐分类1~30个</p>
								</div>
								<div class="drawer-btn s-flex ai-ct jc-ct cursorp" :class="{ disabled: templateSetForm.items.length >= 30 }" @click="handleClickAddData('items', '30', '分类数据', publicListItem.channel)">
									<em class="iconfont">&#xe620;</em>
									<label>添加（@{{ templateSetForm.items.length }}/30）</label>
								</div>
							</div>
						</div>
						<div class="drawer-item-dd">
							<drawer-slides :slide_active.sync="slideIndex" target="alias" default_text="推荐分类" :list="templateSetForm.items"></drawer-slides>
							<div v-for="(item, index) in templateSetForm.items" :key="slideIndex" v-if="slideIndex == index">
								<div class="drawer-item-cate">
									<div class="cate-title s-flex ai-ct jc-bt" style="height: 40px;">
										<h1>板块内容</h1>
										<div class="drawer-btn s-flex ai-ct jc-ct cursorp" v-if="templateSetForm.items.length>1" @click="handleClickDeleteData(slideIndex, 'items', '确定要删除分类吗？')">
											<label>删除</label>
										</div>
									</div>

									<el-form-item label="推荐分类" label-width="110px" :prop="'items.' + index + '.cate_id'" :rules="{ required: true, message: '请选择推荐分类', trigger: ['change','blur'] }">
										<el-cascader
											v-model="item.cate_id"
											style="width: 500px;"
											placeholder="请输入分类ID或名称"
											:options="computedCategoryArr"
											filterable
											:props="{ checkStrictly: true , emitPath: false }"
											@change="(value) => changeCategory(value,item)"
											></el-cascader>
										<div class="tips">数据来源：商品分类表中状态是“显示在移动端”的数据</div>
									</el-form-item>
									<el-form-item label="分类别名" label-width="110px" :prop="'items.' + index + '.alias'" :rules="{ required: true, message: '请输入分类别名', trigger: 'blur' }">
										<el-input v-model="item.alias" style="width: 500px;" :maxlength="10"></el-input>
										<div class="tips">未填写别名，默认获取分类名称；配置别名，则获取别名。</div>
									</el-form-item>
									<el-form-item label="分类排序" label-width="110px" :prop="'items.' + index + '.sort'"  :rules="{validator: validateNumber,trigger: 'blur'}">
										<el-input v-model="item.sort" style="width: 500px;"></el-input>
										<div class="tips">分类从上到下依次显示，数字越大越靠上</div>
									</el-form-item>
									<el-form-item label="右侧子分类" label-width="110px">
										<div class="tips-el">
											<span>① 此处不支持修改，请前往《商品分类》表中修改。<a href="javascript:;" style="color: #0a8cd2" onclick="parent.openTab('商品分类列表', '{!! route('manage.category.index') !!}')">去修改</a></span>
										</div>
										<div class="tips-el">
											<span>② 显示规则：根据“推荐分类”自动显示其下所有显示状态的两层级子类</span>
										</div>
										<div class="tips-el">
											<span>③ 排序规则：按照分类排序大小倒序排列</span>
										</div>
									</el-form-item>
								</div>
								<div class="drawer-item-cate">
									<el-form-item label="趋势品类" label-width="110px">
										<el-switch
											v-model="item.is_trend_cate"
											active-text="显示"
											inactive-text="隐藏"
											active-value = "1"
											inactive-value = "0">
										</el-switch>
									</el-form-item>
									<el-form-item label="选择分类" v-if="item.is_trend_cate === '1'" label-width="110px">
										<div class="s-flex ai-ct">
											<el-cascader
												v-model="temporaryCategoriesArr"
												style="width: 500px;"
												:ref="`cascader-${index}`"
												placeholder="请输入分类ID或名称"
												:options="computedCategoryArrSecond"
												filterable
												:props="{ checkStrictly: true , emitPath: false , multiple:false }"
											></el-cascader>
											<div class="drawer-btn primary s-flex ai-ct jc-ct" style="margin-left: 20px;" :class="{ disabled: item.categories.length >= 15 }"
											     @click="changeItems(index,`cascader-${index}`,item)"
											>添加（@{{ item.categories.length }}/15）</div>
										</div>
									</el-form-item>
									<el-form-item label="已选择分类" v-if="item.is_trend_cate === '1'" label-width="110px" class="cate-choosed" :prop="'items.' + index + '.cate_data'" :rules="
                                        [
                                            { required: true, message: '请选择分类', trigger: 'change' },
                                        ]
                                    ">
										<ul class="s-flex ai-ct flex-wrap" v-if="item.cate_data.length">
											<li class="s-flex ai-ct cursorp" v-for="(child, childIndex) in item.cate_data">
												<span>@{{ child.label }}</span>
												<em class="iconfont" @click="deleteCategory(index,childIndex)">&#xe634;</em>
											</li>
										</ul>
										<p class="warning-text" style="line-height: 40px; font-size: 14px;" v-else>请添加并选择分类</p>
									</el-form-item>
								</div>
								<div class="drawer-item-cate">
									<el-form-item label="热卖品牌" label-width="110px">
										<el-switch
											v-model="item.is_hot_brands"
											active-text="显示"
											inactive-text="隐藏"
											active-value = "1"
											inactive-value = "0">
										</el-switch>
									</el-form-item>
									<dl v-if="item.is_hot_brands === '1'">
										<dt>
											<ul class="s-flex ai-ct" style="padding: 0 40px;">
												<li class="s-flex ai-ct jc-ct" style="width: 90px;">
													<span>*</span>
													<label>图片</label>
													<el-popover
														placement="right"
														width="200"
														trigger="hover">
														<div class="drawer-upload-warning">
															<p>支持jpg、jpeg、png格式</p>
															<p>建议尺寸：26px*26px</p>
															<p>建议大小：2M内</p>
														</div>
														<em slot="reference" class="iconfont cursorp">&#xe72d;</em>
													</el-popover>
												</li>
												<li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">名称</li>
												<li class="s-flex ai-ct jc-ct flex-1" style="width: 300px;">排序</li>
												<li class="s-flex ai-ct jc-ct" style="width: 90px;">操作</li>
											</ul>
										</dt>
										<dd class="s-flex flex-dir ai-ct">
											<ul class="s-flex ai-ct" style="height: 90px; padding: 0 40px;" v-for="(child, childIndex) in item.items">
												<li style="width: 90px;">
													<el-form-item :key="index" :prop="'items.' + index + '.items.' + childIndex + '.image'" :rules="{ required: true, message: '请上传图片', trigger: 'blur' }">
														<div class="drawer-upload s-flex ai-ct jc-ct flex-dir cursorp" style="width: 90px; height: 90px;">
															<img v-if="child.image" :src="child.image" alt="">
															<div class="drawer-upload-null s-flex flex-dir ai-ct jc-ct" v-if="!child.image" @click="handleClickUploadFile({ parent: `items.${index}.items.${childIndex}`, validate: 2, target: 'image'})">
																<div class="drawer-upload-icon s-flex ai-ct jc-ct" style="margin: 0 8px 10px 8px;">
																	<em class="iconfont">&#xe727;</em>
																</div>
																<p>未上传</p>
															</div>
															<div class="drawer-upload-mark s-flex ai-ct jc-ct" v-else>
																<div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickUploadFile({ parent: `items.${index}.items.${childIndex}`, validate: 2, target: 'image'})">
																	<em class="iconfont">&#xe727;</em>
																</div>
																<div class="drawer-upload-icon s-flex ai-ct jc-ct cursorp" @click="handleClickDeleteFile(`items.${index}.items.${childIndex}`, 'image')">
																	<em class="iconfont">&#xe738;</em>
																</div>
															</div>
														</div>
													</el-form-item>
												</li>
												<li class="s-flex ai-ct jc-ct flex-1">
													<el-form-item :prop="'items.' + index + '.items.' + childIndex + '.brand_id'" :rules="{ required: true, message: '请选择品牌', trigger: 'blur' }">
														<el-select v-model="child.brand_id" filterable style="width: 330px;" @change="(value) => changeSelect(value,index,childIndex)" placeholder="请输入品牌ID或名称">
															<el-option
																v-for="itemop in computedDrandArr"
																:key="itemop.value"
																:label="itemop.label"
																:value="itemop.value"
																:disabled="itemop.disabled">
															</el-option>
														</el-select>
													</el-form-item>
												</li>
												<li class="s-flex ai-ct jc-ct flex-1">
													<el-form-item :prop="'items.' + index + '.items.' + childIndex + '.sort'" :rules="[{validator: validateNumber,trigger: 'blur'}]">
														<el-input class="radius-left-none__input" v-model="child.sort" placeholder="1~100" style="width: 330px;"></el-input>
													</el-form-item>
												</li>
												<li style="width: 90px;">
													<el-button class="dangers" v-if="item.items.length > 1" @click="delHot(index,childIndex)">删除</el-button>
												</li>
											</ul>
											<div style="display: flex;justify-content:center;padding: 20px 0">
												<el-button :disabled="item.items.length >= 12" @click="handleClickAddImageData({ parent: `items.${index}`, target: 'items', max: 12, length: item.items.length, item: { brand_id: null, brand_name: '', image: '', sort: '' } })" type="primary">添加（@{{ item.items.length }}/12）</el-button>
											</div>
											<p class="warning-nodata" v-if="!item.items.length">暂无数据！</p>
										</dd>
									</dl>
								</div>
							</div>
						</div>

					</div>

				</div>
				<el-form-item style="margin: 0; box-shadow: 0 -0.2rem 0.2rem -0.22rem rgba(0, 0, 0, 0.2);">
					<div class="s-flex ai-ct jc-ct" style="margin: 20px 0;">
						<div style="width: 120px; margin: 0 15px;" class="drawer-btn primary s-flex ai-ct jc-ct" @click="handleClickSubmitSetForm('templateSetForm')">保存</div>
						<div style="width: 120px; margin: 0 15px;" class="drawer-btn s-flex ai-ct jc-ct" @click="handleClickCancelSetForm('templateSetForm')">取消</div>
					</div>
				</el-form-item>
			</el-form>
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

<!--模块遮罩插件-->
@include('manage.app_website_decoration.components.template-mark-setting')
<!--广告轮播插件-->
@include('manage.app_website_decoration.components.carousel-swiper')
<!--slide tab按钮插件-->
@include('manage.app_website_decoration.components.drawer-slides')<!--素材列表弹窗-->
<script>
	Vue.component('category-tabs-template',{
        props:{
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
            left_indexs:{
                type: Number,
                default: 0
            },
            upload_size_validate: {
                type: Number,
                default: 2
            },    //  上传大小限制，以兆为单位，默认2M
        },
		data(){
            this.validateNumber = (rule, value, callback) => {
                if(value){
                    const num = parseFloat(value);
                    if (isNaN(num)) {
                        callback(new Error("请输入有效的数字"));
                    } else if (num < 1) {
                        callback(new Error("排序最小值是1"));
                    }else if(num > 100){
                        callback(new Error("排序最大值是100"));
                    } else {
                        callback();
                    }
                }
                callback();
            }
            return {
                show_category_drawer:false,
                set_save_loading:false,
                templateSetForm:{

                },
                templateSetRule:{},
                templateSetFormName:'分类数据',
                categoryArr:[],
                brand:[],
                temporaryCategoriesArr:null,
                publicListItem: {
                    channel: {
                        cate_id: null,// 分类id
                        alias: "推荐分类",// 分类别名
                        sort: "",
                        is_trend_cate: "0",// 趋势品类
                        categories: [],// 选择分类
                        is_hot_brands: "0",// 热销品牌
                        items: [
                            {
                                brand_id: null,
                                brand_name: '',
                                image: '',
                                sort: ''
                            }
                        ],
                        cate_data: []
                    }
                },
                slideIndex:0,
                selectedValue:null,
                is_show_material_dialog: false,
                material_dialog_model: 'single',
                material_dialog_max_count: 1,
                material_dialog_selected_count: 0,
                uploadValidateType: 2
            }
		},
        // this.selectedValue = this.templateSetForm[0].id
        watch: {
            slideIndex: {
                handler(newName, oldName) {
                    if(this.show_category_drawer){
                        this.selectedValue = this.templateSetForm.items[newName].cate_id
                    }
                },
                immediate: true
            }

        },
        template: '#category-tabs-template',
        computed: {
            computedOptionsPlaceholder () {
                return function(value) {
                    const filter = this.options.filter(item => item.alias == value)
                    return filter.length ? filter[0].desc : ''
                }
            },
	        computedDrandArr(){
                let disabledDrandArr = Array.from(this.templateSetForm.items[this.slideIndex].items,its => its.brand_id)
                let arr = this.markItemsDisabled(this.brand,disabledDrandArr)
                return arr
	        },
            computedCategoryArr(){
                let ars = this.templateSetForm.items.filter((its,ids) => {
                    if(ids !== this.slideIndex){
                        return true
                    }
                })
                let disabledCategoryArr = Array.from(ars,its => its.cate_id)
	            let arr = this.markItemsDisabled(this.categoryArr,disabledCategoryArr)
	            return arr
            },
            computedCategoryArrSecond(){
                let cate_id = this.templateSetForm.items[this.slideIndex].cate_id
	            let arrCategory = this.markItemsIDArr(this.categoryArr,cate_id)
                let arr = this.markItemsDisabled(arrCategory,this.templateSetForm.items[this.slideIndex].categories)
	            return arr
            }
        },
		methods:{
            // disabled
            markItemsDisabled(data,valuesToDisable){
                // 对原始数据进行深拷贝以创建一个新数组
                const newData = JSON.parse(JSON.stringify(data));

                function recursiveMarkDisabled(items) {
                    for (let i = 0; i < items.length; i++) {
                        const item = items[i];

                        // 如果当前项的value在要禁用的值列表中，将disabled设为true
                        if (valuesToDisable.includes(item.value)) {
                            item.disabled = true;
                        }

                        // 如果当前项有子项，递归处理子项
                        if (item.children && Array.isArray(item.children)) {
                            recursiveMarkDisabled(item.children);
                        }
                    }
                }

                // 在新数组上递归标记disabled
                recursiveMarkDisabled(newData);

                return newData;
            },
			// 取子集
            markItemsIDArr(data,targetValue){
                for (let i = 0; i < data.length; i++) {
                    if (Number(data[i].value) === Number(targetValue)) {
                        return data[i].children || [];
                    }

                    if (data[i].children && data[i].children.length > 0) {
                        const result = this.markItemsIDArr(data[i].children, targetValue);
                        if (result.length > 0) {
                            return result;
                        }
                    }
                }
                return [];
            },
            /** 点击打开组件设置弹窗 */
            handleClickOpenTemplateSetting () {
                const name = this.temp_item.name
                const item = this.temp_item
                const index = this.temp_index
                this.templateSetFormName = name
                this.templateSetFormIndex = index || 0

                this.slideIndex = 0
                this.templateSetForm = item ? JSON.parse(JSON.stringify(item.content)) : {}
                this.templateSetFormType = item.component_name
                this.show_category_drawer = true
				this.selectedValue = this.templateSetForm.items[0].cate_id
	            this.getCategory()
                this.getBrandData()
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
            changeSelect(value,index,childIndex){
                let obj = this.brand.find(a => a.value === value)
                const newValue = this.getNestedProperty(this.templateSetForm, `items.${index}.items.${childIndex}`)
	            if(!newValue.image){
                    this.$set(newValue,'image',obj.brand_logo)
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
                                this.$message.error('仅限上传jpg、jpeg、png、gif格式的图片');
                                resolve(false)
                                return false
                            }
                        } else {
                            if (!isPNG && !isJPEG && !isJPG) {
                                this.$message.error('仅限上传jpg、jpeg、png格式的图片');
                                resolve(false)
                                return false
                            }
                        }
                        if (!isLt2M) {
                            this.$message.error(`图片大小不能超过${this.upload_size_validate}M`);
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
            handleClickDeleteFile (parent, target) {
                let newValue = this.getNestedProperty(this.templateSetForm, parent)
                this.$set(newValue, target, '');
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
            /** 下拉选中禁用 */
            handleRemoteSearchChose (data) {
                const { parent, chose, value = 'value', child_value } = data
                const choseType = Object.prototype.toString.call(chose).slice(8, -1)
                const newList = this.getNestedProperty(this.templateSetForm, parent)
                if (newList && newList.length) {
                    if (choseType == 'Array') {
                        newList.map(item => chose.some(child => item.is_disabled = item[value] == child[child_value]))
                    } else {
                        newList.map(item => item.is_disabled = item[value] == chose)
                    }
                }
                this.$forceUpdate()
            },
            /** 点击提交设置弹窗数据 */
            handleClickSubmitSetForm (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        let flag = -1
                        for(let i = 0; i < this.templateSetForm.items.length; i++) {
                            if(!this.templateSetForm.items[i].alias){
                                this.$message.error(`请将板块分类第【${i + 1}】选项表单完善`)
                                flag = i
	                            break
                            }else if(!this.templateSetForm.items[i].cate_id){
                                this.$message.error(`请将板块分类第【${i + 1}${this.templateSetForm.items[i].alias}】选项表单完善`)
                                flag = i
                                break
                            }else if(this.templateSetForm.items[i].is_trend_cate === '1' && !this.templateSetForm.items[i].categories.length){
                                this.$message.error(`请将板块分类第【${i + 1}${this.templateSetForm.items[i].alias}】选项表单完善`)
                                flag = i
                                break
                            }else{
                                if(this.templateSetForm.items[i].is_hot_brands === '1'){
	                                for(let j = 0; j < this.templateSetForm.items[i].items.length; j++){
	                                    if(!this.templateSetForm.items[i].items[j].image){
	                                        this.$message.error(`请将板块分类第【${i + 1}${this.templateSetForm.items[i].alias}】选项表单完善`)
	                                        flag = i
	                                        break
	                                    }else if(this.templateSetForm.items[i].items[j].sort && (this.templateSetForm.items[i].items[j].sort<1 || this.templateSetForm.items[i].items[j].sort>100)){
	                                        this.$message.error(`请将板块分类第【${i + 1}${this.templateSetForm.items[i].alias}】选项排序输入正确的值`)
	                                        flag = i
	                                        break
	                                    }else if(!this.templateSetForm.items[i].items[j].brand_id){
	                                        this.$message.error(`请将板块分类第【${i + 1}${this.templateSetForm.items[i].alias}】选项表单完善`)
	                                        flag = i
	                                        break
	                                    }
	                                }
                                }
                            }
                        }
                        if(flag === -1){
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
	                                this.handleClickCancelSetForm('templateSetForm')
                                } else {
                                    this.is_setting_save = true
                                    this.set_save_loading = false
                                    this.$message.error(res.message)
                                }
                            })
                        }else{
                            this.$set(this,'slideIndex',flag)
	                        setTimeout(() => {
                                this.$refs[formName].validate()
	                        },0)
                        }
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
                this.show_category_drawer = false
                // this.$emit('cancel')
            },
            /** 点击查看图片 */
            handleClickOpenPreviewImage (list, index, name) {
                this.imagePreviewList = list
                this.preview_index = index || 0
                this.preview_name = name || 'image'
                setTimeout(() => this.is_show_dialog = true, 400)
            },
            /** 点击添加组件数据 */
            async handleClickAddData (dom, len, warning_text, data, validate_name) {
                const item = await JSON.parse(JSON.stringify(data))
                let newValue = this.getNestedProperty(this.templateSetForm, dom)
                if (newValue.length >= len) {
                    this.$message.error(`最多可添加${len}个${warning_text}`)
                    return false
                }
                if (validate_name) {
                    const diff = newValue.filter(label => label[validate_name] == item[validate_name])
                    if (diff.length) {
                        this.$message.error('该选项已存在')
                        return false
                    }
                }
                newValue.push(item)
                setTimeout(() => {
                    this.slideIndex = newValue.length - 1
                },0)
            },
            /** 点击批量添加图片组件数据 */
            handleClickAddImageData (data) {
                let { type, target, image_name = 'image', max, length, parent, validate = 2, item } = data
                if (max > -1 && length >= max) {
                    return false
                }
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
            async handleClickDeleteData(index, parent, confirm){
                const newValue = await this.getNestedProperty(this.templateSetForm, parent)
                if (confirm) {
                    this.$confirm(confirm, '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning',
                        center: true
                    }).then(() => {
                        newValue.splice(index, 1)
                        this.$message.success('删除成功！')
	                    this.$set(this,'slideIndex',0)
                    }).catch(() => {});
                } else {
                    newValue.splice(index, 1)
                    this.$message.success('删除成功！')
                }
            },
            deleteCategory(index,childIndex){
                this.templateSetForm.items[index].cate_data.splice(childIndex,1)
                this.templateSetForm.items[index].categories.splice(childIndex,1)
                this.$message.success('删除成功！')
            },
            delHot(index,childIndex){
                const newValue = this.getNestedProperty(this.templateSetForm, `items`)
	            if(newValue[index].items.length < 2){
                    return false
	            }
                newValue[index].items.splice(childIndex,1)
            },
            changeItems(index,ref,item){
                if(!this.temporaryCategoriesArr || item.categories.length >= 15){
                    return false
                }
                let cateforyArr = this.$refs[ref][0].getCheckedNodes()
                const newValue = this.getNestedProperty(this.templateSetForm, `items`)
                this.$set(newValue[index],'cate_data',[
	                ...newValue[index].cate_data,
                    {
                        label: cateforyArr[0].data.label,
                        parent_id: cateforyArr[0].data.parent_id,
                        value: cateforyArr[0].data.value
                    }
                ])
                this.$set(newValue[index],'categories',[
                    ...newValue[index].categories,
                    cateforyArr[0].value
                ])
                this.temporaryCategoriesArr = null
            },
            getBrandData(){
                this.doPost('{!! route('manage.app_web_decoration.get_options') !!}', {page_type:10,keywords:''}).then(res => {
                    if(res.code === 200){
                        this.brand = res.data
                    }
                })
            },
			getCategory(){
                this.doPost('{!! route('manage.app_web_decoration.get_options') !!}', {page_type:9,keywords:''}).then(res => {
                    if(res.code === 200){
                        this.categoryArr = res.data
                    }
                })
			},
            addBrands(index){
                const newValue = this.getNestedProperty(this.templateSetForm, `items.${index}.items`)
                if(newValue.length >= 12)  return false
                let propsArr = Array.from(newValue, (a,b) => {
                    return [`items.${index}.items.${b}.brand_id`, `items.${index}.items.${b}.sort`, `items.${index}.items.${b}.image`]
                })
	            const flatArray = [].concat(...propsArr)
	            let flag = false
	            let length = 0
                this.$refs['templateSetForm'].validateField(flatArray, (valid) => {
                    if (!valid) {
                        // 校验通过后的操作
                        length+=1
	                    if(!flag && length === flatArray.length){
                            newValue.push({
                                brand_id:null,
                                brand_name:'',
                                image:'',
                                sort:''
                            })
	                    }
                    } else {
                        flag = true
                        return false;
                    }
                })
            },
            changeCategory(value,item){
                if(item.cate_data.length){
                    this.$confirm('修改后，您选择的趋势品类板块中的数据将会被清空。', '确定要修改分类吗？', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => { //确定
                        this.$set(this,'selectedValue',value)
                        this.$set(item,'cate_data',[])
                        this.$set(item,'categories',[])
                    }).catch(() => {
                        this.$set(this.templateSetForm.items,this.slideIndex,{
                            ...item,
                            cate_id:this.selectedValue
                        })
                    });
                }else{
                    this.$set(this,'selectedValue',value)
                }
            }
		},
		mounted(){
		}
	})
</script>
