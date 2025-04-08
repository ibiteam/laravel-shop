import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/pages/auth/Login.vue'
import Home from '@/pages/Home.vue';
import GoodsCateGoryIndex from '@/pages/goods/Category.vue';
import MaterialIndex from '@/pages/material/Index.vue';
import Goods from '@/pages/goods/Goods.vue';
import GoodsForm from '@/pages/goods/GoodsForm.vue';
import SetShopConfig from '@/pages/set/ShopConfig.vue';
import SetRouterCategory from '@/pages/set/RouterCategory.vue';
import SetRouter from '@/pages/set/Router.vue';
import UserIndex from '@/pages/user/UserIndex.vue';
import UserAddress from '@/pages/user/UserAddress.vue';
import PaymentMethod from '@/pages/set/PaymentMethod.vue';
import AppDecoration from '@/pages/decoration/app/Index.vue';
import Refresh from '@/pages/refresh/refresh.vue';
import ShipCompany from '@/pages/set/ShipCompany.vue';
const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_MANAGE_PREFIX||'manage'),
    routes: [
        { path: '/login', component: Login, name: 'login' },
        {
            path:'/',
            component: () => import('@/components/Layout.vue'),
            children:[
                // 首页
                { path: '/home', component: Home, name: 'manage.home.index',meta:{title:'首页'}},
                // 设置
                { path: '/set/shop_config', component: SetShopConfig, name: 'manage.shop_config.index',meta:{title:'商店设置'} },
                { path: '/set/router_category', component: SetRouterCategory, name: 'manage.router_category.index',meta:{title:'访问地址分类'} },
                { path: '/set/router', component: SetRouter, name: 'manage.router.index',meta:{title:'访问地址'} },
                { path: '/set/payment_method', component: PaymentMethod, name: 'manage.payment_method.index',meta:{title:'支付方式'} },
                { path: '/set/ship_company', component: ShipCompany, name: 'manage.ship_company.index',meta:{title:'快递公司'} },
                // 商品
                { path: '/goods/index', component: Goods , name: 'manage.goods.index',meta:{title:'商品列表'}},
                { path: '/goods/form/:id', component: GoodsForm , name: 'manage.goods.form',meta:{title:'编辑商品'} },
                { path: '/goods/category', component: GoodsCateGoryIndex, name: 'manage.category.index',meta:{title:'商品分类'} },
                // 用户
                { path: '/user/index', component: UserIndex , name: 'manage.user.index',meta:{title:'用户'} },
                { path: '/user/address', component: UserAddress , name: 'manage.user.address',meta:{title:'用户地址'} },
                // 工具
                { path: '/material/index', component: MaterialIndex, name: 'manage.material_center.index',meta:{title:'素材中心'} },
                //正在刷新
                { path: '/refresh', component: Refresh, name: 'manage.refresh.index',meta:{title:'正在刷新'} },
                // 移动端装修
                { path: '/decoration/app', component: AppDecoration , name: 'manage.app_decoration.index', meta:{title:'移动端装修'} },
                { path: '/decoration/app/home', component: () => import('@/pages/decoration/app/home/Home.vue'), name: 'decorationAppHome', meta:{title:'移动端装修 - 首页'} },
            ]
        },
    ],
});
export default router
