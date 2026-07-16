import './bootstrap'; // Ini bawaan Laravel (berisi axios)
import '../css/app.scss'; // Import global SCSS

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
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
            .use(vuetify); // Gunakan Vuetify
        
        registerGlobalUsage(app);
        
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