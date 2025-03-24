import { createRouter, createWebHistory } from 'vue-router'
import Register from '@/pages/Register.vue'
import Login from '@/pages/Login.vue'
import ForgetPassword from '@/pages/ForgetPassword.vue'
import Home from '@/pages/Home.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Home, name: 'home' },
        { path: '/login', component: Login, name: 'login' },
        { path: '/register', component: Register , name: 'register' },
        { path: '/forget-password', component: ForgetPassword , name: 'forgetPassword' },
    ],
});

export default router
