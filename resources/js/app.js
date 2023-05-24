import './bootstrap';
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/src/css/index.sass';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import {Dialog, Quasar} from 'quasar';
import route from "ziggy-js/src/js/index.js";
import {i18nVue} from "laravel-vue-i18n";

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Quasar, {
                plugins: {
                    Dialog,
                },
                config: {
                    dark: true,
                }
            })
            .use(i18nVue, {
                resolve: async lang => {
                    const languages = import.meta.glob('../../lang/*.json');
                    return await languages[`../../lang/${lang}.json`]();
                }
            })
        app.config.globalProperties.$route = route;

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
