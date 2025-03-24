<!--价格公共组件-->
<script type="text/x-template" id="form-price">
    <template>
        <div class="price_format" :style="{color:color}">
            <span :style="{fontSize:sign_size+'px'}">￥</span><span :style="{fontWeight:weight,fontSize:int_size+'px'}">@{{integer}}</span><span :style="{fontWeight:weight,fontSize:df_size+'px',display: need_df ? 'inline' : 'none'}">@{{decimal}}</span><span :style="{color:unit_color,fontSize:unit_size+'px'}">@{{unit ? (need_bar ? '/' : '') + unit : ''}}</span>
        </div>
    </template>
</script>
<script>
    /*基础组件*/
    Vue.component('form-price', {
        props: {
            price: {  // 价格
                required: true
            },
            unit: { // 单位
                required: false
            },
            sign_size: { // ￥符号字体大小
                required: false,
                default() {
                    return '12'
                }
            },
            int_size: { // 整数字体大小
                required: false,
                default() {
                    return '18'
                }
            },
            df_size: { // 小数字体大小
                required: false,
                default() {
                    return '18'
                }
            },
            unit_size: { // 单位字体大小
                required: false,
                default() {
                    return '12'
                }
            },
            color: { // 颜色
                required: false,
                default() {
                    return '#F71111'
                }
            },
            unit_color: { // 单位颜色
                required: false,
                default() {
                    return '#333'
                }
            },
            need_df: { // 是否需要小数位
                required: false,
                default() {
                    return true
                }
            },
            need_bar: { // 金额和单位之间是否有'/',默认是有的
                required: false,
                default() {
                    return true
                }
            },
            weight: { // 是否加粗
                required: false,
                default() {
                    return 400
                }
            }
        },
        computed: {
            integer() {
                if (this.price) {
                    let temp = this.price
                    let reg = /¥/
                    let reg2 = /￥/
                    if (reg.test(this.price)) {
                        temp = this.price.split('¥')[1]
                    } else if (reg2.test(this.price)) {
                        temp = this.price.split('￥')[1]
                    }
                    return temp.toString().split('.')[0]
                } else {
                    return 0
                }
            },
            decimal() {
                if (this.price) {
                    let temp = this.price
                    let reg = /¥/
                    let reg2 = /￥/
                    if (reg.test(this.price)) {
                        temp = this.price.split('¥')[1]
                    } else if (reg2.test(this.price)) {
                        temp = this.price.split('￥')[1]
                    }
                    return temp.toString().split('.')[1] ? '.' + (temp.toString().split('.')[1]) : ''
                } else {
                    return 0
                }
            }
        },
        template: '#form-price'
    })
</script>
