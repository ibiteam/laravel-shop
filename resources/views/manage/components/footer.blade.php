<el-footer>
    <div  class="text-center" v-if="pageInfo.total > 0">
        <el-pagination
                class="common-pagination"
                @current-change="handlePage"
                @size-change="handleLimit"
                :current-page="pageInfo.current_page"
                :page-sizes="[10,20,30,50,100]"
                :page-size="pageInfo.per_page"
                :layout="'total,sizes, prev, pager, next, jumper'"
                :total="pageInfo.total">
        </el-pagination>
        <el-pagination
                class="common-pagination-small"
                prev-text="上一页"
                next-text="下一页"
                @current-change="handlePage"
                @size-change="handleLimit"
                :current-page="pageInfo.current_page"
                :page-sizes="[10,20,30,50,100]"
                :page-size="pageInfo.per_page"
                :layout="'total,sizes, prev, pager, next, jumper'"
                :total="pageInfo.total">
        </el-pagination>
    </div>
</el-footer>