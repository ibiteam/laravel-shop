<script>
    const Mask = Vue.extend({
        template: `<div class="template-loading s-flex ai-ct jc-ct" v-if="visible"><img src="https://cdn.toodudu.com/uploads/2022/01/20/loading.gif" style="width: 60px;height: 30px;"></div>`,
        data: function () {
            return {visible: false}
        }
    })
    const toggleLoading = (el, binding) => {
        if (binding.value) {
            Vue.nextTick(() => {
                el.instance.visible = true// 控制loading组件显示
                insertDom(el, el, binding)// 插入到目标元素
            })
        } else {
            Vue.nextTick(()=>{
                el.instance.visible = false
                // removeDom(el,el,binding)
            })
        }
    }

    const insertDom = (parent, el) => {
        parent.appendChild(el.mask)
    }

    const removeDom = (parent,el)=>{
        parent.removeChild(el.mask)
    }
</script>
