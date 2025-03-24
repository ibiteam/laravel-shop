import { createRouter, createWebHistory } from 'vue-router'
import Register from '../pages/Register.vue'
import Login from '../pages/Login.vue'
import Home from '../pages/Home.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Home },
        { path: '/login', component: Login },
        { path: '/register', component: Register },
    ],
});

export default router
