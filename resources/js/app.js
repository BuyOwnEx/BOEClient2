import './bootstrap';
import '../css/app.css';
// STYLES Именно так и никак иначе, поскольку только variables должны быть в секции css.preprocessorOptions.sass.additionalData
import 'vuetify/styles';
import '../sass/vuetify/overrides.scss'
import '../sass/app.scss'
import '../scss/vuetify/settings.scss'
import '../sass/helpers.scss'

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createVuetify } from 'vuetify';
import { aliases, mdi } from 'vuetify/lib/iconsets/mdi-svg';
import i18n, { adapter } from "@/Translations/i18n";
import link from "@/Plugins/link";


const vuetify = createVuetify({
    defaults: {
        global: {
            dense: true,
            ripple: false
        },
        VField: {
            VIcon: {
                color: '#868a8c',
                style: 'opacity: 1',
            }
        },
        VCheckbox: {
            color: 'primary',
            density: 'compact'
        }
    },
    theme: {
        defaultTheme: localStorage.getItem('theme') ?? 'dark',
        themes: {
            dark: {
                dark: true,
                colors: {
                    background: '#05090c',
                    surface: '#1e1e1e',
                    "on-surface": '#edf0f2',
                    primary: '#03a4c2',
                    "on-primary": '#edf0f2',
                    secondary: '#829099',
                    accent: '#82B1FF',
                    error: '#FF5252',
                    info: '#2196F3',
                    success: '#4CAF50',
                    warning: '#FFC107',
                    anchor: '#8c9eff'
                },
            },
            light: {
                dark: false,
                colors: {
                    background: '#ececec',
                    surface: '#f9f9f9',
                    "on-surface": '#000000',
                    primary: '#0096c7',
                    secondary: '#a0b9c8',
                    accent: '#048ba8',
                    error: '#ef476f',
                    info: '#2196F3',
                    success: '#06d6a0',
                    warning: '#ffd166',
                },
            },
        },
    },
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi,
        }
    },
    locale: {
        adapter: adapter,
    },
})

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .use(vuetify)
            .use(ZiggyVue)
            .use(link)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
