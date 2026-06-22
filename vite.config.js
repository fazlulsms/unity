import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                // Stable CSS filename — prevents hash changes breaking production deploys
                assetFileNames: 'assets/[name][extname]',
            },
        },
    },
});
