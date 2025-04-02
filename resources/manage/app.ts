import { createApp } from 'vue'
import App from './App.vue'

import router from '@/router'
import pinia from '@/store/index'
import ElementPlus from 'element-plus'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import VueCookies from 'vue3-cookies'
import mitt from 'mitt'
import 'element-plus/dist/index.css'
import '@/assets/css/common.css'
import '@/assets/css/public.css'
import '@/assets/css/element-reset.css'
import '@/assets/css/iconfont.css'
import dialog from "@/utils/dialog";
import http from "@/utils/http";
import $public from "@/utils/public";



const app = createApp(App);
app.use(router);
app.use(pinia)
app.use(ElementPlus);
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component)
}
app.config.globalProperties.$http = http
app.config.globalProperties.$dialog = dialog
app.config.globalProperties.$public = $public;
app.config.globalProperties.$bus = mitt()
app.use(VueCookies);
app.mount('#app');
