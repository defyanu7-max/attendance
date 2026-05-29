import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    
    let hmrConfig = {};
    if (env.VITE_ASSET_URL) {
        try {
            const url = new URL(env.VITE_ASSET_URL);
            hmrConfig = {
                host: url.hostname,
                protocol: url.protocol === 'https:' ? 'wss' : 'ws',
            };
        } catch (e) {
            console.error('Invalid VITE_ASSET_URL:', env.VITE_ASSET_URL);
        }
    }

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
        server: {
            host: '0.0.0.0',
            hmr: hmrConfig,
        },
    };
});

