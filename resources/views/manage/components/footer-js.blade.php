/**
* 设置分页数据
* @param pageInfo
*/
setPageInfo(pageInfo) {
    this.pageInfo.total = pageInfo.total;
    this.pageInfo.per_page = Number(pageInfo.per_page);
    this.pageInfo.current_page = pageInfo.current_page;
},
/**
* 跳转分页的处理
* @param val
*/
handlePage(val) {
    this.getData(val);
},
/**
* 选择 每页 条数
* @param val
*/
handleLimit(val) {
    this.form.number = val
    this.pageInfo.per_page=val;
    this.getData();
},

