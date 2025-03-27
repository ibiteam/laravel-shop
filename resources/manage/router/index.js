import { createRouter, createWebHistory } from 'vue-router'
import Register from '@/pages/user/Register.vue'
import Login from '@/pages/user/Login.vue'
import ForgetPassword from '@/pages/user/ForgetPassword.vue'
import Home from '@/pages/Home.vue';
import GoodsForm from '@/pages/seller/GoodsForm.vue';
const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_MANAGE_PREFIX||'manage'),
    routes: [
        { path: '/', component: Home, name: 'home' },
        { path: '/login', component: Login, name: 'login' },
        { path: '/register', component: Register , name: 'register' },
        { path: '/forget-password', component: ForgetPassword , name: 'forgetPassword' },
        { path: '/seller/goods-form', component: GoodsForm , name: 'goodsForm' },
        {
            path:'/seller',
            component: () => import('@/components/SellerLayout.vue'),
            children:[
                { path: 'home', component: import('@/pages/seller/Home.vue'), name: 'sellerHome' },
            ]
        },
        {
            path: '/decoration',
            component: () => import('@/pages/front/decoration/DecorationLayout.vue'),
            children: [
                { path: 'app', component: import('@/pages/front/decoration/DecorationApp.vue'), name: 'decorationApp' },
            ]
        }
    ],
});
export default router
