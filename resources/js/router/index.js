import { createRouter, createWebHistory } from 'vue-router'
import Register from '@/pages/front/Register.vue'
import Login from '@/pages/front/Login.vue'
import ForgetPassword from '@/pages/front/ForgetPassword.vue'
import Home from '@/pages/front/Home.vue';
import GoodsForm from '@/pages/seller/GoodsForm.vue';
const router = createRouter({
    history: createWebHistory(),
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
        }
    ],
});

export default router
