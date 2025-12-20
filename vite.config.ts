import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';
import { run } from 'vite-plugin-run'

export default defineConfig(({ mode }) => {
    const isProduction = mode === 'production';

    return {
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
            // Use correct command and only run in development to avoid production hangs
            !isProduction && run([
                {
                    name: 'wayfinder',
                    run: ['php', 'artisan', 'wayfinder:generate', '--no-interaction'],
                    pattern: ['routes/**/*.php'],
                }
            ]),
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
            ViteImageOptimizer({
                test: /\.(jpe?g|png|gif|tiff|webp|svg|avif)$/i,
                exclude: undefined,
                include: undefined,
                includePublic: true,
                logStats: true,
                svg: {
                    multipass: true,
                    plugins: [
                        {
                            name: 'preset-default',
                            params: {
                                overrides: {
                                    cleanupNumericValues: false,
                                    removeViewBox: false,
                                },
                            },
                        },
                        'sortAttrs',
                        {
                            name: 'addAttributesToSVGElement',
                            params: {
                                attributes: [{ xmlns: 'http://www.w3.org/2000/svg' }],
                            },
                        },
                    ],
                },
                png: { quality: 80 },
                jpeg: { quality: 75 },
                jpg: { quality: 75 },
                webp: { lossy: true, quality: 75 },
            }),
        ].filter(Boolean),
    };
});
