<style>
    @charset "UTF-8";
    /*flex布局*/
    .s-flex { display: -webkit-box; display: -moz-box; display: -webkit-flex; display: -moz-flex; display: -ms-flexbox; display: flex; }
    .flex-1 { -prefix-box-flex: 1; -webkit-box-flex: 1; -webkit-flex: 1; -moz-box-flex: 1; -ms-flex: 1; flex: 1; }
    .flex-dir { flex-direction: column; }
    .flex-wrap { flex-wrap: wrap; }
    .flex-shrink { flex-shrink: 0; }
    .jc-ct { justify-content: center; }
    .ai-ct { align-items: center; }
    .ai-bl { align-items: baseline; }
    .jc-bt { justify-content: space-between; }
    .jc-ad { justify-content: space-around; }
    .jc-fe { justify-content: flex-end; }
    .ai-fe { align-items: flex-end; }
    .ai-fs { align-items: flex-start; }
    .img-set { width: 100%; height: 100%; font-size: 0; display: flex; align-items: center; justify-content: center; position: relative; }
    .img-set img { max-width: 100%; max-height: 100%; }
    .img-set-full img { width: 100%; height: 100%; }

    .public-width { width: 750px; margin: 0 auto; }

    @font-face {
        font-family: 'pingfang';
        src: url('../style/PingFang-SC-Semibold.ttf');
    }

    .template-edit-page .el-select-dropdown__item.is-disabled {
        color: #c0c4cc !important;
        cursor: not-allowed;
    }

    /*去除标签样式*/
    em { font-style: normal; }
    @font-face {
        font-family: 'iconfont';  /* Project id 2575177 */
        src: url('//at.alicdn.com/t/c/font_2575177_klh7i6t67j.eot?t=1716877726624'); /* IE9 */
        src: url('//at.alicdn.com/t/c/font_2575177_klh7i6t67j.eot?t=1716877726624#iefix') format('embedded-opentype'), /* IE6-IE8 */
        url('//at.alicdn.com/t/c/font_2575177_klh7i6t67j.woff2?t=1716877726624') format('woff2'),
        url('//at.alicdn.com/t/c/font_2575177_klh7i6t67j.woff?t=1716877726624') format('woff'),
        url('//at.alicdn.com/t/c/font_2575177_klh7i6t67j.ttf?t=1716877726624') format('truetype'),
        url('//at.alicdn.com/t/c/font_2575177_klh7i6t67j.svg?t=1716877726624#iconfont') format('svg');
    }


    /*文字截断*/
    .ellipsis-1 { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
    .ellipsis-2 { display: -webkit-box; overflow: hidden; text-overflow: ellipsis; -webkit-line-clamp: 2; word-break: break-all; -webkit-box-orient: vertical; }

    /*自适应图片居中设置*/
    .imgSet { font-size: 0px; display: table-cell; text-align: center; vertical-align: middle; }
    .imgSet img { max-width: 100%; max-height: 100%; }
    ul, li { padding: 0; margin: 0; list-style: none; }
    .content, .box-header { padding: 0; }
    .cursorp, .cursorp label { cursor: pointer; }
    .cursornot { cursor: not-allowed; }
    .fontbold { font-weight: bold }

    /*格式化Element-UI样式*/
    .el-color-picker__trigger { width: 100%; height: 100%; padding: 0; border: none; }
    .el-color-picker__panel .el-color-dropdown__btns .el-button--text { display: none !important; }
    /*由于素材浮层样式优先级高，因此要设置颜色组件的优先级*/
    .el-popover,
    .el-color-picker__panel { z-index: 99999 !important; }
    .el-popover--plain { max-width: 300px; word-break: break-all; }

    .template-edit-page { overflow: hidden; }
    .backg-color-white { background-color: #ffffff; }

    /*Loading*/
    .template-loading { width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.9); position: absolute; left: 0; top: 0; z-index: 9999; }

    .template-editor { width: 100%; min-width: 1210px; min-height: 90vh; background-color: #f2f2f2; background-position: center; background-repeat: no-repeat; background-size: 100% 100%; position: relative; }
    /*Header*/
    .template-header { width: 100%; height: 60px; padding: 0 30px; background-color: #ffffff; box-sizing: border-box; position: relative; }
    .template-header .template-header-left p { padding-right: 20px; margin-left: 18px; margin-right: 20px; font-size: 14px; color: #333333; position: relative; }
    .template-header .template-header-left p::before { content: ''; width: 1px; height: 8px; background-color: #cccccc; position: absolute; right: 0; top: 2px; }
    .template-header .template-header-warning { font-size: 14px; color: #FFA114; }
    .template-header .template-header-btn { width: 120px; height: 40px; line-height: 38px; text-align: center; margin-left: 20px; border: 1px solid #409EFF; border-radius: 4px; font-size: 14px; color: #409EFF; cursor: pointer; }
    .template-header .template-header-btn.primary { background-color: #278ff0; color: #ffffff; cursor: pointer; }
    .template-header .template-save-warning { /*width: 260px;*/ padding: 15px 18px; -webkit-animation: laodings 2s infinite linear; animation: laodings 2s infinite linear; background-color: rgba(0, 0, 0, 0.7); border-radius: 10px; font-size: 14px; color: #ffffff; position: absolute; right: 30px; top: 110%; z-index: 11; }
    .template-header .template-save-warning::before { content: ''; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 10px solid rgba(0, 0, 0, 0.7); position: absolute; right: 50px; top: -10px; }
    .template-header .template-save-warning p { line-height: 1.2; margin-right: 10px; }
    @keyframes laodings {
        0% {
            transform: translate(0, 7px);
        }
        50% {
            transform: translate(0, -6px);
        }
        100% {
            transform: translate(0, 7px);
        }
    }
    /*Content*/
    .template-content { width: 386px; }
    /*Source*/
    /*.invite-source-switch { width: 36px; height: 66px; padding: 10px; background-color: #FFFFFF; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1); border-radius: 0px 10px 10px 0px; transition: top 0.1s ease; box-sizing: border-box; position: absolute; right: -36px; top: 50px; cursor: pointer; }*/
    .invite-source-switch { width: 12px; height: 28px; background-color: #278ff0; transform: perspective(0.5em) rotateY(-25deg); border-radius: 3px; position: absolute; top: 50%; right: 1px; }
    .invite-source-switch em { width: 10px; transform: rotate(180deg); font-size: 8px; color: #ffffff; }
    .nav-switch { height: 105px; }
    .nav-switch p{ line-height: 1.2;}
    .invite-content-source { width: 420px; height: 100%; transition: all 0.2s ease; position: fixed; left: 0; top: 0; z-index: 991; }
    .check-item { width: 130px; height: 50px; line-height: 48px; text-align: center; background: linear-gradient(0deg, #F9F9F9, #EEEEEE); border: 1px solid #EEEEEE; border-radius: 10px 10px 0px 0px; font-size: 14px; font-weight: 600; cursor: pointer; }
    .check-item.active,
    .check-item:hover { background: #ffffff; border-bottom-color: #ffffff; color: #409EFF; }
    .template-content-tab,
    .template-content-tab .el-tabs__nav { width: 100%; }
    .template-content-tab .el-tabs__header { display: table; padding: 15px 0; margin: 0 auto; }
    .template-content-tab .el-tabs__nav-wrap::after { display: none; }
    .template-content-warning { width: 100%; height: 40px; margin-bottom: 10px; border: 1px solid #FFDDAC; background-color: #FFF8EE; }
    .template-content-warning em { color: #FFB346; }
    .template-content-warning p { margin-left: 6px; font-size: 12px; color: #333333; }
    .template-content-text { width: 100%; height: 66px; line-height: 66px; background-color: #ffffff; font-size: 14px; color: #333333; }

    .template-group-basic { padding: 20px; }
    .template-group-basic h1 { margin-bottom: 20px; font-size: 14px; font-weight: bold; }
    .template-group-basic dl, .template-group-basic dt { padding: 0 !important; }
    .template-group-basic dl { border: none !important; }
    .template-group-basic dl dt { width: auto !important; height: auto !important; line-height: normal !important; background-color: transparent !important; }
    .basic-tab-item { margin-right: 30px; cursor: pointer; }
    .basic-tab-item em { color: #CCCCCC; }
    .basic-tab-item label { margin-left: 10px; font-size: 14px; }
    .basic-tab-item.active em { color: #409EFF; }
    .template-group-basic dd { padding: 30px 24px; }
    .template-group-basic dd label { margin-right: 8px; font-size: 14px; color: #999999; }
    .template-group-basic dd .el-color-picker { width: 42px; height: 42px; }
    .template-group-basic dd .temp-basic-color { margin-left: 8px; line-height: 40px; }
    .template-group-basic dd label span { color: #FF3333; }
    /*.template-group-basic dd .el-color-picker__trigger { width: 40px; height: 40px; }*/
    .template-group-basic > div > p { margin: 14px 0; color: #999999; }
    .template-group-basic input[type='file'] { display: none; }

    /*页面背景/模块设置--上传图片样式*/
    .template-upload { line-height: 1; }
    .template-upload .template-upload-image { width: 100px; height: 100px; margin-right: 10px; background-color: #F2F2F2; position: relative; }
    .template-upload .template-upload-image .add-img { width: 100%; height: 100%; position: absolute; left: 0; top: 0; }
    .template-upload .template-upload-image .add-img em { font-size: 24px; }
    .template-upload .template-upload-image img { max-width: 100%; max-height: 100%; }
    .template-upload .template-group-text { /*width: 150px;*/ font-size: 12px; color: #999999; }
    .template-upload .template-group-text p { line-height: 22px; font-size: 12px; }
    .template-upload .template-group-btn { display: none; /*width: 100%;*/ height: 30px; padding: 0 4px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }
    .template-upload .template-group-btn .btn-item { width: 30px; height: 30px; margin: 0 8px; background-color: rgba(0, 0, 0, 0.5); border-radius: 999px; cursor: pointer; }
    .template-upload .template-group-btn .btn-item em { color: #ffffff; }
    .template-upload .template-upload-image.active:hover .template-group-btn { display: flex; }


    /*暂未添加组件*/
    .no-content-pc {width: 600px;height: 300px;padding: 60px 130px;box-sizing:border-box;border: 1px solid #B1C5F8;background: #F9FAFD;margin: 300px auto 0;}
    .no-content-pc h4 {font-size: 24px;font-weight: bold;color: #A2B9E9;line-height: 40px;}
    .no-content-pc p {font-size: 16px;color: #A2B9E9;line-height: 26px;}

    /*Components Icon*/
    .template-group-icon {  }
    .template-group-icon .template-upload-title { margin: 14px 0; font-size: 14px; color: #999999; }
    .template-group-icon dl { margin-top: 10px; }
    .template-group-icon dl dt { padding: 0 20px; }
    .template-group-icon dl dt h1 { margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #333333; }
    .template-group-icon dl dt p { font-size: 11px; color: #999999; }
    .template-group-icon dl dd {  }
    .template-group-icon dl dd .template-group-list { padding: 0 20px; margin-top: 20px; }
    .template-group-icon dl dd .template-icon-list { width: 65px; margin:0 15px 10px 15px; border-radius: 7px; cursor: pointer; position: relative;padding: 15px 0;box-sizing: border-box }
    .template-group-icon dl dd .usually-item .template-icon-list,
    .template-group-icon dl dd .template-icon-list:hover { background-color: #EDE8E8; }
    .template-group-icon dl dd .template-icon-list.disabled::before { content: '\5df2\8fbe\A\6dfb\52a0\4e0a\9650'; white-space: pre; width: 102%; height: 100%; line-height: 1.6; padding: 30px 10px; text-align: center; background-color: rgba(0, 0, 0, 0.5); font-size: 14px; color: #ffffff; box-sizing: border-box; position: absolute; left: 0; top: 0; cursor: not-allowed; }
    .template-group-icon dl dd .template-icon-list > div {margin-bottom: 10px; cursor: grab; }
    .template-group-icon dl dd .template-icon-list > div em { font-size: 16px; }
    .template-group-icon dl dd .template-icon-list p { text-align: center;  font-size: 14px; color: #666666; }
    .template-group-icon dl dd .template-icon-list.template-icon-list-usual{height: 30px;line-height: 30px;width: auto;padding: 0 17px;font-size: 12px;background: #EBE9E9;color: #3D3D3D;}
    /*Tree*/
    .template-content-box {background-color: #e9ebed;  }
    .template-group { height: 100%; overflow-y: auto; /*background-color: #F0F8FF;*/ }
    .template-group-nodata { display: table; margin: 40px auto; text-align: center; }
    .template-group-nodata p { margin: 20px 0 12px 0; font-size: 14px; color: #777777; }
    .template-group-nodata .no-data-btn { width: 100px; height: 40px; line-height: 40px; text-align: center; margin: 0 auto; border-radius: 4px; background-color: #409EFF; font-size: 14px; color: #ffffff; cursor: pointer; }
    .template-group-parent .template-group-title { width: 100%; height: 40px; padding: 0 20px; margin-bottom: 7px; cursor: pointer; }
    .template-group-parent .template-group-title.active { background-color: #F0F8FF; }
    .template-group-parent .template-group-title em { transition: all 0.3s ease; font-size: 12px; color: #cccccc; }
    .template-group-parent .template-group-title.active em { transform: rotate(90deg); }
    .template-group-parent .template-group-title label { margin-left: 10px; font-size: 14px; color: #333333; }
    .template-group-parent .template-group-title p { font-size: 12px; color: #999999; }
    .template-group-parent .template-group-list { padding: 0 10px 0 30px; }
    .template-group-parent .template-group-list .list-item { width: 27.333%; margin: 0 10px 14px 10px; cursor: grab; position: relative; }
    .template-group-parent .template-group-list .list-name { display: none; padding: 12px 24px; background-color: #ffffff; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.15); position: absolute; left: 50%; top: 100%; transform: translate(-50%); }
    .template-group-parent .template-group-list .list-name:before { content: ''; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 10px solid #ffffff; position: absolute; left: 50%; bottom: -10px; transform: translate(-50%); }
    .template-group-parent .template-group-list .list-name.name-bottom:before { content: ''; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 10px solid #ffffff; position: absolute; left: 50%; top: -10px; transform: translate(-50%); }
    .template-group-parent .template-group-list .list-item:hover .list-name { display: inline-block; }
    .template-group-parent .template-group-list .list-item p { margin: 7px 0; text-align: center; font-size: 12px; color: #999999; }
    .template-group-parent .template-group-list .list-image { width: 93px; height: 93px; background-color: #F2F2F2; }
    .template-group-child {  }
    .template-group-child .template-group-title { width: 100%; height: 40px; padding: 0 20px 0 40px; margin-bottom: 7px; cursor: pointer; }

    /*.temp-drag-chosen { border: solid 1px #3089dc !important; }*/
    /*拖拽列表默认填充占位*/
    .temp-drag-perview .template-icon-list.sortable-ghost,
    .temp-drag-perview .source-item.sortable-ghost { width: 750px; height: 300px; margin: 0 auto; border: 2px dashed #409EFF; position: relative; }
    .temp-drag-perview .temp-drag-free .sortable-ghost,
    .temp-drag-perview .temp-item-custom .sortable-ghost { width: 100%; height: 100%; border: 2px dashed #409EFF; position: absolute; }
    .temp-drag-perview .template-icon-list.sortable-ghost .iconfont,
    .temp-drag-perview .template-icon-list.sortable-ghost p,
    .temp-drag-perview .source-item.sortable-ghost .iconfont { display: none; }
    .temp-drag-perview .template-icon-list.sortable-ghost:before,
    .temp-drag-perview .source-item.sortable-ghost:before { content: '\62d6\5230\8fd9\4e2a\4f4d\7f6e\653e\4e0b'; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; /*background-color: #F5F7FB;*/ position: absolute; font-size: 28px; color: #D5DEF0; }
    .temp-drag-perview .source-item.sortable-ghost .el-image { display: none !important; }
    .temp-drag-perview .template-icon-list.temp-error.sortable-ghost:before { display: none; }
    .temp-drag-perview .template-icon-list.group-tm.sortable-ghost:before { content: '\70b9\51fb\201c\8bbe\7f6e\201d\ff0c\914d\7f6e\7ec4\4ef6\5185\9700\8981\5c55\793a\7684\5185\5bb9\5427'; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; /*background-color: #F5F7FB;*/ position: absolute; font-size: 28px; color: #D5DEF0; z-index: 1; }

    /*Editor Area*/
    .invite-drag-parent { width: calc(100% - 540px); height: 100vh; margin: 0 auto 0 540px; }
    .invite-drag-child { height: calc(100vh - 80px); min-width: 1236px; background-color: #e9ebed; position: relative; overflow-y: auto; overflow-x: hidden; position: relative; }
    /*.temp-drag-perview { width: 600px; padding: 20px 0; margin: 0 auto; background-color: #f3f5f7; }*/
    .temp-drag-perview .temp-not-nest { width: 100%; margin: 0 auto; /*padding-bottom: 30px;*/ position: relative; }
    .temp-drag-perview .temp-not-nest.custom-width { width: auto; max-width: 1920px; }
    .invite-form-box .temp-item { margin-top: 20px; margin-bottom: 14px; box-shadow: 0px 0px 16px 0px #e5e5e6; background-color: #ffffff; border-radius: 14px; position: relative; box-sizing: border-box; }
    .invite-form-box .temp-item.default { width: 750px; min-height: 300px; margin: 0 auto 30px auto; cursor: move; position: relative; }
    .invite-form-box .temp-item.drag { border: 2px dashed #409EFF; }
    /*.invite-form-box .temp-item.active { !*margin-top: 115px !important;*! !*margin-top: 80px !important;*! border: 2px dashed #409EFF; }*/
    .invite-form-box .temp-item.disabled { border-color: #999999; }
    .invite-form-box .temp-item.temp-drag-free { height: 500px; }
    /*.invite-form-box .temp-item.temp-item-custom { margin-top: 20px; }*/
    .invite-form-box .temp-item.temp-item-custom.default { min-height: 402px; }
    .invite-form-box .temp-item.item-custom-four.default { min-height: 346px; }
    .invite-form-box .temp-item.item-custom-five.default { min-height: 300px; }
    .invite-form-box .temp-item.temp-act-countdown { min-height: 200px; margin-top: 20px; }
    .invite-form-box .temp-error { position: relative; }
    .invite-form-box .temp-error .temp-item-nodata { color: #ffffff; }
    .invite-form-box .temp-error::before { content: '\57fa\7840\7ec4\4ef6\4e0d\652f\6301\4e0e\5f53\524d\7ec4\4ef6\53e0\52a0'; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; border: 2px dashed #EA0808; background-color: rgba(255, 255, 255, 0.9); font-size: 28px; color: #EA0808; position: absolute; left: 0; top: 0; z-index: 999; }
    .invite-form-box .temp-item.sortable-chosen { border: 2px dashed #409EFF; }
    .invite-form-box .temp-item.error { border: 2px dashed #EA0808; }
    .invite-form-box .temp-item .temp-item-child { width: 100%; height: 100%; position: absolute; left: 0; top: 0; }
    .invite-form-box .temp-item .temp-item-child .temp-item-content { height: 100%; padding: 26px 28px; box-sizing: border-box; position: relative; }
    .invite-form-box .temp-item .temp-item-child .temp-item-content .temp-item-href { width: 100%; height: 100%; position: absolute; left: 0; top: 0; }
    .invite-form-box .temp-item-text { width: 100%; height: 100%; line-height: 26px; text-align: center; outline: none; border: none; resize: none; -webkit-user-modify: read-write-plaintext-only; background-color: transparent; cursor: text; font-size: 12px; }
    /*模板头部设置*/
    .invite-form-box .temp-item .temp-item-setting { width: 100%; height: 70px; position: absolute; left: 50%; top: -86px; transform: translate(-50%); }
    .invite-form-box .temp-item .temp-item-setting::before { content: ''; width: 0; height: 0; border-left: 25px solid transparent; border-right: 25px solid transparent; border-top: 20px solid #E4E7EE; position: absolute; left: 50%; bottom: -10px; transform: translate(-50%); }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box { width: 100%; /*height: 50px;*/ padding: 0 20px; background-color: #E4E7EE; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.15); border-radius: 8px; box-sizing: border-box; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-btns,
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-show { padding: 12px 0; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .btn,
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .btn:active { width: 60px; height: 34px; padding: 0; margin-right: 10px; line-height: 32px; text-align: center; background-color: #ffffff; box-shadow: none; color: #333333; border: 1px solid #CCCCCC; border-radius: 4px; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .btn.disabled { background-color: #F2F2F2 !important; border-color: #F2F2F2 !important; color: #999999 !important; cursor: not-allowed; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .btn:hover { color: #409EFF; border-color: #c6e2ff; background-color: #ecf5ff; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .btn.primary { margin-left: 24px; background-color: #409EFF; border-color: #409EFF; color: #ffffff; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-name { padding: 5px 0; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-name-center { position: absolute; left: 50%; transform: translate(-50%); }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-name .temp-setting-input { width: 180px; height: 28px; line-height: 26px; text-align: center; border: 1px solid #E4E7EE; font-size: 16px; font-weight: 600; overflow: hidden; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-name input { width: 99%; height: 100%; line-height: 26px; padding: 0 8px; text-align: center; background-color: #E4E7EE; font-size: 16px; font-weight: 600; border: none; outline: none; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-name p { margin-top: 5px; text-align: center; font-size: 12px; color: #777777; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-show { margin-left: 20px; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-show p { margin-top: 6px; font-size: 12px; color: #333333; }
    .invite-form-box .temp-item .temp-item-setting.active .temp-setting-box { background-color: #ffffff; }
    .invite-form-box .temp-item .temp-item-setting.active::before { border-top: 20px solid #ffffff; }
    .invite-form-box .temp-item .temp-item-setting.active .temp-setting-box .temp-setting-name .temp-setting-input,
    .invite-form-box .temp-item .temp-item-setting.active .temp-setting-box .temp-setting-name input { background-color: #ffffff; border-color: #ffffff; }
    .invite-form-box .temp-item .temp-item-setting .temp-setting-box .temp-setting-name .temp-setting-input.active,
    .invite-form-box .temp-item .temp-item-setting.active .temp-setting-box .temp-setting-name div.active { border: 1px solid #409EFF; border-radius: 4px; }
    /*文本字体*/
    .invite-form-box  .temp-item-text__editor { display: none; padding: 12px 20px; background-color: #ffffff; box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.15); border-radius: 8px; position: absolute; left: 50%; top: -74px; transform: translate(-50%); z-index: 10; }
    /*.invite-form-box  .temp-resize-able:hover .temp-item-text__editor,*/
    .invite-form-box  .temp-resize-able.active .basic-text-editor { display: flex; }
    .invite-form-box  .temp-item-text__editor::before { content: ''; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 10px solid #ffffff; position: absolute; left: 50%; bottom: -10px; transform: translate(-50%); }
    .invite-form-box  .temp-item-text__editor .el-dropdown { width: 70px; padding-left: 10px; margin-right: 10px; line-height: 28px; border: 1px solid #CCCCCC; border-radius: 4px; }
    .el-dropdown-menu.el-popper button { padding: 0; border: none; outline: none; background-color: #ffffff; }
    .el-dropdown-menu.el-popper button:hover { background-color: #ecf5ff; }
    .invite-form-box .temp-item-text__editor .temp-item-remove { width: 30px; height: 30px; margin-left: 14px; cursor: pointer; }
    .invite-form-box .temp-item-text__editor .temp-item-remove em { font-size: 20px; }
    .invite-form-box .temp-item-text__editor .temp-item-bold { width: 30px; height: 30px; margin-right: 15px; background-color: #ffffff; border: 1px solid #CCCCCC; border-radius: 4px; }
    .invite-form-box .temp-item-text__editor .temp-item-bold.active,
    .invite-form-box .temp-item-text__editor .temp-item-bold:hover { border-color: #409EFF; color: #409EFF; }
    .invite-form-box .temp-item-text__editor .temp-item-color { width: 30px; height: 30px; padding: 4px 0; position: relative; }
    .invite-form-box .temp-item-text__editor .temp-item-color div.iconfont { line-height: 1; text-align: center; margin-bottom: 2px; font-size: 16px; color: #333333; font-weight: bold; position: relative; }
    .invite-form-box .temp-item-text__editor .temp-item-color p { height: 4px; width: 20px; margin: 0 auto; border: 1px solid #CCCCCC; }
    .invite-form-box .temp-item-text__editor .temp-item-color div.iconfont::before,
    .font-edit-two .font-edit-item .temp-back-box::before { content: ''; width: 0; height: 0; border-left: 4.5px solid transparent; border-right: 4.5px solid transparent; border-top: 6px solid #333333; position: absolute; left: 24px; top: 8px; }
    .invite-form-box .temp-item-text__editor .temp-item-color .el-color-picker { width: 100%; height: 100%; position: absolute; left: 50%; top: 0; transform: translate(-50%); }
    .invite-form-box .temp-item-text__editor .temp-child-setting { width: 50px; line-height: 30px; padding-right: 18px; font-size: 14px; position: relative; }
    .invite-form-box .temp-item-text__editor .temp-child-setting::before { content: ''; width: 1px; height: 8px; background-color: #CCCCCC; position: absolute; right: 0; top: 50%; transform: translate(0, -50%); cursor: pointer; }
    /*.invite-form-box .el-color-picker__trigger { width: 30px; height: 30px; border: none; }*/
    /* .invite-form-box .el-color-picker__color { display: none; border: none; }
    .invite-form-box .el-color-picker__icon { display: none; border: none; } */
    .invite-form-box .temp-item .temp-drag-children { width: 750px !important; min-height: 100%; /*left: 50% !important; transform: translate(-50%);*/ left: 0; }
    .invite-form-box .temp-item .temp-drag-children li { height: 100%; }
    /*基础组件拖拽*/
    .temp-resize-able { position: absolute; left: 0; top: 0; }
    .temp-resize-able .temp-resize-warn { font-size: 14px; position: absolute; left: 0; top: -16px; z-index: 1; }
    .temp-resize-able .handle { width: 24px !important; height: 24px !important; border: none; background: #409EFF; right: -2px !important; bottom: -2px !important; z-index: 9; }
    .temp-resize-able .handle::before { content: '\e71c'; font-family: 'iconfont'; font-size: 24px; color: #ffffff; }
    .temp-resize-able .temp-item-upload { width: 100%; height: 100%; /*background-color: #ffffff;*/ position: relative; }
    .temp-resize-able .temp-item-upload p { margin-top: 20px; font-size: 16px; color: #CCCCCC; }
    .temp-resize-able .temp-item-upload img { width: 100%; max-height: 100%; }
    .temp-resize-able .temp-item-upload input { display: none; }
    .temp-resize-able .temp-item-upload input, .temp-resize-able .temp-item-upload #drop_area { width: 100%; height: 100%; border: none; outline: none; position: absolute; left: 0; top: 0; }
    /*.template-times-tabs::before { content: ''; width: 736px; !*width: calc(100% - 130px);*! height: 6px; background-color: rgba(255, 255, 255, 0.5); border-radius: 4px; position: absolute; position: absolute; left: 50%; bottom: 12px; transform: translate(-50%); }*/
    /*基础组件--直播*/
    .temp-item-live { height: 100%; /*background-color: #EDEDED;*/ background-color: #ffffff; border-radius: 20px; position: relative; }
    .temp-item-live .temp-item-live__static { height: 24px; padding: 4px 12px; border-radius: 12px 12px 12px 0; font-size: 14px; color: #ffffff; position: absolute; left: 20px; top: 10px; }
    .temp-item-live .temp-item-live__static.will { background: #FB480E; }
    .temp-item-live .temp-item-live__static.start { background: linear-gradient(to right, #F03C97, #FE3171); }
    .temp-item-live .temp-item-live__static.end { background: #CACACA; }
    .temp-item-live .temp-item-live__content { width: 100%; height: 100%; }
    .temp-item-live .temp-item-live__content p { margin: 10px 0; text-align: center; }
    .temp-item-live .temp-item-live__content img { max-width: 100%; max-height: 100%; }
    .temp-item-live .temp-item-live__mark { width: 100%; /*height: 28%;*/ background-color: rgba(0, 0, 0, 0.3); border-radius: 0 0 20px 20px; position: absolute; left: 0; bottom: 0; }
    .temp-item-live .temp-item-live__info { /*width: 100%; padding: 10px 20px;*/ }
    .temp-item-live .temp-item-live__info .temp-item-live__header { margin-right: 14px; border-radius: 100%; background-color: #ffffff; overflow: hidden; }

    /*Banner模块*/
    .temp-drag-perview .temp-item.item-temp-banner { width: 100%; max-width: 1920px; min-height: auto; }
    .temp-drag-perview .temp-item.item-temp-banner .temp-custom-item { margin: 0 auto; }

    /*竞拍模块*/
    .template-auction {}
    .template-auction-time { margin: 30px 0; text-align: center; font-size: 20px; color: #ffffff; }
    /*适用于 【竞拍、特卖1、秒杀2】 商品列表样式，样式不同的需要做样式覆盖*/
    .goods-list-horizontal { width: 990px; margin: 0 auto; }
    .goods-list-horizontal .temp-font::before { height: 24px; top: -16px; }
    .goods-list-horizontal .goods-item { padding: 30px; margin: 20px auto; /*background-color: rgba(255, 255, 255, 0.8);*/ background-position: center; background-repeat: no-repeat; background-size: 100% 100%; border-radius: 20px; position: relative; }
    .goods-list-horizontal .goods-item .goods-image { width: 256px; height: 256px; /*background-color: #ffffff;*/ border-radius: 4px; box-shadow: 0px 2px 13px 0px rgba(0,0,0,0.15); position: relative; overflow: hidden; }
    .goods-list-horizontal .goods-item .goods-image .goods-sign { width: 100px; height: 100px; position: absolute; left: 10px; top: 10px; z-index: 4; }
    .goods-list-horizontal .goods-item .goods-image .goods-sign img { max-width: 100%; max-height: 100%; }
    .goods-list-horizontal .goods-item .goods-info { width: 505px; margin-left: 35px; }
    .goods-list-horizontal .goods-item .goods-info .goods-name { height: 60px; line-height: 1.2; font-size: 24px; }
    .goods-list-horizontal .goods-item .goods-info .goods-point { max-width: 662px; height: 16px; margin-top: 20px; font-size: 16px; color: #FFD87A; }
    .goods-list-horizontal .goods-item .goods-info .goods-tag { padding: 8px 10px; margin-right: 10px; background-color: #FFD87A; border-radius: 4px; font-size: 16px; color: #E82C42; }
    .goods-list-horizontal .goods-item .goods-info .goods-view { margin-top: 30px; }
    .goods-list-horizontal .goods-item .goods-info .goods-view em { margin-right: 5px; color: #ACA5A6; }
    .goods-list-horizontal .goods-item .goods-info .goods-view p { /*margin-right: 18px;*/ font-size: 16px; color: #666666; }
    .goods-list-horizontal .goods-item .goods-info .goods-price-info { margin-top: 32px; }
    .goods-list-horizontal .goods-item .goods-info .goods-price-info .goods-price p { font-size: 38px; color: #C61010; }
    .goods-list-horizontal .goods-item .goods-info .goods-price-info .goods-price p label { font-weight: 600; }
    .goods-list-horizontal .goods-item .goods-info .goods-price-info .goods-price em,
    .goods-list-horizontal .goods-item .goods-info .goods-price-info .goods-price span { font-size: 16px; }
    .goods-list-horizontal .goods-item .goods-info .goods-price-info .goods-number { margin-left: 30px; }
    .goods-list-horizontal .goods-item .goods-info .goods-bottom { width: 100%; height: 46px; margin-top: 35px; background-color: #ffffff; border-radius: 4px; }
    .goods-list-horizontal .goods-item .goods-info .font-edit-two { padding: 0 18px 5px 5px; top: -98px; }
    .goods-list-horizontal .goods-item .goods-info .goods-time { padding: 14px 20px; font-size: 18px; color: #E82C42; }
    .goods-list-horizontal .goods-item .goods-info .goods-btn { width: 124px; height: 46px; line-height: 46px; text-align: center; background-color: #E82C42; border-radius: 4px; font-size: 20px; color: #ffffff; }
    .goods-list-horizontal .goods-item.disabled .goods-info .goods-btn { background-color: #CCCCCC !important; color: #ffffff !important; }
    .goods-list-horizontal .goods-item.disabled .goods-info .goods-time { color: #666666 !important; }
    .goods-list-horizontal .goods-item .goods-out-price { min-width: 122px; height: 50px; line-height: 50px; text-align: center; background-color: #FFE1B1; border-radius: 0 20px 0 20px; box-shadow: -1px 3px 7px 0px rgba(199,108,0,0.25); font-size: 20px; color: #E82C42; position: absolute; right: 0; top: 0; }
    .goods-list-horizontal .goods-item .goods-disabled { width: 120px; height: 105px; position: absolute; top: 118px; right: 40px; }
    .goods-list-horizontal .goods-item .goods-disabled-image { width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.4); position: absolute; left: 0; top: 0; z-index: 5; }
    .goods-list-horizontal .goods-item .goods-disabled-image .goods-disabled { position: static; }
    /*特卖一样式*/
    .template-goods-one .goods-item { margin: 40px auto 20px auto; }
    .goods-list-horizontal .font-edit-only { top: -65px; }
    .template-goods-one .goods-item { padding: 50px 30px; /*background-color: rgba(0, 0, 0, 0.15);*/ border: 2px solid #FF5D5D; }
    .template-goods-one .goods-item .goods-image { width: 276px; height: 276px; }
    .template-goods-one .goods-item .goods-time { width: 460px; height: 40px; /*padding: 5px 60px;*/ background-color: #FFF0CB; border-radius: 20px; position: absolute; left: 50%; top: -20px; transform: translate(-50%); }
    .template-goods-one .goods-item .goods-time em { font-size: 20px; color: #E82C42; }
    .template-goods-one .goods-item .goods-time h1 { margin: 0 0 0 14px; font-size: 18px; }
    .template-goods-one .goods-item .goods-time .goods-time-num { width: 30px; height: 30px; line-height: 30px; text-align: center; background-color: #E82C42; border-radius: 5px; font-size: 18px; color: #ffffff; }
    .template-goods-one .goods-item .goods-time .goods-time-text { margin: 0 8px; font-size: 18px; }
    .template-goods-one .goods-item .goods-info { width: 622px; margin-left: 20px; }
    .template-goods-one .goods-item .goods-info .goods-name { color: #ffffff; }
    .template-goods-one .goods-item .goods-info .goods-presenter { height: 32px; margin-top: 30px; }
    .template-goods-one .goods-item .goods-info .goods-presenter-text { font-size: 18px; font-weight: 600; color: #FFD87A; }
    .template-goods-one .goods-item .goods-info .goods-price { margin-top: 36px; font-size: 38px; color: #ffffff; }
    .template-goods-one .goods-item .goods-info .goods-price span { font-size: 16px; }
    .template-goods-one .goods-item .goods-info .goods-price-line { margin-top: 20px; font-size: 16px; color: #ffffff; text-decoration: line-through; }
    .template-goods-one .goods-item .goods-info .goods-btn-box { position: absolute; right: 42px; bottom: 50px; }
    .template-goods-one .goods-item .goods-disabled { top: 127px; }
    /*特卖二样式*/
    .template-goods-two .goods-list-horizontal,
    .template-goods-three .goods-list-horizontal { width: 1210px; }
    .template-goods-two .goods-item { width: 48.26%; padding: 20px; margin: 0 20px 10px 0; }
    .template-goods-two .goods-item .goods-image { width: 232px; height: 232px; }
    .template-goods-two .goods-item .goods-info { width: 296px; }
    .template-goods-two .goods-item .goods-info .goods-name { height: 48px; line-height: 1.3; font-size: 18px; }
    .template-goods-two .goods-item .goods-info .goods-point { max-width: 182px; height: 14px; margin-top: 12px; font-size: 14px; color: #E82C42; }
    .template-goods-two .goods-item .goods-info .goods-presenter { height: 24px; margin-top: 18px; }
    .template-goods-two .goods-item .goods-info .goods-presenter-text { font-weight: 600; font-size: 14px; color: #E82C42; }
    .template-goods-two .goods-item .goods-info .goods-tag { padding: 6px; background-color: #E82C42; font-size: 14px; color: #ffffff; }
    .template-goods-two .goods-item .goods-info .goods-price,
    .template-goods-three .goods-item .goods-info .goods-price { margin-top: 20px; font-size: 30px; color: #E82C42; }
    .template-goods-two .goods-item .goods-info .goods-price span,
    .template-goods-three .goods-item .goods-info .goods-price span { font-size: 16px; }
    .template-goods-two .goods-item .goods-info .goods-price .goods-unit,
    .template-goods-three .goods-item .goods-info .goods-price .goods-unit { font-size: 12px; color: #333333; }
    .template-goods-two .goods-item .goods-info .goods-btn { width: 294px; hieght: 42px; margin-top: 18px; font-size: 16px; color: #FA9DA8; }
    .template-goods-two .goods-item .goods-disabled { top: 88px; right: 10px; }
    /*特卖三样式*/
    .template-goods-three .font-edit-two { top: -118px; }
    .template-goods-three .font-edit-only { top: -56px; }
    .template-goods-three .goods-item { width: 23.26%; padding: 0; margin: 0 20px 10px 0; border-radius: 0; /*background-color: #ffffff;*/ }
    .template-goods-three .goods-item .goods-image { width: 100%; height: 282px; border-radius: 0; box-shadow: none; }
    .template-goods-three .goods-item .goods-image .temp-font { width: 100%; height: 32px; position: absolute; left: 0; bottom: 0; z-index: 3; }
    .template-goods-three .goods-item .goods-image .goods-desc { width: 100%; height: 32px; padding: 0 10px; line-height: 32px; text-align: center; background-color: #FEE2E5; font-size: 14px; color: #E82C42; }
    .template-goods-three .goods-item .goods-info { width: 100%; margin-left: 0; }
    .template-goods-three .goods-item .goods-info .goods-name { max-width: 100%; height: 38px; padding: 0 10px; margin-top: 16px; font-size: 16px; }
    .template-goods-three .goods-item .goods-info .goods-price { padding: 0 10px; margin-top: 12px; }
    .template-goods-three .goods-item .goods-info .goods-price .goods-price-tag { padding: 5px 7px; margin-right: 8px; background-color: #FEE2E5; border-radius: 10px; font-size: 12px; color: #E82C42; }
    .template-goods-three .goods-item .goods-info .goods-price span:first-child { font-weight: 600; }
    .template-goods-three .goods-item .goods-info .goods-price .goods-price-text { max-width: 240px; font-size: 26px; font-weight: 600; }
    .template-goods-three .goods-item .goods-info .goods-price.goods-integral-price .goods-price-text { font-size: 20px; }
    .template-goods-three .goods-item .goods-info .goods-btn { width: 272px; height: 42px; line-height: 42px; margin: 12px auto 5px auto; border-radius: 0; font-size: 16px; color: #FA9DA8; }
    .template-goods-three .goods-item.disabled .goods-info .goods-btn { background-color: #FEE2E5 !important; color: #FA9DA8 !important; }
    .template-goods-three .goods-item .goods-disabled { width: 145px; height: 132px; }

    /*特卖四样式*/
    .template-goods-four {}
    .template-goods-four .goods-item { width: 18.333%; padding: 0; /*margin: 10px;*/ border-radius: 0; /*background-color: #ffffff;*/ }
    .template-goods-four .goods-item .goods-image { width: 100%; height: 222px; }
    .template-goods-four .goods-item .goods-info .goods-btn { width: 212px; }
    .template-goods-four .goods-item .goods-disabled { width: 120px; height: 108px; }
    .template-goods-four .goods-item .goods-disabled-image { height: 222px; }

    /*秒杀样式一*/
    .temp-seckill-one .goods-time-box { display: table; margin: 30px auto 32px auto; font-size: 20px; color: #ffffff; }
    .temp-seckill-one .goods-list-horizontal .goods-item { margin: 0px 8px 10px 12px; }
    .temp-seckill-one .template-goods-three .goods-item .goods-info .goods-btn { width: 262px; }
    /*秒杀样式二*/
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-name-box { padding-bottom: 20px; border-bottom: 1px solid #ffffff; }
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-time { padding: 0; margin-top: 18px; }
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-time p { color: #333333; }
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-time .goods-tag { padding: 5px 4px 5px 6px; background-color: #E82C42; }
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-time .goods-tag p,
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-time .goods-tag em { font-size: 14px; color: #ffffff; }
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-time .goods-tag em { margin-left: 6px; font-size: 16px; }
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-limit { margin-top: 18px; font-size: 16px; color: #E82C42; }
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-disabled-image { height: 256px; }
    .temp-seckill-two .goods-list-horizontal .goods-item .goods-disabled { width: 145px; height: 132px; }
    /*秒杀样式三*/
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-info { margin-left: 20px; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-image,
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-disabled-image { width: 200px; height: 200px; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-time { padding: 0; margin-bottom: 16px; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-time p { color: #333333; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-time .goods-tag { padding: 5px 4px 5px 6px; background-color: #E82C42; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-time .goods-tag p,
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-time .goods-tag em { font-size: 16px; color: #ffffff; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-time .goods-tag em { margin-left: 6px; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-limit { margin-top: 18px; font-size: 16px; color: #E82C42; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-disabled { width: 120px; height: 108px; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-price { margin-top: 30px; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-price span,
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-price em { font-size: 14px; color: #333333; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-price p { font-size: 30px; }
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-price em,
    .temp-seckill-three .goods-list-horizontal .goods-item .goods-price p { color: #E82C42; }

    .temp-custom .photo-null-item,
    .temp-custom-one .photo-null-item { height: 382px; }
    /*自定义样式一*/
    .temp-item-custom .temp-custom-item { width: 100%; height: 382px; margin: 0 auto 20px auto; }
    .temp-custom-item img { max-width: 100%; max-height: 100%; }
    .temp-drag-perview .temp-item.item-custom-one { width: 750px; max-width: 750px; }
    .temp-custom-one.temp-item-nodata { width: 1166px; border: 1px dashed #cccccc; }
    .temp-custom-one .temp-custom-item,
    .temp-custom-one .temp-custom-item img { /*max-height: 382px;*/ }
    .temp-custom-one .temp-custom-item,
    .temp-custom-one .temp-custom-item:last-child { margin-bottom: 0; }
    /*自定义样式二*/
    .temp-custom { width: 1186px; margin: 0 auto; }
    .temp-custom .temp-custom-item { /*height: 382px;*/ margin: 10px; }
    .temp-custom-two .temp-custom-item { width: 48.3%; }
    /*自定义样式三*/
    .temp-custom-three .temp-custom-item { width: 31.64%; }
    /*自定义样式四*/
    .temp-custom-four .temp-custom-item { width: 23.31%; height: 322px; }
    /*自定义样式五*/
    .temp-custom-five .temp-custom-item { width: 18.31%; height: 254px; }
    /*自定义样式占位*/
    .temp-custom .photo-null-item.border-line { margin: 10px; border: 1px dashed #cccccc; }
    /*Banner*/
    .temp-banner-item .temp-custom-item { margin: 0 auto; }

    /*页面导航样式*/
    .nav-right { width: 200px; min-height: 210px; border: 2px dashed rgb(64, 158, 255); box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);  position: fixed; top: 166px; right: 50px; z-index: 9; }
    .nav-right .nav-right-hover { width: 100%; height: 100px; padding-top: 10px; text-align: center; background-color: rgba(0, 0, 0, 0.5); }
    .nav-right .nav-right-hover p { line-height: 36px; margin-bottom: 5px; font-weight: 600; font-size: 16px; color: #ffffff; }
    .nav-right .nav-right-hover .rect-btn { width: 90px; height: 32px; line-height: 28px; background: rgba(0,0,0,0.3); border: 2px dashed #409EFF; font-size: 14px; color: #fff; cursor: pointer; position: relative; }
    .nav-right .nav-right-hover .rect-btn::before { content: '\e71c'; width: 14px; height: 14px; line-height: 14px; background: #409EFF; font-family: 'iconfont'; font-size: 14px; color: #ffffff; border: none; right: -2px; bottom: -2px; z-index: 9; position: absolute; }
    .nav-right .have-bg { width: 100%; height: 100%; position: relative; }
    .nav-right .have-bg .nav-img-wrap { width: 120px; }
    .nav-right .have-bg .nav-img-wrap img { width: 100%; }
    .temp-act-nav { width: 120px; min-height: 220px; /*background-color: #ffffff;*/ }
    .temp-act-nav .temp-nav-icon { width: 120px; height: 120px; border: 2px dashed #E0E4EF; }
    .temp-act-nav .temp-nav-set { display: none; width: 100%; height: 100%; padding: 30px 20px; background-color: rgba(0, 0, 0, 0.8); position: absolute; left: 0; top: 0; }
    .temp-act-nav:hover .temp-nav-set { display: block; }
    .temp-act-nav .temp-nav-set h1 { text-align: center; font-size: 14px; font-weight: 600; color: #ffffff; }
    .temp-act-nav .temp-nav-set .el-switch { display: table; margin: 40px auto 14px auto; }
    .temp-act-nav .temp-nav-set p { margin: 10px 0; text-align: center; font-size: 14px; color: #ffffff; }
    .temp-act-nav .temp-nav-btn { width: 80px; height: 32px; padding: 0; margin-right: 10px; line-height: 32px; text-align: center; background-color: #409EFF; box-shadow: none; color: #ffffff; border-radius: 4px; }
    .temp-act-nav ul { background-position: center; background-repeat: no-repeat; background-size: 100% 100%; }
    .temp-act-nav ul li { width: 120px; height: 40px; line-height: 40px; text-align: center; background-color: #ffffff; border: 1px dashed #E0E4EF; border-top: none; font-size: 14px; color: #333333; }
    .temp-act-nav ul li:first-child { border-top: 1px dashed #E0E4EF; }

    /*导航矩形*/
    .nav-right .have-bg .nav-auto-style{width: 100%;height: 100%;position: relative;position: relative;background: rgba(255,255,255,0.3)}
    .nav-right .have-bg .nav-auto-style .nav-index{ width: 20px; height: 20px; line-height: 20px; text-align: center; font-size: 14px; color: #fff; position: absolute; left: -30px; top:0; bottom: 0; margin: auto; background: #FFA628; border-radius: 50%; }
    .nav-right .have-bg .nav-auto-style .nav-delete{ width: 20px; height: 20px; line-height: 20px; text-align: center; font-size: 14px; position: absolute; right: -30px; top:0; bottom: 0; margin: auto; color: #333; cursor: pointer; border-radius: 50%; }
    @keyframes shan {
        0%{
            box-shadow: 0 0 3px 3px rgba(64,158,255,0.3);
        }
        50%{
            box-shadow: 0 0 3px 3px rgba(64,158,255,1);
        }
        100%{
            box-shadow: 0 0 3px 3px rgba(64,158,255,0.3);
        }
    }
    .nav-right .have-bg .nav-auto-style .nav-dong{ position: absolute; right: 0; left: 0; top:0; width: 6px; height: 6px; animation: shan infinite 1s; border-radius: 50%; background: #409EFF; bottom: 0; margin: auto; }

    /*自定义样式*/
    .set-item.set-custom dl { border: 1px solid #E7F1F9; }
    .set-item.set-custom dl dt { width: 100%; height: 40px; line-height: 40px; background-color: #F4F8FB; }
    .set-item.set-custom dl dt div { text-align: center; }
    .set-item.set-custom dl dt div:nth-of-type(1) { width: 70px; margin: 0 20px; }
    .set-item.set-custom dl dt div:nth-of-type(2) { width: 430px; }
    .set-item.set-custom dl dt div:nth-of-type(3) { width: 80px; margin: 0 20px; }
    .set-item.set-custom dl dt div:nth-of-type(4) { width: 80px; margin-right: 20px; }
    .set-item.set-custom dl dd { padding: 10px 0 5px 0; }
    .set-item.set-custom dl dd .list-item { margin-bottom: 10px; position: relative; }
    .set-item.set-custom dl dd .template-upload .template-upload-image,
    .set-item.set-custom dl dd .template-upload .template-upload-image .el-image { width: 70px; height: 70px; }
    .set-item.set-custom dl dd .template-upload .template-upload-image { margin: 0 20px; }
    .set-item.set-custom dl dd .template-upload .template-upload-image .add-img em { font-size: 20px; }
    .set-item.set-custom dl dd .el-select,
    .set-item.set-custom dl dd .el-input { display: table; }
    .set-item.set-custom dl dd .set-type { width: 130px; height: 100%; }
    .set-item.set-custom dl dd .set-value { width: 200px; height: 100%; }
    .set-item.set-custom dl dd .set-value input,
    .set-item.set-custom dl dd .set-value .el-input input { border-radius: 0px 4px 4px 0px; }
    .set-item.set-custom dl dd .set-type .el-input input { border-radius: 4px 0px 0px 4px; }
    .set-item.set-custom dl dd .set-sort { margin: 0 20px; }
    .set-item.set-custom dl dd .set-delete {  }

    .set-item.set-custom.set-nav dl dt > div, .set-item.set-custom.set-nav dl .list-item > div { width: 260px; margin: 0 10px; }
    .set-item.set-custom.set-nav dl .list-item > div:nth-of-type(1), .set-item.set-custom.set-nav dl dt > div:nth-of-type(1) { width: 60px; }
    .set-item.set-custom.set-nav dl .list-item > div:nth-of-type(2), .set-item.set-custom.set-nav dl dt > div:nth-of-type(2) { width: 260px; }
    .set-item.set-custom.set-nav dl .list-item > div:nth-of-type(4), .set-item.set-custom.set-nav dl dt > div:nth-of-type(4) { width: 80px; }

    .contract_elwarr { height: 22px; line-height: 22px; padding: 0 !important; font-size: 12px; color: #999999; }
    .contract_elwarr span { color: #999999; }

    /*公共时间轴切换*/
    .activity-time-axes { width: 1140px; margin: 50px auto 30px auto; position: relative; }
    .activity-time-axes .activity-time-axes__line { content: ''; width: 736px; /*width: calc(100% - 130px);*/ height: 6px; background-color: rgba(255, 255, 255, 0.5); border-radius: 4px; position: absolute; position: absolute; left: 50%; bottom: 14px; transform: translate(-50%); }
    .activity-time-axes:hover .font-edit-three { display: block; top: -162px; }
    .activity-time-axes .template-times-tabs { width: 1054px; height: 124px; margin: 0 auto; position: relative; overflow: hidden; }
    .activity-time-axes .check-btn { font-size: 36px; color: rgba(255, 255, 255, 0.5); border-radius: 999px; position: absolute; top: 12px; }
    .activity-time-axes .check-btn.prev { left: -20px; }
    .activity-time-axes .check-btn.next { right: -20px; }
    .activity-time-axes .temp-tab-translate { width: 100%; height: 60px; transition: left 0.3s ease; position: absolute; left: 0; top: 0; }
    .activity-time-axes .temp-tab-translate .tab-item { width: 150px; height: 100%; padding: 6px 0; margin-right: 31px; background-color: rgba(255, 255, 255, 0.3); border-radius: 30px; position: relative; }
    .activity-time-axes .temp-tab-translate .tab-item .draw { width: 32px; height: 32px; position: absolute; left: 50%; bottom: -62px; transform: translate(-50%); }
    .activity-time-axes .temp-tab-translate .tab-item .draw .big-draw { content: ''; width: 32px; height: 32px; /*background-color: rgba(255, 255, 255, 0.5);*/ background-color: #E82C42; border-radius: 999px; position: absolute; left: 50%; bottom: -1px; transform: translate(-50%); }
    .activity-time-axes .temp-tab-translate .tab-item .draw .small-draw { content: ''; width: 14px; height: 14px; background-color: #ffffff; border-radius: 999px; position: absolute; left: 50%; bottom: 8px; transform: translate(-50%); z-index: 1; }
    .activity-time-axes .temp-tab-translate .tab-item .triangle { display: none; width: 0; height: 0; border-left: 12px solid transparent; border-right: 12px solid transparent; border-top: 14px solid #ffffff; position: absolute; left: 50%; bottom: -14px; transform: translate(-50%); }
    .activity-time-axes .temp-tab-translate .tab-item p,
    .activity-time-axes .temp-tab-translate .tab-item span { color: rgba(255, 255, 255, 0.5); }
    .activity-time-axes .temp-tab-translate .tab-item p { font-size: 24px; }
    .activity-time-axes .temp-tab-translate .tab-item span { font-size: 16px; }
    /*当时间轴不满足左右切换(6个)*/
    .activity-time-axes .temp-tab-translate.no-translate { width: auto; left: 50% !important; transform: translate(-50%); }
    .activity-time-axes .temp-tab-translate.no-translate .tab-item:last-child { margin-right: 0; }
    /*时间轴选中*/
    .activity-time-axes .temp-tab-translate .tab-item.active { background-color: #ffffff; }
    .activity-time-axes .temp-tab-translate .tab-item.active p,
    .activity-time-axes .temp-tab-translate .tab-item.active span { color: #E82C42; }
    .activity-time-axes .temp-tab-translate .tab-item.active .triangle { display: block; }
    .activity-time-axes .temp-tab-translate .tab-item.active .draw .big-draw { content: ''; width: 32px; height: 32px; background-color: #ffffff !important; border-radius: 999px; position: absolute; left: 50%; bottom: -0.5px; transform: translate(-50%); }
    .activity-time-axes .temp-tab-translate .tab-item.active .draw .small-draw { content: ''; width: 14px; height: 14px; background-color: #E82C42; border-radius: 999px; position: absolute; left: 50%; bottom: 8px; transform: translate(-50%); z-index: 1; }
    /*当组件位置置顶时，将文字设置组件向下偏移*/
    .activity-time-axes:hover .temp-font.is-first::before { top: 50px; }
    .activity-time-axes:hover .is-first .font-edit-three { display: block; top: 84px; }
    .is-first .font-edit-three .font-edit-item {  }
    /*.temp-font.is-first:hover .temp-font-edit .font-edit-first { top: 40px; }*/
    .temp-font.is-first:hover .temp-font-edit .font-edit-first { top: 100%; }
    .goods-list-horizontal .temp-font.is-first::before,
        /*直播组件*/
    .temp-item-live__config .temp-font.is-first::before { top: 16px; }
    .temp-item-live__config .temp-font::before { top: -16px; }
    .temp-item-live__config .temp-font.is-first::before { height: 16px; }
    .is-first .font-edit-first.temp-item-text__editor::before,
    .is-first .font-edit-three.temp-item-text__editor::before { border-top: 10px solid transparent; border-bottom: 10px solid #ffffff; bottom: auto; top: -18px; }

    /*标签导航模块*/
    .temp-drag-perview .temp-item.temp-activity-label { min-height: 750px; margin: 0 auto; }
    .activity-label-axes { margin: 30px auto; }
    .activity-label-axes .template-times-tabs { height: 60px; }
    .activity-label-axes .temp-tab-translate .tab-item { padding: 6px 32px; }
    .activity-label-axes .temp-tab-translate .tab-item p { max-height: 42px; line-height: 1.2; text-align: center; font-size: 18px; overflow: hidden; }

    /*倒计时模块*/
    .template-countdown { padding: 42px 0; background-position: center; background-repeat: no-repeat; background-size: 100%; }
    .template-countdown .template-countdown-name { width: 100%; padding: 0 10px; position: relative; }
    .template-countdown .template-countdown-name .temp-item-text { width: 100%; text-align: center; font-size: 26px; font-weight: 600; }
    .template-countdown .template-countdown-name .temp-count-num { height: 60px; line-height: 60px; margin-top: 30px; }
    .template-countdown .template-countdown-name .count-num,
    .template-countdown .template-countdown-name .count-num-box { margin-right: 8px; font-size: 38px; font-weight: 600; }
    .template-countdown .template-countdown-name .count-num-box { width: 50px; background-color: #ffffff; border-radius: 4px; text-align: center; }
    .template-countdown .template-countdown-name span { margin-right: 8px; font-size: 22px; }

    /*固定组件--公共*/
    /*.temp-item-nodata { !*line-height: 300px; text-align: center;*! font-size: 28px; color: #D5DEF0; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); }*/
    .temp-item-nodata { width: 100%; height: 100%; padding: 10% 0; font-size: 28px; color: #D5DEF0; }
    .temp-item-nodata p { line-height: 60px; text-align: center; }
    /*固定组件--竞拍*/

    /*拖拽占位*/
    .temp-item-drag { width: 100%; height: 200px; border: 2px dashed #409EFF; }

    /*字体编辑*/
    .temp-font { position: relative; cursor: pointer; }
    .temp-font::before { content: ''; width: 100%; height: 30px; background-color: transparent; position: absolute; left: 0; top: -30px; z-index: 9; }
    .temp-font-edit {  }
    .temp-font:hover .temp-font-edit .font-edit-one { display: flex; }
    .temp-font:hover .temp-font-edit .font-edit-only,
    .temp-font:hover .temp-font-edit .font-edit-two { display: flex; flex-direction: column; align-items: center; }
    .temp-font:hover .temp-font-edit .font-edit-three { display: block; }
    .font-edit-two { width: 148px; }
    .font-edit-two .font-edit-item { margin: 8px 0; }
    .font-edit-two .font-edit-item p { margin-right: 20px; font-size: 14px; color: #777777; }
    .font-edit-two .font-edit-item .temp-item-color,
    .font-edit-two .font-edit-item .temp-item-color h1 { width: 20px; }
    .font-edit-two .font-edit-item .temp-back-box { width: 20px; height: 20px; position: relative; }
    .font-edit-two .font-edit-item .temp-back-box .el-color-picker { width: 100%; height: 100%; border: 1px solid #cccccc; position: absolute; left: 0; top: 0; }
    .font-edit-two .font-edit-item .temp-item-opacity { width: 50px; height: 30px; margin-left: 24px; border: 1px solid #CCCCCC; border-radius: 4px; overflow: hidden; }
    .font-edit-two .font-edit-item .temp-item-opacity input { width: 100%; height: 100%; line-height: 28px; text-align: center; border: none; outline: none; padding: 5px; font-size: 12px; color: #08090B; }

    .font-edit-three { width: 260px; }
    .font-edit-three .font-edit-item:nth-of-type(2) { margin-top: 20px; }
    .font-edit-three .font-edit-item .temp-item-color,
    .font-edit-three .font-edit-item .temp-item-back { width: 80px; height: auto; }
    .font-edit-three .font-edit-item .temp-item-color h1::before { right: inherit; left: 20px; }
    .font-edit-three .font-edit-item .temp-item-color p { margin: 0; border: 1px solid #cccccc; }
    .font-edit-three .font-edit-item .temp-item-warn { margin-top: 10px; font-size: 12px; }

    /*音视频占位设置*/
    .temp-media-warn { display: flex; width: 100%; height: 100%; margin: 0; align-items: center; justify-content: center; font-size: 16px; color: #cccccc; }
    .temp-media-warn p { line-height: 1.2; margin-top: 0 !important; text-align: center; }

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
    .warning-text { line-height: 2; font-size: 12px; color: #999999; }
    .warning-nodata { font-size: 12px; color: #999999; margin: 20px 0; }

    .chooses-list li { height: 42px; padding: 10px 18px; margin-right: 20px; margin-bottom: 10px; border: 1px solid #D6EAFF; background-color: #F3F9FF; border-radius: 4px; font-size: 14px; color: #409EFF; box-sizing: border-box; }
    .chooses-list li label { margin-right: 10px; }

    /*.set-dialog .drswer-setting .el-form-item { margin-bottom: 22px; }*/

    /*优惠券*/
    .temp-item-coupon { min-height: auto !important; }
    .temp-item-coupon .list-item { width: 862px; height: 196px; padding: 46px 44px; margin: 25px auto auto; background-color: #ccc; background-size: 100% 100% !important; border-radius: 20px; box-sizing: border-box; position: relative; }
    .temp-item-coupon .list-item.used::before { content: ''; width: 80px; height: 80px; background: url("https://cdn.toodudu.com/uploads/2023/09/11/used.png") left top no-repeat; background-size: 114px 100px; border-radius: 0 0 30px 0; position: absolute; right: 16px; bottom: 18px; z-index: 1; }
    .temp-item-coupon .list-item.over::before { content: ''; width: 80px; height: 80px; background: url("https://cdn.toodudu.com/uploads/2023/09/11/over.png") left top no-repeat; background-size: 114px 100px; border-radius: 0 0 30px 0; position: absolute; right: 16px; bottom: 18px; z-index: 1; }
    .temp-item-coupon .list-item:first-child { margin-top: 0; }
    .temp-item-coupon .list-item:last-child { margin-bottom: 0; }
    .temp-item-coupon .list-item .list-info { width: 344px; padding: 0 30px; /*margin-left: 42px;*/ box-sizing: border-box; }
    .temp-item-coupon .list-item .list-price-font { width: 274px; }
    .temp-item-coupon .list-item .list-name { font-size: 40px; font-weight: bold; }
    .temp-item-coupon .list-item .list-desc { margin: 26px 0; font-size: 20px; }
    .temp-item-coupon .list-item .list-warn { font-size: 16px; }
    .temp-item-coupon .list-item .list-btn { width: 160px; height: 60px; line-height: 60px; text-align: center; border-radius: 30px; font-size: 28px; }
    .temp-item-coupon .list-item.used .list-btn { background-color: rgba(255, 255, 255, 0.3) !important; color: #ffffff !important; border-radius: 30px; }


    .temp-item-coupon .coupon-list.two,
    .temp-item-coupon .coupon-list.three,
    .temp-item-coupon .coupon-list.four { margin-left: -20px; }
    .temp-item-coupon .coupon-list.five { margin-left: -42px; }
    .temp-item-coupon .coupon-list.two .list-item,
    .temp-item-coupon .coupon-list.three .list-item,
    .temp-item-coupon .coupon-list.four .list-item {  margin: 25px 0 0 20px; }
    .temp-item-coupon .coupon-list.five .list-item {  margin: 25px 0 0 42px; }

    .temp-item-coupon .coupon-list.two .list-name-font .temp-item-text__editor,
    .temp-item-coupon .coupon-list.three .list-name-font .temp-item-text__editor,
    .temp-item-coupon .coupon-list.four .list-name-font .temp-item-text__editor,
    .temp-item-coupon .coupon-list.five .list-name-font .temp-item-text__editor { top: -64px; }
    .temp-item-coupon .coupon-list.two .list-warn-font .temp-item-text__editor,
    .temp-item-coupon .coupon-list.three .list-warn-font .temp-item-text__editor,
    .temp-item-coupon .coupon-list.four .list-warn-font .temp-item-text__editor,
    .temp-item-coupon .coupon-list.five .list-warn-font .temp-item-text__editor { top: -56px; }
    .temp-item-coupon .coupon-list.two .temp-font::before,
    .temp-item-coupon .coupon-list.three .temp-font::before,
    .temp-item-coupon .coupon-list.four .temp-font::before,
    .temp-item-coupon .coupon-list.five .temp-font::before { height: 24px; top: -10px; }

    .temp-item-coupon .coupon-list.one .list-info { margin-left: 280px; }
    .temp-item-coupon .coupon-list.one .list-desc { margin: 12px 0; }

    .temp-item-coupon .coupon-list.two .list-item { width: 582px; height: 220px; padding: 10px; }
    .temp-item-coupon .coupon-list.two .list-info { width: 329px; padding: 30px 10px 30px 18px; margin: 0 6px 0 0; }
    .temp-item-coupon .coupon-list.two .list-price-font { width: 210px; }
    .temp-item-coupon .coupon-list.two .list-name p { font-size: 26px; font-weight: bold; }
    .temp-item-coupon .coupon-list.two .list-name p strong { font-size: 46px; font-weight: bold; }
    .temp-item-coupon .coupon-list.two .list-desc { padding: 22px 0; margin: 0 0 16px 0; border-bottom: 1px dashed #BB8056; font-size: 18px; }
    .temp-item-coupon .coupon-list.two .list-warn { font-size: 16px; }
    .temp-item-coupon .coupon-list.two .list-btn { width: 160px; height: 50px; line-height: 50px; margin: 20px auto auto; }

    .temp-item-coupon .coupon-list.three .list-item { width: 382px; height: 176px; padding: 12px 10px 12px 18px; }
    .temp-item-coupon .coupon-list.three .list-item.used::before { content: ''; width: 66px; height: 64px; background-size: 78px 70px; border-radius: 0 0 30px 0; right: 0; bottom: 0; }
    .temp-item-coupon .coupon-list.three .list-info { width: 50%; padding: 0 10px; margin: 0; }
    .temp-item-coupon .coupon-list.three .list-price { width: 50%; }
    .temp-item-coupon .coupon-list.three .list-price-font { width: 160px; }
    .temp-item-coupon .coupon-list.three .list-name p { font-size: 18px; font-weight: bold; }
    .temp-item-coupon .coupon-list.three .list-name p strong { font-size: 30px; font-weight: bold; }
    .temp-item-coupon .coupon-list.three .list-desc { padding: 18px 0; margin: 0; font-size: 14px; }
    .temp-item-coupon .coupon-list.three .list-warn { padding: 18px 0; font-size: 16px; }
    .temp-item-coupon .coupon-list.three .list-btn { width: 120px; height: 40px; line-height: 40px; margin: 0 auto; font-size: 20px; }

    .temp-item-coupon .coupon-list.four .list-item { width: 281px; height: 130px; padding: 12px 10px 12px 18px; }
    .temp-item-coupon .coupon-list.four .list-item.used::before,
    .temp-item-coupon .coupon-list.five .list-item.used::before { content: ''; width: 52px; height: 46px; background-size: 56px 50px; border-radius: 0 0 30px 0; right: 0; bottom: 0; }
    .temp-item-coupon .coupon-list.four .list-info { width: 50%; padding: 0 10px; margin: 0; }
    .temp-item-coupon .coupon-list.four .list-price { width: 50%; }
    .temp-item-coupon .coupon-list.four .list-price-font { width: 110px; }
    .temp-item-coupon .coupon-list.four .list-name p { font-size: 12px; font-weight: bold; }
    .temp-item-coupon .coupon-list.four .list-name p strong { font-size: 22px; font-weight: bold; }
    .temp-item-coupon .coupon-list.four .list-desc { padding: 18px 0; margin: 0; font-size: 14px; }
    .temp-item-coupon .coupon-list.four .list-warn { padding: 12px 0; font-size: 12px; }
    .temp-item-coupon .coupon-list.four .list-btn { width: 82px; height: 26px; line-height: 26px; margin: 0 auto; font-size: 14px; }

    .temp-item-coupon .coupon-list.five .list-item { width: 203px; height: 220px; padding: 12px 10px; }
    .temp-item-coupon .coupon-list.five .list-info { padding: 0 10px; margin: 0; }
    .temp-item-coupon .coupon-list.five .list-price { width: 85%; margin-top: 8px; }
    .temp-item-coupon .coupon-list.five .list-name { margin-top: 6px; font-size: 18px; font-weight: bold; }
    .temp-item-coupon .coupon-list.five .list-desc { margin: 10px 0 0; font-size: 14px; }
    .temp-item-coupon .coupon-list.five .list-warn { margin-top: 15px; font-size: 14px; }
    .temp-item-coupon .coupon-list.five .list-btn-font { margin-top: 30px; }
    .temp-item-coupon .coupon-list.five .list-btn { width: 130px; height: 34px; line-height: 34px; margin: 0 auto; font-size: 22px; }

    .temp-item-coupon .coupon-list.five .list-name,
    .temp-item-coupon .coupon-list.five .list-desc,
    .temp-item-coupon .coupon-list.five .list-warn { width: 172px; text-align: center; }

    /*红包*/
    .temp-item-redpack .list-item  { padding: 46px 24px; }
    .temp-item-redpack .list-item .list-price-font { width: 278px; }
    .temp-item-redpack .list-item .list-btn { margin-left: 20px; }

    .temp-item-redpack .coupon-list.two .list-info { width: 290px; padding: 30px 10px 30px 24px; margin: 0 48px 0 0; }

    .temp-item-redpack .coupon-list.three .list-price-font { width: 150px; }

    .temp-item-redpack .coupon-list.five .list-btn { width: 100px; height: 30px; line-height: 30px; font-size: 14px; }

    .drawer-radio .el-form-item__content,
    .drawer-radio .el-form-item__content { height: 80px; }
    .drawer-radio .el-radio,
    .drawer-radio .el-radio { line-height: 40px; margin-left: 0; margin-right: 30px; }

    /*公共添加按钮*/
    .drawer-btn { height: 40px; padding: 0 20px; background-color: #F3F9FF; border: 1px solid #D6EAFF; font-size: 14px; color: #409EFF; border-radius: 4px; cursor: pointer; }
    .drawer-btn em { height: 14px; margin-right: 10px; }
    .drawer-btn.disabled { background-color: #F8F8F8; border-color: #EEEEEE; color: #CCCCCC; cursor: not-allowed; }
    .drawer-btn.disabled label { cursor: not-allowed; }
    .drawer-btn.primary { padding: 0 14px; border-color: #409EFF; background-color: #409EFF; color: #ffffff; }
    .drawer-btn.primary.disabled { border-color: #EEEEEE; background-color: #F8F8F8; color: #CCCCCC; }
    .drawer-btn.small { height: 30px; padding: 0 10px; }

    .input-none-border input,
    .invite-drag-perview .el-form-item,
    .invite-drag-perview .el-form-item .el-form-item__content { width: 100%; height: auto; line-height: normal; }
    .invite-drag-perview .el-form-item { margin-bottom: 16px; transition: all 0.3s; }
    .template-content-box .el-form-item .el-form-item { margin-bottom: 22px; transition: all 0.3s; }
    .invite-drag-perview .el-form-item.is-error,
    .template-content-box .el-form-item .el-form-item.is-error { margin-bottom: 22px; }
    .invite-drag-perview .el-form-item .el-form-item__error { padding-top: 0; transition: all 0.3s; }
    .invite-drag-perview .el-form-item.is-error .el-form-item__error { padding-top: 4px; }
    .invite-drag-perview .el-form-item.none-margin .el-form-item__content { margin-left: 0 !important; }
    .input-none-border input { padding: 0; border: none; font-size: 18px; }
    .input-none-border.input-sub input { font-size: 14px; }
    .input-disabled input,
    .input-disabled textarea { background-color: #ffffff !important; cursor: text !important; }
    .input-center input { text-align: center; }
    .input-form-title input { font-size: 24px; }
    .input-form-desc input { font-size: 14px; font-weight: normal; }
    .invite-drag-perview textarea { height: 150px !important; }
    .input-error-margin .el-form-item__error { margin-left: 52px !important; }
    .submit-form .input-error-margin .el-form-item__error { margin-left: 64px; }

    /*.invite-form-parent { width: 44%; min-width: 600px; margin: 20px 0 0 0; }*/
    .invite-form-parent, .invite-form-view { width: 780px; flex-shrink: 0; }
    .invite-form-box { background-color: #ffffff; border-radius: 10px;height: 100%;box-sizing: border-box; }
    .invite-form-title{font-weight: bold;color: #3D3D3D;font-size: 24px;padding-top: 20px;text-align: center}
    .invite-page-set { padding: 0 30px 20px;  background-color: #ffffff; box-sizing: border-box;height: 100%;border-radius: 10px 10px 0 0; }

    .invite-form { padding: 20px; position: relative; }
    .invite-form .public-handle-drag { content: ''; width: 100%; height: 20px; cursor: move; position: absolute; left: 0; top: 0; }
    .invite-form-setting { line-height: 40px; padding: 0 0 0 40px; border-top: 1px solid #e5e5e6; }
    .invite-form-setting label { margin-right: 20px; font-size: 12px; }
    .invite-form-setting label em { margin-right: 10px; font-size: 14px; }
    .invite-form-setting input { border: none; }
    .invite-form-setting .el-input__suffix i { color: #333333 !important; }
    .invite-form-setting input::-webkit-input-placeholder { /* WebKit browsers */
        color: #333333;
    }
    .invite-form-setting input:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
        color: #333333;
    }
    .invite-form-setting input::-moz-placeholder { /* Mozilla Firefox 19+ */
        color: #333333;
    }
    .invite-form-setting input:-ms-input-placeholder { /* Internet Explorer 10+ */
        color: #333333;
    }
    .invite-setting-item * { user-select: none; }
    .invite-setting-item em { margin-right: 5px; }
    .invite-setting-item:hover { color: #278ff0; }

    .invite-required { margin-right: 4px; color: #F56C6C; }
    .invite-title { width: 100%; }
    .invite-label { width: 52px; line-height: 28px; font-size: 20px; font-weight: 500; }
    .invite-comment { line-height: 1; font-size: 18px; color: #606266; }
    .invite-delete { height: 42px; line-height: 42px; color: #f71111; }
    .invite-delete em { margin-right: 6px; }
    .invite-add { margin-left: 52px; color: #278ff0; }
    .invite-add em { margin-right: 10px; }
    .invite-remove { color: #f71111; }
    .invite-upload-file { width: 74px; height: 74px; margin-right: 10px; border: 1px dashed #e5e5e6; border-radius: 6px; }
    .invite-upload-file em { font-size: 28px; font-weight: bold; }
    .invite-upload-file img { max-width: 100%; max-height: 100%; }
    .invite-form:hover .public-handle-drag { display: block; }
    .public-handle-drag { display: none; font-size: 20px; font-weight: bold; color: #999999; cursor: move; position: absolute; left: 10px; top: 30px; z-index: 9; }

    .invite-add-temp { width: 100%; height: 78px; margin: 20px auto; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; }
    .invite-add-temp p { font-size: 14px; color: #409EFF; }
    .invite-add-temp:hover { border-color: #278ff0; }
    .invite-drag-footer { width: 780px; background-color: #ffffff; border-radius: 0 0 10px 10px; box-sizing: border-box; position: fixed; bottom: 0; left: 540px; z-index: 9; }
    .invite-drag-footer .footer-btn { width: 100%; height: 80px; line-height: 80px; text-align: center; margin: 0 auto; border-radius: 10px; background-color: #EA0000; font-size: 24px; color: #ffffff; }


    /*图标样式*/
    em.iconfont[data-type="text"] { background: -webkit-linear-gradient(#80B7FB, #0B6CFC); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    em.iconfont[data-type="textarea"] { background: -webkit-linear-gradient(#FFCE7D, #FF852B); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    em.iconfont[data-type="radio"] { background: -webkit-linear-gradient(#84E1C3, #19BF87); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    em.iconfont[data-type="checkbox"] { background: -webkit-linear-gradient(#FFB9A5, #FF2D25); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    em.iconfont[data-type="select"] { background: -webkit-linear-gradient(#CBC6FA, #5721FC); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    em.iconfont[data-type="date"] { background: -webkit-linear-gradient(#89B8FF, #1673FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    em.iconfont[data-type="file"] { background: -webkit-linear-gradient(#91C2F7, #1988FE); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    em.iconfont[data-type="more_file"] { background: -webkit-linear-gradient(#FFDCA4, #FFB12B); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    em.iconfont[data-type="address"] { background: -webkit-linear-gradient(#8FF7FD, #1589FF); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

</style>
