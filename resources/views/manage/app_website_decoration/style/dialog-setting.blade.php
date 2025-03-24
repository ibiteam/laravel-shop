<style>
    @charset "UTF-8";

    /*公共设置弹窗*/
    /*.template-editor .el-drawer__wrapper { z-index: 99991 !important; }*/
    .template-editor .el-drawer__container { z-index: 99999 !important; }

    .setting-drawer-widget { margin: 0; position: fixed; top: 0; right: 0; bottom: 0; left: 0; overflow: hidden; z-index: 9991; }
    .setting-drawer-widget .setting-drawer-mark { width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); position: relative; left: 0; right: 0; top: 0; bottom: 0; }
    .setting-drawer-box { height: 100%; right: 0; top: 0; }

    /*公共添加按钮*/
    .drawer-btn { height: 40px; padding: 0 20px; background-color: #F3F9FF; border: 1px solid #D6EAFF; font-size: 14px; color: #409EFF; user-select: none; border-radius: 4px; cursor: pointer; }
    .drawer-btn em { height: 14px; margin-right: 10px; }
    .drawer-btn.disabled { background-color: #F8F8F8; border-color: #EEEEEE; color: #CCCCCC; cursor: not-allowed; }
    .drawer-btn.disabled label { cursor: not-allowed; }
    .drawer-btn.primary { padding: 0 14px; border-color: #409EFF; background-color: #409EFF; color: #ffffff; }
    .drawer-btn.primary.disabled { border-color: #EEEEEE; background-color: #F8F8F8; color: #CCCCCC; }
    .el-textarea textarea { height: 100%; resize: none; }
    .el-textarea.pad-bot-40 textarea { padding-bottom: 40px; }

    /*暂无数据*/
    .drawer-nodata { display: table; animation:rtl-drawer-in .3s 1ms; margin: 0 auto; }
    .drawer-nodata img { width: 248px; height: 210px; margin: 30px auto; }
    .drawer-nodata p { text-align: center; margin-bottom: 26px; font-size: 14px; color: #999999; }

    .drawer-content { height: 100vh; overflow-y: auto; }
    .drawer-name { height: 70px; padding: 0 20px; background-color: #F4F8FB; font-size: 16px; }
    .drawer-content .drawer-item { padding: 0 20px 30px 20px; border-bottom: 1px solid #EFEFEF; }
    .border-bottom-none,
    .drawer-content .drawer-item:last-child { border-bottom: none; }
    .drawer-content .drawer-item .drawer-item-dt { margin: 30px 0; }
    .drawer-content .drawer-item .drawer-item-dt h1 { font-size: 14px; font-weight: bold; }
    .drawer-content .drawer-item .drawer-item-dt > div { margin-top: 20px; }
    .drawer-content .drawer-item .drawer-item-dt p { line-height: 1.4; margin-left: 10px; font-size: 14px; color: #666666; }
    .drawer-content .drawer-item .drawer-item-dt span { font-size: 12px; color: #999999; }
    .drawer-content .drawer-item .drawer-item-dt .waring-sn { margin: 10px 0; }
    .drawer-content .drawer-item .drawer-item-dt .drawer-btn { margin-right: 30px; }
    .drawer-content .drawer-item .drawer-item-dt .warning-text { margin-top: 16px; }
    .drawer-content .drawer-item .el-radio__input.is-checked .el-radio__inner {
        border-color: #409EFF;
        background: #fff;
    }
    .drawer-content .drawer-item .el-radio__input.is-disabled .el-radio__inner {
        border-color: #C0C4CC;
    }
    .drawer-content .drawer-item .el-radio__inner::after { width: 8px; height: 8px; background-color: #409EFF; }


    /*推荐分类数据*/
    .drawer-item-cate { padding: 20px; margin-top: 20px; border: 1px solid #E7F1F9; box-sizing: border-box; }
    .drawer-item-cate .cate-title { margin-bottom: 8px; }
    .drawer-item-cate .cate-title h1 { font-size: 12px;  font-weight: bold; }
    .cate-choosed {  }
    .cate-choosed ul {  }
    .cate-choosed ul li { height: 42px; padding: 10px 18px; margin-right: 20px; margin-bottom: 10px; border: 1px solid #D6EAFF; background-color: #F3F9FF; border-radius: 4px; font-size: 14px; color: #409EFF; box-sizing: border-box; }
    .cate-choosed ul li span { margin-right: 10px; }
    .cate-attr .el-checkbox-group { width: 590px; }
    .cate-attr .cate-attr-title span { font-size: 14px; color: #999999; }
    .cate-attr .cate-attr-title p { margin-left: 40px; font-size: 14px; color: #FF3333; }
    .cate-attr .el-checkbox { width: 14%; }
    .cate-attr .el-checkbox+.el-checkbox { margin-top: 20px; margin-left: 0; }


    .drawer-item-dd dl ul { padding: 0 20px; box-sizing: border-box; }
    .drawer-item-dd dl ul li:not(:last-child) { margin-right: 20px; }
    .drawer-item-dd dl ul li:nth-of-type(1) { width: 180px; }
    .drawer-item-dd dl ul li:nth-of-type(2) { width: 230px; }
    .drawer-item-dd dl ul li:nth-of-type(3) { width: 320px; }
    .drawer-item-dd dl ul li:nth-of-type(4) { width: 100px; }
    .drawer-item-dd dl ul li:nth-of-type(5) { width: 90px; }
    .drawer-item-dd dl ul li:nth-of-type(6) { width: 80px; }
    .drawer-item-dd dl ul li .el-date-editor { padding: 0px 10px; border: none; position: relative; }
    .drawer-item-dd dl ul li .el-range-separator { width: 100%; line-height: 40px; position: absolute; }
    .drawer-item-dd dl ul li .range-separator-hide .el-range-separator { display: none; }
    .drawer-item-dd dl ul li .el-date-editor input { width: 100%; }
    .drawer-item-dd dl ul li .el-date-editor .el-range__close-icon { display: none; }
    .drawer-item-dd dl dt { border: 1px solid #E7F1F9; border-bottom: none; }
    .drawer-item-dd dl dt ul { height: 40px; background-color: #F4F8FB; }
    .drawer-item-dd dl dt ul li { font-size: 14px; }
    .drawer-item-dd dl dt ul li span { color: #FF3333; }
    .drawer-item-dd dl dt ul li em { height: 22px; line-height: 22px; margin-left: 6px; color: #409EFF; }
    .drawer-item-dd dl dd { padding: 20px 0; border: 1px solid #E7F1F9; }
    .drawer-item-dd dl dd ul { width: 100%; height: 40px; }
    .drawer-item-dd dl dd ul:not(:last-child) { margin-bottom: 20px; }
    .drawer-item-dd dl dd ul .el-form-item { margin-bottom: 0; }
    .drawer-item-dd .el-color-picker__color { border-color: #DCDFE6; }
    .drawer-upload { width: 100px; height: 100px; background-color: #F2F2F2; border-radius: 4px; position: relative; }
    .drawer-upload > img { max-width: 100%; max-height: 100%; }
    .drawer-upload-mark { display: none; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); position: absolute; left: 0; top: 0; }
    .drawer-upload-icon { width: 30px; height: 30px; margin: 0 8px; background-color: rgba(0, 0, 0, 0.3); border-radius: 100%; }
    .drawer-upload:hover .drawer-upload-mark { display: flex; }
    .drawer-upload-icon em { font-size: 14px; color: #ffffff; }
    .drawer-upload p { line-height: 1.2; text-align: center; font-size: 12px; }
    .drawer-upload-warning { margin-left: 10px; }
    .drawer-upload-warning p { line-height: 1; margin-bottom: 10px; font-size: 12px; color: #999999; }
    .drawer-upload-warning p:last-child { margin-bottom: 0; }

    .drawer-preview-image { min-width: 100px; }
    .drawer-preview-image .drawer-upload-warning { margin: 0; }
    .drawer-preview-image .drawer-preview-icon { width: 100px; height: 100px; background-color: rgba(0, 0, 0, 0.5); position: absolute; z-index: 1; }
    .drawer-preview-image .drawer-upload-warning .el-image { display: flex; align-items: center; justify-content: center; }
    .drawer-preview-image .drawer-upload-warning img { width: auto; height: auto; max-width: 100%; max-height: 100%; position: relative; }
    .drawer-preview-image .drawer-upload-warning img::before { content: ''; width: 100px; height: 100px; background-color: rgba(0, 0, 0, 0.5); position: absolute; }
    .drawer-preview-image .drawer-upload-warning em { color: #ffffff; position: absolute; }
    .el-hide { display: none !important; }
    .template-data-null { width: 100%; height: 100%; border: 1px dashed #D5DEF0; font-size: 24px; color: #D5DEF0; }
    .drawer-upload-null { width: 100%; height: 100%; }

    @keyframes rtl-drawer-in {
        0% { -webkit-transform:translate(100%,0); transform:translate(100%,0) }
        100% { -webkit-transform:translate(0,0); transform:translate(0,0) }
    }

    @-webkit-keyframes rtl-drawer-out {
        0% { -webkit-transform: translate(0, 0); transform: translate(0, 0) }
        100% { -webkit-transform: translate(100%, 0); transform: translate(100%, 0) }
    }

    /** 问号提示弹框 */
    .el-tooltip__popper.is-light { box-shadow: 0 2px 12px 0 rgba(0,0,0,.1); border: none!important; }
    .el-tooltip__popper.is-light[x-placement^=top] .popper__arrow,
    .el-tooltip__popper.is_light[x-placement^=top] .popper__arrow::after{ box-shadow: 0 2px 12px 0 rgba(0,0,0,.1); border: none!important; }
    .el-tooltip__popper.is-light[x-placement^=right] .popper__arrow,
    .el-tooltip__popper.is_light[x-placement^=right] .popper__arrow::after{ box-shadow: 0 2px 12px 0 rgba(0,0,0,.1); border: none!important; }
</style>
