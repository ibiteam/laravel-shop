import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/pages/user/Login.vue'
import Home from '@/pages/Home.vue';
import GoodsCateGoryIndex from '@/pages/goods/Category.vue';
import GoodsForm from '@/pages/goods/GoodsForm.vue';
const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_MANAGE_PREFIX||'manage'),
    routes: [
        { path: '/login', component: Login, name: 'login' },
        { path: '/goods/category', component: GoodsCateGoryIndex, name: 'GoodsCategoryIndex' },

        {
            path:'/',
            component: () => import('@/components/Layout.vue'),
            children:[
                { path: 'home', component: import('@/pages/Home.vue'), name: 'sellerHome' },
                { path: '/goods/form', component: GoodsForm , name: 'goodsForm' },
            ]
        },
        {
            path: '/decoration',
            component: () => import('@/pages/decoration/DecorationLayout.vue'),
            children: [
                { path: 'app/home', component: import('@/pages/decoration/app/home/Home.vue'), name: 'decorationApp' },
            ]
        }
    ],
});
export default router
