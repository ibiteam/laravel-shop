import { defineWorkspace } from 'vitest/config'
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineWorkspace([
    {
        plugins: [vue()],
        test: {
            include: ['resources/manage/test/unit/**/*.test.{ts,js}'],
            name: 'unit',
            environment: 'node',
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/manage/'), // Maps @ to the src directory
            },
        },
    },
    {
        plugins: [vue()],
        test: {
            include: [
                'resources/manage/test/browser/**/*.test.{ts,js}',
            ],
            name: 'browser',
            browser: {
                enabled: true,
                instances: [
                    { browser: 'chromium' },
                ],
            },
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/manage/'), // Maps @ to the src directory
            },
        },
    },
])
