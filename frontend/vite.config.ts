import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
    root: '.',
    plugins: [vue()],
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
            },
        },
    },
    optimizeDeps: {
        include: ['@fortawesome/fontawesome-svg-core']
    },
    build: {
        outDir: './dist',
        emptyOutDir: true,
        sourcemap: true,
        rollupOptions: {
            input: './index.html'
        }
    }

})
