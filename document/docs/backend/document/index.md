# 项目文档

### 1. 项目说明

存放目录 `document`, 使用的是[Rspress](https://rspress.dev/zh/index) 进行构建的

文档目录 `document/docs`  用于文档的书写

主题目录 `document/theme` 用于设置页面的样式


### 2. 文档说明

文档书写在`document/docs`

* `index.md` 用于定义首页内容
* `docs/_meta.json` 用于定义头部导航栏
* `docs/api/` 用于定义api接口文档相关的
* `project` 用于说明项目的文档


### 3. 项目运行

```sh
cd document
npm install
npm run dev
```

### 4. 项目构建

```sh
npm run build
```
