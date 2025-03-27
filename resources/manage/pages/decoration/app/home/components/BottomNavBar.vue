<template>
    <div>
        <div class="bottom-navbar-wrapper">
            <tabbar
                :fixed="false"
                safe-area-inset-bottom
                :before-change="() => false"
                >
                <tabbar-item v-for="(tab, index) in form.data.items"
                    :key="tab.alias"
                    :name="tab.alias"
                    :icon="active == tab.alias ? tab.selection_image : tab.default_image"
                    :badge="tab.is_show_number && tab.number ? tab.number : ''"
                    :to="tab.url"
                >
                    <template #icon>
                        <img width='22' height='22' :src="index == 0 ? tab.selection_image : tab.default_image" />
                    </template>
                    <span :style="{color: index == 0 ? form.data.font_selection_color : form.data.font_default_color}">{{ index == 0 ? tab.check_title : tab.title }}</span>
                </tabbar-item>
            </tabbar>
        </div>
        <setting-bar v-bind="{name: form.name}" >
            <template #content="slotProps">
                {{slotProps.type}}
                <div class="setting-bar-item"></div>
            </template>
        </setting-bar>
    </div>
</template>

<script setup>
import SettingBar from '@/pages/decoration/components/SettingBar.vue'
import { Tabbar, TabbarItem } from 'vant';
import { ref, reactive, watch } from 'vue'

const props = defineProps({
    component: {
        type: Object,
        default: () => {
            return {}
        }
    }
})

const form = reactive({
    component_name: '',
    content: {},
    data: {},
    id: '',
    is_show: 1,
    name: '',
    sort: 0,
})

const active = ref('')

watch([() => props.component], (newValue) => {
    if (newValue[0]) {
        Object.keys(newValue[0]).forEach(key => {
            form[key] = newValue[0][key]
            if (key == 'data') {

            }
        })
    }
}, {
    immediate: true,
    deep: true
})
</script>

<style lang='scss' scoped>
.bottom-navbar-wrapper{
    width: 375px;
    height: 50px;
    background-color: #fff;
    margin: 0 auto;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
}
</style>