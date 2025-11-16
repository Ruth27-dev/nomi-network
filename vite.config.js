import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/admin/css/app.css',
                'resources/admin/sass/app.scss',
                'resources/admin/js/app.js',
                'resources/admin/js/body.js',
            ],

            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            // Ignore backend PHP changes so they don't trigger a reload
            ignored: [
                "**/app/**/*.php",
                "**/routes/**/*.php",
                "**/config/**/*.php",
                "**/database/**/*.php",
                "**/bootstrap/**/*.php",
                "**/tests/**/*.php",
                "vendor/**",
                "public/storage/**",
            ],
        },
    },
    css: {
        postCss: {
            plugins: {
                tailwindcss: {},
                autoprefixer: {},
            },
        },
    },
});
