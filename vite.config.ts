import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import AutoImport from 'unplugin-auto-import/vite';
import IconsResolver from 'unplugin-icons/resolver';
import Icons from 'unplugin-icons/vite';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';
import VueDevTools from 'vite-plugin-vue-devtools';

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
                IconsResolver({
                    prefix: 'i',
                    enabledCollections: ['solar'],
                }),
            ],
            dts: 'resources/js/types/components.d.ts',
        }),
        Icons({
            autoInstall: true,
        }),
    ],
});
