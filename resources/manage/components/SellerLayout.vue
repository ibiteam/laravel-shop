<template>
    <div class='seller-layout'>
        <el-container>
            <el-aside :class="{'left-hidden':!leftShow}">
                <div class='layout-left-header s-flex ai-ct jc-bt'>
                    <div class='seller-picture s-flex ai-ct jc-ct'>
                        <img src='https://fastly.jsdelivr.net/npm/@vant/assets/logo.png' alt=''>
                    </div>
                    <div class='s-flex jc-bt ai-ct' style="font-size: 20px;cursor: pointer;" @click="leftShow = false">
                        <Fold style="width: 1.5em; height: 1.5em;" />
                    </div>
                </div>
                <div class='menu-tree'>
                    <el-tree
                        style="max-width: 200px"
                        :data="menus[menuIndex].children"
                        :icon='ArrowRight'
                        :props="{
                              children: 'children',
                              label: 'label',
                            }">
                        <template #default="{ node, data }">
                            <div class="custom-tree-node s-flex ai-bs">
                                <div class='icons'>
                                    <el-icon v-if='data.children'><Setting /></el-icon>
                                </div>
                                <span>{{ data.title }}</span>
                            </div>
                        </template>
                    </el-tree>
                </div>
            </el-aside>
            <el-container>
                <el-header>
                    <div class='seller-header s-flex jc-bt ai-ct'>
                        <div class='indentation s-flex jc-bt ai-ct' :class="{'indentation-show' : !leftShow}" style="font-size: 20px;cursor: pointer;" @click="leftShow = !leftShow">
                            <Expand style="width: 1.5em; height: 1.5em;" />
                        </div>
                        <div class='header-left s-flex ai-ct'>
                            <div class='flow-menu s-flex'>
                                <div class='menu-box'>
                                    <div class='s-flex'>
                                        <div class='menu-list s-flex jc-ct ai-ct' :class='{actived:index === menuIndex}' :key="item.index" v-for='(item,index) in menus'  @click="leftShow = true,menuIndex = index">
                                            <div class="icon-imgs"><i class="iconfont co-666" :class='item.icon'></i></div>
                                            <div class="menu-first-name co-666"><span>{{item.title}}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='header-right s-flex ai-ct'>
                            <div class='search-box'>
                                <div class="header-search" :class="{'show':searchShow,'border-half':searchMenuArr.length}">
                                    <input type="text" v-model="searchtools" @input="debounceSearch" ref="searchtoolRef" class="search-input" placeholder="输入 / 快速搜索">
                                    <el-icon @click="openSearchShow()" :size="20"><Search /></el-icon>
                                </div>
                                <div class="position-search" v-if="searchShow">
                                    <div class="search-list-box" :class="searchMenuArr.length?'':'nomore'">
                                        <template v-if="!!searchMenuArr.length">
                                            <div class="search-list" v-for="(item,index) in searchMenuArr" :key="item.index">
                                                <div class="search-list-parent">
                                                    <span>{{item.title}}</span>
                                                </div>
                                                <div class="search-list-child" v-for="(its,ids) in item.children" :key="its.index">
                                                    <template v-if="!!its.children">
                                                        <div class="search-list-parent" style="padding: 0 15px;">
                                                            <span>{{its.title}}</span>
                                                        </div>
                                                        <div class="childsUl">
                                                            <template v-for="(itas,idas) in its.children">
                                                                <div class="search-list-child" v-if="!!itas.children" :key="itas.index">
                                                                    <div class="search-list-parent" style="padding: 0 30px;">
                                                                        <span>{{itas.title}}</span>
                                                                    </div>
                                                                    <div class="list-search-show" style="padding: 0 45px;"
                                                                         v-for="(itds,itdd) in itas.children"
                                                                         @click="chooseSearch(item,itds)">
                                                                        <span>{{itds.title}}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="list-search-show" style="padding: 0 30px;" v-else
                                                                     :key="itas.index" @click="chooseSearch(item,itas)">
                                                                    <span>{{itas.title}}</span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="childsUl">
                                                            <div class="list-search-show" style="padding: 0 8px;"
                                                                 @click="chooseSearch(item,its)">
                                                                <svg width="20" height="20" viewBox="0 0 20 20">
                                                                    <path d="M17 6v12c0 .52-.2 1-1 1H4c-.7 0-1-.33-1-1V2c0-.55.42-1 1-1h8l5 5zM14 8h-3.13c-.51 0-.87-.34-.87-.87V4"
                                                                          stroke="currentColor" fill="none" fill-rule="evenodd"
                                                                          stroke-linejoin="round"></path>
                                                                </svg>
                                                                <span>{{its.title}}</span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class='more-func s-flex ai-ct'>
                                <div class='circle s-flex ai-ct jc-ct'>
                                    <el-icon :size="20"><Bell /></el-icon>
                                </div>
                                <div class='circle s-flex ai-ct jc-ct'>
                                    <el-icon :size="20"><Setting /></el-icon>
                                </div>
                            </div>
                            <div class='user-info'>
                                <el-dropdown>
                                    <div class='el-dropdown-link s-flex ai-ct'>
                                        <div class='user-picture'>
                                            <img src='https://fastly.jsdelivr.net/npm/@vant/assets/logo.png' alt=''>
                                        </div>
                                        <span class='co-3D'>张三</span>
                                        <el-icon class='el-icon--right'>
                                            <arrow-down />
                                        </el-icon>
                                    </div>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item>Action 1</el-dropdown-item>
                                            <el-dropdown-item>Action 2</el-dropdown-item>
                                            <el-dropdown-item>Action 3</el-dropdown-item>
                                            <el-dropdown-item disabled>Action 4</el-dropdown-item>
                                            <el-dropdown-item divided>Action 5</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </div>
                        </div>
                    </div>
                </el-header>
                <el-main style='height: 100%' class='s-flex flex-dir'>
                    <div class='router-tabs'>
                        <el-tabs
                            v-model="routerActived"
                            closable
                        >
                            <el-tab-pane
                                v-for="item in editableTabs"
                                :key="item.name"
                                :label="item.title"
                                :name="item.name"
                            />
                        </el-tabs>
                    </div>
                    <div class='flex-1' style='height: 0;background: var(--page-bg-color);padding: 16px;overflow-y: auto;'>
                        <router-view></router-view>
                    </div>
                </el-main>
            </el-container>
        </el-container>
        <Transition name="fade">
            <div class="mask-search" v-if="searchShow" @click="closeSearch()"></div>
        </Transition>
    </div>
</template>

<script setup>
import { nextTick, ref } from 'vue';
import $public from '@/utils/public'
import { Search , Bell , Setting , ArrowRight , Fold , Expand } from '@element-plus/icons-vue'
const menus = ref([{"index":1,"value":1,"parent_id":0,"collection":false,"title":"设置","src":"","icon":"el-icon-setting","key_value":"set","level":0,"children":[{"index":2,"value":2,"parent_id":1,"collection":false,"title":"系统设置","src":"","icon":"el-icon-tickets","key_value":"system_set","level":1,"children":[{"index":3,"value":3,"parent_id":2,"collection":false,"title":"站点设置","src":"https://testdoc.ptdplat.com/manage/config","icon":"el-icon-tickets","key_value":"site_set","level":2},{"index":4,"value":4,"parent_id":2,"collection":false,"title":"自定义导航","src":"https://testdoc.ptdplat.com/manage/web_nav","icon":"el-icon-tickets","key_value":"web_nav","level":2},{"index":5,"value":5,"parent_id":2,"collection":false,"title":"路由表","src":"https://testdoc.ptdplat.com/manage/router","icon":"el-icon-tickets","key_value":"router","level":2},{"index":6,"value":6,"parent_id":2,"collection":false,"title":"路由分类","src":"https://testdoc.ptdplat.com/manage/router/cate","icon":"el-icon-tickets","key_value":"router_cate","level":2},{"index":7,"value":7,"parent_id":2,"collection":false,"title":"授权配置","src":"https://testdoc.ptdplat.com/manage/auth_config","icon":"el-icon-tickets","key_value":"auth_config","level":2}]},{"index":8,"value":8,"parent_id":1,"collection":false,"title":"工作台","src":"","icon":"el-icon-tickets","key_value":"website","level":1,"children":[{"index":9,"value":9,"parent_id":8,"collection":false,"title":"应用分组","src":"https://testdoc.ptdplat.com/manage/website/category","icon":"el-icon-tickets","key_value":"website_cagegory","level":2},{"index":10,"value":10,"parent_id":8,"collection":false,"title":"应用列表","src":"https://testdoc.ptdplat.com/manage/website/website","icon":"el-icon-tickets","key_value":"application_program_list","level":2},{"index":11,"value":11,"parent_id":8,"collection":false,"title":"申请列表","src":"https://testdoc.ptdplat.com/manage/website/apply","icon":"el-icon-tickets","key_value":"website_apply","level":2},{"index":12,"value":12,"parent_id":8,"collection":false,"title":"用户应用","src":"https://testdoc.ptdplat.com/manage/website/user_website","icon":"el-icon-tickets","key_value":"user_website","level":2},{"index":13,"value":13,"parent_id":8,"collection":false,"title":"角色置换","src":"https://testdoc.ptdplat.com/manage/role_displace","icon":"el-icon-tickets","key_value":"role_displace","level":2}]},{"index":14,"value":14,"parent_id":1,"collection":false,"title":"权限管理","src":"","icon":"el-icon-tickets","key_value":"permission_manage","level":1,"children":[{"index":15,"value":15,"parent_id":14,"collection":false,"title":"管理员","src":"https://testdoc.ptdplat.com/manage/admin_user","icon":"el-icon-tickets","key_value":"admin_user","level":2},{"index":16,"value":16,"parent_id":14,"collection":false,"title":"角色管理","src":"https://testdoc.ptdplat.com/manage/role","icon":"el-icon-tickets","key_value":"role_manage","level":2},{"index":17,"value":17,"parent_id":14,"collection":false,"title":"权限菜单","src":"https://testdoc.ptdplat.com/manage/forum_permission","icon":"el-icon-tickets","key_value":"permission_menu","level":2},{"index":18,"value":18,"parent_id":14,"collection":false,"title":"操作日志","src":"https://testdoc.ptdplat.com/manage/action_log","icon":"el-icon-tickets","key_value":"operation_log","level":2},{"index":19,"value":19,"parent_id":14,"collection":false,"title":"最近访问","src":"https://testdoc.ptdplat.com/manage/forum_permission/backend_recent_visit_list","icon":"el-icon-tickets","key_value":"recent_visit_list","level":2}]},{"index":20,"value":20,"parent_id":1,"collection":false,"title":"三方服务","src":"","icon":"el-icon-tickets","key_value":"third_service","level":1,"children":[{"index":21,"value":21,"parent_id":20,"collection":false,"title":"外部服务","src":"https://testdoc.ptdplat.com/manage/app_service_config","icon":"el-icon-tickets","key_value":"external_services","level":2},{"index":22,"value":22,"parent_id":20,"collection":false,"title":"外部服务日志","src":"https://testdoc.ptdplat.com/manage/app_service_log","icon":"el-icon-tickets","key_value":"external_service_logs","level":2},{"index":124,"value":124,"parent_id":20,"collection":false,"title":"短信平台","src":"https://testdoc.ptdplat.com/manage/phone_msg","icon":"el-icon-tickets","key_value":"phone_msg","level":2}]},{"index":23,"value":23,"parent_id":1,"collection":false,"title":"设备管理","src":"","icon":"el-icon-tickets","key_value":"equipment_manage","level":1,"children":[{"index":24,"value":24,"parent_id":23,"collection":false,"title":"客户端管理","src":"https://testdoc.ptdplat.com/manage/client_version","icon":"el-icon-tickets","key_value":"manage_client_version_index","level":2},{"index":25,"value":25,"parent_id":23,"collection":false,"title":"设备列表","src":"https://testdoc.ptdplat.com/manage/device","icon":"el-icon-tickets","key_value":"manage_device_info_index","level":2}]}]},{"index":26,"value":26,"parent_id":0,"collection":false,"title":"用户","src":"","icon":"el-icon-s-data","key_value":"user","level":0,"children":[{"index":27,"value":27,"parent_id":26,"collection":false,"title":"用户管理","src":"","icon":"el-icon-tickets","key_value":"user_manage","level":1,"children":[{"index":28,"value":28,"parent_id":27,"collection":false,"title":"用户列表","src":"https://testdoc.ptdplat.com/manage/member","icon":"el-icon-tickets","key_value":"user_list","level":2},{"index":29,"value":29,"parent_id":27,"collection":false,"title":"积分等级","src":"https://testdoc.ptdplat.com/manage/user_level","icon":"el-icon-tickets","key_value":"forum_user_level","level":2},{"index":30,"value":30,"parent_id":27,"collection":false,"title":"部门列表","src":"https://testdoc.ptdplat.com/manage/department","icon":"el-icon-tickets","key_value":"department_list","level":2},{"index":31,"value":31,"parent_id":27,"collection":false,"title":"考勤列表","src":"https://testdoc.ptdplat.com/manage/forum_check_on","icon":"el-icon-tickets","key_value":"check_on","level":2},{"index":120,"value":120,"parent_id":27,"collection":false,"title":"用户分组","src":"https://testdoc.ptdplat.com/manage/member_group","icon":"el-icon-tickets","key_value":"member_group","level":2},{"index":121,"value":121,"parent_id":27,"collection":false,"title":"公司员工","src":"https://testdoc.ptdplat.com/manage/company_member","icon":"el-icon-tickets","key_value":"company_member","level":2},{"index":122,"value":122,"parent_id":27,"collection":false,"title":"公司部门","src":"https://testdoc.ptdplat.com/manage/company_work","icon":"el-icon-tickets","key_value":"company_work","level":2}]},{"index":32,"value":32,"parent_id":26,"collection":false,"title":"积分管理","src":"","icon":"el-icon-tickets","key_value":"integral_manage","level":1,"children":[{"index":33,"value":33,"parent_id":32,"collection":false,"title":"积分列表","src":"https://testdoc.ptdplat.com/manage/integral","icon":"el-icon-tickets","key_value":"integral_list","level":2},{"index":34,"value":34,"parent_id":32,"collection":false,"title":"积分统计报表","src":"https://testdoc.ptdplat.com/manage/integral_statistics","icon":"el-icon-tickets","key_value":"integral_statistics","level":2}]}]},{"index":35,"value":35,"parent_id":0,"collection":false,"title":"文章","src":"","icon":"el-icon-document","key_value":"article","level":0,"children":[{"index":36,"value":36,"parent_id":35,"collection":false,"title":"文章管理","src":"","icon":"el-icon-tickets","key_value":"article_manage","level":1,"children":[{"index":37,"value":37,"parent_id":36,"collection":false,"title":"文章列表","src":"https://testdoc.ptdplat.com/manage/article_index","icon":"el-icon-tickets","key_value":"article_list","level":2},{"index":38,"value":38,"parent_id":36,"collection":false,"title":"分类列表","src":"https://testdoc.ptdplat.com/manage/category_index","icon":"el-icon-tickets","key_value":"cate_index","level":2},{"index":39,"value":39,"parent_id":36,"collection":false,"title":"用户访问列表","src":"https://testdoc.ptdplat.com/manage/user_view","icon":"el-icon-tickets","key_value":"user_view","level":2},{"index":40,"value":40,"parent_id":36,"collection":false,"title":"回收站","src":"https://testdoc.ptdplat.com/manage/article_recycle","icon":"el-icon-tickets","key_value":"article_recycle","level":2},{"index":41,"value":41,"parent_id":36,"collection":false,"title":"用户文章统计","src":"https://testdoc.ptdplat.com/manage/article_statistics","icon":"el-icon-tickets","key_value":"statistics","level":2}]},{"index":42,"value":42,"parent_id":35,"collection":false,"title":"文档管理","src":"","icon":"el-icon-tickets","key_value":"document_manage","level":1,"children":[{"index":43,"value":43,"parent_id":42,"collection":false,"title":"文档列表","src":"https://testdoc.ptdplat.com/manage/document","icon":"el-icon-tickets","key_value":"document_list","level":2},{"index":44,"value":44,"parent_id":42,"collection":false,"title":"模板分类列表","src":"https://testdoc.ptdplat.com/manage/document_template_category","icon":"el-icon-tickets","key_value":"template_category","level":2},{"index":45,"value":45,"parent_id":42,"collection":false,"title":"模板列表","src":"https://testdoc.ptdplat.com/manage/document_template","icon":"el-icon-tickets","key_value":"template_list","level":2}]},{"index":46,"value":46,"parent_id":35,"collection":false,"title":"消息管理","src":"","icon":"el-icon-tickets","key_value":"comment_manage","level":1,"children":[{"index":47,"value":47,"parent_id":46,"collection":false,"title":"评论列表","src":"https://testdoc.ptdplat.com/manage/comment_index","icon":"el-icon-tickets","key_value":"comment_list","level":2},{"index":48,"value":48,"parent_id":46,"collection":false,"title":"私信列表","src":"https://testdoc.ptdplat.com/manage/message","icon":"el-icon-tickets","key_value":"message_list","level":2},{"index":49,"value":49,"parent_id":46,"collection":false,"title":"通知列表","src":"https://testdoc.ptdplat.com/manage/notification","icon":"el-icon-tickets","key_value":"notification_list","level":2}]}]},{"index":50,"value":50,"parent_id":0,"collection":false,"title":"工具","src":"","icon":"el-icon-tickets","key_value":"tool","level":0,"children":[{"index":51,"value":51,"parent_id":50,"collection":false,"title":"站点工具","src":"","icon":"el-icon-tickets","key_value":"site_tool","level":1,"children":[{"index":52,"value":52,"parent_id":51,"collection":false,"title":"素材列表","src":"https://testdoc.ptdplat.com/manage/materials_list","icon":"el-icon-tickets","key_value":"materials_list","level":2}]},{"index":53,"value":53,"parent_id":50,"collection":false,"title":"工单管理","src":"","icon":"el-icon-tickets","key_value":"work_order","level":1,"children":[{"index":54,"value":54,"parent_id":53,"collection":false,"title":"工单列表","src":"https://testdoc.ptdplat.com/manage/work_order","icon":"el-icon-tickets","key_value":"work_order_list","level":2},{"index":55,"value":55,"parent_id":53,"collection":false,"title":"分类配置","src":"https://testdoc.ptdplat.com/manage/work_order_category","icon":"el-icon-tickets","key_value":"work_order_category","level":2},{"index":56,"value":56,"parent_id":53,"collection":false,"title":"评分配置","src":"https://testdoc.ptdplat.com/manage/work_order_score","icon":"el-icon-tickets","key_value":"work_order_score","level":2}]},{"index":57,"value":57,"parent_id":50,"collection":false,"title":"培训管理","src":"","icon":"el-icon-tickets","key_value":"tran_manage","level":1,"children":[{"index":58,"value":58,"parent_id":57,"collection":false,"title":"培训列表","src":"https://testdoc.ptdplat.com/manage/train","icon":"el-icon-tickets","key_value":"train","level":2}]},{"index":59,"value":59,"parent_id":50,"collection":false,"title":"会议室管理","src":"","icon":"el-icon-tickets","key_value":"meeting_room_manage","level":1,"children":[{"index":60,"value":60,"parent_id":59,"collection":false,"title":"会议室列表","src":"https://testdoc.ptdplat.com/manage/meeting_room_list/index","icon":"el-icon-tickets","key_value":"meeting_room_list","level":2},{"index":61,"value":61,"parent_id":59,"collection":false,"title":"会议室预约","src":"https://testdoc.ptdplat.com/manage/meeting_room_reserve/index","icon":"el-icon-tickets","key_value":"meeting_room_reserve","level":2}]},{"index":62,"value":62,"parent_id":50,"collection":false,"title":"工具管理","src":"","icon":"el-icon-tickets","key_value":"tool_manage","level":1,"children":[{"index":63,"value":63,"parent_id":62,"collection":false,"title":"生成器","src":"https://testdoc.ptdplat.com/manage/tool_generator","icon":"el-icon-tickets","key_value":"generator","level":2}]},{"index":64,"value":64,"parent_id":50,"collection":false,"title":"堡垒机","src":"","icon":"el-icon-tickets","key_value":"fortress_machine","level":1,"children":[{"index":65,"value":65,"parent_id":64,"collection":false,"title":"服务器","src":"https://testdoc.ptdplat.com/manage/serve_organ","icon":"el-icon-tickets","key_value":"serve_organ","level":2},{"index":66,"value":66,"parent_id":64,"collection":false,"title":"服务器执行记录","src":"https://testdoc.ptdplat.com/manage/serve_organ_log","icon":"el-icon-tickets","key_value":"serve_organ_log","level":2},{"index":119,"value":119,"parent_id":64,"collection":false,"title":"服务器分组","src":"https://testdoc.ptdplat.com/manage/serve_cate","icon":"el-icon-tickets","key_value":"serve_cate","level":2}]},{"index":67,"value":67,"parent_id":50,"collection":false,"title":"配置中心","src":"","icon":"el-icon-tickets","key_value":"config_center","level":1,"children":[{"index":68,"value":68,"parent_id":67,"collection":false,"title":"应用列表","src":"https://testdoc.ptdplat.com/manage/config_center/application","icon":"el-icon-tickets","key_value":"config_center_application_list","level":2},{"index":69,"value":69,"parent_id":67,"collection":false,"title":"应用分组","src":"https://testdoc.ptdplat.com/manage/config_center/group","icon":"el-icon-tickets","key_value":"config_center_application_group","level":2},{"index":70,"value":70,"parent_id":67,"collection":false,"title":"应用请求记录","src":"https://testdoc.ptdplat.com/manage/config_center/request_log","icon":"el-icon-tickets","key_value":"config_center_request_log","level":2}]}]},{"index":71,"value":71,"parent_id":0,"collection":false,"title":"数据","src":"","icon":"el-icon-c-scale-to-original","key_value":"data","level":0,"children":[{"index":72,"value":72,"parent_id":71,"collection":false,"title":"广告管理","src":"","icon":"el-icon-tickets","key_value":"advert_manage","level":1,"children":[{"index":73,"value":73,"parent_id":72,"collection":false,"title":"轮播图","src":"https://testdoc.ptdplat.com/manage/advert_cate","icon":"el-icon-tickets","key_value":"advert_list","level":2}]},{"index":74,"value":74,"parent_id":71,"collection":false,"title":"日志管理","src":"","icon":"el-icon-tickets","key_value":"login_log_manage","level":1,"children":[{"index":75,"value":75,"parent_id":74,"collection":false,"title":"最近访问","src":"https://testdoc.ptdplat.com/manage/recent_visit_list","icon":"el-icon-tickets","key_value":"recent_visit_list","level":2},{"index":76,"value":76,"parent_id":74,"collection":false,"title":"登录日志","src":"https://testdoc.ptdplat.com/manage/login_log","icon":"el-icon-tickets","key_value":"login_log_list","level":2},{"index":77,"value":77,"parent_id":74,"collection":false,"title":"访问统计","src":"https://testdoc.ptdplat.com/manage/access_statistic","icon":"el-icon-tickets","key_value":"access_statistic","level":2}]},{"index":78,"value":78,"parent_id":71,"collection":false,"title":"常用功能","src":"","icon":"el-icon-tickets","key_value":"common_functions","level":1,"children":[{"index":79,"value":79,"parent_id":78,"collection":false,"title":"备忘录","src":"https://testdoc.ptdplat.com/manage/memorandum","icon":"el-icon-tickets","key_value":"memorandum","level":2}]},{"index":80,"value":80,"parent_id":71,"collection":false,"title":"应用管理","src":"","icon":"el-icon-tickets","key_value":"application_manage","level":1,"children":[{"index":81,"value":81,"parent_id":80,"collection":false,"title":"应用列表","src":"https://testdoc.ptdplat.com/manage/application_list","icon":"el-icon-tickets","key_value":"application_list","level":2},{"index":82,"value":82,"parent_id":80,"collection":false,"title":"应用请求日志","src":"https://testdoc.ptdplat.com/manage/request_logs","icon":"el-icon-tickets","key_value":"request_logs","level":2}]}]},{"index":83,"value":83,"parent_id":0,"collection":false,"title":"项目","src":"","icon":"el-icon-user-solid","key_value":"forum_project","level":0,"children":[{"index":84,"value":84,"parent_id":83,"collection":false,"title":"项目管理","src":"","icon":"el-icon-tickets","key_value":"project_manage","level":1,"children":[{"index":85,"value":85,"parent_id":84,"collection":false,"title":"项目列表","src":"https://testdoc.ptdplat.com/manage/project_list","icon":"el-icon-tickets","key_value":"project_list","level":2},{"index":86,"value":86,"parent_id":84,"collection":false,"title":"需求列表","src":"https://testdoc.ptdplat.com/manage/demand_list","icon":"el-icon-tickets","key_value":"demand_list","level":2},{"index":87,"value":87,"parent_id":84,"collection":false,"title":"任务列表","src":"https://testdoc.ptdplat.com/manage/task_list","icon":"el-icon-tickets","key_value":"task_list","level":2},{"index":91,"value":91,"parent_id":84,"collection":false,"title":"问题列表","src":"https://testdoc.ptdplat.com/manage/problem_list","icon":"el-icon-tickets","key_value":"problem_list","level":2},{"index":92,"value":92,"parent_id":84,"collection":false,"title":"项目统计","src":"https://testdoc.ptdplat.com/manage/project_statistics_list","icon":"el-icon-tickets","key_value":"project_statistics","level":2},{"index":93,"value":93,"parent_id":84,"collection":false,"title":"模板设置","src":"https://testdoc.ptdplat.com/manage/project_template","icon":"el-icon-tickets","key_value":"project_template_config","level":2},{"index":94,"value":94,"parent_id":84,"collection":false,"title":"项目操作日志","src":"https://testdoc.ptdplat.com/manage/project_operation_log","icon":"el-icon-tickets","key_value":"project_operation_log","level":2},{"index":95,"value":95,"parent_id":84,"collection":false,"title":"项目动态","src":"https://testdoc.ptdplat.com/manage/project_task_log","icon":"el-icon-tickets","key_value":"project_task_log","level":2}]},{"index":88,"value":88,"parent_id":83,"collection":false,"title":"工分管理","src":"","icon":"el-icon-tickets","key_value":"work_point_manage","level":1,"children":[{"index":89,"value":89,"parent_id":88,"collection":false,"title":"工分列表","src":"https://testdoc.ptdplat.com/manage/project_work_point_list","icon":"el-icon-tickets","key_value":"project_work_point_list","level":2},{"index":90,"value":90,"parent_id":88,"collection":false,"title":"工分详情","src":"https://testdoc.ptdplat.com/manage/project_details_work_point","icon":"el-icon-tickets","key_value":"project_details_work_point","level":2}]}]},{"index":96,"value":96,"parent_id":0,"collection":false,"title":"圈子","src":"","icon":"el-icon-chat-round","key_value":"forum_circle","level":0,"children":[{"index":97,"value":97,"parent_id":96,"collection":false,"title":"发现管理","src":"","icon":"el-icon-tickets","key_value":"discover_manage","level":1,"children":[{"index":98,"value":98,"parent_id":97,"collection":false,"title":"发现列表","src":"https://testdoc.ptdplat.com/manage/discover_list","icon":"el-icon-tickets","key_value":"discover_list","level":2}]},{"index":99,"value":99,"parent_id":96,"collection":false,"title":"圈子管理","src":"","icon":"el-icon-tickets","key_value":"circle_manage","level":1,"children":[{"index":100,"value":100,"parent_id":99,"collection":false,"title":"圈子列表","src":"https://testdoc.ptdplat.com/manage/circle_list","icon":"el-icon-tickets","key_value":"circle_list","level":2},{"index":101,"value":101,"parent_id":99,"collection":false,"title":"圈文列表","src":"https://testdoc.ptdplat.com/manage/circle_post_list","icon":"el-icon-tickets","key_value":"circle_post_list","level":2}]},{"index":102,"value":102,"parent_id":96,"collection":false,"title":"资源管理","src":"","icon":"el-icon-tickets","key_value":"resource_manage","level":1,"children":[{"index":103,"value":103,"parent_id":102,"collection":false,"title":"资源列表","src":"https://testdoc.ptdplat.com/manage/resource_list","icon":"el-icon-tickets","key_value":"resource_list","level":2},{"index":104,"value":104,"parent_id":102,"collection":false,"title":"违禁词","src":"https://testdoc.ptdplat.com/manage/forbidden_word_list","icon":"el-icon-tickets","key_value":"forbidden_word_list","level":2}]}]},{"index":105,"value":105,"parent_id":0,"collection":false,"title":"消息","src":"","icon":"el-icon-chat-round","key_value":"forum_chat","level":0,"children":[{"index":106,"value":106,"parent_id":105,"collection":false,"title":"应用管理","src":"","icon":"el-icon-tickets","key_value":"application_program_manage","level":1,"children":[{"index":107,"value":107,"parent_id":106,"collection":false,"title":"应用列表","src":"https://testdoc.ptdplat.com/manage/chat/application_program/list","icon":"el-icon-tickets","key_value":"application_program_list","level":2}]},{"index":108,"value":108,"parent_id":105,"collection":false,"title":"机器人管理","src":"","icon":"el-icon-tickets","key_value":"application_program_robot_manage","level":1,"children":[{"index":109,"value":109,"parent_id":108,"collection":false,"title":"机器人列表","src":"https://testdoc.ptdplat.com/manage/chat/application_program_robot/list","icon":"el-icon-tickets","key_value":"application_program_robot_list","level":2}]}]},{"index":110,"value":110,"parent_id":0,"collection":false,"title":"企淘","src":"","icon":"el-icon-goods","key_value":"qi_tao","level":0,"children":[{"index":111,"value":111,"parent_id":110,"collection":false,"title":"商品管理","src":"","icon":"el-icon-tickets","key_value":"goods_manage","level":1,"children":[{"index":112,"value":112,"parent_id":111,"collection":false,"title":"商品列表","src":"https://testdoc.ptdplat.com/manage/qi_tao/goods/goods_list","icon":"el-icon-tickets","key_value":"goods_list","level":2},{"index":113,"value":113,"parent_id":111,"collection":false,"title":"商品访问","src":"https://testdoc.ptdplat.com/manage/qi_tao/goods/view_user_goods","icon":"el-icon-tickets","key_value":"view_user_goods","level":2}]},{"index":114,"value":114,"parent_id":110,"collection":false,"title":"订单管理","src":"","icon":"el-icon-tickets","key_value":"order_manage","level":1,"children":[{"index":115,"value":115,"parent_id":114,"collection":false,"title":"订单列表","src":"https://testdoc.ptdplat.com/manage/qi_tao/order/order_list","icon":"el-icon-tickets","key_value":"order_list","level":2}]},{"index":116,"value":116,"parent_id":110,"collection":false,"title":"分类管理","src":"","icon":"el-icon-tickets","key_value":"category_manage","level":1,"children":[{"index":117,"value":117,"parent_id":116,"collection":false,"title":"分类列表","src":"https://testdoc.ptdplat.com/manage/qi_tao/goods/goods_cate","icon":"el-icon-tickets","key_value":"category_list","level":2}]}]}])
const menuIndex = ref(0)

const data = [
    {
        label: '基础设置',
    },
    {
        label: '权限管理',
        children: [
            {
                label: '商品管理',

            },
            {
                label: '商品编辑',
            },
        ],
    },
]
const editableTabs = [
    {
        title: '基础设置',
        name: '1',
    },
    {
        title: '商品管理',
        name: '2',
    },
]

const leftShow = ref(true)

const searchtoolRef = ref(null)
const searchShow = ref(false)
const searchtools = ref('')
const searchMenuArr = ref([])

const routerActived = ref('1')


const openSearchShow = () => {
    searchtools.value = ''
    searchShow.value = true
    nextTick(() => {
        searchtoolRef.value.focus()
    })
}
const closeSearch = () => {
    setTimeout(() => {
        searchtools.value = ''
        searchShow.value = false;
        searchMenuArr.value = []
    },50)
}

const filterMenuData = (arr, searchTerm) => {
    return arr
        .filter(item => item.title) // 过滤掉没有标题的节点
        .map(item => {
            if (item.title.includes(searchTerm)) {
                return { ...item }; // 匹配直接返回
            } else if (item.children) {
                // 递归过滤子节点
                const filteredChildren = filterMenuData(item.children, searchTerm);
                if (filteredChildren.length > 0) {
                    return { ...item, children: filteredChildren }; // 子节点有匹配内容
                }
            }
            return null; // 过滤掉不匹配的节点
        })
        .filter(item => item !== null); // 移除空节点
}

const  handleChange = () => { //查询事件
    if (!searchtools.value) {
        searchMenuArr.value = []; // 搜索词为空时，清空结果
    } else {
        searchMenuArr.value = filterMenuData(menus.value, searchtools.value);
    }
}
const debounceSearch = $public.debounce(handleChange,600)

</script>

<style scoped lang='scss'>
.seller-layout{
    &,.el-container{
        width: 100vw;
        height: 100vh;
        overflow: hidden;
    }
    .el-aside{
        width: 200px;
        opacity: 1;
        transition: width 0.5s ease-in-out, opacity 0.5s ease-in-out;
        box-shadow: 0px 0px 3px 0px rgba(16, 43, 76, 0.08);
        &.left-hidden{
            width: 0;
            opacity: 0;
        }
        .layout-left-header{
            height: 60px;
            padding: 0 15px;
            .seller-picture{
                width: 91px;
                height: 41px;
                margin-right: 30px;
            }
        }
        .menu-tree{
            width: 200px;
            padding: 20px 0;
            :deep(.el-tree){
                .el-tree-node{
                    .el-tree-node__content{
                        width: 200px;
                        height: 40px;
                        padding-left: 18px !important;
                        .el-tree-node__expand-icon{
                            position: absolute;
                            right: 10px;
                            color: #31373D;
                            font-size: 14px;
                        }
                        .custom-tree-node{
                            .icons{
                                width: 20px;
                                height: 20px;
                            }

                        }
                    }
                    &.is-current{
                        > .el-tree-node__content{
                            background: #F6FAFF;
                            .custom-tree-node{
                                color: #077FFF;
                            }
                        }
                    }

                }
            }
        }
    }
    .el-container{
        transition: flex 0.5s ease-in;
        .el-header{
            padding: 0;
            box-shadow: 0px 1px 6px 0px rgba(16, 43, 76, 0.08);
            .seller-header{
                height: 60px;
                padding: 0 10px;
                .indentation{
                    opacity: 0;
                    width: 0;
                    transition: opacity 0.5s ease-in-out, width 0.5s ease-in-out;
                    &.indentation-show{
                        opacity: 1;
                        //margin-right: 10px;
                        width: 30px;
                    }
                }
                .header-left{
                    flex: 1 1 0%;
                    width: 0px;
                    .flow-menu{
                        overflow-x: auto;
                        .menu-box{
                            user-select: none;
                            .menu-list{
                                width: 100px;
                                height: 32px;
                                cursor: pointer;
                                border-radius: 4px;
                                margin-right: 16px;
                                .icon-imgs{
                                    i{
                                        font-size: 18px;
                                    }
                                }
                                .menu-first-name{
                                    margin-left: 8px;
                                    span{
                                        font-weight: normal;
                                        font-size: 18px;
                                    }
                                }
                                &.actived , &:hover{
                                    background: var(--main-color-10);
                                }
                            }
                        }
                    }
                }
                .header-right{
                    .search-box{
                        display: flex;
                        align-items: center;
                        cursor: pointer;
                        position: relative;
                        z-index: 3000;
                        .header-search{
                            position: relative;
                            width: 32px; /* 初始宽度 */
                            height: 32px;
                            overflow: hidden;
                            transition: width 0.3s ease, background-color 0.3s ease; /* 平滑宽度变化 */
                            border-radius: 20px;
                            margin-right: 16px;

                            border: 1px solid #F2F3F5;
                            &.show {
                                width: 350px;
                                background: rgba(51, 51, 51, 0.05);
                                background: #fff;
                                box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;

                                .search-input {
                                    opacity: 1; /* 显示输入框 */
                                }
                            }

                            &.border-half {
                                border-radius: 0;
                                border-top-right-radius: 20px;
                                border-top-left-radius: 20px;
                                border-bottom: 0;
                            }

                            .search-input {
                                position: absolute;
                                left: 10px; /* 输入框左内边距 */
                                top: 50%;
                                transform: translateY(-50%);
                                width: calc(100% - 50px); /* 留出右侧图标空间 */
                                border: none;
                                outline: none;
                                font-size: 14px;
                                opacity: 0; /* 初始不可见 */
                                background: transparent;
                                transition: opacity 0.3s ease; /* 渐显动画 */
                                color: #333;
                            }

                            .el-icon {
                                position: absolute;
                                right: 5px; /* 图标始终贴靠右侧 */
                                top: 50%;
                                transform: translateY(-50%);
                                color: #888;
                                cursor: pointer;
                                z-index: 10;
                            }
                        }
                        .position-search{
                            background: #fff;
                            position: absolute;
                            width: 350px;
                            /*height: 600px;*/
                            top: 32px;
                            left: 0;

                            border-bottom-right-radius: 20px;
                            border-bottom-left-radius: 20px;
                            box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;
                            border: 1px solid #F2F3F5;
                            border-top: none;
                            /*search样式*/
                            .search-list-box{
                                max-height: 430px;
                                margin: 20px;
                                overflow-y: auto;
                                position: relative;
                                box-sizing: border-box;
                            }
                            .search-list-box.nomore{
                                margin: 0;
                            }
                            .search-list-box .search-list{margin: 0 0 16px;}
                            .search-list-box .search-list .search-list-parent{
                                color: #666666;
                                margin: 13px 0 11px;
                                font-weight: 400;
                                font-size: 16px;
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                            }
                            .search-list-box .search-list .search-list-child > .search-list-parent{
                                position: relative;
                            }
                            .search-list-box .search-list .search-list-child > .search-list-parent::before{
                                content: '';
                                position: absolute;
                                width: 4px;
                                height: 4px;
                                background: #999999;
                                left: 0px;
                                border-radius: 50%;
                            }
                            .search-list-box .search-list .search-list-child > .childsUl > .search-list-child > .search-list-parent::before{
                                left: 15px !important;
                            }
                            .search-list-box .search-list .search-list-child > .search-list-parent > span{
                                font-size: 14px;
                                font-weight: 500;
                                color: #666666;
                            }
                            .search-list-box .search-list .list-search-show{
                                cursor: pointer;
                                padding: 0 16px;
                                /*min-height: 40px;*/
                                border-radius: 4px;
                                line-height: 30px;
                                display: flex;
                                align-items: center;
                            }
                            .search-list-box .search-list .list-search-show:hover span,.search-list-box .search-list .list-search-show.active span{
                                color: #278FF0;
                            }
                            .search-list-box .search-list .list-search-show span{
                                margin-left: 8px;
                                font-size: 14px;
                                font-family: Source Han Sans CN;
                                font-weight: 400;
                                color: #333333;
                            }
                            .search-tips {
                                text-align: center;
                                font-size: 20px;
                                font-weight: 600;
                                position: absolute;
                                left: 50%;
                                top: 50%;
                                transform: translate(-50%,-50%);
                            }
                        }
                    }

                    .more-func{
                        .circle{
                            width: 32px;
                            height: 32px;
                            margin-right: 16px;
                            border-radius: 50%;
                            border: 1px solid #F2F3F5;
                            cursor: pointer;

                        }
                    }
                    .user-info{
                        :deep(.el-dropdown){
                            cursor: pointer;
                            .el-dropdown-link:focus-visible{
                                outline: none;
                            }
                            .user-picture{
                                width: 32px;
                                height: 32px;
                                img {

                                }
                            }
                            span{
                                font-size: 16px;
                                font-weight: normal;
                                margin: 0 5px;
                            }
                        }

                    }
                }

            }
        }
        .el-main{
            padding: 0;
            .router-tabs{
                :deep(.el-tabs){
                    padding: 4px 77px 0 7px;
                    .el-tabs__header{
                        margin-bottom: 0;
                    }
                    .el-tabs__nav-wrap{
                        &::after{
                            content: none;
                        }
                    }
                    .el-tabs__active-bar{
                        visibility: hidden;
                    }
                    .el-tabs__item{
                        width: 122px;
                        height: 30px;
                        border-radius: 8px 8px 0px 0px;
                        border: solid 1px #D8D8D8;
                        border-bottom: none;
                        padding: 0;
                        margin-right: 4px;

                        display: flex;
                        justify-content: space-around;

                        font-size: 12px;
                        color: #888888;
                        &:last-child{
                            margin-right: 0;
                        }

                        &.is-active{
                            background: #F6FAFF;
                            border: none;
                            color: #077FFF;
                        }
                    }
                }
            }
        }
    }
}
.mask-search{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* background-color: rgba(0, 0, 0, 0.5); */
    z-index: 2999;
}
img{
    max-width: 100%;
    max-height: 100%;
}


</style>
