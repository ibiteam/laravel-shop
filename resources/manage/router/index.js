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
import SetAdminUser from '@/pages/set/AdminUser.vue';
import SetRole from '@/pages/set/Role.vue';
import SetPermission from '@/pages/set/Permission.vue';
import SetAdminOperationLog from '@/pages/set/AdminOperationLog.vue';
import UserIndex from '@/pages/user/UserIndex.vue';
import UserAddress from '@/pages/user/UserAddress.vue';
import Payment from '@/pages/set/Payment.vue';
import AppDecoration from '@/pages/decoration/app/Index.vue';
import Refresh from '@/pages/refresh/refresh.vue';
import ShipCompany from '@/pages/set/ShipCompany.vue';
import Order from '@/pages/order/Order.vue';
import OrderDetail from '@/pages/order/Detail.vue';
import ApplyRefundReason from '@/pages/order/ApplyRefundReason.vue';
import ApplyRefund from '@/pages/order/ApplyRefund.vue';
import OrderDelivery from '@/pages/order/OrderDelivery.vue';
import Transaction from '@/pages/set/Transaction.vue';
import WechatUser from '@/pages/user/WechatUser.vue';
import Evaluate from '@/pages/order/Evaluate.vue';
import ArticleCategory from '@/pages/article/ArticleCategory.vue';
import Article from '@/pages/article/Article.vue';
import ArticleForm from '@/pages/article/ArticleForm.vue';

const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_MANAGE_PREFIX||'manage'),
    routes: [
        { path: '/login', component: Login, name: 'login' },
        {
            path:'/',
            redirect: '/',
            component: () => import('@/components/Layout.vue'),
            children:[
                // 首页
                { path: '/', component: Home, name: 'manage.home.index',meta:{title:'首页'}},
                // 设置
                { path: '/set/shop_config', component: SetShopConfig, name: 'manage.shop_config.index',meta:{title:'商店设置'} },
                { path: '/set/router_category', component: SetRouterCategory, name: 'manage.router_category.index',meta:{title:'访问地址分类'} },
                { path: '/set/router', component: SetRouter, name: 'manage.router.index',meta:{title:'访问地址'} },
                { path: '/set/admin_user', component: SetAdminUser, name: 'manage.admin_user.index',meta:{title:'管理员列表'} },
                { path: '/set/role', component: SetRole, name: 'manage.role.index',meta:{title:'角色管理'} },
                { path: '/set/permission', component: SetPermission, name: 'manage.permission.index',meta:{title:'权限菜单'} },
                { path: '/set/admin_operation_log', component: SetAdminOperationLog, name: 'manage.admin_operation_log.index',meta:{title:'管理员日志'} },
                { path: '/set/payment', component: Payment, name: 'manage.payment.index',meta:{title:'支付方式'} },
                { path: '/set/transaction', component: Transaction, name: 'manage.transaction.index',meta:{title:'交易记录'} },
                { path: '/set/ship_company', component: ShipCompany, name: 'manage.ship_company.index',meta:{title:'快递公司'} },
                // 商品
                { path: '/goods/index', component: Goods , name: 'manage.goods.index',meta:{title:'商品列表'}},
                { path: '/goods/form/:id', component: GoodsForm , name: 'manage.goods.form',meta:{title:'编辑商品'} },
                { path: '/goods/category', component: GoodsCateGoryIndex, name: 'manage.category.index',meta:{title:'商品分类'} },
                // 订单
                { path: '/order/index', component: Order, name: 'manage.order.index',meta:{title:'订单列表'} },
                { path: '/order/detail/:no', component: OrderDetail, name: 'manage.order.detail',meta:{title:'订单详情'} },
                { path: '/order/apply_refund_reason', component: ApplyRefundReason, name: 'manage.apply_refund_reason.index',meta:{title:'退款原因'} },
                { path: '/order/apply_refund', component: ApplyRefund, name: 'manage.apply_refund.index',meta:{title:'退款申请'} },
                { path: '/order/delivery', component: OrderDelivery, name: 'manage.order_delivery.index',meta:{title:'发货列表'} },
                { path: '/order/evaluate', component: Evaluate, name: 'manage.order_evaluate.index',meta:{title:'评价列表'} },
                // 用户
                { path: '/user/index', component: UserIndex , name: 'manage.user.index',meta:{title:'用户'} },
                { path: '/user/address', component: UserAddress , name: 'manage.user.address',meta:{title:'用户地址'} },
                { path: '/user/wechat_user', component: WechatUser , name: 'manage.wechat_user.index',meta:{title:'微信服务号'} },
                // 文章
                { path: '/article/article_category', component: ArticleCategory, name: 'manage.article_category.index',meta:{title:'文章分类'} },
                { path: '/article/article', component: Article, name: 'manage.article.index',meta:{title:'文章列表'} },
                { path: '/article/article_form/:id', component: ArticleForm , name: 'manage.article.form',meta:{title:'编辑文章'} },
                // 工具
                { path: '/material/index', component: MaterialIndex, name: 'manage.material_center.index',meta:{title:'素材中心'} },
                //正在刷新
                { path: '/refresh', component: Refresh, name: 'manage.refresh.index',meta:{title:'正在刷新'} },
                // 移动端装修
                { path: '/decoration/app', component: AppDecoration , name: 'manage.app_decoration.index', meta:{title:'移动端装修'} },
                { path: '/decoration/app/home', component: () => import('@/pages/decoration/app/home/Home.vue'), name: 'decorationAppHome', meta:{title:'移动端装修 - 首页'} },
            ]
        },
        { path: '/:pathMatch(.*)*', name: 'not-found', redirect: '/' }
    ],
});
export default router
