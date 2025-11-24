import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import path from "path";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "");

    // 🔥 FIX URL: pulizia da caratteri malformati (\x3a, \) letti da Vite
    let appUrl = env.APP_URL || "http://localhost";
    appUrl = appUrl.replace(/\\x3a/g, ":").replace(/\\/g, "/");

    // 🐳 Docker configuration detection
    const isDocker =
        process.env.DOCKER_ENV === "true" || env.DOCKER_ENV === "true";
    const viteHost = isDocker ? "0.0.0.0" : "localhost";

    // ✅ FIX PERMANENT: EGI usa porta 5174 (NATAN_LOC usa 5173)
    // NEVER use 5173 to avoid conflict and infinite HMR loop
    const vitePort = 5174; // HARDCODED - non usare env.VITE_PORT

    return {
        plugins: [
            react(), // ✨ Plugin React per FlorenceEGI Info Page
            laravel({
                input: [
                    // 🎨 REACT SPA - FlorenceEGI Info Page
                    "resources/react/florenceegi-info/main.tsx",
                    // 🚀 PERFORMANCE CRITICAL - Load first for fast navbar
                    "resources/react/home/home-splash.tsx",
                    "resources/css/critical-navbar.css",
                    "resources/css/performance.css",
                    "resources/js/navbar-performance.js",
                    "resources/js/lazy-loader.js",
                    // Regular assets
                    "resources/ts/main.ts",
                    "resources/ts/main_app.ts",
                    "resources/js/guest.js",
                    "resources/js/polyfills.js",
                    "resources/js/logo3d.js",
                    "resources/css/app.css",
                    "resources/css/guest.css",
                    "resources/css/gdpr.css",
                    "resources/css/home-nft.css",
                    "resources/css/create-collection-modal-context.css",
                    "resources/css/creator-home.css",
                    "resources/css/reservation-history.css",
                    "resources/css/traits-manager.css",
                    "resources/css/trait-detail-modal.css",
                    "resources/css/mega-menu.css",
                    "resources/js/creator-home.js",
                    "resources/js/mega-menu-mobile.js",
                    "resources/css/collections-show.css",
                    "resources/js/app.js",
                    "resources/js/collection.js",
                    "resources/js/collection-carousel.js",
                    "resources/js/biography-edit.js",
                    "resources/js/reservation-history.js",
                    "resources/js/collections-show.js",
                    "resources/js/home-nft.js",
                    "resources/js/components/create-collection-modal.js",
                    "resources/css/personal-data.css",
                    "resources/ts/domain/personal-data.ts",
                    "resources/css/modal-fix.css",
                    "vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts",
                    "vendor/ultra/ultra-upload-manager/resources/css/app.css",
                    "resources/js/components/vanilla-mobile-menu.js",
                    "resources/js/components/vanilla-desktop-menu.js",
                    "resources/js/collection-edit-modal.js",
                    "resources/js/coa/vocabulary-modal.js",
                    "resources/js/traits-viewer-integrated.js",
                    "public/js/collection-edit-modal.js",
                    "resources/js/modules/notifications/responses/notification.js",
                    "resources/js/florence-shader.js",
                ],
                refresh: [
                    "resources/views/**",
                    "resources/js/**",
                    "resources/ts/**",
                    "resources/css/**",
                    "resources/react/**", // ✨ React files refresh
                    "routes/**/*.php",
                    "app/Http/Controllers/**/*.php",
                    "app/Models/**/*.php",
                    "packages/ultra/egi-module/resources/**",
                    "packages/ultra/egi-module/routes/**",
                    "packages/ultra/egi-module/src/**",
                ],
            }),
        ],
        define: {
            "process.env.APP_URL": JSON.stringify(appUrl),
        },
        resolve: {
            alias: {
                "@": path.resolve(__dirname, "./resources/js"),
                "@ts": path.resolve(__dirname, "./resources/ts"),
                "@domains": path.resolve(__dirname, "./resources/js/domains"),
                "@ultra-images": path.resolve(
                    __dirname,
                    "./vendor/ultra/ultra-upload-manager/resources/ts/assets/images"
                ),
            },
            preserveSymlinks: true,
        },
        server: {
            host: viteHost,
            port: vitePort,
            hmr: {
                host: "localhost",
                port: vitePort,
                overlay: true,
            },
            watch: {
                usePolling: true,
                interval: 1000,
                ignored: [
                    "**/node_modules/**",
                    "**/.git/**",
                    "**/.venv/**",
                    "**/storage/**",
                    "**/bootstrap/cache/**",
                    "**/database/**",
                    "**/public/build/**",
                    "**/vendor/**",
                    "**/*.log",
                ],
            },
            fs: {
                allow: [
                    ".",
                    path.resolve(__dirname, "./packages/ultra"),
                    path.resolve(__dirname, "./vendor/ultra"),
                ],
            },
        },
        build: {
            outDir: "public/build",
            manifest: "manifest.json",
            sourcemap: true,
            cssCodeSplit: true,
            chunkSizeWarningLimit: 1000,
            rollupOptions: {
                output: {
                    entryFileNames: `assets/[name]-[hash].js`,
                    chunkFileNames: `assets/[name]-[hash].js`,
                    assetFileNames: `assets/[name]-[hash].[ext]`,
                },
            },
        },
        optimizeDeps: {
            include: [
                "tailwindcss",
                "daisyui",
                "three",
                "three/examples/jsm/controls/OrbitControls.js",
                "react",
                "react-dom",
                "@react-three/fiber",
                "@react-three/drei",
                "gsap",
            ],
        },
    };
});
