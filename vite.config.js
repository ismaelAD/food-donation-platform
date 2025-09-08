import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    define: {
        global: 'globalThis',
    },
    base: '/', // <-- Ajouté pour servir tous les assets avec un chemin relatif
    build: {
        outDir: 'public/build',
        emptyOutDir: true, // vide le dossier build avant chaque build pour éviter les fichiers obsolètes
    }
});
