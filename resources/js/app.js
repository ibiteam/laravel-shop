import { createApp } from 'vue'
import App from './App.vue'

import router from '@/router'
import ElementPlus from 'element-plus'
import VueCookies from 'vue3-cookies'
import 'element-plus/dist/index.css'
import '@/css/app.css'
import '@/css/element-reset.css'
import '@/css/iconfont.css'
import dialog from "@/utils/dialog";
import http from "@/utils/http";
import $public from "@/utils/public";


const app = createApp(App);
app.config.globalProperties.$http = dialog
app.config.globalProperties.$dialog = http
app.config.globalProperties.$public = $public;
app.use(router);
app.use(ElementPlus);
app.use(VueCookies);
app.mount('#app');
