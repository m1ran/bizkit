import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Head } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import AppLayout from './Layouts/AppLayout.vue';

import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'

import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faThList, faExclamationCircle } from '@fortawesome/free-solid-svg-icons';

import NProgress from 'nprogress';

dayjs.extend(utc);
dayjs.extend(timezone);

library.add([faThList, faExclamationCircle]);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : `${appName}`,
    resolve: (name) => {
        return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))
            .then((module) => {
                module.default.layout = module.default.layout || AppLayout;
                return module;
            });
    },
    setup({ el, App, props, plugin }) {
        NProgress.configure({ showSpinner: false });
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('Head', Head)
            .component('FontAwesomeIcon', FontAwesomeIcon);
        vueApp.config.globalProperties.$dayjs = dayjs;
        return vueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
        showSpinner: false,
    },
});
