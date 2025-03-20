import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'], // 入口文件
            refresh: true,                  // 启用热更新
        }),
        vue(),                             // 支持 Vue 文件
    ],
});
