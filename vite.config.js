import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    build: {
        // Optimize chunk splitting
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('alpinejs')) {
                        return 'vendor';
                    }
                },
                // Better asset naming for caching
                assetFileNames: 'assets/[name]-[hash][extname]',
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
            },
        },
        // Increase chunk size warning limit
        chunkSizeWarningLimit: 1000,
        // Enable CSS code splitting
        cssCodeSplit: true,
        // Minify for production
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                passes: 2,
                pure_funcs: ['console.log', 'console.info'],
            },
            format: {
                comments: false,
            },
        },
        // Optimize asset inlining
        assetsInlineLimit: 4096,
        // Source maps for production debugging (optional - remove if not needed)
        sourcemap: false,
    },
    server: {
        // Hot reload optimization
        hmr: {
            overlay: true,
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
            // Use polling for better compatibility
            usePolling: false,
        },
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['alpinejs'],
    },
});
