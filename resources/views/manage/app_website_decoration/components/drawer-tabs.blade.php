<style>
    /*Tab切换*/
    .drawer-tabs { padding: 0 12px; border-bottom: 1px solid #EFEFEF; }
    .drawer-tabs .tab-item { padding: 18px 0; margin: 0 30px; font-size: 14px; cursor: pointer; position: relative; }
    .drawer-tabs .tab-item:hover,
    .drawer-tabs .tab-item.active { color: #409EFF; }
    .drawer-tabs .tab-item:hover::before,
    .drawer-tabs .tab-item.active::before { content: ''; width: 100%; height: 2px; background-color: #409EFF; position: absolute; left: 0; bottom: 0; }
</style>
<!--Tab按钮切换组件-->
<script type="text/x-template" id="drawer-tabs">
    <div class="drawer-tabs s-flex ai-ct">
        <div class="tab-item" v-for="(item, index) in list" :class="{ active: tab_active == index }" @click="handleClickTabItem(index)">@{{ item }}</div>
    </div>
</script>
<script>
    /**
     * name Tab按钮切换组件
     * **/
    Vue.component('drawer-tabs', {
        props: {
            tab_active: {
                type: Number,
                default: 0
            },
            list: Array,
            initial_index: {
                type: Number,
                default: 0
            }
        },
        data () {
            return{
                tabIndex: 0
            }
        },
        template: '#drawer-tabs',
        methods: {
            handleClickTabItem (index) {
                this.$set(this, 'tab_active', index);
                this.$emit('update:tab_active', index);
            },
            /** 点击tab切换 */
            handleClickChangeTab (index) {
                this.tabIndex = index
            }
        },
        mounted (){
            if (this.initial_index <= this.list.length - 1) {
                this.tabIndex = this.initial_index
            }
        }
    })
</script>
