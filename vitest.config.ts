// vitest.config.ts
import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [vue()],
    test: {
        environment: 'node',
        include: ['resources/manage/test/**/*.test.{ts,js}'],
        coverage: {
            provider: 'v8',
            reporter: ['text', 'json', 'html'],
            reportsDirectory: './public/build/coverage',
            exclude: [
                'vendor/**',
                'document/**',
                'public/**',
                './*.ts',
                './*.js',
            ],
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/manage/'), // Maps @ to the src directory

        },
    },
});
