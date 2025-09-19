import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    // 🔥 FIX URL: pulizia da caratteri malformati (\x3a, \) letti da Vite
    let appUrl = env.APP_URL || 'http://localhost';
    appUrl = appUrl.replace(/\\x3a/g, ':').replace(/\\/g, '/');

    // 🐳 Docker configuration detection
    const isDocker = process.env.DOCKER_ENV === 'true' || env.DOCKER_ENV === 'true';
    const viteHost = isDocker ? '0.0.0.0' : 'localhost';
    const vitePort = parseInt(env.VITE_PORT || '5173');

    return {
        plugins: [
            laravel({
                input: [
                    // 🚀 PERFORMANCE CRITICAL - Load first for fast navbar
                    'resources/css/critical-navbar.css',
                    'resources/css/performance.css',
                    'resources/js/navbar-performance.js',
                    'resources/js/lazy-loader.js',
                    // Regular assets
                    'resources/ts/main.ts', // Corretto da main.js a main.ts
                    'resources/ts/main_app.ts',
                    'resources/js/guest.js',
                    'resources/js/polyfills.js',
                    'resources/js/logo3d.js',
                    'resources/css/app.css',
                    'resources/css/guest.css',
                    'resources/css/gdpr.css',
                    'resources/css/home-nft.css',
                    'resources/css/create-collection-modal-context.css',
                    'resources/css/creator-home.css',
                    'resources/css/reservation-history.css', // Stili per cronologia prenotazioni
                    'resources/css/traits-manager.css', // Stili per gestione traits
                    'resources/css/trait-detail-modal.css', // Stili per modal dettaglio trait
                    'resources/css/mega-menu.css', // Revolutionary mega menu styles
                    'resources/js/creator-home.js',
                    'resources/js/mega-menu-mobile.js', // Mobile mega menu functionality
                    'resources/css/collections-show.css',
                    'resources/js/app.js',
                    'resources/js/collection.js',
                    'resources/js/collection-carousel.js', // Carousel helper
                    'resources/js/biography-edit.js',
                    'resources/js/reservation-history.js', // Sistema cronologia prenotazioni
                    'resources/js/collections-show.js',
                    'resources/js/home-nft.js',
                    'resources/js/components/create-collection-modal.js',
                    // 🎯 USER DOMAINS - Personal Data
                    'resources/css/personal-data.css',
                    'resources/ts/domain/personal-data.ts',
                    // Modal Fix CSS
                    'resources/css/modal-fix.css',
                    // Ultra Upload Manager
                    'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts',
                    'vendor/ultra/ultra-upload-manager/resources/css/app.css',
                    'resources/js/components/vanilla-mobile-menu.js',
                    'resources/js/components/vanilla-desktop-menu.js',
                    'resources/js/collection-edit-modal.js', // Modal per editing collection metadata
                    'resources/js/coa/vocabulary-modal.js', // Vocabulary traits modal for CoA certificates
                    'resources/js/traits-viewer-integrated.js', // Integrated traits viewer & image manager
                    'public/js/collection-edit-modal.js',
                    // Notifications system
                    'resources/js/modules/notifications/responses/notification.js'
                ],
                refresh: [
                    'resources/**',
                    'routes/**',
                    'app/**',
                    'packages/ultra/egi-module/resources/**',
                    'packages/ultra/egi-module/routes/**',
                    'packages/ultra/egi-module/src/**',
                ],
            }),
        ],
        define: {
            // 🔑 Qui passiamo l'URL pulito a tutto il codice JS
            'process.env.APP_URL': JSON.stringify(appUrl),
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
                '@ts': path.resolve(__dirname, './resources/ts'),
                '@domains': path.resolve(__dirname, './resources/js/domains'),
                '@ultra-images': path.resolve(__dirname, './vendor/ultra/ultra-upload-manager/resources/ts/assets/images'),
            },
            preserveSymlinks: true,
        },
        server: {
            host: viteHost, // 🐳 0.0.0.0 for Docker, localhost for local
            port: vitePort,
            hmr: {
                host: 'localhost', // 🐳 Always localhost for HMR from browser perspective
                port: vitePort,
                overlay: true,
            },
            watch: {
                usePolling: true,
                interval: 1000,
                ignored: [
                    '**/node_modules/**',
                    '**/.git/**',
                    '**/.venv/**',
                    '**/storage/**',
                ]
            },
            fs: {
                allow: [
                    '.',
                    path.resolve(__dirname, './packages/ultra'),
                    path.resolve(__dirname, './vendor/ultra'),
                ],
            },
        },
        build: {
            outDir: 'public/build',
            manifest: 'manifest.json', // Genera il manifest direttamente in public/build/
            sourcemap: true,
            cssCodeSplit: true,
            chunkSizeWarningLimit: 1000,
            rollupOptions: {
                output: {
                    entryFileNames: `assets/[name]-[hash].js`,
                    chunkFileNames: `assets/[name]-[hash].js`,
                    assetFileNames: `assets/[name]-[hash].[ext]`,
                }
            }
        },
        optimizeDeps: {
            include: ['tailwindcss', 'daisyui', 'three', 'three/examples/jsm/controls/OrbitControls.js'],
        }
    };
});
