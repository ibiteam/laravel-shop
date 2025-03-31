import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/pages/user/Login.vue'
import Home from '@/pages/Home.vue';
import GoodsCateGoryIndex from '@/pages/goods/Category.vue';
import MaterialIndex from '@/pages/material/Index.vue';
import GoodsForm from '@/pages/goods/GoodsForm.vue';
import SetShopConfig from '@/pages/set/ShopConfig.vue';

import UserIndex from '@/pages/user/UserIndex.vue';
import UserAddress from '@/pages/user/UserAddress.vue';
const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_MANAGE_PREFIX||'manage'),
    routes: [
        { path: '/login', component: Login, name: 'login' },
        {
            path:'/',
            component: () => import('@/components/Layout.vue'),
            children:[
                { path: 'home', component: import('@/pages/Home.vue'), name: 'sellerHome' },
                { path: '/goods/form', component: GoodsForm , name: 'goodsForm' },
                { path: '/goods/category', component: GoodsCateGoryIndex, name: 'GoodsCategoryIndex' },
                { path: '/material/index', component: MaterialIndex, name: 'MaterialIndex' },
                { path: '/set/shop_config', component: SetShopConfig, name: 'SetShopConfig' },
                { path: '/user/index', component: UserIndex , name: 'UserIndex' },
                { path: '/user/address', component: UserAddress , name: 'UserAddress' },
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
