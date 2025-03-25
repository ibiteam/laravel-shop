import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import path from 'path'

export default defineConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js/')
        }
    },
    server: {
        cors: true, // Enable CORS
    },
    plugins: [
        vue(),   // 支持 Vue 文件
        laravel({
            input: ['resources/js/app.js'], // 入口文件
            refresh: true,                  // 启用热更新
        }),
    ],
});
