@if(is_mobile_request())
<link rel="stylesheet" href="/css/swiper.min.css">
@endif
<style>
    .good-component {

    }
    .good-component .pull_left{
        width: 250px;
        position: relative;
        margin-right: 30px;
    }
    .good-component .pull_left .auction-img{
        width: 250px;
        height: 250px;
        line-height: 250px;
        text-align: center;
        border: 1px solid #eee;
        position: relative;
    }

    .good-component .pull_left .auction-img:hover .zoomPup {
        display: block;
    }

    .good-component .pull_left .auction-img:hover .zoomDiv {
        display: block;
    }

    .good-component .pull_left .imgGood.auction-img {
        /*cursor: move;*/
    }

    .good-component .pull_left .imgGood.auction-img > img {
        width: 100%;
        height: 100%;
    }
    .good-component .pull_left .imgGood.auction-img .zoomPup {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 150px;
        height: 150px;
        background-color: rgba(50, 50, 50, 0.1);
        visibility: visible;
    }
    .good-component .pull_left .imgGood.auction-img .zoomDiv {
        position: absolute;
        width: 508px;
        height: 508px;
        border: 1px solid #e5e5e5;
        top: 0px;
        left: 350px;
        z-index: 10;
        background-color: #FFF;
        overflow: hidden;

    }
    .good-component .pull_left .imgGood.auction-img .zoomDiv img {
        position: absolute;
        top: 0px;
        left: 0px;
    }

    /**/

    .good-component .pull_left .auction-img-list {
        margin-top: 10px;
        overflow: hidden;
        position: relative;
    }

    .good-component .pull_left .auction-img-list-pd {
        padding: 0 17.5px;
    }

    .good-component .pull_left .auction-img-list .prev, .auction-img-list .next {
        font-family: monospace;
        position: absolute;
        height: 30px;
        top: 50%;
        margin-top: -15px;
        cursor: pointer;
        line-height: 30px;
        text-align: center;
        font-size: 30px;
        font-weight: bold;
        color: #C0C0C0;
        cursor: pointer;
    }

    .good-component .pull_left .auction-img-list label:hover {
        color: #E1251B;
    }

    .good-component .pull_left .auction-img-list .prev {
        left: -8px;
    }

    .good-component .pull_left .auction-img-list .next {
        right: -8px;
    }

    .good-component .pull_left .auction-img-list div {
        overflow: hidden;
    }

    .good-component .pull_left .auction-img-list ul {
        overflow: hidden;
        white-space: nowrap;
    }

    .good-component .pull_left .auction-img-list li {
        float: left;
        width: 59px;
        height: 59px;
        margin-right: 5px;
        border: 1px solid #e5e5e5;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .good-component .pull_left .auction-img-list li.video::after {
        content: '';
        width: 36px;
        height: 36px;
        position: absolute;
        background: url("https://cdn.toodudu.com/uploads/2022/01/21/play.png") no-repeat center;
        background-size: 100% 100%;
        left: 0;
        right: 0;
        top: 0;
        margin: auto;
        bottom: 0;
    }

    .good-component .pull_left .auction-img-list li:hover {
        border-color: #ee8164;
    }

    .good-component .pull_left .auction-img-list li.choose {
        border-color: #ee8164;
    }

    .good-component .pull_left .auction-img-list li:last-child {
        margin-right: 0px;
    }

    .good-component .pull_left .auction-img-list li img {
        width: 100%;
        max-height: 100%;
    }

    /**/

    .good-component .pull_right{
        @if(!is_mobile_request())
        flex: 1;
        width: 0;
        @endif
        max-width: 1050px;
    }
    .good-component .pull_right .good-title .good-type{
        width: 78px;
        height: 28px;
        border-radius: 5px;
        background: #EB1515;
    }
    .good-component .pull_right .good-title .good-type span{
        font-size: 14px;
        font-weight: normal;
        color: #FFFFFF;
    }
    .good-component .pull_right .good-title .good-name{
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 60%;
        /*width: auto;*/
    }
    .good-component .pull_right .good-title .good-name span{
        font-size: 18px;
        font-weight: bold;
        color: #3D3D3D;
    }
    .good-component .pull_right .good-title .second-hand{
        /*width: 52px;*/
        padding: 0 10px;
        height: 23px;
        border-radius: 4px;
        border: 1px solid #B3E09C;
    }
    .good-component .pull_right .good-title .second-hand span{
        font-size: 12px;
        font-weight: normal;
        color: #67C23A;

    }
    .good-component .pull_right .good-desc span{
        font-size: 12px;
        font-weight: bold;
        line-height: 22px;
        color: #9E9E9E;
    }

    .good-component .pull_right .good-priceBox{
        max-width: 1050px;
        background: #F7F3F3;
        padding: 20px;
        margin-top: 13px;
    }
    .good-component .pull_right .good-priceBox .price-detail{
        display: flex;
        align-items: center;
        line-height: 2;
    }
    .good-component .pull_right .good-priceBox .price-detail .labels{
        width: 85px;
    }
    .good-component .pull_right .good-priceBox .price-detail .labels span{
        font-size: 16px;
        font-weight: normal;
        line-height: 22px;
        color: #3D3D3D;
    }
    .good-component .pull_right .good-priceBox .price-detail .detail span{
        font-size: 14px;
        font-weight: normal;
        line-height: 22px;
        color: #3D3D3D;
    }
    .good-component .pull_right .good-priceBox .horizontal{
        width: 100%;
        border-bottom: dashed 1px #ccc;
        margin: 13px 0;
    }

    /*    */
    .good-component .pull_right .color-category{
        margin-top: 12px;
        max-width: 761px;
    }
    .good-component .pull_right  .labels{
        width: 85px;
    }
    .good-component .pull_right .color-category .labels span,
    .good-component .pull_right .good-number .labels span{
        font-size: 14px;
        font-weight: normal;
        line-height: 22px;
        color: #3D3D3D;
    }
    .good-component .pull_right .color-category .category-box{
        width: 0;
        flex: 1;
    }
    .good-component .pull_right .color-category .category-box .category-li{
        height: 38px;
        border: 1px solid #D8D8D8;
        box-sizing: border-box;
        margin-right: 12px;
        margin-bottom: 20px;
        cursor: pointer;
    }
    .good-component .pull_right .color-category .category-box .category-li .imgs{
        width: 50px;
        height: 36px;
    }
    .good-component .pull_right .color-category .category-box .category-li .imgs img{
        width: 100%;
        height: 100%;
    }
    .good-component .pull_right .color-category .category-box .category-li .name{
        padding: 0 11px;
    }
    .good-component .pull_right .color-category .category-box .category-li .name span{
        font-size: 14px;
        font-weight: normal;
        line-height: 38px;
        color: #3D3D3D;
    }
    .good-component .pull_right .color-category .category-box .category-li.actived{
        border: 1px solid #3298F6;
    }
    .good-component .pull_right .color-category .category-box .category-li.noChoose{
        cursor: no-drop;
        background: #f9f9f9;
    }
    .good-component .pull_right .color-category .category-box .category-li.noChoose .name span{
        color: #ccc;
    }
    .good-component .pull_right .color-category .category-box .category-li.actived .name span{
        color: #3298F6;
    }
    .good-component .pull_right .good-number .inventory{
        margin-left: 32px;
    }
    .good-component .pull_right .good-number .inventory span{
        font-size: 14px;
        font-weight: normal;
        line-height: 22px;
        color: #EB1515;
    }
    .good-component .pull_right .good-btns{
        margin-top: 30px;
        @if(is_mobile_request())
            position: fixed;
            bottom: 0;
            background: #fff;
            width: 100%;
            height: 60px;
            line-height: 60px;
            z-index: 99;
            left: 0;
            text-align: center;
        @endif
    }
    .good-component .pull_right .good-btns .el-button{
        background: #EB1515;
        border-radius: 10px;
        @if(is_mobile_request())
            width: 80%;
            height: 50px;
        @endif
    }
    .good-component .pull_right .good-btns .el-button span{
        color: #FFFFFF;
    }
    .good-component .pull_right .good-btns .el-button.is-disabled{
        background: #D8D8D8;
    }
    .good-component .pull_right .good-btns .el-button.is-disabled span{
        color: #3D3D3D;
    }

    @if(is_mobile_request())
    .good-component .home-right-advise{height: 380px;border-radius: 15px;overflow: hidden;margin-bottom: 10px;}
    .good-component .home-right-advise .swiper-container {width: 100%;height: 100%;}
    .good-component .home-right-advise .swiper-container .swiper-icon-prev,
    .good-component .home-right-advise .swiper-container .swiper-icon-prev {
        left: 10px;
    }

    .good-component .home-right-advise .swiper-container .swiper-icon-next {
        right: 10px;
    }
    .good-component .home-right-advise .swiper-wrapper{display: flex;width: fit-content;flex-wrap: nowrap;}
    .good-component .home-right-advise .swiper-slide {display: flex;justify-content: center;align-items: center;width: 100%px;height: 380px;flex: none;}
    .good-component .home-right-advise img{width: 100%;height: 100%;}
    @endif

    .el-popover{
        padding: 8px 20px;
        text-align: center;
        min-width: 80px;
    }


    /*    */
    @media screen and (max-width: 1920px) {
        .good-component .pull_left{
            width: 200px;
            margin-right: 10px;
        }
        .good-component .pull_left .auction-img{
            width: 200px;
            height: 200px;
            line-height: 200px;
        }
        @media screen and (max-width: 1450px){
            .good-component .pull_left .imgGood.auction-img .zoomPup{
                width: 60px;
                height: 60px;
            }
            .good-component .pull_left .imgGood.auction-img .zoomDiv{
                width: 250px;
                height: 250px;
                left: 160px;
            }
        }
    }
</style>
<script type="text/x-template" id="goodDetail">
    <div class="good-component s_flex @if(is_mobile_request()) flex-dir @endif ">
        @if(!is_mobile_request())
        <div class="pull_left">
            <div class="imgGood auction-img" ref="imgGood">
                <template v-if="goodDetails.goods_img && goodDetails.goods_img.length">
                    <img :src="goodDetails.goods_img[img_index]" onerror="this.src='https://cdn.toodudu.com/uploads/2021/08/24/app_nopic.png'" width="100%" alt="">
                </template>
                <template v-else>
                    <span style="font-size: 18px;color: #ccc">{{ __('shop/goods.index.goods_picture') }}</span>
                </template>
            </div>
            <div class="auction-img-list " :class="{'auction-img-list-pd':goodDetails.goods_img.length > 3}" style="margin-bottom: 10px;">
                <div>
                    <ul style="margin-bottom: 0px;">
                        <li :class="{'choose':img_index == index}" :key="index" @click="chooseImg(item,index)" v-for="(item, index) in goodDetails.goods_img">
                            <img :src="item" alt="{{ __('shop/goods.index.goods_picture') }}" onerror="this.src='https://cdn.toodudu.com/uploads/2021/08/24/app_nopic.png'"  width="402" height="402">
                        </li>
                    </ul>
                </div>
                <template v-if="goodDetails.goods_img.length > 3">
                    <label class="el-icon-arrow-left prev"></label>
                    <label class="el-icon-arrow-right next"></label>
                </template>
            </div>
        </div>
        @else
        <div class="home-right-advise">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide" v-for="item in goodDetails.goods_img">
                        <img :src="item">
                    </div>
                </div>
                <div class="swiper-pagination  swiper-pagination-white"></div>
            </div>
        </div>
        @endif
        <div class="pull_right">
            <div class="good-title s_flex ai_fs">
                <div class="good-type s_flex ai_ct jc_ct" v-if="view || goodDetails.tag" style="margin-right: 13px;">
                    <span v-if="goodDetails.tag">@{{ goodDetails.tag }}</span>
                    <span v-else>{{ __('shop/goods.index.goods_tag') }}</span>
                </div>
                <div class="good-name">
                    <el-popover
                        placement="bottom"
                        title=""
                        :disabled="!goodDetails.goods_name"
                        width="auto"
                        trigger="click"
                        :content="goodDetails.goods_name?goodDetails.goods_name:''">
                        <template slot="reference">
                            <span v-if="goodDetails.goods_name">@{{ goodDetails.goods_name }}</span>
                            <span v-else>{{ __('shop/goods.index.goods_name') }}</span>
                        </template>
                    </el-popover>
                </div>
                <div class="second-hand s_flex ai_ct jc_ct" style="margin-left: 23px;" v-if="goodDetails.goods_type === 2">
                    <span>{{ __('shop/goods.index.second_hand') }}</span>
                </div>
            </div>
            <div class="good-desc" style="margin-top: 9px;" v-if="view || goodDetails.goods_brief">
                <span v-if="goodDetails.goods_brief">@{{ goodDetails.goods_brief }}</span>
                <span v-else>{{ __('shop/goods.index.goods_subtitle') }}</span>
            </div>
            <div class="good-priceBox">
                <div class="price-detail">
                    <div class="labels">
                        <span>{{ __('shop/goods.index.price') }}</span>
                    </div>
                    <div class="detail">
                        <span style="color: #EB1515;font-size: 12px;">
                            <template v-if="(view && (!goodsPrice.integral_money && !goodsPrice.shop_price)) || goodsPrice.integral_money">
                                <span style="font-size: 20px;color: #EB1515;">@{{ goodsPrice.integral_money }}</span>
                                {{ __('shop/goods.index.points') }}
                            </template>
                            <template v-if="(view && (!goodsPrice.integral_money && !goodsPrice.shop_price)) || (goodsPrice.integral_money && goodsPrice.shop_price)">+</template>
                            <template v-if="(view && (!goodsPrice.integral_money && !goodsPrice.shop_price)) || goodsPrice.shop_price">
                                <span style="font-size: 20px;color: #EB1515;">@{{ goodsPrice.shop_price }}</span>
                                {{ __('shop/goods.index.yuan') }}
                            </template>
                            <template v-if="goodDetails.unit">
                                /@{{ goodDetails.unit }}
                            </template>
                        </span>
                    </div>
                </div>
                <div class="price-detail">
                    <div class="labels">
                        <span>{{ __('shop/goods.index.sales_volume') }}</span>
                    </div>
                    <div class="detail">
                        <span style="color: #333">@{{ goodDetails.sale_num ? goodDetails.sale_num : 0 }}<span style="color: rgb(140 136 136);font-size: 12px">@{{ goodDetails.unit }}</span></span>
                    </div>
                </div>
                <div class="price-detail">
                    <div class="labels">
                        <span>{{ __('shop/goods.index.limited_settle') }}</span>
                    </div>
                    <div class="detail">
                        <span v-if="!goodDetails.limit_number">{{ __('shop/goods.index.not_limited') }}</span>
                        <span v-else>{{ __('shop/goods.index.every_limited') }} @{{ goodDetails.limit_number }} @{{ goodDetails.unit }}</span>
                    </div>
                </div>
                <template v-if="goodDetails.goods_attr.length">
                    <div class="horizontal"></div>
                    <div class="price-detail" v-for="(its,ids) in goodDetails.goods_attr">
                        <div class="labels">
                            <span>@{{ its.attr_name }}</span>
                        </div>
                        <div class="detail">
                            <span>@{{ its.attr_value }}</span>
                        </div>
                    </div>
                </template>
            </div>
            <template v-if="goodDetails.goods_specs && goodDetails.goods_specs.length">
                <div class="color-category s_flex ai_fs" v-for="(item,index) in goods_specs" :key="index">
                    <div class="labels">
                        <span>@{{ item.spec_name }}</span>
                    </div>
                    <div class="category-box s_flex flex_wrap" v-if="item.spec_value.length">
                        <div class="category-li s_flex" :class="{'actived':its.checked,'noChoose':its.is_show === 0}" v-for="(its,ids) in item.spec_value" :key="ids" @click="chooseSpec(item,ids,its)" v-if="its.spec_value_name">
                            <div class="imgs" v-if="!index && its.thumb">
                                <img :src="its.thumb" onerror="this.src='https://cdn.toodudu.com/uploads/2021/08/24/app_nopic.png'" alt="">
                            </div>
                            <div class="name no-select">
                                <span>@{{ its.spec_value_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <div class="good-number s_flex ai_ct" style="margin-top: 12px;">
                <div class="labels">
                    <span>{{ __('shop/goods.index.number') }}</span>
                </div>
                <el-input-number size="small" v-model="number" :disabled="goodsPrice.number < 1" @change="handleChange" :min="1" :max="maxGoods_number" label="{{ __('shop/goods.index.desc_text') }}"></el-input-number>
                <div class="inventory">
                    <template v-if="!goodDetails.goods_skus || !goodDetails.goods_skus.length">
                        <span v-if="goodsPrice.number > 50">{{ __('shop/goods.index.well_stocked') }}</span>
                        <span v-else>{{ __('shop/goods.index.surplus_stock') }}@{{ goodsPrice.number }}@{{ goodDetails.unit }}</span>
                    </template>
                    <template v-else-if="goodDetails.goods_skus && goodDetails.goods_skus.length && categoryIndex !== null">
                        <span v-if="goodsPrice.number > 50">{{ __('shop/goods.index.well_stocked') }}</span>
                        <span v-else>{{ __('shop/goods.index.surplus_stock') }}@{{ goodsPrice.number }}@{{ goodDetails.unit }}</span>
                    </template>
                </div>
            </div>
            <div class="good-btns">
                <template v-if="goodDetails.is_on_sale">
                    <template v-if="goodsPrice.number > 0">
                        <el-button v-if="goodDetails.all_point < (goodsPrice.integral_money * number)" key="button1" style="background: #D8D8D8;cursor: not-allowed;">
                            {{ __('shop/goods.index.not_enough_points') }}</el-button>
                        <template v-else>
                            <el-button key="button2" v-if="goodDetails.goods_type === 2"  @click="buy()">{{ __('shop/goods.index.i_want_to') }}</el-button>
                            <template v-else>
                                <el-button key="button3" v-if="!goodsPrice.shop_price" @click="buy()">{{ __('shop/goods.index.new_exchange') }}</el-button>
                                <el-button key="button4" v-else @click="buy()">{{ __('shop/goods.index.buy_now') }}</el-button>
                            </template>
                        </template>
                    </template>
                    <el-button key="button5" disabled v-else>{{ __('shop/goods.index.insufficient_inventory') }}</el-button>
                </template>
                <el-button key="button6" disabled v-else>{{ __('shop/goods.index.on_sale_no') }}</el-button>
            </div>
        </div>
    </div>
</script>

<script>
    Vue.component('good-detail', {
        template: '#goodDetail',
        props: {
            goodDetails: {
                type: Object,
                default: {}
            },
            view:{
                type: Boolean,
                default:false
            },
        },
        data() {
            return {
                img_index:0,
                categoryIndex:null, // 规格下标
                goods_specs:[],
                number:1, // 选择的数量
            }
        },
        watch:{
            'goodDetails.goods_skus':{
                handler(val){
                    if(val && val.length){
                        let goods_specs = JSON.parse(JSON.stringify(this.goodDetails.goods_specs))
                        if(goods_specs.length === 1){
                            goods_specs[0].spec_value.map(a => {
                                a.is_show = this.goodDetails.goods_skus.find(b => b.template_1 === a.spec_value_name).is_show ? 1 : 0
                            })
                        }
                        goods_specs[0].spec_value.map(a => {
                            a.thumb = val.find(b => b.template_1 === a.spec_value_name).thumb
                        })
                        this.goods_specs = goods_specs
                    }
                },
                deep: true,
                immediate:true
            },
            'goodDetails.goods_img.length':{
                handler(val){
                    this.$nextTick(() => {
                        const $imgList = $(".auction-img-list ul");
                        const $imgItems = $(".auction-img-list ul li");
                        const itemCount = $imgItems.length;
                        const itemWidth = 59;
                        const itemMargin = 5;

                        // 设置图片列表的宽度
                        $imgList.width(itemCount * itemWidth + (itemCount - 1) * itemMargin + "px");

                        // 设置左右滚动按钮的点击事件
                        $(".prev").off('click').on('click', function() {
                            $(".auction-img-list>div").scrollLeft($(".auction-img-list>div").scrollLeft() - (itemWidth + itemMargin));
                        });
                        $(".next").off('click').on('click', function() {
                            $(".auction-img-list>div").scrollLeft($(".auction-img-list>div").scrollLeft() + (itemWidth + itemMargin));
                        });
                    });
                },
                deep: true,
                immediate:true
            }
        },
        computed:{
            goodsPrice(){
                if(!this.goodDetails.goods_skus || !this.goodDetails.goods_skus.length){
                    return {
                        integral_money:Number(this.goodDetails.integral_money),
                        shop_price:Number(this.goodDetails.shop_price),
                        number:Number(this.goodDetails.goods_number)
                    }
                } else {
                    if(this.categoryIndex === null){
                        const goods_skus = this.goodDetails.goods_skus;
                        const goods_specs = this.goods_specs;

                        const itemWithLowest = goods_skus.reduce(
                            (lowest, item, index) => {
                                return item.shop_price < lowest.item.shop_price
                                    ? { item, index }
                                    : lowest;
                            },
                            { item: goods_skus[0], index: 0 }
                        );

                        this.categoryIndex = itemWithLowest.index;

                        goods_specs.forEach((spec, specIndex) => {
                            spec.spec_value.forEach(value => {
                                value.checked = itemWithLowest.item[`template_${specIndex + 1}`] === value.spec_value_name;
                            });
                        });

                        return {
                            integral_money: Number(itemWithLowest.item.integral_money),
                            shop_price: Number(itemWithLowest.item.shop_price),
                            number: Number(itemWithLowest.item.number)
                        };

                    }else{
                        return {
                            integral_money:Number(this.goodDetails.goods_skus[this.categoryIndex].integral_money),
                            shop_price:Number(this.goodDetails.goods_skus[this.categoryIndex].shop_price),
                            number:Number(this.goodDetails.goods_skus[this.categoryIndex].number)
                        }
                    }

                }
            },
            maxGoods_number() {
                let limit_number = this.goodDetails.limit_number
                let goods_number = this.goodsPrice.number
                return  Number(limit_number) > 0 ? (Number(limit_number) > Number(goods_number) ? Number(goods_number) : Number(limit_number)) : Number(goods_number)
            },
        },
        methods:{
            chooseImg(item,index){
                this.img_index = index
            },
            handleChange(value){
                // this.$parent.getgoodsInfo()
            },
            setSpecifications(){
                const selectedValues = this.goods_specs.reduce((acc, spec, index) => {
                    const checkedValue = spec.spec_value.find(value => value.checked);
                    if (checkedValue) {
                        acc[`template_${index + 1}`] = checkedValue.spec_value_name;
                    }
                    return acc;
                }, {});
                const hiddenSpecs = this.goodDetails.goods_skus
                    .filter(sku => {
                        // 检查 SKU 是否完全匹配所有选中的规格值
                        const matches = Object.entries(selectedValues).every(([key, val]) => {
                            return Object.values(sku).includes(val);
                        });
                        return matches && sku.is_show === 0;
                    })
                    .flatMap(sku => Object.entries(sku).filter(([key, value]) => key.startsWith('template_') && value))
                    .map(([, value]) => value);
                const updatedSpecData = this.goods_specs.map(spec => {
                    const updatedSpecValues = spec.spec_value.map(value => {
                        if (value.checked) {
                            return value; // 如果 checked 为 true，不修改 is_show
                        }
                        const shouldHide = hiddenSpecs.some(hiddenValue => hiddenValue === value.spec_value_name);
                        if (shouldHide) {
                            return { ...value, is_show: 0 };
                        }else{
                            return { ...value, is_show: 1 };
                        }
                    });
                    return { ...spec, spec_value: updatedSpecValues };
                });
                this.$set(this,'goods_specs',updatedSpecData)
            },
            chooseSpec(item,ids,its){
                if(!this.view && its.is_show !== 0){
                    this.$set(item, 'spec_value', item.spec_value.map((a, b) => ({
                        ...a,
                        checked: b === ids
                    })));
                    let specArr_name = []
                    let flag = this.goods_specs.every(a => {
                        const checkedItem = a.spec_value.find(b => b.checked);
                        if (checkedItem) {
                            specArr_name.push(checkedItem.spec_value_name);
                            return true;
                        }
                        return false;
                    });
                    if(flag){
                        function findFirstMatchingIndex(target, objects) {
                            return objects.findIndex(obj =>
                                target.every((value, idx) => obj[`template_${idx + 1}`] === value)
                            );
                        }
                        const firstMatchingIndex = findFirstMatchingIndex(specArr_name, this.goodDetails.goods_skus) // 取出相对应的下标
                        this.$set(this,'categoryIndex',firstMatchingIndex)
                    }
                    let specIndex = this.goods_specs.findIndex(a => a.spec_id === item.spec_id)
                    if(specIndex < (this.goods_specs.length - 1)){
                        this.specData = this.goods_specs.map((spec, index) => {
                            if (index > specIndex) {
                                spec.spec_value = spec.spec_value.map(value => ({
                                    ...value,
                                    is_show:1,
                                    checked: false
                                }));
                            }
                            return spec;
                        });
                    }
                    specIndex === (this.goods_specs.length - 2) && this.setSpecifications()
                }
            },
            buy(){
                if(this.view) return false
                let flag = true
                let goods_sku_id = 0
                if(this.goodDetails.goods_skus){
                    if(this.categoryIndex === null) {
                        this.$message.warning('{{ __('shop/goods.index.please_select') }}')
                        return false
                    }
                    flag = this.goods_specs.every(a => a.spec_value.some(b => b.checked));
                    goods_sku_id = this.goodDetails.goods_skus[this.categoryIndex].goods_sku_id
                }
                if(flag){
                    this.$parent.buyGoods(goods_sku_id)
                }
            }
        },
        mounted(){
            @if(is_mobile_request())
                var swiper = new Swiper('.swiper-container', {
                    pagination: {
                        el: '.swiper-pagination',
                    },
                    navigation: {
                        nextEl: '.swiper-icon-next',
                        prevEl: '.swiper-icon-prev',
                    },
                    autoplay: true,
                    loop: true,
                    paginationClickable: false
                })
            @endif
            // console.log(this.goodDetails)


        }
    })
</script>
