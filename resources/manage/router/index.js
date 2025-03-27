import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/pages/user/Login.vue'
import Home from '@/pages/Home.vue';
import GoodsForm from '@/pages/goods/GoodsForm.vue';
const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_MANAGE_PREFIX||'manage'),
    routes: [
        { path: '/', component: Home, name: 'home' },
        { path: '/login', component: Login, name: 'login' },
        { path: '/goods/form', component: GoodsForm , name: 'goodsForm' },
        {
            path:'/seller',
            component: () => import('@/components/SellerLayout.vue'),
            children:[
                { path: 'home', component: import('@/pages/seller/Home.vue'), name: 'sellerHome' },
            ]
        },
        {
            path: '/decoration',
            component: () => import('@/pages/decoration/DecorationLayout.vue'),
            children: [
                { path: 'app', component: import('@/pages/decoration/DecorationApp.vue'), name: 'decorationApp' },
            ]
        }
    ],
});
export default router
