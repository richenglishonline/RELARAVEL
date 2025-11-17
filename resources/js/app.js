// resources/js/app.js
import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { Ziggy } from "./ziggy";
import { route } from "ziggy-js";
import AOS from "aos";
import "aos/dist/aos.css";
import { QueryClient, VueQueryPlugin } from "@tanstack/vue-query";

import Swal from "sweetalert2";

const appName = import.meta.env.VITE_APP_NAME || "Rich English Online";

const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            refetchOnWindowFocus: false,
            retry: 1,
            staleTime: 5 * 60 * 1000,
            cacheTime: 10 * 60 * 1000,
        },
        mutations: {
            onError: (error) => {
                console.error("Mutation error:", error);
            },
        },
    },
});

if (typeof window !== "undefined") {
    window.Ziggy = Ziggy;
    window.Ziggy.url = "";
    window.Ziggy.absolute = false;
}

window.route = (name, params) => {
    return route(name, params, false, window.Ziggy);
};

createInertiaApp({
    title: (title) => `${title} ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.config.globalProperties.route = route;

        AOS.init({
            duration: 800,
            easing: "ease-in-out",
            once: true,
            mirror: false,
            offset: 120,
            delay: 0,
        });

        // Apply Inertia plugin first
        app.use(plugin);

        // Then Vue Query plugin
        app.use(VueQueryPlugin, { queryClient });

        return app.mount(el);
    },

    progress: {
        color: "#4B5563",
        showSpinner: true,
    },
});
