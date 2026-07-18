import './bootstrap'; // Ini bawaan Laravel (berisi axios)
import '../css/app.scss'; // Import global SCSS

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Setup Vuetify
import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import '@mdi/font/css/materialdesignicons.css';

// Setup Pinia
import { createPinia } from 'pinia';

// Setup Global Usage
import registerGlobalUsage from './useGlobal';

// Setup Icons
import { aliases, mdi } from 'vuetify/iconsets/mdi'

// helpers format
import formatHelpers from './Utils/format';

const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi,
        },
    },
});

const pinia = createPinia();

createInertiaApp({
    // Mengatur direktori halaman ke resources/js/Pages
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin) // Gunakan Inertia
            .use(pinia)  // Gunakan Pinia
            .use(vuetify) // Gunakan Vuetify
            .use(Toast, { // Toastifikasi
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnHover: true
            });
        
        registerGlobalUsage(app);
        

        Object.entries(formatHelpers).forEach(([key, value]) => {
            app.config.globalProperties[`$${key}`] = value;
        });

        app.mount(el);
    },

    progress: {
        // Efek loading bar di atas layar
        delay: 250, // Muncul jika request lebih dari 250ms
        color: '#29d', // Warna biru terang
        includeCSS: true,
        showSpinner: false, // Matikan spinner jika hanya ingin bar
    },
});