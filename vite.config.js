import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { quasar } from '@quasar/vite-plugin'
import i18n from 'laravel-vue-i18n/vite'
import basicSsl from '@vitejs/plugin-basic-ssl'

export default defineConfig({
    server: {
        host: 'localhost',
    },
    plugins: [
        basicSsl(),
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        i18n(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        quasar({
            sassVariables: 'resources/css/quasar-variables.sass'
        }),
    ],
});
