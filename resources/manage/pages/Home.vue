<template>
    <div class='seller-home'>
        <div class="home-container">
            <div class="home-content s-flex jc-bt">
                <div class="home-left">
                    <div class="information">
                        <div class="welcome">
                            <em class=""></em>
                            <span><em style="font-size: 30px;margin-right: 5px;">👏</em>早上好，胡俊</span>
                        </div>
                        <div class="quick-view-box s-flex">
                            <div class="quick-view s-flex ai-ct">
                                <div class="fonts s-flex ai-ct jc-ct">
                                    <em class="iconfont icon-reyonghushujufenxi" style="color: #789bfa"></em>
                                </div>
                                <div class="s-flex flex-dir ai-fs">
                                    <span>订单数量</span>
                                    <span>373</span>
                                </div>
                            </div>
                            <div class="quick-view s-flex ai-ct">
                                <div class="fonts s-flex ai-ct jc-ct">
                                    <em class="iconfont icon-wenzhang1" style="color: #b557fc;font-size: 22px;"></em>
                                </div>
                                <div class="s-flex flex-dir ai-fs">
                                    <span>销售额</span>
                                    <span>368</span>
                                </div>
                            </div>
                            <div class="quick-view s-flex ai-ct">
                                <div class="fonts s-flex ai-ct jc-ct">
                                    <em class="iconfont icon-xiaoxi" style="color: #fdae62;font-size: 22px;"></em>
                                </div>
                                <div class="s-flex flex-dir ai-fs">
                                    <span>商品数</span>
                                    <span>368</span>
                                </div>
                            </div>
                            <div class="quick-view s-flex ai-ct">
                                <div class="fonts s-flex ai-ct jc-ct">
                                    <em class="iconfont icon-icon-rizhi" style="color: #2cb5fa"></em>
                                </div>
                                <div class="s-flex flex-dir ai-fs">
                                    <span>未发货订单数</span>
                                    <span>0</span>
                                </div>
                            </div>
                        </div>
                        <div class="access-data">
                            <div class="access-data-header s-flex jc-bt ai-ct">
                                <div class="data-header">
                                    <span>销售数据</span>
                                    <span style="font-size: 12px;color: #ccc">（近7日）</span>
                                </div>
                                <div class="mores">
                                    <span>查看更多</span>
                                </div>
                            </div>
                            <div style="width: 100%;height:400px;" id="access-data"></div>
                        </div>
                    </div>
                    <div class="information-bottom s-flex jc-bt">
                        <div class="project-proportion">
                            <div class="access-data-header s-flex ai-ct">
                                <div class="data-header">
                                    <span>商品销售品类占比</span>
                                </div>
                            </div>
                            <div style="width: 100%;height:350px;" id="sector-data"></div>
                        </div>
                        <div class="project-proportion">
                            <div class="access-data-header s-flex ai-ct">
                                <div class="data-header">
                                    <span>订单来源占比</span>
                                </div>
                            </div>
                            <div style="width: 100%;height:350px;" id="order-data"></div>
                        </div>
                    </div>
                </div>
                <div class="home-right">
                    <div class="shortcut">
                        <div class="collection">
                            <div class="access-data-header s-flex jc-bt ai-ct">
                                <div class="data-header">
                                    <span>我的收藏</span>
                                </div>
                                <div class="mores" @click="openCollect">
                                    <span>管理</span>
                                </div>
                            </div>
                            <div class="more-opt s-flex flex-wrap">
                                <div class="opt-list" v-for="item in my_collect" :key="item.value">
                                    <div class="icon">
                                        <em class="iconfont icon-neirong"></em>
                                    </div>
                                    <div class="titles">
                                        <span>{{item.title}}</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="line" style="border-bottom: solid 1px #F2F3F5;margin: 20px 0;"></div>
                        <div class="recently-visited">
                            <div class="access-data-header s-flex jc-bt ai-ct">
                                <div class="data-header">
                                    <span>最近访问</span>
                                </div>
                            </div>
                            <div class="more-opt s-flex flex-wrap">
                                <div class="opt-list" v-for="item in recent_access_records">
                                    <div class="icon">
                                        <em :class="item.icon"></em>
                                    </div>
                                    <div class="titles">
                                        <span>{{item.name}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <el-drawer
            class="collection-drawer"
            v-model="collectionVisible"
            @close='closeCollect'
            size="80%"
            :append-to-body='false'
            direction="rtl">
            <div class="collection-box s-flex jc-bt">
                <div class="right-collection flex-1">
                    <template v-if="my_collect.length">
                        <div class="menu-listRouter" v-for="(item,index) in my_collect" :key="item.value">
                            <span>@{{ item.title }}</span>
                            <i class="el-icon-error" style="display: none;color: #ccc;"></i>
                        </div>
                    </template>
                    <div class="no-data" v-else style="padding: 20px 20px 0 20px;height: 100%;">
                        <span>暂无收藏</span>
                    </div>
                </div>
                <div class="left-collection">
                    <div class="search-menu">
                        <el-input placeholder="请输入关键词" v-model="searchtools" ref='searchtoolsRef' @input='searchMenusFnc'>
                            <template #prefix>
                                <div style='font-size: 20px' class='s-flex ai-ct jc-ct'>
                                    <Search style='width: 1.5em;height: 1.5em;color: var(--main-color)' />
                                </div>
                            </template>
                        </el-input>
                    </div>
                    <div class='menu-overflow'>
                        <div class="menu-scroll">
                            <div class="menu-list" v-for="(item,index) in searchMenus" :key="item.value">
                                <div class="menu-title"><span>{{ item.title }}</span></div>
                                <div class="menu-second" v-for="(its,ids) in item.children" :key="its.value">
                                    <div class="menu-title"><span>{{ its.title }}</span></div>
                                    <div class="menu-listRouter s-flex jc-bt ai-ct" v-for="itas in its.children" style="cursor: pointer;" @click="collectionFnc(itas,index,ids)">
                                        <span>{{ itas.title }}</span>
                                        <em class="el-icon-star-on" style="color: #ff6a00" v-if="itas.collection"></em>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </el-drawer>
    </div>
</template>

<script setup lang='ts'>
import { nextTick, onMounted, ref, reactive, onUnmounted, computed } from 'vue';
import * as echarts from 'echarts'
import $public from '@/utils/public'
import { Search } from '@element-plus/icons-vue'


let lineRef = null
let saleRef = null
let sourceRef = null

const searchtoolsRef = ref(null)
const searchtools = ref('')

const recent_access_records = ref([{"name":"应用分组","url":"https://testdoc.ptdplat.com/manage/config_center/group","icon":"el-icon-tickets"},{"name":"应用列表","url":"https://testdoc.ptdplat.com/manage/config_center/application","icon":"el-icon-tickets"},{"name":"服务器","url":"https://testdoc.ptdplat.com/manage/serve_organ","icon":"el-icon-tickets"},{"name":"机器人列表","url":"https://testdoc.ptdplat.com/manage/chat/application_program_robot/list","icon":"el-icon-tickets"},{"name":"应用列表","url":"https://testdoc.ptdplat.com/manage/chat/application_program/list","icon":"el-icon-tickets"},{"name":"服务器分组","url":"https://testdoc.ptdplat.com/manage/serve_cate","icon":"el-icon-tickets"},{"name":"用户分组","url":"https://testdoc.ptdplat.com/manage/member_group","icon":"el-icon-tickets"},{"name":"消息","url":"","icon":"el-icon-chat-round"}])
const my_collect = ref([])
const access_statistic = reactive({"pc":{"name":"PC","statistic_date":["2025-03-20","2025-03-21","2025-03-22","2025-03-23","2025-03-24","2025-03-25","2025-03-26"],"uv_number":[16,16,2,1,20,16,18]},"admin":{"name":"Admin","statistic_date":["2025-03-20","2025-03-21","2025-03-22","2025-03-23","2025-03-24","2025-03-25","2025-03-26"],"uv_number":[0,0,0,0,0,0,0]},"ibi":{"name":"App","statistic_date":["2025-03-20","2025-03-21","2025-03-22","2025-03-23","2025-03-24","2025-03-25","2025-03-26"],"uv_number":[0,0,0,0,0,0,0]}})
const collectionVisible = ref<boolean>(false)
const menus = ref([{"index":1,"value":1,"parent_id":0,"collection":false,"title":"设置","src":"","icon":"el-icon-setting","key_value":"set","level":0,"children":[{"index":2,"value":2,"parent_id":1,"collection":false,"title":"系统设置","src":"","icon":"el-icon-tickets","key_value":"system_set","level":1,"children":[{"index":3,"value":3,"parent_id":2,"collection":false,"title":"站点设置","src":"https://testdoc.ptdplat.com/manage/config","icon":"el-icon-tickets","key_value":"site_set","level":2},{"index":4,"value":4,"parent_id":2,"collection":false,"title":"自定义导航","src":"https://testdoc.ptdplat.com/manage/web_nav","icon":"el-icon-tickets","key_value":"web_nav","level":2},{"index":5,"value":5,"parent_id":2,"collection":false,"title":"路由表","src":"https://testdoc.ptdplat.com/manage/router","icon":"el-icon-tickets","key_value":"router","level":2},{"index":6,"value":6,"parent_id":2,"collection":false,"title":"路由分类","src":"https://testdoc.ptdplat.com/manage/router/cate","icon":"el-icon-tickets","key_value":"router_cate","level":2},{"index":7,"value":7,"parent_id":2,"collection":false,"title":"授权配置","src":"https://testdoc.ptdplat.com/manage/auth_config","icon":"el-icon-tickets","key_value":"auth_config","level":2}]},{"index":8,"value":8,"parent_id":1,"collection":false,"title":"工作台","src":"","icon":"el-icon-tickets","key_value":"website","level":1,"children":[{"index":9,"value":9,"parent_id":8,"collection":false,"title":"应用分组","src":"https://testdoc.ptdplat.com/manage/website/category","icon":"el-icon-tickets","key_value":"website_cagegory","level":2},{"index":10,"value":10,"parent_id":8,"collection":false,"title":"应用列表","src":"https://testdoc.ptdplat.com/manage/website/website","icon":"el-icon-tickets","key_value":"application_program_list","level":2},{"index":11,"value":11,"parent_id":8,"collection":false,"title":"申请列表","src":"https://testdoc.ptdplat.com/manage/website/apply","icon":"el-icon-tickets","key_value":"website_apply","level":2},{"index":12,"value":12,"parent_id":8,"collection":false,"title":"用户应用","src":"https://testdoc.ptdplat.com/manage/website/user_website","icon":"el-icon-tickets","key_value":"user_website","level":2},{"index":13,"value":13,"parent_id":8,"collection":false,"title":"角色置换","src":"https://testdoc.ptdplat.com/manage/role_displace","icon":"el-icon-tickets","key_value":"role_displace","level":2}]},{"index":14,"value":14,"parent_id":1,"collection":false,"title":"权限管理","src":"","icon":"el-icon-tickets","key_value":"permission_manage","level":1,"children":[{"index":15,"value":15,"parent_id":14,"collection":false,"title":"管理员","src":"https://testdoc.ptdplat.com/manage/admin_user","icon":"el-icon-tickets","key_value":"admin_user","level":2},{"index":16,"value":16,"parent_id":14,"collection":false,"title":"角色管理","src":"https://testdoc.ptdplat.com/manage/role","icon":"el-icon-tickets","key_value":"role_manage","level":2},{"index":17,"value":17,"parent_id":14,"collection":false,"title":"权限菜单","src":"https://testdoc.ptdplat.com/manage/forum_permission","icon":"el-icon-tickets","key_value":"permission_menu","level":2},{"index":18,"value":18,"parent_id":14,"collection":false,"title":"操作日志","src":"https://testdoc.ptdplat.com/manage/action_log","icon":"el-icon-tickets","key_value":"operation_log","level":2},{"index":19,"value":19,"parent_id":14,"collection":false,"title":"最近访问","src":"https://testdoc.ptdplat.com/manage/forum_permission/backend_recent_visit_list","icon":"el-icon-tickets","key_value":"recent_visit_list","level":2}]},{"index":20,"value":20,"parent_id":1,"collection":false,"title":"三方服务","src":"","icon":"el-icon-tickets","key_value":"third_service","level":1,"children":[{"index":21,"value":21,"parent_id":20,"collection":false,"title":"外部服务","src":"https://testdoc.ptdplat.com/manage/app_service_config","icon":"el-icon-tickets","key_value":"external_services","level":2},{"index":22,"value":22,"parent_id":20,"collection":false,"title":"外部服务日志","src":"https://testdoc.ptdplat.com/manage/app_service_log","icon":"el-icon-tickets","key_value":"external_service_logs","level":2},{"index":124,"value":124,"parent_id":20,"collection":false,"title":"短信平台","src":"https://testdoc.ptdplat.com/manage/phone_msg","icon":"el-icon-tickets","key_value":"phone_msg","level":2}]},{"index":23,"value":23,"parent_id":1,"collection":false,"title":"设备管理","src":"","icon":"el-icon-tickets","key_value":"equipment_manage","level":1,"children":[{"index":24,"value":24,"parent_id":23,"collection":false,"title":"客户端管理","src":"https://testdoc.ptdplat.com/manage/client_version","icon":"el-icon-tickets","key_value":"manage_client_version_index","level":2},{"index":25,"value":25,"parent_id":23,"collection":false,"title":"设备列表","src":"https://testdoc.ptdplat.com/manage/device","icon":"el-icon-tickets","key_value":"manage_device_info_index","level":2}]}]},{"index":26,"value":26,"parent_id":0,"collection":false,"title":"用户","src":"","icon":"el-icon-s-data","key_value":"user","level":0,"children":[{"index":27,"value":27,"parent_id":26,"collection":false,"title":"用户管理","src":"","icon":"el-icon-tickets","key_value":"user_manage","level":1,"children":[{"index":28,"value":28,"parent_id":27,"collection":false,"title":"用户列表","src":"https://testdoc.ptdplat.com/manage/member","icon":"el-icon-tickets","key_value":"user_list","level":2},{"index":29,"value":29,"parent_id":27,"collection":false,"title":"积分等级","src":"https://testdoc.ptdplat.com/manage/user_level","icon":"el-icon-tickets","key_value":"forum_user_level","level":2},{"index":30,"value":30,"parent_id":27,"collection":false,"title":"部门列表","src":"https://testdoc.ptdplat.com/manage/department","icon":"el-icon-tickets","key_value":"department_list","level":2},{"index":31,"value":31,"parent_id":27,"collection":false,"title":"考勤列表","src":"https://testdoc.ptdplat.com/manage/forum_check_on","icon":"el-icon-tickets","key_value":"check_on","level":2},{"index":120,"value":120,"parent_id":27,"collection":false,"title":"用户分组","src":"https://testdoc.ptdplat.com/manage/member_group","icon":"el-icon-tickets","key_value":"member_group","level":2},{"index":121,"value":121,"parent_id":27,"collection":false,"title":"公司员工","src":"https://testdoc.ptdplat.com/manage/company_member","icon":"el-icon-tickets","key_value":"company_member","level":2},{"index":122,"value":122,"parent_id":27,"collection":false,"title":"公司部门","src":"https://testdoc.ptdplat.com/manage/company_work","icon":"el-icon-tickets","key_value":"company_work","level":2}]},{"index":32,"value":32,"parent_id":26,"collection":false,"title":"积分管理","src":"","icon":"el-icon-tickets","key_value":"integral_manage","level":1,"children":[{"index":33,"value":33,"parent_id":32,"collection":false,"title":"积分列表","src":"https://testdoc.ptdplat.com/manage/integral","icon":"el-icon-tickets","key_value":"integral_list","level":2},{"index":34,"value":34,"parent_id":32,"collection":false,"title":"积分统计报表","src":"https://testdoc.ptdplat.com/manage/integral_statistics","icon":"el-icon-tickets","key_value":"integral_statistics","level":2}]}]},{"index":35,"value":35,"parent_id":0,"collection":false,"title":"文章","src":"","icon":"el-icon-document","key_value":"article","level":0,"children":[{"index":36,"value":36,"parent_id":35,"collection":false,"title":"文章管理","src":"","icon":"el-icon-tickets","key_value":"article_manage","level":1,"children":[{"index":37,"value":37,"parent_id":36,"collection":false,"title":"文章列表","src":"https://testdoc.ptdplat.com/manage/article_index","icon":"el-icon-tickets","key_value":"article_list","level":2},{"index":38,"value":38,"parent_id":36,"collection":false,"title":"分类列表","src":"https://testdoc.ptdplat.com/manage/category_index","icon":"el-icon-tickets","key_value":"cate_index","level":2},{"index":39,"value":39,"parent_id":36,"collection":false,"title":"用户访问列表","src":"https://testdoc.ptdplat.com/manage/user_view","icon":"el-icon-tickets","key_value":"user_view","level":2},{"index":40,"value":40,"parent_id":36,"collection":false,"title":"回收站","src":"https://testdoc.ptdplat.com/manage/article_recycle","icon":"el-icon-tickets","key_value":"article_recycle","level":2},{"index":41,"value":41,"parent_id":36,"collection":false,"title":"用户文章统计","src":"https://testdoc.ptdplat.com/manage/article_statistics","icon":"el-icon-tickets","key_value":"statistics","level":2}]},{"index":42,"value":42,"parent_id":35,"collection":false,"title":"文档管理","src":"","icon":"el-icon-tickets","key_value":"document_manage","level":1,"children":[{"index":43,"value":43,"parent_id":42,"collection":false,"title":"文档列表","src":"https://testdoc.ptdplat.com/manage/document","icon":"el-icon-tickets","key_value":"document_list","level":2},{"index":44,"value":44,"parent_id":42,"collection":false,"title":"模板分类列表","src":"https://testdoc.ptdplat.com/manage/document_template_category","icon":"el-icon-tickets","key_value":"template_category","level":2},{"index":45,"value":45,"parent_id":42,"collection":false,"title":"模板列表","src":"https://testdoc.ptdplat.com/manage/document_template","icon":"el-icon-tickets","key_value":"template_list","level":2}]},{"index":46,"value":46,"parent_id":35,"collection":false,"title":"消息管理","src":"","icon":"el-icon-tickets","key_value":"comment_manage","level":1,"children":[{"index":47,"value":47,"parent_id":46,"collection":false,"title":"评论列表","src":"https://testdoc.ptdplat.com/manage/comment_index","icon":"el-icon-tickets","key_value":"comment_list","level":2},{"index":48,"value":48,"parent_id":46,"collection":false,"title":"私信列表","src":"https://testdoc.ptdplat.com/manage/message","icon":"el-icon-tickets","key_value":"message_list","level":2},{"index":49,"value":49,"parent_id":46,"collection":false,"title":"通知列表","src":"https://testdoc.ptdplat.com/manage/notification","icon":"el-icon-tickets","key_value":"notification_list","level":2}]}]},{"index":50,"value":50,"parent_id":0,"collection":false,"title":"工具","src":"","icon":"el-icon-tickets","key_value":"tool","level":0,"children":[{"index":51,"value":51,"parent_id":50,"collection":false,"title":"站点工具","src":"","icon":"el-icon-tickets","key_value":"site_tool","level":1,"children":[{"index":52,"value":52,"parent_id":51,"collection":false,"title":"素材列表","src":"https://testdoc.ptdplat.com/manage/materials_list","icon":"el-icon-tickets","key_value":"materials_list","level":2}]},{"index":53,"value":53,"parent_id":50,"collection":false,"title":"工单管理","src":"","icon":"el-icon-tickets","key_value":"work_order","level":1,"children":[{"index":54,"value":54,"parent_id":53,"collection":false,"title":"工单列表","src":"https://testdoc.ptdplat.com/manage/work_order","icon":"el-icon-tickets","key_value":"work_order_list","level":2},{"index":55,"value":55,"parent_id":53,"collection":false,"title":"分类配置","src":"https://testdoc.ptdplat.com/manage/work_order_category","icon":"el-icon-tickets","key_value":"work_order_category","level":2},{"index":56,"value":56,"parent_id":53,"collection":false,"title":"评分配置","src":"https://testdoc.ptdplat.com/manage/work_order_score","icon":"el-icon-tickets","key_value":"work_order_score","level":2}]},{"index":57,"value":57,"parent_id":50,"collection":false,"title":"培训管理","src":"","icon":"el-icon-tickets","key_value":"tran_manage","level":1,"children":[{"index":58,"value":58,"parent_id":57,"collection":false,"title":"培训列表","src":"https://testdoc.ptdplat.com/manage/train","icon":"el-icon-tickets","key_value":"train","level":2}]},{"index":59,"value":59,"parent_id":50,"collection":false,"title":"会议室管理","src":"","icon":"el-icon-tickets","key_value":"meeting_room_manage","level":1,"children":[{"index":60,"value":60,"parent_id":59,"collection":false,"title":"会议室列表","src":"https://testdoc.ptdplat.com/manage/meeting_room_list/index","icon":"el-icon-tickets","key_value":"meeting_room_list","level":2},{"index":61,"value":61,"parent_id":59,"collection":false,"title":"会议室预约","src":"https://testdoc.ptdplat.com/manage/meeting_room_reserve/index","icon":"el-icon-tickets","key_value":"meeting_room_reserve","level":2}]},{"index":62,"value":62,"parent_id":50,"collection":false,"title":"工具管理","src":"","icon":"el-icon-tickets","key_value":"tool_manage","level":1,"children":[{"index":63,"value":63,"parent_id":62,"collection":false,"title":"生成器","src":"https://testdoc.ptdplat.com/manage/tool_generator","icon":"el-icon-tickets","key_value":"generator","level":2}]},{"index":64,"value":64,"parent_id":50,"collection":false,"title":"堡垒机","src":"","icon":"el-icon-tickets","key_value":"fortress_machine","level":1,"children":[{"index":65,"value":65,"parent_id":64,"collection":false,"title":"服务器","src":"https://testdoc.ptdplat.com/manage/serve_organ","icon":"el-icon-tickets","key_value":"serve_organ","level":2},{"index":66,"value":66,"parent_id":64,"collection":false,"title":"服务器执行记录","src":"https://testdoc.ptdplat.com/manage/serve_organ_log","icon":"el-icon-tickets","key_value":"serve_organ_log","level":2},{"index":119,"value":119,"parent_id":64,"collection":false,"title":"服务器分组","src":"https://testdoc.ptdplat.com/manage/serve_cate","icon":"el-icon-tickets","key_value":"serve_cate","level":2}]},{"index":67,"value":67,"parent_id":50,"collection":false,"title":"配置中心","src":"","icon":"el-icon-tickets","key_value":"config_center","level":1,"children":[{"index":68,"value":68,"parent_id":67,"collection":false,"title":"应用列表","src":"https://testdoc.ptdplat.com/manage/config_center/application","icon":"el-icon-tickets","key_value":"config_center_application_list","level":2},{"index":69,"value":69,"parent_id":67,"collection":false,"title":"应用分组","src":"https://testdoc.ptdplat.com/manage/config_center/group","icon":"el-icon-tickets","key_value":"config_center_application_group","level":2},{"index":70,"value":70,"parent_id":67,"collection":false,"title":"应用请求记录","src":"https://testdoc.ptdplat.com/manage/config_center/request_log","icon":"el-icon-tickets","key_value":"config_center_request_log","level":2}]}]},{"index":71,"value":71,"parent_id":0,"collection":false,"title":"数据","src":"","icon":"el-icon-c-scale-to-original","key_value":"data","level":0,"children":[{"index":72,"value":72,"parent_id":71,"collection":false,"title":"广告管理","src":"","icon":"el-icon-tickets","key_value":"advert_manage","level":1,"children":[{"index":73,"value":73,"parent_id":72,"collection":false,"title":"轮播图","src":"https://testdoc.ptdplat.com/manage/advert_cate","icon":"el-icon-tickets","key_value":"advert_list","level":2}]},{"index":74,"value":74,"parent_id":71,"collection":false,"title":"日志管理","src":"","icon":"el-icon-tickets","key_value":"login_log_manage","level":1,"children":[{"index":75,"value":75,"parent_id":74,"collection":false,"title":"最近访问","src":"https://testdoc.ptdplat.com/manage/recent_visit_list","icon":"el-icon-tickets","key_value":"recent_visit_list","level":2},{"index":76,"value":76,"parent_id":74,"collection":false,"title":"登录日志","src":"https://testdoc.ptdplat.com/manage/login_log","icon":"el-icon-tickets","key_value":"login_log_list","level":2},{"index":77,"value":77,"parent_id":74,"collection":false,"title":"访问统计","src":"https://testdoc.ptdplat.com/manage/access_statistic","icon":"el-icon-tickets","key_value":"access_statistic","level":2}]},{"index":78,"value":78,"parent_id":71,"collection":false,"title":"常用功能","src":"","icon":"el-icon-tickets","key_value":"common_functions","level":1,"children":[{"index":79,"value":79,"parent_id":78,"collection":false,"title":"备忘录","src":"https://testdoc.ptdplat.com/manage/memorandum","icon":"el-icon-tickets","key_value":"memorandum","level":2}]},{"index":80,"value":80,"parent_id":71,"collection":false,"title":"应用管理","src":"","icon":"el-icon-tickets","key_value":"application_manage","level":1,"children":[{"index":81,"value":81,"parent_id":80,"collection":false,"title":"应用列表","src":"https://testdoc.ptdplat.com/manage/application_list","icon":"el-icon-tickets","key_value":"application_list","level":2},{"index":82,"value":82,"parent_id":80,"collection":false,"title":"应用请求日志","src":"https://testdoc.ptdplat.com/manage/request_logs","icon":"el-icon-tickets","key_value":"request_logs","level":2}]}]},{"index":83,"value":83,"parent_id":0,"collection":false,"title":"项目","src":"","icon":"el-icon-user-solid","key_value":"forum_project","level":0,"children":[{"index":84,"value":84,"parent_id":83,"collection":false,"title":"项目管理","src":"","icon":"el-icon-tickets","key_value":"project_manage","level":1,"children":[{"index":85,"value":85,"parent_id":84,"collection":false,"title":"项目列表","src":"https://testdoc.ptdplat.com/manage/project_list","icon":"el-icon-tickets","key_value":"project_list","level":2},{"index":86,"value":86,"parent_id":84,"collection":false,"title":"需求列表","src":"https://testdoc.ptdplat.com/manage/demand_list","icon":"el-icon-tickets","key_value":"demand_list","level":2},{"index":87,"value":87,"parent_id":84,"collection":false,"title":"任务列表","src":"https://testdoc.ptdplat.com/manage/task_list","icon":"el-icon-tickets","key_value":"task_list","level":2},{"index":91,"value":91,"parent_id":84,"collection":false,"title":"问题列表","src":"https://testdoc.ptdplat.com/manage/problem_list","icon":"el-icon-tickets","key_value":"problem_list","level":2},{"index":92,"value":92,"parent_id":84,"collection":false,"title":"项目统计","src":"https://testdoc.ptdplat.com/manage/project_statistics_list","icon":"el-icon-tickets","key_value":"project_statistics","level":2},{"index":93,"value":93,"parent_id":84,"collection":false,"title":"模板设置","src":"https://testdoc.ptdplat.com/manage/project_template","icon":"el-icon-tickets","key_value":"project_template_config","level":2},{"index":94,"value":94,"parent_id":84,"collection":false,"title":"项目操作日志","src":"https://testdoc.ptdplat.com/manage/project_operation_log","icon":"el-icon-tickets","key_value":"project_operation_log","level":2},{"index":95,"value":95,"parent_id":84,"collection":false,"title":"项目动态","src":"https://testdoc.ptdplat.com/manage/project_task_log","icon":"el-icon-tickets","key_value":"project_task_log","level":2}]},{"index":88,"value":88,"parent_id":83,"collection":false,"title":"工分管理","src":"","icon":"el-icon-tickets","key_value":"work_point_manage","level":1,"children":[{"index":89,"value":89,"parent_id":88,"collection":false,"title":"工分列表","src":"https://testdoc.ptdplat.com/manage/project_work_point_list","icon":"el-icon-tickets","key_value":"project_work_point_list","level":2},{"index":90,"value":90,"parent_id":88,"collection":false,"title":"工分详情","src":"https://testdoc.ptdplat.com/manage/project_details_work_point","icon":"el-icon-tickets","key_value":"project_details_work_point","level":2}]}]},{"index":96,"value":96,"parent_id":0,"collection":false,"title":"圈子","src":"","icon":"el-icon-chat-round","key_value":"forum_circle","level":0,"children":[{"index":97,"value":97,"parent_id":96,"collection":false,"title":"发现管理","src":"","icon":"el-icon-tickets","key_value":"discover_manage","level":1,"children":[{"index":98,"value":98,"parent_id":97,"collection":false,"title":"发现列表","src":"https://testdoc.ptdplat.com/manage/discover_list","icon":"el-icon-tickets","key_value":"discover_list","level":2}]},{"index":99,"value":99,"parent_id":96,"collection":false,"title":"圈子管理","src":"","icon":"el-icon-tickets","key_value":"circle_manage","level":1,"children":[{"index":100,"value":100,"parent_id":99,"collection":false,"title":"圈子列表","src":"https://testdoc.ptdplat.com/manage/circle_list","icon":"el-icon-tickets","key_value":"circle_list","level":2},{"index":101,"value":101,"parent_id":99,"collection":false,"title":"圈文列表","src":"https://testdoc.ptdplat.com/manage/circle_post_list","icon":"el-icon-tickets","key_value":"circle_post_list","level":2}]},{"index":102,"value":102,"parent_id":96,"collection":false,"title":"资源管理","src":"","icon":"el-icon-tickets","key_value":"resource_manage","level":1,"children":[{"index":103,"value":103,"parent_id":102,"collection":false,"title":"资源列表","src":"https://testdoc.ptdplat.com/manage/resource_list","icon":"el-icon-tickets","key_value":"resource_list","level":2},{"index":104,"value":104,"parent_id":102,"collection":false,"title":"违禁词","src":"https://testdoc.ptdplat.com/manage/forbidden_word_list","icon":"el-icon-tickets","key_value":"forbidden_word_list","level":2}]}]},{"index":105,"value":105,"parent_id":0,"collection":false,"title":"消息","src":"","icon":"el-icon-chat-round","key_value":"forum_chat","level":0,"children":[{"index":106,"value":106,"parent_id":105,"collection":false,"title":"应用管理","src":"","icon":"el-icon-tickets","key_value":"application_program_manage","level":1,"children":[{"index":107,"value":107,"parent_id":106,"collection":false,"title":"应用列表","src":"https://testdoc.ptdplat.com/manage/chat/application_program/list","icon":"el-icon-tickets","key_value":"application_program_list","level":2}]},{"index":108,"value":108,"parent_id":105,"collection":false,"title":"机器人管理","src":"","icon":"el-icon-tickets","key_value":"application_program_robot_manage","level":1,"children":[{"index":109,"value":109,"parent_id":108,"collection":false,"title":"机器人列表","src":"https://testdoc.ptdplat.com/manage/chat/application_program_robot/list","icon":"el-icon-tickets","key_value":"application_program_robot_list","level":2}]}]},{"index":110,"value":110,"parent_id":0,"collection":false,"title":"企淘","src":"","icon":"el-icon-goods","key_value":"qi_tao","level":0,"children":[{"index":111,"value":111,"parent_id":110,"collection":false,"title":"商品管理","src":"","icon":"el-icon-tickets","key_value":"goods_manage","level":1,"children":[{"index":112,"value":112,"parent_id":111,"collection":false,"title":"商品列表","src":"https://testdoc.ptdplat.com/manage/qi_tao/goods/goods_list","icon":"el-icon-tickets","key_value":"goods_list","level":2},{"index":113,"value":113,"parent_id":111,"collection":false,"title":"商品访问","src":"https://testdoc.ptdplat.com/manage/qi_tao/goods/view_user_goods","icon":"el-icon-tickets","key_value":"view_user_goods","level":2}]},{"index":114,"value":114,"parent_id":110,"collection":false,"title":"订单管理","src":"","icon":"el-icon-tickets","key_value":"order_manage","level":1,"children":[{"index":115,"value":115,"parent_id":114,"collection":false,"title":"订单列表","src":"https://testdoc.ptdplat.com/manage/qi_tao/order/order_list","icon":"el-icon-tickets","key_value":"order_list","level":2}]},{"index":116,"value":116,"parent_id":110,"collection":false,"title":"分类管理","src":"","icon":"el-icon-tickets","key_value":"category_manage","level":1,"children":[{"index":117,"value":117,"parent_id":116,"collection":false,"title":"分类列表","src":"https://testdoc.ptdplat.com/manage/qi_tao/goods/goods_cate","icon":"el-icon-tickets","key_value":"category_list","level":2}]}]}])
const searchMenus = ref([])

const getUvChartOption = (item) => {
    let xAxisData = [];
    let pc_uv = 0;
    let app_uv = 0;
    if (item?.pc?.statistic_date && Array.isArray(item.pc.statistic_date)) {
        xAxisData = item.pc.statistic_date;
        pc_uv = item.pc.uv_number;
    }
    if (item?.ibi?.statistic_date && Array.isArray(item.ibi.statistic_date)) {
        xAxisData = item.ibi.statistic_date;
        app_uv = item.ibi.uv_number;
    }
    return {
        title: {
            text: '',
            subtextStyle: {
                color: '#333333'
            }
        },
        legend: {},
        tooltip: {
            trigger: 'axis',
            padding: [10, 20, 10, 10]
        },
        grid: {
            left: '3%',
            right: '6%',
            bottom: '3%',
            containLabel: true
        },
        toolbox: {
            feature: {
                // saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: xAxisData,
            axisLabel: {
                showMaxLabel: true
            }
        },
        yAxis: {
            type: 'value',
            axisLine: {
                show: false // 隐藏 Y 轴的边框线
            }

        },
        series: [
            {
                name: '订单数',
                type: 'line',
                data: pc_uv,
                lineStyle: {
                    color: '#4081ff'
                },
                itemStyle: {
                    color: '#4081ff'
                },
                label: {
                    show: true // 显示数据值

                },
                smooth: true

            },
            {
                name: '销售额',
                type: 'line',
                data: app_uv,
                lineStyle: {
                    color: '#1EE7FF'
                },
                itemStyle: {
                    color: '#1EE7FF'
                },
                label: {
                    show: true // 显示数据值

                },
                smooth: true

            }

        ]
    };
}

const resizeFnc = () => {
    console.log(111);
    saleRef.resize()
    sourceRef.resize()
    lineRef.resize()
}

const debounceResize = $public.debounce(resizeFnc,100)

const closeCollect = () => {
    searchMenus.value = []
    searchtools.value = ''
}
const openCollect = () => {
    collectionVisible.value = true
    searchMenus.value = menus.value
    setTimeout(() => {
        searchtoolsRef.value.focus()
    },500)
}
const mapData = (arrs,newArr) => {
    for(let i=0;i<arrs.length;i++){
        if(arrs[i].hasOwnProperty('children')){ // 下拉节点
            if(arrs[i].title.indexOf(searchtools.value) > -1){
                newArr.push(arrs[i])
            }else{
                let newData = mapData(arrs[i].children,[])
                if(newData.length>0){
                    newArr.push({...arrs[i],children:newData})
                }else{
                    continue;
                }
            }
        }else{ // 最后一级
            if(arrs[i].title.indexOf(searchtools.value) > -1){
                newArr.push(arrs[i])
            }
            continue;
        }
    }
    return newArr
}
const searchMenusFnc = $public.debounce(() => {
    let arrs = menus.value
    let newArr = new Array()
    for(let i=0;i<arrs.length;i++){
        if(arrs[i].title.indexOf(searchtools.value) > -1){ // 如果顶级标题包含查询内容
            newArr.push({...arrs[i]})
            continue;
        }else if(arrs[i].hasOwnProperty('children')){
            let mapDatas = mapData(arrs[i].children,[])
            if(mapDatas.length>0){
                newArr.push({...arrs[i],children:mapDatas})
            }
        }
    }
    searchMenus.value = newArr
},500)


onMounted(() => {
    nextTick(() => {
        lineRef = echarts.init(document.getElementById('access-data'));
        lineRef.setOption(getUvChartOption(access_statistic));

        let dataSector = [
            { value: 500, name: '钛白', itemStyle: { color: '#259eff' } },
            { value: 400, name: '化工', itemStyle: { color: '#21ccff' } },
            { value: 300, name: '粮油', itemStyle: { color: '#313ba8' } },
            { value: 200, name: '芯片', itemStyle: { color: '#313ba8' } }
        ];

        let sumSector = dataSector.reduce(function(prev, curr) {
            return prev + curr.value;
        }, 0);
        saleRef = echarts.init(document.getElementById('sector-data'));
        saleRef.setOption({
            title: {
                text: '数量' + sumSector, // 将总和显示在标题中
                left: 'center', // 标题居中显示
                top: 'middle', // 标题垂直居中显示
                textStyle: {
                    fontSize: 14,
                    fontWeight: 400,
                    color: '#333'
                }
            },
            legend: {
                orient: 'horizontal', // 设置图例水平排列
                bottom: 10, // 设置图例位置为底部，距离底部的距离为10px
                data: ['钛白', '化工', '粮油', '芯片'] // 设置图例的数据，与数据项的名称对应
            },
            series: [
                {
                    type: 'pie',
                    radius: ['40%', '60%'],
                    label: {
                        formatter: '{d}% \n{c}个', // 设置标签格式化，显示名称、数值和百分比
                        position: 'top', // 标签位置在图中心
                        color: '#333', // 标签文字颜色
                        fontSize: 14, // 标签文字大小
                        fontWeight: 400

                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c}' // 设置tooltip的格式，显示名称和值
                    },
                    data:dataSector
                }
            ]
        });

        let dataOrder = [
            { value: 500, name: 'pc', itemStyle: { color: '#259eff' } },
            { value: 400, name: 'app', itemStyle: { color: '#21ccff' } },
            { value: 300, name: 'h5', itemStyle: { color: '#313ba8' } },
            { value: 200, name: '小程序', itemStyle: { color: '#313ba8' } }
        ];

        let sumOrder = dataOrder.reduce(function(prev, curr) {
            return prev + curr.value;
        }, 0);
        sourceRef = echarts.init(document.getElementById('order-data'));
        sourceRef.setOption({
            title: {
                text: '数量' + sumOrder, // 将总和显示在标题中
                left: 'center', // 标题居中显示
                top: 'middle', // 标题垂直居中显示
                textStyle: {
                    fontSize: 14,
                    fontWeight: 400,
                    color: '#333'
                }
            },
            legend: {
                orient: 'horizontal', // 设置图例水平排列
                bottom: 10, // 设置图例位置为底部，距离底部的距离为10px
                data: ['pc', 'app', 'h5', '小程序'] // 设置图例的数据，与数据项的名称对应
            },
            series: [
                {
                    type: 'pie',
                    radius: ['40%', '60%'],
                    label: {
                        formatter: '{d}% \n{c}个', // 设置标签格式化，显示名称、数值和百分比
                        position: 'top', // 标签位置在图中心
                        color: '#333', // 标签文字颜色
                        fontSize: 14, // 标签文字大小
                        fontWeight: 400

                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c}' // 设置tooltip的格式，显示名称和值
                    },
                    data:dataOrder
                }
            ]
        });
    });

    window.addEventListener('resize', debounceResize)
});
onUnmounted(() => {
    window.removeEventListener('resize', debounceResize)
});
</script>

<style scoped lang='scss'>
.seller-home {
    transform: translate(0, 0);
    .container.member {
        overflow-y: hidden;
    }

    .el-card {
        background: transparent;
    }

    .el-card__body {
        background: transparent;
    }

    .home-container {
        width: 100%;
        height: 100%;

        .home-content {

            .home-left {
                /*width: 75%;*/
                width: 0;
                flex: 1;
                margin-right: 20px;
            }

            .home-left .information {
                padding: 10px 20px;
                box-sizing: border-box;
                background: #fff;
            }

            .home-left .information .welcome {
                padding: 10px 0 20px 0;
                border-bottom: 1px #f2f3f5 solid;
            }

            .home-left .information .welcome span {
                font-size: 18px;
                color: #333;
                font-weight: 600;
            }

            .home-left .information .quick-view-box {
                padding: 20px 0;
                border-bottom: 1px #f2f3f5 solid;
            }

            .home-left .information .quick-view-box .fonts {
                width: 60px;
                height: 60px;
                background: #f6f7fb;
                border-radius: 50px;
            }

            .home-left .information .quick-view-box .quick-view {
                flex: 1;
                position: relative;
                cursor: pointer;
            }

            .home-left .information .quick-view-box .quick-view &::after {
                position: absolute;
                content: '';
                border-right: solid 1px #f2f3f5;
                height: 100%;
                right: 30px;
                top: 0;
            }

            .home-left .information .quick-view-box .quick-view &:last-child &::after {
                content: none;
            }

            .home-left .information .quick-view-box .quick-view > div {
                margin-right: 10px;
                text-align: center;
            }

            .home-left .information .quick-view-box .quick-view .fonts em {
                font-size: 20px;
                color: red;
            }

            .home-left .information .quick-view-box .quick-view > div &:last-child {
                margin-right: 0px;
            }

            .home-left .information .quick-view-box .quick-view > div span &:first-child {
                font-size: 16px;
                color: #333;
                font-weight: 500;
            }

            .home-left .information .quick-view-box .quick-view > div span &:last-child {
                font-size: 22px;
                color: #551a8a;
                margin-top: 15px;
                font-weight: 500;
            }

            .information .access-data {
                margin-top: 20px;
            }

            .access-data-header {
                padding: 10px 0 20px 0;
            }

            .access-data-header .data-header span {
                font-size: 18px;
                color: #333;
                font-weight: 600;
            }

            .access-data-header .mores {
                color: #0C54A6;
                font-size: 14px;
                cursor: pointer;
            }

            .home-left .information-bottom {
                margin-top: 20px;
            }



            .home-left .information-bottom .project-proportion {
                width: 49%;
                background: #fff;
                padding: 10px 20px;
                box-sizing: border-box;
            }

            .home-right {
                width: 450px;
            }

            .home-right .shortcut {
                width: 100%;
                background: #fff;
                padding: 10px 20px;
                box-sizing: border-box;
            }

            .home-right .shortcut .more-opt {
                /*column-gap: 6%;*/
                display: grid;
                justify-content: space-between;
                grid-template-columns:repeat(auto-fill, 70px);
                min-height: 60px;
            }

            .home-right .shortcut .opt-list {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 10px;
                cursor: pointer;
            }

            .home-right .shortcut .opt-list .icon {
                width: 40px;
                height: 40px;
                border-radius: 5px;
                background: #f7f8fa;
                text-align: center;
                line-height: 40px;
            }

            .home-right .shortcut .opt-list .icon em {
                font-size: 20px;
                color: #333;
            }

            .home-right .shortcut .opt-list .titles {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                width: 80px;
                text-align: center;
                margin-top: 5px;
            }

            .home-right .shortcut .opt-list span {
                font-size: 14px;
                color: #ccc;
                font-weight: 400;
                margin-top: 5px;
            }
        }
    }

    .table-title {
        -webkit-line-clamp: 2;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
        word-break: break-all;
        overflow: hidden;
    }

}
/*  首页收藏弹窗  */
:deep(.collection-drawer) {
    .collection-box {
        width: 100%;
        height: 100%;
        padding: 20px;
        box-sizing: border-box;
        display: flex;
        padding-left: 0;
    }

    &.el-drawer {
        border-radius: 20px 0 0 0;
    }

    .el-drawer__header span {
        font-weight: 500;
        font-size: 20px;
        color: #333333;
    }

    .el-drawer__body {
        height: 0;
        padding: 0;
        overflow: hidden !important;
    }

    .collection-box .left-collection {
        width: 85%;
        height: 100%;
        overflow: hidden;
        margin-left: 20px;
        display: flex;
        flex-direction: column;

        .menu-overflow {
            height: 0;
            flex: 1;
            overflow-y: auto;
            margin-top: 74px;
        }

        .menu-scroll {
            columns: 4;
            column-gap: 20px;
        }

        .menu-list {
            display: inline-block;
            width: 100%;
            break-inside: avoid;

            .menu-title span, .menu-title i {
                color: #ff822c;
                font-size: 18px;
                line-height: 3;
            }

            .menu-second .menu-title span {
                font-size: 16px;
                color: #333;
                line-height: 3;
                font-weight: 500;
            }
        }

        .search-menu .el-input__wrapper {
            height: 36px;
            background: #F5F7FA;
            border-radius: 10px;
            border: none;
            outline: none;
            box-shadow: none;
        }

        .search-menu .el-input__prefix {
            display: flex;
            align-items: center;
            padding-left: 10px;
        }
    }

    .menu-list .menu-second .menu-title span {
        font-size: 16px;
        color: #333;
        line-height: 3;
        font-weight: 500;
    }

    .collection-box .menu-listRouter {
        height: 30px;
        border-radius: 10px;
        padding: 0 10px;
        cursor: pointer;
        display: flex;
        align-items: baseline;
        justify-content: space-between;
    }

    .collection-box .menu-listRouter {
        &:hover {
            background-color: #f4f6f7;

            i {
                display: block !important;
            }
        }

        i &:hover {
            color: rgb(124, 124, 124) !important;
        }
    }

    .collection-box .menu-listRouter span,
    .collection-box .menu-listRouter i {
        cursor: pointer;
        color: #333;
        font-size: 14px;
        line-height: 30px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .collection-box .right-collection {
        width: 0;
        height: 100%;
        overflow: hidden;
        overflow-y: auto;
        padding: 20px;
        flex: 1;
        box-shadow: 1px 0px 6px 0px rgba(16, 43, 76, 0.08);
        border-radius: 0px 20px 20px 0px;
        scrollbar-width: none;
        -ms-overflow-style: none;

        &::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        &::-webkit-scrollbar-track {
            display: none;
        }
    }

    .menu-overflow .menu-listRouter {
        padding: 0 20px;
        border-radius: 10px;
        height: 30px;

        &:hover {
            background-color: #f4f6f7;
        }

        .hideIcon {
            display: none;
        }

        &:hover .hideIcon {
            display: block;
        }
    }

    .remind {
        background: #E90013;
        border-radius: 60px;
        height: 20px;
        min-width: 20px;
        text-align: center;
        line-height: 1;
        padding: 0 5px;
        margin-left: 10px;

        span {
            font-weight: 500;
            font-size: 12px;
            color: #FFFFFF;
        }
    }
}


/**/
.no-data{
    display: flex;align-items: center;justify-content: center;
}
.no-data span{
    font-size: 16px;color: #ccc;font-weight: 400;
}
</style>
