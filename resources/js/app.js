import { createApp } from 'vue'
import App from './App.vue'
import '../css/app.css'
import router from './router'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import dialog from "./utils/dialog";
import http from "./utils/http";


const app = createApp(App);
app.config.globalProperties.$http = dialog
app.config.globalProperties.$dialog = http
app.use(router);
app.use(ElementPlus);
app.mount('#app');
