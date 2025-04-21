# 项目文档

### 1.开发说明

#### 项目架构

使用vue3组合式写法进行开发 [https://cn.vuejs.org/](https://cn.vuejs.org/)

状态管理使用pinia [https://pinia.vuejs.org/](https://pinia.vuejs.org/)

UI库采用vant4版本 [https://vant-ui.github.io/vant/#/zh-CN/home](https://vant-ui.github.io/vant/#/zh-CN/home)

#### 命名规范

##### 组件/页面 一律使用大驼峰命名 如：Home.vue

##### class命名使用小写字母和“-”中划线命名  如：
```
<div class="home-bannner"></div>
```

#### 页面颜色/字体(所有常规色值需要这样写样式，特殊色值除外)
```
:root {
  --main-color: #278ff0; // 主色
  --page-bg-color: #f2f2f2; // 空白主背景色
  --red-color: #f71111; // 红色主色
  --color-text: #333; // 常规字体颜色
  --color-text-desc: #999; // 描述性字体颜色
  --base-font-size: 0.24rem; // 基本字体大小，本项目以750设计图分辨率
}

// 如：
.home-banner{
	color:var(--color-text);
	background:var(--page-bg-color);
}
.home-banner .btn{
	color:var(--main-color);
}
```
