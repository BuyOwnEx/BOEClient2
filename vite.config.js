import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify';

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
            styles: {
                configFile: 'resources/sass/vuetify/settings.scss', // не трогать! отсюда изначально загружаются все css variables для vuetify
            },
        }),
    ],
    css: {
        preprocessorOptions: {
            sass: {
                additionalData: [
                    // Make the variables defined in these files available to all components, without requiring an explicit
                    // @import of the files themselves
                    //'@import "./resources/sass/theme.scss"',
                    // '@import "./resources/sass/highcharts"',
                    '', // end with newline
                ].join('\n'),
            },
        },
    },
});
