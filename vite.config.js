import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify';
import VueI18nPlugin from '@intlify/unplugin-vue-i18n/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: { transformAssetUrls },
        }),
        vuetify({
            autoImport: true,
            styles: { configFile: 'resources/scss/vuetify/settings.scss' },
        }),
        VueI18nPlugin({
            include: 'resources/js/Translations/*'
        }),
    ],
});
