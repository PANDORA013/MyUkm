import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/profile.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: 'manifest.json',
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                profile: 'resources/js/profile.js'
            },
            external: ['axios'],
        },
    },
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            '~': path.resolve('resources'),
            'pusher-js': 'pusher-js/with-encryption',
        },
    },
    optimizeDeps: {
        include: ['pusher-js'],
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
