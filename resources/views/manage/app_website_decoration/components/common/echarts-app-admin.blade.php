<!--指数趋势图组件-->
<script type="text/x-template" id="echarts-app-admin">
    <div class="echarts-app-admin" style="width: 100%; height: 100%;">
        <div style="width: 100%; height: 100%;" :id="`main${index}`"></div>
    </div>
</script>
<script>
    /**
     * name 指数趋势图组件
     * **/
    Vue.component('echarts-app-admin', {
        props: {
            index: {
                type: Number,
                default: 0
            },
            //	趋势图标题
            title: {
                type: String,
                default: ''
            },
            charts_type: {
                type: String,
                default: 'line'
            },
            color: {
                type: Array,
                default: () => { return ['#4936FF', '#F65858'] }
            },
            x_axis_value: {
                type: Array,
                default: () => { return [] }
            },
            y_axis_value_last: {
                type: Array,
                default: () => { return [] }
            },
            is_show_legend: {   //  是否展示标记按钮
                type: Boolean,
                default: () => { return false }
            },
            grid_top: {
                type: Number,
                default: 50
            },
            grid_bottom: {
                type: Number,
                default: 80
            },
            grid_left: {
                type: Number,
                default: 50
            },
            is_area_line: { //  是否展示面积图
                type: Boolean,
                default: () => { return false }
            },
            is_linear_gradient: { //  是否开启面积图渐变
                type: Boolean,
                default: () => { return false }
            },
            linear_gradient_color: { //  开启面积图渐变颜色,开启面积图后有效
                type: Array,
                default: () => { return ['#4936FF', '#F65858'] }
            },
            linear_gradient_scope: { //  开启面积图渐变范围-------暂时废弃
                type: Array,
                default: () => { return [0.8, 0.3] }
            },
            series_view_data: { //  需要渲染的趋势图数据
                type: Array,
                default: () => { return [] }
            }
        },
        data() {
            return {
                myChart: null
            }
        },
        template: '#echarts-app-admin',
        methods: {
            /** 初始化趋势图 */
            initCharts () {
                setTimeout(() => {
                    let chartDom = document.getElementById(`main${this.index}`);
                    if (!chartDom) return false
                    this.myChart = echarts.init(chartDom);
                    let option;
                    const that = this
                    option = {
                        title: {
                            text: this.title,
                            textStyle: {
                                fontWeight: 'bold',
                                fontSize: '18px'
                            }
                        },
                        tooltip: {
                            show: true,
                            confine: true,  //  设置超出限制
                            trigger: 'axis',
                            axisPointer: {
                                type: 'line'
                            },
                            backgroundColor: 'rgba(0, 0, 0, 0)',
                            borderWidth: 0,
                            shadowColor: 'rgba(172,100,40,0.3)',
                            padding: 0,
                            textStyle:{
                                color: '#ffffff'
                            },
                            position: function (point, params, dom, rect, size) {
                                return [point[0] + 10, '40%']  //返回x、y（横向、纵向）两个点的位置
                            },
                            formatter: function (params) {
                                let param = params[0];
                                let date_format = '', format_html = ''
                                if (param.axisValue.indexOf('/')) {
                                    let date = param.axisValue.split('/')
                                    date_format = `${date[0]}月${date[1]}日`
                                }

                                format_html = '<div class="echarts-tooltip s-flex flex-dir ai-ct jc-bt"><p>' + param.axisValue + '</p>'
                                if (that.series_view_data.length) {
                                    for (let key in that.series_view_data) {
                                        format_html += '<p>' + that.series_view_data[key].name + ': ' + param.data + '</p>'
                                    }
                                }
                                format_html += '</div>'
                                return format_html
                            }
                        },
                        toolbox: {
                            show: true,
                        },
                        legend: {
                            show: this.is_show_legend,
                            bottom: -5,
                            data: []
                        },
                        grid: {
                            show: false,
                            top: this.grid_top,
                            bottom: this.grid_bottom,
                            left: this.grid_left,
                            borderColor: '#dddddd'
                        },
                        xAxis: {
                            type: 'category',
                            data: this.x_axis_value,
                            // boundaryGap: false,
                            axisTick: {       //x轴刻度线
                                show: false,
                            },
                            minorTick: {
                                show: true
                            },
                            splitLine: {     //网格线
                                show: false,
                                lineStyle: {
                                    width: 1,
                                    color: '#F4F4F4',
                                    type: 'solid',
                                    cap: 'round'
                                }
                            },
                            axisLabel: {//坐标轴名字
                                textStyle: {
                                    color: '#86909C',
                                    fontSize: '14px',
                                    lineHeight: 16,
                                }
                            },
                            axisLine: {
                                lineStyle: {
                                    color: '#F4F4F4',
                                    type: 'solid'
                                }
                            },
                        },
                        yAxis: {
                            type: 'value',
                            scale: true,
                            axisLabel: {//坐标轴名字
                                textStyle: {
                                    color: '#86909C',
                                    fontSize: '14px'
                                }
                            },
                            axisLine: {
                                lineStyle: {
                                    color: '#999',
                                }
                            },
                            axisTick: {       //y轴刻度线
                                show: false,
                            },
                            minorTick: {
                                show: false
                            },
                            splitLine: {     //网格线
                                show: false,
                                lineStyle: {
                                    width: 2,
                                    color: '#F4F4F4',
                                    type: 'dashed',
                                    cap: 'round'
                                }
                            },
                        },
                        color: this.color,
                        series: []
                    };

                    //  遍历数据趋势图
                    if (this.series_view_data) {
                        option.series = []
                        option.legend.data = []
                        this.series_view_data.forEach((item, index) => {
                            option.series.push({
                                name: item.name,
                                data: item.data,
                                type: this.charts_type || 'line',
                                barWidth: 20,
                                showSymbol: false,
                                smooth: true,
                                areaStyle: this.is_area_line ? this.is_linear_gradient ? {
                                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                        {
                                            offset: 0,
                                            color: `rgba(${this.handleHexFormatRgba(this.linear_gradient_color[(this.linear_gradient_color.length - 1) > index ? index : 0], 0.4)})`
                                        },
                                        {
                                            offset: 1,
                                            color: `rgba(${this.handleHexFormatRgba(this.linear_gradient_color[(this.linear_gradient_color.length) > index ? index : 1], 0)})`
                                        }
                                    ])
                                } : '' : null    //  面积折线图处理
                            })
                            option.legend.data.push(item.name)
                        })
                    }

                    option && this.myChart.setOption(option);
                }, 100)
            },
            /** 将二进制色值转换为RGBA */
            handleHexFormatRgba (value, opacity = 1) {
                const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(value);
                return result ? `${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)}, ${opacity}` : null
            },
            /** 将RGB色值转换为二进制 */
            handleRgbFormatHex (value) {
                const arr = value.split(',')
                const [r, g, b] = arr
                return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
            }
        },
        mounted () {
            if (this.x_axis_value && this.series_view_data && this.series_view_data.length) {
                this.initCharts()
            }
        }
    })
</script>
