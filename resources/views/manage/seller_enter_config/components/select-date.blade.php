<script type="text/x-template" id="select-date-component">
    <div style="display: inline-block;width: 100%;">
        <div class="s_flex ai_ct flex_wrap">
            <el-date-picker
                    v-model="dateValueStart"
                    :type="type"
                    :picker-options="startOptions"
                    @change="changeDateStart"
                    :value-format="format"
                    :size="size"
                    :placeholder="startPlaceholder">
            </el-date-picker>
            <span style="margin: 0 5px;">至</span>
            <el-date-picker
                    v-model="dateValueEnd"
                    :type="type"
                    :picker-options="endOptions"
                    @change="changeDateEnd"
                    :value-format="format"
                    :size="size"
                    :placeholder="endPlaceholder">
            </el-date-picker>
        </div>
    </div>
</script>
<script>
    Vue.component('select-date-component', {
        template: '#select-date-component',
        data() {
            return {
                dateValueStart:'',
                dateValueEnd:'',
                startOptions:{
                    disabledDate: (time) => {
                        if(this.disableddate){
                            if(this.dateValueEnd){
                                return time.getTime() > new Date(this.dateValueEnd+(this.type == 'date' ? ' 00:00:00' : '')).getTime()||time.getTime() < this.disableddate;
                            }else {
                                return time.getTime() < this.disableddate;
                            }
                        }else {
                            if(this.dateValueEnd){
                                return time.getTime() > new Date(this.dateValueEnd+(this.type == 'date' ? ' 00:00:00' : '')).getTime();
                            }
                        }
                    }
                },
                endOptions:{
                    disabledDate: (time) => {
                        if(this.disabledDate){
                            if(this.dateValueStart){
                                return time.getTime() < new Date(this.dateValueStart+(this.type == 'date' ? ' 00:00:00' : '')).getTime()||time.getTime() > this.disableddate;
                            }else {
                                return time.getTime() > this.disableddate;
                            }
                        }else {
                            if(this.dateValueStart){
                                return time.getTime() < new Date(this.dateValueStart+(this.type == 'date' ? ' 00:00:00' : '')).getTime();
                            }
                        }
                    }
                }
            }
        },
        props: {
            value: {
                default: ''
            },
            startPlaceholder: {
                default: '选择开始日期'
            },
            endPlaceholder: {
                default: '选择结束日期'
            },
            format:{
                default: 'yyyy-MM-dd'
            },
            size:{
                default: ''
            },
            disableddate:{
                default: ''
            },
            type: {
                default: 'date'
            }
        },
        watch: {
            value(newValue, oldValue) {
                if(Array.isArray(newValue)&&newValue.length==2){
                    this.dateValueStart = newValue[0];
                    this.dateValueEnd = newValue[1];
                }else {
                    this.dateValueStart = '';
                    this.dateValueEnd = '';
                }
            }
        },
        methods: {
            changeDateStart(val){
                this.$emit('change')
                this.dateValueStart = this.dateValueStart?this.dateValueStart:'';
                this.dateValueEnd = this.dateValueEnd?this.dateValueEnd:'';
                this.$emit('input', [this.dateValueStart,this.dateValueEnd])
            },
            changeDateEnd(val){
                this.$emit('change')
                this.dateValueStart = this.dateValueStart?this.dateValueStart:'';
                this.dateValueEnd = this.dateValueEnd?this.dateValueEnd:'';
                this.$emit('input', [this.dateValueStart,this.dateValueEnd])

            }
        },
        mounted() {
            if (this.value) {
                if(Array.isArray(this.value)&&this.value.length==2){
                    this.dateValueStart = this.value[0];
                    this.dateValueEnd = this.value[1];
                }else {
                    this.dateValueStart = '';
                    this.dateValueEnd = '';
                }
            }
        }
    })
</script>
@section('css')
    <style>
        .date-wrap>span{margin: 0 10px;font-size: 14px;color: #333;}
        .s_flex {
            display: -webkit-box;
            display: -moz-box;
            display: -webkit-flex;
            display: -moz-flex;
            display: -ms-flexbox;
            display: flex;
        }
        .ai_ct {
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
        }

    </style>
@endsection
<style>
    .fd_cl{
        flex-direction: column;
    }
</style>
