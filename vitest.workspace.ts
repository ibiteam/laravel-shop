import { defineWorkspace } from 'vitest/config'
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineWorkspace([
    {
        plugins: [vue()],
        test: {
            include: ['resources/js/test/unit/**/*.test.{ts,js}'],
            name: 'unit',
            environment: 'node',
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js/'), // Maps @ to the src directory
            },
        },
    },
    {
        plugins: [vue()],
        test: {
            include: [
                'resources/js/test/browser/**/*.test.{ts,js}',
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
                '@': path.resolve(__dirname, './resources/js/'), // Maps @ to the src directory
            },
        },
    },
])
