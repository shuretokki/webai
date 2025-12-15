import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import AutoImport from 'unplugin-auto-import/vite';
import path from 'path';

export default defineConfig({
    plugins: [
        vue(),
        AutoImport({
            imports: [
                'vue',
                '@vueuse/core',
            ],
            dts: false, // No need to generate dts in test
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    test: {
        globals: true,
        environment: 'jsdom',
        include: ['tests/js/**/*.{test,spec}.{js,mjs,cjs,ts,mts,cts,jsx,tsx}'],
    },
});
