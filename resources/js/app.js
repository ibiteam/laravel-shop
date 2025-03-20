import { createApp } from 'vue'
import App from './App.vue'
import { createRouter, createWebHistory } from 'vue-router'
import Register from './pages/Register.vue'
import Home from './pages/Home.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Home },
        { path: '/register', component: Register },
    ],
});

const app = createApp(App);
app.use(router);
app.mount('#app');
