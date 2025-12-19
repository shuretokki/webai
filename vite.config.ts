import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import viteImagemin from 'vite-plugin-imagemin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';
import VueDevTools from 'vite-plugin-vue-devtools';
import { run } from 'vite-plugin-run'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        run([
            {
                name: 'wayfinder',
                run: ['php', 'artisan', 'wayfinder'],
                pattern: ['routes/**/*.php'],
            }
        ]),
        VueDevTools(),
        AutoImport({
            imports: [
                'vue',
                '@vueuse/core',
                {
                    '@inertiajs/vue3': ['useForm', 'usePage', 'router'],
                    'laravel-precognition-vue': ['useForm as usePrecognitionForm'],
                }
            ],
            dts: 'resources/js/types/auto-imports.d.ts',
            vueTemplate: true,
        }),
        Components({
            resolvers: [
                (name) => {
                    if (name === 'Motion') {
                        return { name, from: 'motion-v' }
                    }
                }
            ],
            dts: 'resources/js/types/components.d.ts',
        }),
        viteImagemin({
            gifsicle: {
                optimizationLevel: 7,
                interlaced: false,
            },
            optipng: {
                optimizationLevel: 7,
            },
            mozjpeg: {
                quality: 20,
            },
            pngquant: {
                quality: [0.8, 0.9],
                speed: 4,
            },
            svgo: {
                plugins: [
                    {
                        name: 'removeViewBox',
                    },
                    {
                        name: 'removeEmptyAttrs',
                        active: false,
                    },
                ],
            },
        }),
    ],
});
