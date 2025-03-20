<style>
    *{
        margin:0;
        list-style:none;
    }
    .contextmenu{
        background:#fff;
        z-index:100;
        position:absolute;
        padding:5px 0;
        border-radius:4px;
        font-size:12px;
        font-weight:400;
        color:#333;
        webkit-box-shadow:2px 2px 3px 0 rgba(0, 0, 0, 0.3);
        box-shadow:2px 2px 3px 0 rgba(0, 0, 0, 0.3);
    }
    .contextmenu li{
        padding:7px 16px;
        cursor:pointer;
    }
    .contextmenu li:hover{
        background:#efefef;
    }
</style>

<script type="text/x-template" id="card-cmp">
    <ul class="contextmenu" v-show="isV" :style="{left:lefts,top:tops}">
        <li @click="operating(0)">刷新</li>
        <li @click="operating(1)">关闭</li>
        <li @click="operating(2)">关闭其他</li>
        <li @click="operating(3)">关闭所有</li>
    </ul>
</script>

<script>
    Vue.component('card-cmp',{
        data: function(){
            return {
                isV:false
            }
        },
        props:{
            isVisible:{
                type:Boolean,
                default:false
            },
            // name:String,
            // src:String,
            tops:String,
            lefts:String
        },
        watch:{
            isVisible: function(v){
                this.isV = v;
            }
        },
        methods:{
            operating: function(e){
                PubSub.publish('operats', e)
            },
        },
        mounted: function(){},
        'template':'#card-cmp'
    })
</script>
